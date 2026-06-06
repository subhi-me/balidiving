<?php
// crm_schema.php
require_once __DIR__.'/config.php';

/* ====== SCHEMA HELPERS ====== */
function table_exists(PDO $pdo, string $name): bool {
    $st = $pdo->prepare("
        SELECT 1 FROM information_schema.tables
        WHERE table_schema = DATABASE() AND table_name = :t
    ");
    $st->execute([':t' => $name]);
    return (bool)$st->fetchColumn();
}

function col_exists(PDO $pdo, string $table, string $col): bool {
    $st = $pdo->prepare("
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = :t
          AND column_name = :c
    ");
    $st->execute([':t' => $table, ':c' => $col]);
    return (bool)$st->fetchColumn();
}

function qexec(PDO $pdo, string $sql): void {
    try {
        $pdo->exec($sql);
    } catch (Throwable $e) {
        error_log($e->getMessage().' SQL='.$sql);
    }
}

/* ====== TABLE: leads ====== */
if (!table_exists($pdo, 'leads')) {
    qexec($pdo, "
        CREATE TABLE leads (
            id              VARCHAR(64)  PRIMARY KEY,
            `column`        VARCHAR(32)  NOT NULL DEFAULT 'leads',
            name            VARCHAR(255) NOT NULL,
            email           VARCHAR(255) NULL,
            phone           VARCHAR(64)  NULL,
            country         VARCHAR(64)  NULL,
            source          VARCHAR(64)  NULL,
            package         VARCHAR(128) NULL,
            cert            VARCHAR(64)  NULL,
            dive_date       DATE         NULL,
            pax             INT          NULL DEFAULT 0,
            budget          DECIMAL(12,2) NULL DEFAULT 0,
            payment_status  VARCHAR(20)  NOT NULL DEFAULT 'unpaid',
            payment_method  VARCHAR(32)  NULL,
            deposit_amount  DECIMAL(12,2) NULL DEFAULT 0,
            deposit_currency VARCHAR(3)  NOT NULL DEFAULT 'USD',
            deposit_rate    DECIMAL(12,4) NULL,
            booking_stage   VARCHAR(32)  NULL,
            archived_stage  VARCHAR(32)  NULL,
            created_at      DATETIME     NOT NULL,
            updated_at      DATETIME     NOT NULL,
            INDEX idx_col(`column`),
            INDEX idx_email(email),
            INDEX idx_phone(phone)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
} else {
    $defs = [
        "`column` VARCHAR(32) NOT NULL DEFAULT 'leads'",
        "email VARCHAR(255) NULL",
        "phone VARCHAR(64) NULL",
        "country VARCHAR(64) NULL",
        "source VARCHAR(64) NULL",
        "package VARCHAR(128) NULL",
        "cert VARCHAR(64) NULL",
        "dive_date DATE NULL",
        "pax INT NULL DEFAULT 0",
        "budget DECIMAL(12,2) NULL DEFAULT 0",
        "payment_status VARCHAR(20) NOT NULL DEFAULT 'unpaid'",
        "payment_method VARCHAR(32) NULL",
        "deposit_amount DECIMAL(12,2) NULL DEFAULT 0",
        "deposit_currency VARCHAR(3) NOT NULL DEFAULT 'USD'",
        "deposit_rate DECIMAL(12,4) NULL",
        "booking_stage VARCHAR(32) NULL",
        "archived_stage VARCHAR(32) NULL",
        "created_at DATETIME NOT NULL",
        "updated_at DATETIME NOT NULL"
    ];
    foreach ($defs as $def) {
        $col = trim(strtok($def, ' '), '`');
        if (!col_exists($pdo, 'leads', $col)) {
            qexec($pdo, "ALTER TABLE leads ADD COLUMN {$def}");
        }
    }
}

/* ====== TABLE: trip_history ====== */
if (!table_exists($pdo, 'trip_history')) {
    qexec($pdo, "
        CREATE TABLE trip_history (
            id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            lead_id         VARCHAR(64) NOT NULL,
            package         VARCHAR(128) NULL,
            dive_date       DATE NULL,
            pax             INT NULL,
            budget          DECIMAL(12,2) NULL,
            payment_status  VARCHAR(20) NULL,
            payment_method  VARCHAR(32) NULL,
            deposit_amount  DECIMAL(12,2) NULL,
            note            TEXT NULL,
            created_at      DATETIME NOT NULL,
            INDEX (lead_id),
            CONSTRAINT fk_trip_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}
