<?php
// Simple PHP built-in server router for the Forbidden City Portal
// Serves static files directly, falls back to index.php for everything else

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$filePath = __DIR__ . $uri;

// If requesting a real file that exists, serve it
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false; // Let PHP built-in server handle it
}

// Otherwise, route everything to index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
require __DIR__ . '/index.php';