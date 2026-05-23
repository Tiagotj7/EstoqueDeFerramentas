<?php

require_once __DIR__ . '/config/shared/shared.php';

render_header('Editar Ferramenta', true);

$pdo = db();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    set_flash('error', 'Ferramenta inválida');
    redirect('tool_list.php');
}

$stmt = $pdo->prepare("SELECT id, name, category, unit, min_stock FROM tools WHERE id = :id");
$stmt->execute([':id' => $id]);
$tool = $stmt->fetch();

if (!$tool) {
    set_flash('error', 'Ferramenta não encontrada');
    redirect('tool_list.php');
}

$errors = [];
$name = $tool['name'];
$category = $tool['category'];
$unit = $tool['unit'];
$min_stock = (string)$tool['min_stock'];

if (is_post()) {
    $name = sanitize_trim($_POST['name'] ?? '');
    $category = sanitize_trim($_POST['category'] ?? '');
    $unit = sanitize_trim($_POST['unit'] ?? '');
    $min_stock = sanitize_trim($_POST['min_stock'] ?? '');

    if ($name === '') $errors[] = 'Nome da ferramenta é obrigatório';
    if ($category === '') $errors[] = 'Categoria é obrigatória';
    if ($unit === '') $errors[] = 'Unidade é obrigatória';
    if ($min_stock === '' || !ctype_digit($min_stock) || (int)$min_stock < 0) $errors[] = 'Estoque mínimo deve ser um inteiro >= 0';

    if (!$errors) {
        $stmt = $pdo->prepare("
            UPDATE tools
            SET name = :name, category = :category, unit = :unit, min_stock = :min_stock
            WHERE id = :id
        ");
        $stmt->execute([
            ':name' => $name,
            ':category' => $category,
            ':unit' => $unit,
            ':min_stock' => (int)$min_stock,
            ':id' => $id
        ]);

        set_flash('success', 'ferramenta atualizada com sucesso');
        redirect('tool_list.php');
    }
}

echo '<div class="card">';
if ($errors) {
    echo '<div class="alert error"><ul>';
    foreach ($errors as $er) echo '<li>' . e($er) . '</li>';
    echo '</ul></div>';
}

echo '<form method="post" action="tool_edit.php?id=' . (int)$id . '" novalidate>';
echo '<div class="grid">';
echo '<div><label for="name">Nome</label><input id="name" name="name" required type="text" value="' . e($name) . '"></div>';
echo '<div><label for="category">Categoria</label><input id="category" name="category" required type="text" value="' . e($category) . '"></div>';
echo '<div><label for="unit">Unidade</label><input id="unit" name="unit" required type="text" value="' . e($unit) . '"></div>';
echo '<div><label for="min_stock">Estoque mínimo</label><input id="min_stock" name="min_stock" required type="number" min="0" step="1" value="' . e($min_stock) . '"></div>';
echo '</div>';
echo '<div class="actions">';
echo '<button type="submit">Atualizar</button>';
echo '<a class="btn secondary" href="tool_list.php">Voltar</a>';
echo '</div>';
echo '</form>';
echo '</div>';

render_footer();