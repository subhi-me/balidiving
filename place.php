<?php
/*********************************************************
 * Google Business Reviews (Business Profile API + OAuth2)
 * - Auth via OAuth2 (offline, refresh token)
 * - Ambil review via Business Profile API (accounts.locations.reviews.list)
 * - Cache ke MySQL (max refresh tiap 5 hari)
 * - Tampilkan hanya review <= 60 hari terakhir (~2 bulan)
 * - UI modern dengan Tailwind, data dari DB
 *********************************************************/

declare(strict_types=1);
date_default_timezone_set('Asia/Makassar');
session_start();

/* ========= DB CONFIG ========= */
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

/* ========= GOOGLE OAUTH + BUSINESS PROFILE CONFIG ========= */

// OAuth 2.0 Client ID (dari kamu)
$CLIENT_ID = '1058143510160-8ulo6s7erv6in25e5gjoqghvkah1jtqf.apps.googleusercontent.com';

// TODO: isi manual dari Google Cloud Console
$CLIENT_SECRET = 'ISI_CLIENT_SECRET_KAMU_DI_SINI';

// TODO: redirect URI HARUS sama persis dengan yang ada di Google Cloud Console
// Contoh: 'https://www.balidiving.com/google-reviews-gbp.php'
$REDIRECT_URI = 'https://www.balidiving.com/google-reviews-gbp.php';

// TODO: isi dengan accountId dan locationId Business Profile kamu
// Format resource location untuk API: accounts/{accountId}/locations/{locationId}
$GBIZ_ACCOUNT_ID  = 'YOUR_ACCOUNT_ID';
$GBIZ_LOCATION_ID = 'YOUR_LOCATION_ID';

// Label yang muncul di UI (judul tempat)
$GBIZ_LOCATION_LABEL = 'Bali Diving – Main Dive Center';

// Scope minimum untuk Business Profile API (reviews)
$OAUTH_SCOPES = [
    'https://www.googleapis.com/auth/business.manage'
];

// Resource name untuk location (dipakai di DB juga)
$GBP_LOCATION_RESOURCE = "accounts/{$GBIZ_ACCOUNT_ID}/locations/{$GBIZ_LOCATION_ID}";

/* ========= CACHING CONFIG ========= */

// Cache API maksimal setiap X hari
$CACHE_DAYS = 5;

// Batas review: maksimal 60 hari terakhir (~2 bulan)
$REVIEW_MAX_AGE_DAYS = 60;
$review_cutoff_time   = time() - ($REVIEW_MAX_AGE_DAYS * 86400);

/* ========= CONNECT DB (PDO) ========= */

$pdo = null;
$db_error = '';

try {
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    $db_error = $e->getMessage();
}

/* ========= HELPER FUNCTIONS ========= */

function now(): string { return date('Y-m-d H:i:s'); }

