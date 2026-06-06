<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Makassar');

/* === DB CONFIG (samakan dengan CRM) === */
$DB_HOST = 'localhost';
$DB_NAME = 'u1783223_bd_crm';
$DB_USER = 'u1783223_bd_crm';
$DB_PASS = 'finD0!bd.crm';

/** @var PDO|null $pdo */
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

/* === Helper functions === */
function db(): PDO
{
    global $pdo;
    if (!$pdo) {
        throw new RuntimeException('Database not connected');
    }
    return $pdo;
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

function today(): string
{
    return date('Y-m-d');
}
