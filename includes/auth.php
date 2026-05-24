<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['usuario_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /Taller-Aplicaciones-Web/login.php');
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if ($_SESSION['usuario_rol'] !== 'administrador') {
        header('Location: /Taller-Aplicaciones-Web/dashboard.php?error=acceso_denegado');
        exit;
    }
}

function currentUser(): array {
    return [
        'id'     => $_SESSION['usuario_id']   ?? null,
        'nombre' => $_SESSION['usuario_nombre'] ?? '',
        'rol'    => $_SESSION['usuario_rol']   ?? '',
    ];
}