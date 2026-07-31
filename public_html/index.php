<?php

$path = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
);

// Exceptions that don't follow the simple "route -> pages/route.php" pattern
$exceptions = [
    '/'        => '/index',
    '/admin'   => '/admin/index',
    '/adminer' => '/adminer-5.4.4-mysql-en', // consider removing/renaming in production
];

// Normalize: strip trailing slash (except root)
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

$notFound = function () {
    http_response_code(404);
    require __DIR__ . '/../pages/404.php';
    exit;
};

// Only allow letters, numbers, dashes, underscores, and slashes in the path.
// This blocks ../, null bytes, and anything else nasty.
if (!preg_match('#^/[a-zA-Z0-9_\-/]*$#', $path)) {
    $notFound();
}

$relative = $exceptions[$path] ?? $path;
$file = __DIR__ . '/../pages' . $relative . '.php';

// Resolve symlinks/.. and make sure we're still inside pages/
$pagesDir = realpath(__DIR__ . '/../pages');
$resolved = realpath($file);

if ($resolved === false || strpos($resolved, $pagesDir) !== 0 || !is_file($resolved)) {
    $notFound();
}

require $resolved;