function migrate_tables(PDO $pdo): void
{
    // Tabel utama review
    $sqlReviews = "
        CREATE TABLE IF NOT EXISTS google_reviews (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            place_id VARCHAR(191) NOT NULL,
            review_id VARCHAR(191) NOT NULL,
            author_name VARCHAR(255) DEFAULT NULL,
            rating TINYINT UNSIGNED DEFAULT NULL,
            text TEXT DEFAULT NULL,
            relative_time_description VARCHAR(255) DEFAULT NULL,
            profile_photo_url VARCHAR(512) DEFAULT NULL,
            language VARCHAR(10) DEFAULT NULL,
            review_time INT UNSIGNED DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE KEY uniq_place_review (place_id, review_id),
            INDEX idx_place_time (place_id, review_time)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    // Tabel meta: info tempat + last fetch
    $sqlMeta = "
        CREATE TABLE IF NOT EXISTS google_reviews_meta (
            id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
            place_id VARCHAR(191) NOT NULL,
            place_name VARCHAR(255) DEFAULT NULL,
            avg_rating DECIMAL(3,2) DEFAULT NULL,
            user_ratings_total INT UNSIGNED DEFAULT NULL,
            last_fetched_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    // Tabel token OAuth
    $sqlOauth = "
        CREATE TABLE IF NOT EXISTS google_oauth_tokens (
            id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
            access_token TEXT,
            refresh_token TEXT,
            expires_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $pdo->exec($sqlReviews);
    $pdo->exec($sqlMeta);
    $pdo->exec($sqlOauth);
}

/* ========= META (REVIEW SUMMARY) ========= */

function get_meta(PDO $pdo, string $placeId): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM google_reviews_meta WHERE id = 1 AND place_id = :pid LIMIT 1");
    $stmt->execute([':pid' => $placeId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function upsert_meta(PDO $pdo, string $placeId, array $payload): void
{
    $stmt = $pdo->prepare("
        INSERT INTO google_reviews_meta 
            (id, place_id, place_name, avg_rating, user_ratings_total, last_fetched_at, created_at, updated_at)
        VALUES
            (1, :place_id, :place_name, :avg_rating, :user_ratings_total, :last_fetched_at, :created_at, :updated_at)
        ON DUPLICATE KEY UPDATE
            place_id = VALUES(place_id),
            place_name = VALUES(place_name),
            avg_rating = VALUES(avg_rating),
            user_ratings_total = VALUES(user_ratings_total),
            last_fetched_at = VALUES(last_fetched_at),
            updated_at = VALUES(updated_at)
    ");

    $now = now();
    $stmt->execute([
        ':place_id'           => $placeId,
        ':place_name'         => $payload['place_name'] ?? null,
        ':avg_rating'         => $payload['avg_rating'] ?? null,
        ':user_ratings_total' => $payload['user_ratings_total'] ?? null,
        ':last_fetched_at'    => $payload['last_fetched_at'] ?? $now,
        ':created_at'         => $payload['created_at'] ?? $now,
        ':updated_at'         => $now,
    ]);
}

function should_refresh(?array $meta, int $cacheDays): bool
{
    if (!$meta || empty($meta['last_fetched_at'])) {
        return true;
    }
    $last = strtotime($meta['last_fetched_at']);
    if ($last === false) return true;

    $diffSeconds = time() - $last;
    $diffDays = $diffSeconds / 86400;
    return $diffDays >= $cacheDays;
}

/* ========= OAUTH TOKEN STORAGE ========= */

function get_oauth_tokens(PDO $pdo): ?array
{
    $stmt = $pdo->query("SELECT * FROM google_oauth_tokens WHERE id = 1 LIMIT 1");
    $row = $stmt->fetch();
    return $row ?: null;
}

function save_oauth_tokens(PDO $pdo, string $accessToken, ?string $refreshToken, ?int $expiresIn): void
{
    $now = now();
    $expiresAt = null;

    if ($expiresIn !== null) {
        $expiresAtTs = time() + $expiresIn - 60; // buffer 60 detik
        $expiresAt    = date('Y-m-d H:i:s', $expiresAtTs);
    }

    $stmt = $pdo->prepare("
        INSERT INTO google_oauth_tokens
            (id, access_token, refresh_token, expires_at, created_at, updated_at)
        VALUES
            (1, :access_token, :refresh_token, :expires_at, :created_at, :updated_at)
        ON DUPLICATE KEY UPDATE
            access_token = VALUES(access_token),
            refresh_token = CASE 
                WHEN VALUES(refresh_token) IS NULL OR VALUES(refresh_token) = '' 
                    THEN google_oauth_tokens.refresh_token
                ELSE VALUES(refresh_token)
            END,
            expires_at = VALUES(expires_at),
            updated_at = VALUES(updated_at)
    ");

    $stmt->execute([
        ':access_token'  => $accessToken,
        ':refresh_token' => $refreshToken,
        ':expires_at'    => $expiresAt,
        ':created_at'    => $now,
        ':updated_at'    => $now,
    ]);
}

function clear_oauth_tokens(PDO $pdo): void
{
    $pdo->exec("DELETE FROM google_oauth_tokens WHERE id = 1");
}

/* ========= OAUTH 2.0 FLOW ========= */

function build_auth_url(string $clientId, string $redirectUri, array $scopes): string
{
    $state = bin2hex(random_bytes(16));
    $_SESSION['google_oauth_state'] = $state;

    $params = [
        'client_id'             => $clientId,
        'redirect_uri'          => $redirectUri,
        'response_type'         => 'code',
        'scope'                 => implode(' ', $scopes),
        'access_type'           => 'offline',
        'include_granted_scopes'=> 'true',
        'prompt'                => 'consent',
        'state'                 => $state,
    ];

    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

function exchange_code_for_tokens(string $code, string $clientId, string $clientSecret, string $redirectUri): array
{
    $tokenUrl = 'https://oauth2.googleapis.com/token';

    $postData = [
        'code'          => $code,
        'client_id'     => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['ok' => false, 'error' => $curlError ?: 'Unknown cURL error'];
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        return ['ok' => false, 'error' => 'Failed to decode token JSON'];
    }

    if (isset($data['error'])) {
        return ['ok' => false, 'error' => $data['error'] . ': ' . ($data['error_description'] ?? '')];
    }

    return [
        'ok'           => true,
        'access_token' => $data['access_token'] ?? null,
        'refresh_token'=> $data['refresh_token'] ?? null,
        'expires_in'   => isset($data['expires_in']) ? (int)$data['expires_in'] : null,
        'raw'          => $data,
    ];
}

function refresh_access_token(string $refreshToken, string $clientId, string $clientSecret): array
{
    $tokenUrl = 'https://oauth2.googleapis.com/token';

    $postData = [
        'client_id'     => $clientId,
        'client_secret' => $clientSecret,
        'refresh_token' => $refreshToken,
        'grant_type'    => 'refresh_token',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['ok' => false, 'error' => $curlError ?: 'Unknown cURL error'];
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        return ['ok' => false, 'error' => 'Failed to decode token JSON'];
    }

    if (isset($data['error'])) {
        return ['ok' => false, 'error' => $data['error'] . ': ' . ($data['error_description'] ?? '')];
    }

    return [
        'ok'           => true,
        'access_token' => $data['access_token'] ?? null,
        'expires_in'   => isset($data['expires_in']) ? (int)$data['expires_in'] : null,
        'raw'          => $data,
    ];
}

function get_valid_access_token(PDO $pdo, string $clientId, string $clientSecret): array
{
    $tokens = get_oauth_tokens($pdo);
    if (!$tokens) {
        return ['ok' => false, 'access_token' => null, 'need_auth' => true, 'error' => 'Not connected to Google Business API'];
    }

    $accessToken  = $tokens['access_token'] ?? null;
    $refreshToken = $tokens['refresh_token'] ?? null;
    $expiresAt    = $tokens['expires_at'] ?? null;

    if (!$accessToken) {
        return ['ok' => false, 'access_token' => null, 'need_auth' => true, 'error' => 'No access token stored'];
    }

    // cek expired
    if ($expiresAt && strtotime($expiresAt) <= time()) {
        if (!$refreshToken) {
            return ['ok' => false, 'access_token' => null, 'need_auth' => true, 'error' => 'Access token expired and no refresh token'];
        }

        $res = refresh_access_token($refreshToken, $clientId, $clientSecret);
        if (!$res['ok'] || empty($res['access_token'])) {
            return ['ok' => false, 'access_token' => null, 'need_auth' => true, 'error' => 'Failed to refresh access token: ' . ($res['error'] ?? '')];
        }

        save_oauth_tokens($pdo, $res['access_token'], $refreshToken, $res['expires_in'] ?? null);
        $accessToken = $res['access_token'];
    }

    return ['ok' => true, 'access_token' => $accessToken, 'need_auth' => false, 'error' => null];
}

/* ========= FETCH REVIEWS FROM BUSINESS PROFILE API ========= */

function star_enum_to_int(?string $enum): ?int
{
    if ($enum === null) return null;
    switch ($enum) {
        case 'ONE':   return 1;
        case 'TWO':   return 2;
        case 'THREE': return 3;
        case 'FOUR':  return 4;
        case 'FIVE':  return 5;
        default:      return null;
    }
}

/**
 * Ambil review via Business Profile API (paginated), lalu return:
 * [
 *   'ok' => bool,
 *   'avg_rating' => float|null,
 *   'review_count' => int|null,
 *   'reviews' => [ array review mentah dari API ],
 *   'error' => string|null
 * ]
 */
function fetch_reviews_from_gbp(string $accessToken, string $accountId, string $locationId): array
{
    $baseUrl = "https://mybusiness.googleapis.com/v4/accounts/{$accountId}/locations/{$locationId}/reviews";
    $allReviews = [];
    $nextPageToken = null;
    $avgRating = null;
    $reviewCount = null;

    do {
        $url = $baseUrl;
        $params = [
            'pageSize' => 50,
            'orderBy'  => 'updateTime desc',
        ];
        if ($nextPageToken) {
            $params['pageToken'] = $nextPageToken;
        }

        $url .= '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [
                'ok' => false,
                'error' => $curlError ?: 'Unknown cURL error when calling Business Profile API',
            ];
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return [
                'ok' => false,
                'error' => 'Failed to decode Business Profile reviews JSON',
            ];
        }

        if (isset($data['error'])) {
            return [
                'ok' => false,
                'error' => $data['error']['message'] ?? 'Business Profile API error',
            ];
        }

        if (!empty($data['reviews']) && is_array($data['reviews'])) {
            $allReviews = array_merge($allReviews, $data['reviews']);
        }

        // beberapa implementasi menambahkan ringkasan rating/ count
        if (isset($data['averageRating'])) {
            $avgRating = (float)$data['averageRating'];
        }
        if (isset($data['reviewCount'])) {
            $reviewCount = (int)$data['reviewCount'];
        }

        $nextPageToken = $data['nextPageToken'] ?? null;
    } while ($nextPageToken);

    return [
        'ok'           => true,
        'avg_rating'   => $avgRating,
        'review_count' => $reviewCount,
        'reviews'      => $allReviews,
        'error'        => null,
    ];
}

/* ========= UPSERT REVIEWS KE DB (HANYA <= 60 HARI) ========= */

function upsert_reviews(PDO $pdo, string $placeId, array $reviewsRaw): void
{
    global $review_cutoff_time;

    if (empty($reviewsRaw) || !is_array($reviewsRaw)) {
        return;
    }

    $sql = "
        INSERT INTO google_reviews
            (place_id, review_id, author_name, rating, text, 
             relative_time_description, profile_photo_url, language, review_time, 
             created_at, updated_at)
        VALUES
            (:place_id, :review_id, :author_name, :rating, :text,
             :relative_time_description, :profile_photo_url, :language, :review_time,
             :created_at, :updated_at)
        ON DUPLICATE KEY UPDATE
            author_name = VALUES(author_name),
            rating = VALUES(rating),
            text = VALUES(text),
            relative_time_description = VALUES(relative_time_description),
            profile_photo_url = VALUES(profile_photo_url),
            language = VALUES(language),
            review_time = VALUES(review_time),
            updated_at = VALUES(updated_at)
    ";

    $stmt = $pdo->prepare($sql);
    $now  = now();

    foreach ($reviewsRaw as $rev) {

        $reviewId = $rev['reviewId'] ?? null;
        if (!$reviewId) {
            continue;
        }

        $createTime = $rev['createTime'] ?? null;
        $ts = $createTime ? strtotime($createTime) : null;

        // Filter: hanya review yang masih dalam 60 hari
        if ($ts && $ts < $review_cutoff_time) {
            continue;
        }

        $ratingEnum = $rev['starRating'] ?? null;
        $ratingInt  = star_enum_to_int($ratingEnum);

        $reviewer   = $rev['reviewer'] ?? [];
        $authorName = $reviewer['displayName'] ?? 'Anonymous';
        $photoUrl   = $reviewer['profilePhotoUrl'] ?? null;

        $comment    = $rev['comment'] ?? '';
        $language   = null;

        // relative_time_description bisa dikalkulasi sendiri, tapi untuk sekarang kosong,
        // nanti di UI kita hitung manual saat display.
        $relative = null;

        $stmt->execute([
            ':place_id'                  => $placeId,
            ':review_id'                 => $reviewId,
            ':author_name'               => $authorName,
            ':rating'                    => $ratingInt,
            ':text'                      => $comment,
            ':relative_time_description' => $relative,
            ':profile_photo_url'         => $photoUrl,
            ':language'                  => $language,
            ':review_time'               => $ts,
            ':created_at'                => $now,
            ':updated_at'                => $now,
        ]);
    }
}

/* ========= GET REVIEWS UNTUK UI (<= 60 HARI) ========= */

function get_reviews(PDO $pdo, string $placeId, int $limit = 20): array
{
    global $review_cutoff_time;

    $stmt = $pdo->prepare("
        SELECT *
        FROM google_reviews
        WHERE place_id = :pid
          AND (review_time IS NULL OR review_time >= :cutoff)
        ORDER BY review_time DESC, id DESC
        LIMIT :lim
    ");
    $stmt->bindValue(':pid', $placeId, PDO::PARAM_STR);
    $stmt->bindValue(':cutoff', $review_cutoff_time, PDO::PARAM_INT);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll() ?: [];
}

/* ========= SMALL HELPER FOR "X days ago" ========= */

function human_diff_from_timestamp(?int $ts): ?string
{
    if (!$ts) return null;
    $diff = time() - $ts;
    if ($diff < 0) $diff = 0;

    $days = floor($diff / 86400);
    if ($days <= 0) {
        $hours = floor($diff / 3600);
        if ($hours <= 0) {
            $mins = max(1, floor($diff / 60));
            return $mins . ' minutes ago';
        }
        return $hours . ' hours ago';
    }
    if ($days === 1) return '1 day ago';
    if ($days < 30) return $days . ' days ago';
    $months = floor($days / 30);
    if ($months === 1) return '1 month ago';
    return $months . ' months ago';
}

/* ========= MAIN LOGIC: HANDLE OAUTH & FETCH ========= */

$api_error    = null;
$oauth_error  = null;
$need_auth    = false;
$meta         = null;
$reviews      = [];
$has_tokens   = false;
$forceRefresh = isset($_GET['refresh']) && $_GET['refresh'] === '1';

// Pastikan DB siap
if ($pdo && !$db_error) {
    migrate_tables($pdo);

    // ---- Handle logout manual dari param ----
    if (isset($_GET['disconnect']) && $_GET['disconnect'] === '1') {
        clear_oauth_tokens($pdo);
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }

    // ---- Handle OAuth callback (code) ----
    if (isset($_GET['code'])) {
        $code  = $_GET['code'];
        $state = $_GET['state'] ?? null;

        if (!empty($_SESSION['google_oauth_state']) && $state !== $_SESSION['google_oauth_state']) {
            $oauth_error = 'Invalid OAuth state. Please try connecting again.';
        } else {
            $res = exchange_code_for_tokens($code, $CLIENT_ID, $CLIENT_SECRET, $REDIRECT_URI);
            if ($res['ok'] && !empty($res['access_token'])) {
                save_oauth_tokens($pdo, $res['access_token'], $res['refresh_token'] ?? null, $res['expires_in'] ?? null);
                unset($_SESSION['google_oauth_state']);
                // redirect bersih agar ?code= tidak nongkrong di URL
                header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
                exit;
            } else {
                $oauth_error = 'Failed to exchange authorization code: ' . ($res['error'] ?? 'Unknown error');
            }
        }
    }

    // ---- Handle tombol "Connect" ----
    if (isset($_GET['connect']) && $_GET['connect'] === '1') {
        $authUrl = build_auth_url($CLIENT_ID, $REDIRECT_URI, $OAUTH_SCOPES);
        header('Location: ' . $authUrl);
        exit;
    }

    // ---- Cek token ada / valid ----
    $tokens = get_oauth_tokens($pdo);
    $has_tokens = (bool)$tokens;

    $accessTokenRes = null;
    if ($has_tokens) {
        $accessTokenRes = get_valid_access_token($pdo, $CLIENT_ID, $CLIENT_SECRET);
        if (!$accessTokenRes['ok']) {
            $need_auth   = $accessTokenRes['need_auth'] ?? false;
            $oauth_error = $accessTokenRes['error'] ?? null;
        }
    } else {
        $need_auth = true;
    }

    // ---- Meta dari DB ----
    $meta = get_meta($pdo, $GBP_LOCATION_RESOURCE);

    // ---- Fetch dari API kalau ada access token dan butuh refresh ----
    if (!$need_auth && $accessTokenRes && !empty($accessTokenRes['access_token'])) {
        $accessToken = $accessTokenRes['access_token'];

        if ($forceRefresh || should_refresh($meta, $CACHE_DAYS)) {
            $res = fetch_reviews_from_gbp($accessToken, $GBIZ_ACCOUNT_ID, $GBIZ_LOCATION_ID);
            if ($res['ok']) {
                upsert_meta($pdo, $GBP_LOCATION_RESOURCE, [
                    'place_name'         => $GBIZ_LOCATION_LABEL,
                    'avg_rating'         => $res['avg_rating'] ?? null,
                    'user_ratings_total' => $res['review_count'] ?? null,
                    'last_fetched_at'    => now(),
                ]);

                upsert_reviews($pdo, $GBP_LOCATION_RESOURCE, $res['reviews'] ?? []);
                $meta = get_meta($pdo, $GBP_LOCATION_RESOURCE);
            } else {
                $api_error = $res['error'] ?? 'Unknown Business Profile API error';
            }
        }
    } elseif ($need_auth && !$oauth_error) {
        $oauth_error = 'Not connected to Google Business Profile API. Please connect first.';
    }

    // ---- Ambil review untuk UI dari DB (cache) ----
    $reviews = get_reviews($pdo, $GBP_LOCATION_RESOURCE, 20);
}

// Setelah semua logic, baru include layout (agar header redirect tetap bisa jalan di atas)
include('01-start.php');

?>

<!-- ========= UI: GOOGLE REVIEWS (TAILWIND) ========= -->
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-100 tracking-tight">
            Google Reviews – Business Profile
        </h1>
        <p class="mt-2 text-slate-400 text-sm md:text-base">
            Reviews are synced via <span class="font-semibold">Google Business Profile API</span>,
            cached every <span class="font-semibold"><?php echo (int)$CACHE_DAYS; ?> days</span>,
            dan hanya menampilkan ulasan dalam
            <span class="font-semibold"><?php echo (int)$REVIEW_MAX_AGE_DAYS; ?> hari</span> terakhir.
        </p>
    </div>

    <?php if ($db_error): ?>
        <div class="mb-6 rounded-2xl border border-red-500/40 bg-red-900/40 px-4 py-3 text-sm text-red-100">
            <div class="font-semibold mb-1">Database Error</div>
            <div><?php echo htmlspecialchars($db_error, ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
    <?php endif; ?>

    <?php if ($oauth_error): ?>
        <div class="mb-6 rounded-2xl border border-amber-500/40 bg-amber-900/40 px-4 py-3 text-sm text-amber-50">
            <div class="font-semibold mb-1">Google OAuth Notice</div>
            <div><?php echo htmlspecialchars($oauth_error, ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
    <?php endif; ?>

    <?php if ($api_error && !$oauth_error): ?>
        <div class="mb-6 rounded-2xl border border-amber-500/40 bg-amber-900/40 px-4 py-3 text-sm text-amber-50">
            <div class="font-semibold mb-1">Business Profile API Notice</div>
            <div><?php echo htmlspecialchars($api_error, ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
    <?php endif; ?>

    <?php
        $placeName  = $meta['place_name']         ?? $GBIZ_LOCATION_LABEL;
        $avgRating  = $meta['avg_rating']         ?? null;
        $totalCount = $meta['user_ratings_total'] ?? null;
        $lastFetch  = $meta['last_fetched_at']    ?? null;
    ?>

    <!-- HEADER CARD -->
    <div class="mb-8 rounded-3xl border border-sky-500/30 bg-gradient-to-br from-slate-900/80 via-slate-900/60 to-sky-900/60 shadow-xl shadow-sky-900/40 p-6 md:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="space-y-2">
            <p class="text-xs uppercase tracking-[0.2em] text-sky-400/80">
                Google Business Profile
            </p>
            <h2 class="text-2xl md:text-3xl font-semibold text-slate-50">
                <?php echo htmlspecialchars($placeName, ENT_QUOTES, 'UTF-8'); ?>
            </h2>
            <div class="flex items-center gap-3">
                <?php if ($avgRating !== null): ?>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-bold text-amber-300">
                            <?php echo number_format((float)$avgRating, 1); ?>
                        </span>
                        <span class="text-sm text-slate-400">/ 5.0</span>
                    </div>
                <?php endif; ?>

                <div class="flex items-center gap-1 text-amber-300">
                    <?php
                    $ratingVal = $avgRating !== null ? (float)$avgRating : 0;
                    for ($i = 1; $i <= 5; $i++):
                        $full = $ratingVal >= $i;
                    ?>
                        <span class="text-lg">
                            <?php echo $full ? '★' : '☆'; ?>
                        </span>
                    <?php endfor; ?>
                </div>

                <?php if ($totalCount !== null): ?>
                    <span class="text-xs md:text-sm text-slate-400">
                        Based on <?php echo (int)$totalCount; ?> Google reviews
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($lastFetch): ?>
                <p class="text-xs text-slate-500">
                    Last synced:
                    <span class="text-slate-300 font-medium">
                        <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($lastFetch)), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    (server time)
                </p>
            <?php endif; ?>
        </div>

        <div class="flex flex-col md:items-end gap-3 w-full md:w-auto">
            <div class="flex flex-wrap gap-2 justify-end">
                <?php if ($need_auth): ?>
                    <a href="?connect=1"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-full border border-emerald-400/60 bg-emerald-500/10 text-emerald-100 text-sm font-medium hover:bg-emerald-500/20 hover:border-emerald-300 transition-colors">
                        <span class="mr-2 text-xs">●</span>
                        Connect Google Business
                    </a>
                <?php else: ?>
                    <a href="?refresh=1"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-full border border-sky-400/60 bg-sky-500/10 text-sky-100 text-sm font-medium hover:bg-sky-500/20 hover:border-sky-300 transition-colors">
                        <span class="mr-2 text-xs">⟳</span>
                        Manual Refresh
                    </a>
                <?php endif; ?>

                <?php if ($has_tokens): ?>
                    <a href="?disconnect=1"
                       class="inline-flex items-center justify-center px-3 py-2 rounded-full border border-slate-600/80 bg-slate-800/60 text-slate-200 text-xs font-medium hover:bg-slate-700/80 hover:border-slate-400 transition-colors">
                        Disconnect
                    </a>
                <?php endif; ?>
            </div>

            <p class="text-[11px] text-slate-500 max-w-xs text-right mt-1">
                Only admins should click Connect / Disconnect. Visitors see cached reviews only.
            </p>
        </div>
    </div>

    <!-- REVIEWS GRID -->
    <?php if (!empty($reviews)): ?>
        <div class="grid gap-4 md:gap-6 md:grid-cols-2">
            <?php foreach ($reviews as $rev): ?>
                <?php
                    $author   = $rev['author_name'] ?? 'Anonymous';
                    $rating   = $rev['rating'] ?? null;
                    $text     = $rev['text'] ?? '';
                    $photo    = $rev['profile_photo_url'] ?? '';
                    $revTime  = $rev['review_time'] ?? null;
                    $dateStr  = $revTime ? date('d M Y', (int)$revTime) : '';
                    $relative = $revTime ? human_diff_from_timestamp((int)$revTime) : null;
                ?>
                <article class="group relative rounded-3xl border border-slate-700/60 bg-slate-900/60 p-5 md:p-6 shadow-lg shadow-slate-900/40 hover:border-sky-400/70 hover:shadow-sky-900/40 transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="relative">
                            <?php if ($photo): ?>
                                <img src="<?php echo htmlspecialchars($photo, ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo htmlspecialchars($author, ENT_QUOTES, 'UTF-8'); ?>"
                                     class="w-11 h-11 rounded-full object-cover border border-slate-600/70">
                            <?php else: ?>
                                <div class="w-11 h-11 rounded-full bg-sky-500/20 border border-sky-400/40 flex items-center justify-center text-sky-200 font-semibold text-lg">
                                    <?php echo strtoupper(mb_substr($author, 0, 1, 'UTF-8')); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="text-sm font-semibold text-slate-50 truncate">
                                    <?php echo htmlspecialchars($author, ENT_QUOTES, 'UTF-8'); ?>
                                </h3>
                                <?php if ($rating !== null): ?>
                                    <div class="flex items-center gap-1 text-amber-300 text-xs">
                                        <span><?php echo number_format((float)$rating, 1); ?></span>
                                        <span>★</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-500">
                                <?php if ($relative): ?>
                                    <span><?php echo htmlspecialchars($relative, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                                <?php if ($dateStr): ?>
                                    <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                                    <span><?php echo htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                            </div>

                            <p class="mt-3 text-sm text-slate-200 leading-relaxed line-clamp-5">
                                <?php echo nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8')); ?>
                            </p>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="rounded-3xl border border-slate-700/60 bg-slate-900/60 p-6 text-sm text-slate-300">
            <div class="font-semibold mb-1">Belum ada review terbaru dalam 2 bulan terakhir</div>
            <p class="text-slate-400">
                Tidak ada review yang berusia kurang dari <?php echo (int)$REVIEW_MAX_AGE_DAYS; ?> hari
                di cache saat ini. Jika Anda admin, coba tekan
                <span class="font-semibold text-sky-300">Manual Refresh</span> setelah menghubungkan
                Google Business Profile.
            </p>
        </div>
    <?php endif; ?>
</div>

<?php include('03-end.php'); ?>
