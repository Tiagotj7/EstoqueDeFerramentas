<?php

require_once __DIR__ . '/config/shared/shared.php';

render_header('Create Stock Move', true);

$pdo = db();

$errors = [];
$tool_id = '';
$move_type = 'IN';
$quantity = '';
$note = '';

$toolsStmt = $pdo->query("SELECT id, name FROM tools ORDER BY name ASC");
$tools = $toolsStmt->fetchAll();

if (is_post()) {
    $tool_id = sanitize_trim($_POST['tool_id'] ?? '');
    $move_type = sanitize_trim($_POST['move_type'] ?? 'IN');
    $quantity = sanitize_trim($_POST['quantity'] ?? '');
    $note = sanitize_trim($_POST['note'] ?? '');

    if ($tool_id === '' || !ctype_digit($tool_id) || (int)$tool_id <= 0) {
        $errors[] = 'Tool is required';
    }

    if (!in_array($move_type, ['IN', 'OUT'], true)) {
        $errors[] = 'Move type must be IN or OUT';
    }

    if ($quantity === '' || !ctype_digit($quantity) || (int)$quantity <= 0) {
        $errors[] = 'Quantity must be an integer > 0';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM tools WHERE id = :id");
        $stmt->execute([':id' => (int)$tool_id]);
        if (!$stmt->fetch()) {
            $errors[] = 'Tool not found';
        }
    }

    if (!$errors && $move_type === 'OUT') {
        $current = tool_current_stock((int)$tool_id);
        if ((int)$quantity > $current) {
            $errors[] = 'Cannot move OUT. Quantity exceeds current stock';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare("
            INSERT INTO stock_moves (tool_id, user_id, move_type, quantity, note, moved_at)
            VALUES (:tool_id, :user_id, :move_type, :quantity, :note, NOW())
        ");
        $stmt->execute([
            ':tool_id' => (int)$tool_id,
            ':user_id' => current_user_id(),
            ':move_type' => $move_type,
            ':quantity' => (int)$quantity,
            ':note' => $note === '' ? null : $note
        ]);

        set_flash('success', 'stock move created successfully');
        redirect('move_list.php');
    }
}

echo '<div class="card">';
if ($errors) {
    echo '<div class="alert error"><ul>';
    foreach ($errors as $er) echo '<li>' . e($er) . '</li>';
    echo '</ul></div>';
}

echo '<form method="post" action="move_create.php" novalidate>';
echo '<div class="grid">';

echo '<div>';
echo '<label for="tool_id">Tool</label>';
echo '<select id="tool_id" name="tool_id" required>';
echo '<option value="">Select...</option>';
foreach ($tools as $t) {
    $selected = ((string)$t['id'] === (string)$tool_id) ? ' selected' : '';
    echo '<option value="' . (int)$t['id'] . '"' . $selected . '>' . e($t['name']) . '</option>';
}
echo '</select>';
echo '</div>';

echo '<div>';
echo '<label for="move_type">Move type</label>';
echo '<select id="move_type" name="move_type" required>';
echo '<option value="IN"' . ($move_type === 'IN' ? ' selected' : '') . '>IN</option>';
echo '<option value="OUT"' . ($move_type === 'OUT' ? ' selected' : '') . '>OUT</option>';
echo '</select>';
echo '</div>';

echo '<div>';
echo '<label for="quantity">Quantity</label>';
echo '<input id="quantity" name="quantity" type="number" min="1" step="1" required value="' . e($quantity) . '">';
echo '</div>';

echo '<div>';
echo '<label for="note">Note (optional)</label>';
echo '<input id="note" name="note" type="text" value="' . e($note) . '">';
echo '</div>';

echo '</div>';

echo '<div class="actions">';
echo '<button type="submit">Save</button>';
echo '<a class="btn secondary" href="move_list.php">Back</a>';
echo '</div>';

echo '</form>';
echo '</div>';

render_footer();
