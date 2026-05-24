<?php
$pageTitle = 'Inventario Bajo';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$pdo  = getDB();
$rows = $pdo->query(
    "SELECT p.codigo, p.nombre, c.nombre AS categoria,
            p.stock, p.stock_minimo
     FROM productos p JOIN categorias c ON c.id=p.categoria_id
     WHERE p.activo=1 AND p.stock <= p.stock_minimo
     ORDER BY p.stock ASC"
)->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
  <h2>Inventario Bajo</h2>
  <a href="index.php" class="btn btn-secondary">Volver</a>
</div>
<?php if (!empty($rows)): ?>
<div class="alert alert-warning"><?= count($rows) ?> productos con stock por debajo del minimo.</div>
<?php endif; ?>
<div class="card">
  <div class="table-wrapper">
    <table>
      <thead><tr><th>Codigo</th><th>Producto</th><th>Categoria</th><th>Stock actual</th><th>Stock minimo</th><th>Diferencia</th></tr></thead>
      <tbody>
        <?php if (empty($rows)): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--text-muted)">Todo el inventario esta en niveles normales.</td></tr>
        <?php endif; ?>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= sanitize($r['codigo']) ?></td>
          <td><?= sanitize($r['nombre']) ?></td>
          <td><?= sanitize($r['categoria']) ?></td>
          <td class="badge-stock-low"><?= $r['stock'] ?></td>
          <td><?= $r['stock_minimo'] ?></td>
          <td class="badge-stock-low"><?= $r['stock'] - $r['stock_minimo'] ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
