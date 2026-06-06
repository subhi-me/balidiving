<?php
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'headers' => getallheaders(),
  'method'  => $_SERVER['REQUEST_METHOD'] ?? null,
  'content_type' => $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? null),
  'raw_body_preview' => substr(file_get_contents('php://input') ?: '', 0, 200)
], JSON_PRETTY_PRINT);
