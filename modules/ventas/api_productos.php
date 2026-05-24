<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
requireLogin();
header('Content-Type: application/json');

$q    = trim($_GET['q'] ?? '');
$pdo  = getDB();
$stmt = $pdo->prepare(
    'SELECT id, codigo, nombre, precio, stock FROM productos
     WHERE activo=1 AND stock>0 AND (nombre LIKE ? OR codigo LIKE ?) LIMIT 10'
);
$stmt->execute(["%$q%", "%$q%"]);
echo json_encode($stmt->fetchAll());
