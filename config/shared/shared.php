<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $to): void
{
    header('Location: ' . $to);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

function sanitize_trim(?string $v): string
{
    return trim((string)$v);
}

function tool_current_stock(int $toolId): int
{
    $pdo = db();
    $stmt = $pdo->prepare("
        SELECT
            COALESCE(SUM(CASE WHEN move_type = 'IN' THEN quantity ELSE 0 END), 0) -
            COALESCE(SUM(CASE WHEN move_type = 'OUT' THEN quantity ELSE 0 END), 0) AS current_stock
        FROM stock_moves
        WHERE tool_id = :tool_id
    ");
    $stmt->execute([':tool_id' => $toolId]);
    $row = $stmt->fetch();
    return (int)($row['current_stock'] ?? 0);
}

function render_header(string $title, bool $requireAuth = true): void
{
    if ($requireAuth) {
        require_login();
    }

    $flash = get_flash();
    $userName = is_logged_in() ? ($_SESSION['user']['name'] ?? '') : '';

    echo '<!doctype html>';
    echo '<html lang="pt-br">';
    echo '<head>';
    echo '<meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . e($title) . ' - ' . e(APP_NAME) . '</title>';
    echo '<link rel="stylesheet" href="assets/css/style.css">';
    echo '</head>';
    echo '<body>';

    echo '<div class="topbar">';
    echo '<div class="container topbar-inner">';
    echo '<div class="brand"><a href="dashboard.php">' . e(APP_NAME) . '</a></div>';
    echo '<nav class="nav">';
    if (is_logged_in()) {
        echo '<a href="dashboard.php">Painel</a>';
        echo '<a href="tool_list.php">Ferramentas</a>';
        echo '<a href="move_list.php">Movimentações</a>';
        echo '<a href="move_create.php">Nova Movimentação</a>';
        echo '<a href="logout_delete.php">Sair</a>';
    } else {
        echo '<a href="login_create.php">Entrar</a>';
        echo '<a href="register_create.php">Cadastrar</a>';
    }
    echo '</nav>';
    echo '</div>';
    echo '</div>';

    echo '<div class="container">';
    echo '<div class="page-head">';
    echo '<h1>' . e($title) . '</h1>';
    if (is_logged_in()) {
        echo '<div class="muted">Logado como: ' . e($userName) . '</div>';
    }
    echo '</div>';

    if ($flash && isset($flash['type'], $flash['message'])) {
        $cls = $flash['type'] === 'success' ? 'alert success' : 'alert error';
        echo '<div class="' . e($cls) . '" data-autohide="true">' . e($flash['message']) . '</div>';
    }
}

function render_footer(): void
{
    echo '</div>';
    echo '<script src="assets/js/app.js"></script>';
    echo '</body>';
    echo '</html>';
}