<?php

require_once __DIR__ . '/config/shared/shared.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

redirect('login_create.php');