<?php
/*************************************************
 * save_data.php — Save JSON/Text to a file
 * Default target: ./data.txt  (relative to this file)
 *************************************************/

// -------- CORS --------
$allowed_origins = [
  'https://apps.subhi.me',
  'https://subhi.me',
  'http://localhost',
];
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
if (in_array($origin, $allowed_origins)) {
  header("Access-Control-Allow-Origin: $origin");
} else {
  header("Access-Control-Allow-Origin: *");
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

// -------- Target path --------
$defaultRelativePath = __DIR__ . '/data.txt';
$requestedFile = isset($_GET['file']) ? trim($_GET['file']) : '';

if ($requestedFile !== '') {
  // prevent path traversal
  $requestedFile = str_replace(['..', '\\'], ['', '/'], $requestedFile);
  $targetPath = realpath(__DIR__) . '/' . ltrim($requestedFile, '/');
} else {
  $targetPath = $defaultRelativePath;
}

// ensure directory exists
$dir = dirname($targetPath);
if (!is_dir($dir)) {
  if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>false,'error'=>'Failed to create directory','dir'=>$dir]);
    exit;
  }
}

// -------- Read input --------
$raw = file_get_contents('php://input');
$ctype = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
$dataToWrite = '';
$isJson = false;

if (stripos($ctype, 'application/json') !== false) {
  $decoded = json_decode($raw, true);
  if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
    // invalid JSON -> save raw text
    $dataToWrite = $raw;
  } else {
    $dataToWrite = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $isJson = true;
  }
} else {
  $dataToWrite = $raw;
}

// -------- Write file --------
$bytes = @file_put_contents($targetPath, $dataToWrite);
if ($bytes === false) {
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'ok'=>false,
    'error'=>'Failed to write file. Check permissions.',
    'path'=>$targetPath
  ]);
  exit;
}
@chmod($targetPath, 0644);

// -------- Response --------
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'ok'=>true,
  'bytes'=>$bytes,
  'path'=>$targetPath,
  'as'=>$isJson ? 'json' : 'text'
]);
