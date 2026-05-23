<?php

require_once __DIR__ . '/config/shared/shared.php';

require_login();
logout_user();
redirect('login_create.php');
