<?php

declare(strict_types=1);

require_once __DIR__.'/../includes/layout.php';

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$prefix = '/browse/';
$file = str_starts_with($uriPath, $prefix)
    ? substr($uriPath, strlen($prefix))
    : 'welcome.txt';

$storageDir = realpath(__DIR__.'/../storage') ?: (__DIR__.'/../storage');
$target = $storageDir.DIRECTORY_SEPARATOR.$file;

renderHeader('Browse files');

$file = basename(str_replace('\\', '/', $file));
if ($file === '' || str_contains($file, '..')) {
    http_response_code(400);
    exit('Invalid file name.');
}

$storageDir = realpath(__DIR__.'/../storage');
if ($storageDir === false) {
    http_response_code(500);
    exit;
}

$resolved = realpath($storageDir.DIRECTORY_SEPARATOR.$file);
if ($resolved === false || ! str_starts_with($resolved, $storageDir) || ! is_file($resolved)) {
    http_response_code(404);
    exit('File not found.');
}

header('Content-Type: text/plain; charset=utf-8');
readfile($resolved);
exit;
