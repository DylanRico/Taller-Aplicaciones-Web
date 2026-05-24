<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id  = (int)($_POST['id'] ?? 0);
    $pdo = getDB();
    $check = $pdo->prepare('SELECT COUNT(*) FROM ventas WHERE cliente_id=?');
    $check->execute([$id]);
    if ($check->fetchColumn() > 0) {
        flashMessage('danger', 'No se puede eliminar: el cliente tiene ventas registradas.');
    } else {
        $pdo->prepare('DELETE FROM clientes WHERE id=?')->execute([$id]);
        flashMessage('success', 'Cliente eliminado.');
    }
}
redirect('/Taller-Aplicaciones-Web/modules/clientes/index.php');
