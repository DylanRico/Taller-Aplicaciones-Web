<?php
$pageTitle = 'Reportes';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
  <h2>Reportes</h2>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
  <a href="ventas_diarias.php" class="stat-card" style="text-decoration:none;cursor:pointer">
    <span class="stat-icon">📊</span>
    <div class="stat-label" style="font-size:.95rem;color:var(--text-main);font-weight:600;margin-top:.35rem">Ventas Diarias</div>
    <div class="stat-label" style="margin-top:.25rem">Resumen de ingresos por día</div>
  </a>
  <a href="productos_mas_vendidos.php" class="stat-card" style="text-decoration:none;cursor:pointer">
    <span class="stat-icon">🏆</span>
    <div class="stat-label" style="font-size:.95rem;color:var(--text-main);font-weight:600;margin-top:.35rem">Más Vendidos</div>
    <div class="stat-label" style="margin-top:.25rem">Productos con mayor rotación</div>
  </a>
  <a href="inventario_bajo.php" class="stat-card" style="text-decoration:none;cursor:pointer;box-shadow:0 0 0 1px rgba(243,156,18,.3),0 2px 12px rgba(0,0,0,.2)">
    <span class="stat-icon">⚠️</span>
    <div class="stat-label" style="font-size:.95rem;color:var(--text-main);font-weight:600;margin-top:.35rem">Inventario Bajo</div>
    <div class="stat-label" style="margin-top:.25rem">Productos con stock crítico</div>
  </a>
  <a href="clientes_frecuentes.php" class="stat-card" style="text-decoration:none;cursor:pointer">
    <span class="stat-icon">⭐</span>
    <div class="stat-label" style="font-size:.95rem;color:var(--text-main);font-weight:600;margin-top:.35rem">Clientes Frecuentes</div>
    <div class="stat-label" style="margin-top:.25rem">Compradores con más transacciones</div>
  </a>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
