/* ---------- DB CONFIG (SAMA DENGAN setting.php) ---------- */
$DB_HOST='localhost';
$DB_NAME='u1783223_bd_crm';
$DB_USER='u1783223_bd_crm';
$DB_PASS='finD0!bd.crm';

$dsn="mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
$opt=[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES=>false
];

try {
    $pdo=new PDO($dsn,$DB_USER,$DB_PASS,$opt);
} catch(Throwable $e){
    http_response_code(500);
    echo "<pre>DB connect failed: ".htmlspecialchars($e->getMessage())."</pre>";
    exit;
}

/* ---------- HELPERS ---------- */
function json_headers(): void {
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

function weekday_key(DateTime $d): string {
    $map=['sun','mon','tue','wed','thu','fri','sat'];
    return $map[(int)$d->format('w')];
}

/* ---------- LOAD GLOBALS (usd_to_idr, weekly_defaults, global_template) ---------- */
$GLOBAL_CUTOFF   = '13:00';
$USD_TO_IDR      = 16000;
$WEEKLY_DEFAULTS = null;
$GLOBAL_TEMPLATE = null;

if (!function_exists('get_dynamic_bca_rate')) {
    function get_dynamic_bca_rate($default_rate = 17595) {
        $cacheFile = __DIR__ . '/../../cart/bca_usd_rate.json';
        $cacheTime = 3600 * 6; // 6 jam cache

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if (isset($data['rate']) && is_numeric($data['rate'])) {
                return (float)$data['rate'];
            }
        }

        $url = "https://www.bca.co.id/id/informasi/kurs";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $html = curl_exec($ch);
        curl_close($ch);
        
        if ($html) {
            if (preg_match('/code="USD"[\s\S]*?rate-type="eRate-sell"[\s\S]*?<p>([\d\.,]+)<\/p>/i', $html, $matches)) {
                $rateStr = str_replace(['.', ','], ['', '.'], $matches[1]);
                if (is_numeric($rateStr)) {
                    $rate = (float)$rateStr;
                    file_put_contents($cacheFile, json_encode(['rate' => $rate, 'time' => time()]));
                    return $rate;
                }
            }
        }
        
        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if (isset($data['rate']) && is_numeric($data['rate'])) {
                return (float)$data['rate'];
            }
        }
        
        return $default_rate;
    }
}

try{
    $g = $pdo->query("SELECT cutoff_time, usd_to_idr, weekly_defaults, global_template FROM booking_globals ORDER BY id DESC LIMIT 1")->fetch();
    if($g){
        $GLOBAL_CUTOFF = $g['cutoff_time'] ?? '13:00';
        $db_usd_to_idr = (int)($g['usd_to_idr'] ?? 16000);
        $USD_TO_IDR    = (int)get_dynamic_bca_rate($db_usd_to_idr);
        $WEEKLY_DEFAULTS = $g['weekly_defaults'] ? json_decode($g['weekly_defaults'], true) : null;
        $GLOBAL_TEMPLATE = $g['global_template'] ? json_decode($g['global_template'], true) : null;
    } else {
        $USD_TO_IDR = (int)get_dynamic_bca_rate(16000);
    }
} catch(Throwable $e){
    $USD_TO_IDR = (int)get_dynamic_bca_rate(16000);
}

/* Default WEEKLY_DEFAULTS kalau belum ada */
if(!$WEEKLY_DEFAULTS){
    $WEEKLY_DEFAULTS = [
        'snorkeling' => [
            'sun'=>true,'mon'=>true,'tue'=>true,'wed'=>true,'thu'=>true,'fri'=>true,'sat'=>true
        ]
    ];
}
