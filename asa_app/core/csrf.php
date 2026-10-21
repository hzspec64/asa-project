<?php

function csrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrfField()
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrfToken())
        . '">';
}

function csrfVerify()
{
    $submitted = $_POST['csrf_token'] ?? '';

    return $submitted !== ''
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $submitted);
}
