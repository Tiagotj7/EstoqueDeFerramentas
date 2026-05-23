<?php

require_once __DIR__ . '/config/shared/shared.php';

render_header('Login', false);

$pdo = db();

$errors = [];
$email = '';
$password = '';

if (is_post()) {
    $email = sanitize_trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email válido é obrigatório';
    }
    if ($password === '') {
        $errors[] = 'Senha é obrigatória';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'invalid credentials';
        } else {
            login_user($user);
            redirect('dashboard.php');
        }
    }
}

echo '<div class="card">';
if ($errors) {
    echo '<div class="alert error"><ul>';
    foreach ($errors as $er) {
        echo '<li>' . e($er) . '</li>';
    }
    echo '</ul></div>';
}

echo '<form method="post" action="login_create.php" novalidate>';
echo '<div class="grid">';
echo '<div>';
echo '<label for="email">Email</label>';
echo '<input id="email" name="email" type="email" required value="' . e($email) . '">';
echo '</div>';
echo '<div>';
echo '<label for="password">Senha</label>';
echo '<input id="password" name="password" type="password" required value="">';
echo '</div>';
echo '</div>';
echo '<div class="actions">';
echo '<button type="submit">Entrar</button>';
echo '<a class="btn secondary" href="register_create.php">Cadastrar</a>';
echo '</div>';
echo '</form>';
echo '</div>';

render_footer();