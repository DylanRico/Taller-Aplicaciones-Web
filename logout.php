<?php
require_once __DIR__ . '/includes/auth.php';
session_destroy();
header('Location: /Taller-Aplicaciones-Web/login.php');
exit;
