<?php
declare(strict_types=1);

/**
 * Auto-migrate simple tables:
 * - booking_globals
 * - booking_catalog
 * - booking_date_snapshots
 *
 * + seed minimal data kalau kosong, supaya UI tidak blank.
 */

if (!function_exists('run_booking_migrations')) {
    function run_booking_migrations(): void
    {
        $pdo = db();

        /* === booking_globals === */
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS booking_globals (
                id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                usd_to_idr DECIMAL(18,4) NOT NULL DEFAULT 16000,
                rate_mode ENUM('manual','auto') NOT NULL DEFAULT 'manual',
                updated_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        /* seed satu row kalau kosong */
        $countGlobals = (int)$pdo->query("SELECT COUNT(*) FROM booking_globals")->fetchColumn();
        if ($countGlobals === 0) {
            $stmt = $pdo->prepare("
                INSERT INTO booking_globals (usd_to_idr, rate_mode, updated_at)
                VALUES (:rate, 'manual', :updated_at)
            ");
            $stmt->execute([
                ':rate'       => 16000,
                ':updated_at' => now(),
            ]);
        }

        /* === booking_catalog === */
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS booking_catalog (
                id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                category VARCHAR(64) NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT NULL,
                base_usd DECIMAL(18,2) NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $countCatalog = (int)$pdo->query("SELECT COUNT(*) FROM booking_catalog")->fetchColumn();
        if ($countCatalog === 0) {
            $seed = [
                ['Snorkeling',  'Bali Snorkeling – Padang Bai', 85.00],
                ['Try Diving',  'Intro Dive – Tanjung Benoa',   120.00],
                ['Fun Diving',  'Fun Dive – Nusa Penida 2x',    180.00],
                ['PADI Course', 'PADI Open Water Diver',        450.00],
                ['Special',     'Mola-Mola Season Package',     650.00],
                ['Add-on',      'Underwater Photo Package',     60.00],
            ];
            $stmt = $pdo->prepare("
                INSERT INTO booking_catalog (category, name, description, base_usd, is_active, created_at, updated_at)
                VALUES (:category, :name, :description, :base_usd, 1, :created_at, :updated_at)
            ");
            foreach ($seed as [$cat, $name, $usd]) {
                $stmt->execute([
                    ':category'    => $cat,
                    ':name'        => $name,
                    ':description' => null,
                    ':base_usd'    => $usd,
                    ':created_at'  => now(),
                    ':updated_at'  => now(),
                ]);
            }
        }

        /* === booking_date_snapshots === */
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS booking_date_snapshots (
                id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                activity_id INT UNSIGNED NOT NULL,
                snap_date DATE NOT NULL,
                is_open TINYINT(1) NOT NULL DEFAULT 1,
                price_override_usd DECIMAL(18,2) NULL,
                note VARCHAR(255) NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX idx_activity_date (activity_id, snap_date),
                CONSTRAINT fk_snap_activity FOREIGN KEY (activity_id)
                    REFERENCES booking_catalog(id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }
}

/* jalankan migrasi setiap kali file ini di-include */
if (empty($db_error)) {
    try {
        run_booking_migrations();
    } catch (Throwable $e) {
        // kalau mau, bisa log error ke file
        // file_put_contents(__DIR__.'/booking_migrations_error.log', $e->getMessage().PHP_EOL, FILE_APPEND);
    }
}
