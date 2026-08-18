<?php
// Router for PHP built-in server
// - Serve static files directly
// - Fall back to index.php for everything else

$uri = urldecode(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
$filePath = __DIR__ . $uri;

// Return false tells PHP server to handle the request as static file
if ($uri !== "/" && file_exists($filePath) && is_file($filePath)) {
    return false;
}

// Route everything else to index.php
$_SERVER["SCRIPT_NAME"] = "/index.php";
$_SERVER["SCRIPT_FILENAME"] = __DIR__ . "/index.php";
require __DIR__ . "/index.php";