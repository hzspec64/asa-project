<?php
require_once __DIR__ . '/../core/session.php';

session_destroy();

header('Location: /signin');
exit;