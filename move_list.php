<?php

require_once __DIR__ . '/config/shared/shared.php';

render_header('Stock Move History', true);

$pdo = db();

$stmt = $pdo->query("
    SELECT
        sm.id,
        sm.move_type,
        sm.quantity,
        sm.note,
        sm.moved_at,
        t.name AS tool_name,
        u.name AS user_name
    FROM stock_moves sm
    INNER JOIN tools t ON t.id = sm.tool_id
    INNER JOIN users u ON u.id = sm.user_id
    ORDER BY sm.moved_at DESC, sm.id DESC
");
$moves = $stmt->fetchAll();

echo '<div class="card">';
echo '<div class="actions">';
echo '<a class="btn" href="move_create.php">New Move</a>';
echo '</div>';

if (!$moves) {
    echo '<p class="muted">No stock moves found.</p>';
} else {
    echo '<table class="table">';
    echo '<thead><tr><th>Date</th><th>Tool</th><th>User</th><th>Type</th><th>Quantity</th><th>Note</th></tr></thead><tbody>';
    foreach ($moves as $m) {
        echo '<tr>';
        echo '<td>' . e($m['moved_at']) . '</td>';
        echo '<td>' . e($m['tool_name']) . '</td>';
        echo '<td>' . e($m['user_name']) . '</td>';
        echo '<td>' . e($m['move_type']) . '</td>';
        echo '<td>' . e((string)$m['quantity']) . '</td>';
        echo '<td>' . e((string)($m['note'] ?? '')) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

echo '</div>';

render_footer();
