<?php $rol = currentUser()['rol']; ?>
<nav class="sidebar" id="sidebar">
  <ul>
    <li><a href="/distribuciones-caribe/dashboard.php">Dashboard</a></li>
    <li><a href="/distribuciones-caribe/modules/clientes/index.php">Clientes</a></li>
    <li><a href="/distribuciones-caribe/modules/productos/index.php">Productos</a></li>
    <li><a href="/distribuciones-caribe/modules/ventas/index.php">Ventas</a></li>
    <li><a href="/distribuciones-caribe/modules/reportes/index.php">Reportes</a></li>
    <?php if ($rol === 'administrador'): ?>
    <li class="sidebar-divider"></li>
    <li><a href="/distribuciones-caribe/modules/usuarios/index.php">Usuarios</a></li>
    <?php endif; ?>
  </ul>
</nav>
<main class="content">