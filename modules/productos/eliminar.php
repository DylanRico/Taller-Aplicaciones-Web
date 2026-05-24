<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id  = (int)($_POST['id'] ?? 0);
    $pdo = getDB();
    $check = $pdo->prepare('SELECT COUNT(*) FROM detalle_ventas WHERE producto_id=?');
    $check->execute([$id]);
    if ($check->fetchColumn() > 0) {
        flashMessage('danger', 'No se puede eliminar: el producto tiene ventas registradas.');
    } else {
        $pdo->prepare('UPDATE productos SET activo=0 WHERE id=?')->execute([$id]);
        flashMessage('success', 'Producto desactivado del inventario.');
    }
}
redirect('/distribuciones-caribe/modules/productos/index.php');
