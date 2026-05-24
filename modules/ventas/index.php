<?php
$pageTitle = 'Ventas';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$pdo   = getDB();
$fecha = $_GET['fecha'] ?? date('Y-m-d');
$stmt  = $pdo->prepare(
    "SELECT v.*, c.nombre AS cliente FROM ventas v
     JOIN clientes c ON c.id=v.cliente_id
     WHERE DATE(v.creado_en)=? ORDER BY v.creado_en DESC"
);
$stmt->execute([$fecha]);
$ventas = $stmt->fetchAll();
$ventas_activas = array_filter($ventas, fn($v) => $v['estado']==='activa');
$total_dia = array_sum(array_column($ventas_activas, 'total'));

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
  <h2>Ventas</h2>
  <a href="crear.php" class="btn btn-success">+ Nueva Venta</a>
</div>
<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem;align-items:center">
    <label>Fecha:</label>
    <input type="date" name="fecha" value="<?= $fecha ?>" class="form-control" style="width:180px">
    <button class="btn btn-secondary">Filtrar</button>
    <span style="margin-left:auto;font-weight:600">Total del dia: <?= formatMoney($total_dia) ?></span>
  </form>
  <div class="table-wrapper">
    <table>
      <thead><tr><th>Numero</th><th>Cliente</th><th>Subtotal</th><th>IVA</th><th>Total</th><th>Estado</th><th>Fecha</th><th>Accion</th></tr></thead>
      <tbody>
        <?php if (empty($ventas)): ?>
        <tr><td colspan="8" style="text-align:center;color:var(--text-muted)">Sin ventas para esta fecha.</td></tr>
        <?php endif; ?>
        <?php foreach ($ventas as $v): ?>
        <tr>
          <td><?= sanitize($v['numero']) ?></td>
          <td><?= sanitize($v['cliente']) ?></td>
          <td><?= formatMoney((float)$v['subtotal']) ?></td>
          <td><?= formatMoney((float)$v['iva']) ?></td>
          <td><?= formatMoney((float)$v['total']) ?></td>
          <td><span class="badge-<?= $v['estado']==='activa'?'stock-ok':'stock-low' ?>"><?= $v['estado'] ?></span></td>
          <td><?= formatDate($v['creado_en']) ?></td>
          <td><a href="factura.php?numero=<?= $v['numero'] ?>" class="btn btn-secondary">Ver Factura</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
