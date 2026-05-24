<?php
$pageTitle = 'Productos mas Vendidos';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$pdo  = getDB();
$rows = $pdo->query(
    "SELECT p.codigo, p.nombre, c.nombre AS categoria,
            SUM(dv.cantidad) AS total_vendido,
            SUM(dv.subtotal) AS total_ingresos
     FROM detalle_ventas dv
     JOIN productos p ON p.id=dv.producto_id
     JOIN categorias c ON c.id=p.categoria_id
     JOIN ventas v ON v.id=dv.venta_id AND v.estado='activa'
     GROUP BY p.id ORDER BY total_vendido DESC LIMIT 20"
)->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
  <h2>Productos mas Vendidos</h2>
  <a href="index.php" class="btn btn-secondary">Volver</a>
</div>
<div class="card">
  <div class="table-wrapper">
    <table>
      <thead><tr><th>#</th><th>Codigo</th><th>Producto</th><th>Categoria</th><th>Unidades vendidas</th><th>Ingresos totales</th></tr></thead>
      <tbody>
        <?php if (empty($rows)): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--text-muted)">Sin datos.</td></tr>
        <?php endif; ?>
        <?php foreach ($rows as $i => $r): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= sanitize($r['codigo']) ?></td>
          <td><?= sanitize($r['nombre']) ?></td>
          <td><?= sanitize($r['categoria']) ?></td>
          <td><?= $r['total_vendido'] ?></td>
          <td><?= formatMoney((float)$r['total_ingresos']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
