<?php
require_once __DIR__ . '/session.php';

if (!isset($_SESSION['user'])) {
    header('Location: /signin');

    exit;
}