<?php

function is_logged_in(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login_create.php');
        exit;
    }
}

function current_user_id(): ?int
{
    return is_logged_in() ? (int)$_SESSION['user']['id'] : null;
}

function login_user(array $userRow): void
{
    $_SESSION['user'] = [
        'id' => (int)$userRow['id'],
        'name' => $userRow['name'],
        'email' => $userRow['email'],
    ];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}