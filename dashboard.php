<?php

require_once __DIR__ . '/config/shared/shared.php';

render_header('Dashboard', true);

$pdo = db();

$stmt = $pdo->query("
    SELECT
        t.id,
        t.name,
        t.category,
        t.unit,
        t.min_stock,
        COALESCE(SUM(CASE WHEN sm.move_type = 'IN' THEN sm.quantity ELSE 0 END), 0) -
        COALESCE(SUM(CASE WHEN sm.move_type = 'OUT' THEN sm.quantity ELSE 0 END), 0) AS current_stock
    FROM tools t
    LEFT JOIN stock_moves sm ON sm.tool_id = t.id
    GROUP BY t.id, t.name, t.category, t.unit, t.min_stock
    ORDER BY t.name ASC
");
$tools = $stmt->fetchAll();

echo '<div class="card">';
echo '<div class="inline-links">';
echo '<a class="btn" href="tool_create.php">New Tool</a> ';
echo '<a class="btn" href="move_create.php">New Stock Move</a>';
echo '</div>';

if (!$tools) {
    echo '<p class="muted">No tools registered.</p>';
} else {
    echo '<table class="table">';
    echo '<thead><tr>';
    echo '<th>Tool name</th><th>Category</th><th>Unit</th><th>Min stock</th><th>Current stock</th><th>Indicator</th><th>Actions</th>';
    echo '</tr></thead><tbody>';

    foreach ($tools as $t) {
        $current = (int)$t['current_stock'];
        $min = (int)$t['min_stock'];
        $low = $current < $min;

        echo '<tr>';
        echo '<td>' . e($t['name']) . '</td>';
        echo '<td>' . e($t['category']) . '</td>';
        echo '<td>' . e($t['unit']) . '</td>';
        echo '<td>' . e((string)$min) . '</td>';
        echo '<td>' . e((string)$current) . '</td>';
        echo '<td>' . ($low ? '<span class="badge low">LOW</span>' : '-') . '</td>';
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