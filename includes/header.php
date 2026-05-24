<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
$flash = getFlash();
$user  = currentUser();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Distribuciones Caribe S.A.S' ?></title>
  <link rel="stylesheet" href="/Taller-Aplicaciones-Web/assets/css/style.css">
</head>
<body>
<div class="layout">
  <!-- Topbar -->
  <header class="topbar">
    <button class="sidebar-toggle" onclick="toggleSidebar()">&#9776;</button>
    <span class="topbar-brand">Distribuciones Caribe S.A.S</span>
    <div class="topbar-user">
      <span><?= sanitize($user['nombre']) ?></span>
      <span class="badge-rol"><?= sanitize($user['rol']) ?></span>
      <a href="/Taller-Aplicaciones-Web/logout.php" class="btn-logout">Salir</a>
    </div>
  </header>
<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type'] ?>"><?= sanitize($flash['msg']) ?></div>
<?php endif; ?>