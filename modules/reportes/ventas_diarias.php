<?php
$pageTitle = 'Reporte: Ventas Diarias';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$pdo   = getDB();
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');

$stmt = $pdo->prepare(
    "SELECT DATE(v.creado_en) AS fecha, COUNT(*) AS num_ventas,
            SUM(v.total) AS total_dia
     FROM ventas v WHERE v.estado='activa'
       AND DATE(v.creado_en) BETWEEN ? AND ?
     GROUP BY DATE(v.creado_en) ORDER BY fecha DESC"
);
$stmt->execute([$desde, $hasta]);
$filas = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
  <h2>Ventas Diarias</h2>
  <a href="index.php" class="btn btn-secondary">Volver</a>
</div>
<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem;align-items:center">
    <label>Desde:</label>
    <input type="date" name="desde" value="<?= $desde ?>" class="form-control" style="width:160px">
    <label>Hasta:</label>
    <input type="date" name="hasta" value="<?= $hasta ?>" class="form-control" style="width:160px">
    <button class="btn btn-primary">Filtrar</button>
  </form>
  <div class="table-wrapper">
    <table>
      <thead><tr><th>Fecha</th><th>Numero de ventas</th><th>Total del dia</th></tr></thead>
      <tbody>
        <?php if (empty($filas)): ?>
        <tr><td colspan="3" style="text-align:center;color:var(--text-muted)">Sin datos para este periodo.</td></tr>
        <?php endif; ?>
        <?php foreach ($filas as $f): ?>
        <tr>
          <td><?= date('d/m/Y', strtotime($f['fecha'])) ?></td>
          <td><?= $f['num_ventas'] ?></td>
          <td><?= formatMoney((float)$f['total_dia']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!empty($filas)): ?>
        <tr style="font-weight:700;border-top:2px solid var(--border)">
          <td>TOTAL</td>
          <td><?= array_sum(array_column($filas,'num_ventas')) ?></td>
          <td><?= formatMoney(array_sum(array_column($filas,'total_dia'))) ?></td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
