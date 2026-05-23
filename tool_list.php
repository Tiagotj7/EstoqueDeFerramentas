<?php

require_once __DIR__ . '/config/shared/shared.php';

render_header('Tools', true);

$pdo = db();

$stmt = $pdo->query("SELECT id, name, category, unit, min_stock, created_at FROM tools ORDER BY name ASC");
$tools = $stmt->fetchAll();

echo '<div class="card">';
echo '<div class="actions">';
echo '<a class="btn" href="tool_create.php">New Tool</a>';
echo '</div>';

if (!$tools) {
    echo '<p class="muted">No tools found.</p>';
} else {
    echo '<table class="table">';
    echo '<thead><tr><th>Name</th><th>Category</th><th>Unit</th><th>Min stock</th><th>Current stock</th><th>Actions</th></tr></thead><tbody>';
    foreach ($tools as $t) {
        $current = tool_current_stock((int)$t['id']);
        echo '<tr>';
        echo '<td>' . e($t['name']) . '</td>';
        echo '<td>' . e($t['category']) . '</td>';
        echo '<td>' . e($t['unit']) . '</td>';
        echo '<td>' . e((string)$t['min_stock']) . '</td>';
        echo '<td>' . e((string)$current) . '</td>';
        echo '<td class="inline-links">';
        echo '<a href="tool_edit.php?id=' . (int)$t['id'] . '">Edit</a>';
        echo '<a href="tool_delete.php?id=' . (int)$t['id'] . '" data-confirm="Delete this tool?">Delete</a>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

echo '</div>';

render_footer();
