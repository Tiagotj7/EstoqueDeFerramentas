<?php

require_once __DIR__ . '/config/shared/shared.php';

require_login();

$pdo = db();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('error', 'Invalid tool');
    redirect('tool_list.php');
}

$stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM stock_moves WHERE tool_id = :id");
$stmt->execute([':id' => $id]);
$cnt = (int)($stmt->fetch()['cnt'] ?? 0);

if ($cnt > 0) {
    set_flash('error', 'Cannot delete tool with stock moves');
    redirect('tool_list.php');
}

$stmt = $pdo->prepare("DELETE FROM tools WHERE id = :id");
$stmt->execute([':id' => $id]);

set_flash('success', 'tool deleted successfully');
redirect('tool_list.php');
