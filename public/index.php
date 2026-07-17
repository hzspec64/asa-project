<?php

$path = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
);

$path = rtrim($path, '/');

switch ($path) {
    case '':
        require __DIR__ . '/../pages/index.php';
        break;

    case '/login':
        require __DIR__ . '/../pages/login.php';
        break;

    case '/adminer':
        require __DIR__ . '/../pages/adminer-5.4.4-mysql-en.php';
        break;

    case '/admin':
        require __DIR__ . '/../pages/admin/index.php';
        break;

    default:
        http_response_code(404);

        echo 'Page not found.';
        break;
}