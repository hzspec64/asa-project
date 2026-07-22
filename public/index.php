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

    case '/adminer':
        require __DIR__ . '/../pages/adminer-5.4.4-mysql-en.php';
        break;

    case '/admin':
        require __DIR__ . '/../pages/admin/index.php';
        break;
    case '/admin/inventory':
        require __DIR__ . '/../pages/admin/inventory.php';
        break;
    case '/admin/create-product':
        require __DIR__ . '/../pages/admin/create-product.php';
        break;
    case '/admin/reports':
        require __DIR__ . '/../pages/admin/reports.php';
        break;
    case '/admin/404':
        require __DIR__ . '/../pages/admin/404.php';
        break;
    case '/admin/docs':
        require __DIR__ . '/../pages/admin/docs.php';
        break;

    case '/signin':
        require __DIR__ . '/../pages/signin.php';
        break;
    case '/signup':
        require __DIR__ . '/../pages/signup.php';
        break;

    default:
        http_response_code(404);

        echo 'Page not found.';
        break;
}