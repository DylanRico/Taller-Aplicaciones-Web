<?php
$pageTitle = 'Reportes';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<h2 style="margin-bottom:1.5rem">Reportes</h2>
<div class="stats-grid">
  <a href="ventas_diarias.php" class="card" style="text-decoration:none;text-align:center;cursor:pointer">
    <div class="stat-value" style="font-size:2.5rem">&#128202;</div>
    <div class="stat-label" style="font-size:1rem;margin-top:.5rem">Ventas Diarias</div>
  </a>
  <a href="productos_mas_vendidos.php" class="card" style="text-decoration:none;text-align:center;cursor:pointer">
    <div class="stat-value" style="font-size:2.5rem">&#127942;</div>
    <div class="stat-label" style="font-size:1rem;margin-top:.5rem">Productos mas Vendidos</div>
  </a>
  <a href="inventario_bajo.php" class="card" style="text-decoration:none;text-align:center;cursor:pointer">
    <div class="stat-value" style="font-size:2.5rem">&#9888;&#65039;</div>
    <div class="stat-label" style="font-size:1rem;margin-top:.5rem">Inventario Bajo</div>
  </a>
  <a href="clientes_frecuentes.php" class="card" style="text-decoration:none;text-align:center;cursor:pointer">
    <div class="stat-value" style="font-size:2.5rem">&#128101;</div>
    <div class="stat-label" style="font-size:1rem;margin-top:.5rem">Clientes Frecuentes</div>
  </a>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
