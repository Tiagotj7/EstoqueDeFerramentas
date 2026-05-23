<?php

require_once __DIR__ . '/config/shared/shared.php';

render_header('Register', false);

$pdo = db();

$errors = [];
$name = '';
$email = '';
$password = '';

if (is_post()) {
    $name = sanitize_trim($_POST['name'] ?? '');
    $email = sanitize_trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($name === '') {
        $errors[] = 'Name is required';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }

    if ($password === '' || strlen($password) < 6) {
        $errors[] = 'Password is required (min 6 chars)';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $exists = $stmt->fetch();

        if ($exists) {
            $errors[] = 'Email already in use';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password_hash, created_at)
                VALUES (:name, :email, :password_hash, NOW())
            ");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password_hash' => $hash
            ]);

            set_flash('success', 'register completed successfully');
            redirect('login_create.php');
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

echo '<form method="post" action="register_create.php" novalidate>';
echo '<div class="grid">';
echo '<div>';
echo '<label for="name">Name</label>';
echo '<input id="name" name="name" type="text" required value="' . e($name) . '">';
echo '</div>';
echo '<div>';
echo '<label for="email">Email</label>';
echo '<input id="email" name="email" type="email" required value="' . e($email) . '">';
echo '</div>';
echo '<div>';
echo '<label for="password">Password</label>';
echo '<input id="password" name="password" type="password" required minlength="6" value="">';
echo '</div>';
echo '</div>';
echo '<div class="actions">';
echo '<button type="submit">Create account</button>';
echo '<a class="btn secondary" href="login_create.php">Back to login</a>';
echo '</div>';
echo '</form>';
echo '</div>';

render_footer();
