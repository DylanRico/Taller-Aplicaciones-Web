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
<div class="page-header">
  <h2>Ventas</h2>
  <a href="crear.php" class="btn btn-success">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Nueva Venta
  </a>
</div>

<div class="card">
  <div style="display:flex;gap:.75rem;margin-bottom:1.125rem;align-items:center;flex-wrap:wrap">
    <form method="GET" style="display:flex;gap:.5rem;align-items:center">
      <label style="color:var(--text-muted);font-size:.875rem;white-space:nowrap">Fecha:</label>
      <input type="date" name="fecha" value="<?= $fecha ?>" class="form-control" style="width:180px">
      <button class="btn btn-secondary">Filtrar</button>
    </form>
    <div style="margin-left:auto;display:flex;align-items:center;gap:.5rem">
      <span style="color:var(--text-muted);font-size:.875rem">Total del día:</span>
      <span style="font-weight:700;font-size:1.05rem;font-variant-numeric:tabular-nums;color:var(--accent)"><?= formatMoney($total_dia) ?></span>
    </div>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>Número</th><th>Cliente</th><th>Subtotal</th><th>IVA</th><th>Total</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr>
      </thead>
      <tbody>
        <?php if (empty($ventas)): ?>
        <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:2rem">Sin ventas para esta fecha.</td></tr>
        <?php endif; ?>
        <?php foreach ($ventas as $v): ?>
        <tr>
          <td style="font-weight:600;color:var(--accent)"><?= sanitize($v['numero']) ?></td>
          <td><?= sanitize($v['cliente']) ?></td>
          <td><?= formatMoney((float)$v['subtotal']) ?></td>
          <td><?= formatMoney((float)$v['iva']) ?></td>
          <td style="font-weight:600"><?= formatMoney((float)$v['total']) ?></td>
          <td>
            <span class="<?= $v['estado']==='activa' ? 'badge-stock-ok' : 'badge-stock-low' ?>">
              <?= $v['estado'] ?>
            </span>
          </td>
          <td><?= formatDate($v['creado_en']) ?></td>
          <td><a href="factura.php?numero=<?= $v['numero'] ?>" class="btn btn-secondary">Ver factura</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
