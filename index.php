<?php
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) {
    header('Location: /distribuciones-caribe/dashboard.php');
} else {
    header('Location: /distribuciones-caribe/login.php');
}
exit;
