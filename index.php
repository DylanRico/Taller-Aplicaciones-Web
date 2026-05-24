<?php
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) {
    header('Location: /Taller-Aplicaciones-Web/dashboard.php');
} else {
    header('Location: /Taller-Aplicaciones-Web/login.php');
}
exit;
