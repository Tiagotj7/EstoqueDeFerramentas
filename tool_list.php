<?php

require_once __DIR__ . '/config/shared/shared.php';

render_header('Ferramentas', true);

$pdo = db();

$stmt = $pdo->query("SELECT id, name, category, unit, min_stock, created_at FROM tools ORDER BY name ASC");
$tools = $stmt->fetchAll();

echo '<div class="card">';
echo '<div class="actions">';
echo '<a class="btn" href="tool_create.php">Nova Ferramenta</a>';
echo '</div>';

if (!$tools) {
    echo '<p class="muted">Nenhuma ferramenta encontrada.</p>';
} else {
    echo '<table class="table">';
    echo '<thead><tr><th>Nome</th><th>Categoria</th><th>Unidade</th><th>Estoque mínimo</th><th>Estoque atual</th><th>Ações</th></tr></thead><tbody>';
    foreach ($tools as $t) {
        $current = tool_current_stock((int)$t['id']);
        echo '<tr>';
        echo '<td>' . e($t['name']) . '</td>';
        echo '<td>' . e($t['category']) . '</td>';
        echo '<td>' . e($t['unit']) . '</td>';
        echo '<td>' . e((string)$t['min_stock']) . '</td>';
        echo '<td>' . e((string)$current) . '</td>';
        echo '<td class="inline-links">';
        echo '<a href="tool_edit.php?id=' . (int)$t['id'] . '">Editar</a>';
        echo '<a href="tool_delete.php?id=' . (int)$t['id'] . '" data-confirm="Excluir esta ferramenta?">Excluir</a>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

echo '</div>';

render_footer();