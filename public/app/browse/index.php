<?php

declare(strict_types=1);

require_once __DIR__.'/../../includes/layout.php';
require_once __DIR__.'/../../includes/auth.php';

requireAuth();

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$prefix = '/app/browse/';
$file = str_starts_with($uriPath, $prefix)
    ? substr($uriPath, strlen($prefix))
    : 'welcome.txt';

$storageDir = realpath(__DIR__.'/../../storage') ?: (__DIR__.'/../../storage');
$target = $storageDir.DIRECTORY_SEPARATOR.$file;

renderHeader('Secure files');

if (! is_file($target)) {
    http_response_code(404);
    echo '<p>File not found.</p>';
    renderFooter();
    exit;
}

header('Content-Type: text/plain; charset=utf-8');
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

         $target = $storageDir.DIRECTORY_SEPARATOR.$file;
         $resolved = realpath($target);

         if ($resolved === false || ! str_starts_with($resolved, $storageDir)) {
            http_response_code(403);
            exit('Access denied.');
         }

         if (! is_file($resolved)) {
            http_response_code(404);
            exit('File not found.');
         }

         header('Content-Type: text/plain; charset=utf-8');
         readfile($resolved);
exit;
