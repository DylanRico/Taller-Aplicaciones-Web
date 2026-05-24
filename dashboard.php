<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$pdo = getDB();
$stats = [
    'clientes'  => $pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn(),
    'productos' => $pdo->query('SELECT COUNT(*) FROM productos WHERE activo=1')->fetchColumn(),
    'ventas_hoy'=> $pdo->query("SELECT COUNT(*) FROM ventas WHERE DATE(creado_en)=CURDATE() AND estado='activa'")->fetchColumn(),
    'total_hoy' => $pdo->query("SELECT COALESCE(SUM(total),0) FROM ventas WHERE DATE(creado_en)=CURDATE() AND estado='activa'")->fetchColumn(),
    'stock_bajo'=> $pdo->query('SELECT COUNT(*) FROM productos WHERE stock <= stock_minimo AND activo=1')->fetchColumn(),
];

$ventas_recientes = $pdo->query(
    "SELECT v.numero, v.total, v.creado_en, c.nombre AS cliente
     FROM ventas v JOIN clientes c ON c.id = v.cliente_id
     WHERE v.estado='activa' ORDER BY v.creado_en DESC LIMIT 5"
)->fetchAll();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>
<div class="page-header">
  <h1>Dashboard</h1>
</div>

<div class="stats-grid">
  <div class="stat-card">
    <span class="stat-icon">👥</span>
    <div class="stat-value"><?= $stats['clientes'] ?></div>
    <div class="stat-label">Clientes registrados</div>
  </div>
  <div class="stat-card">
    <span class="stat-icon">📦</span>
    <div class="stat-value"><?= $stats['productos'] ?></div>
    <div class="stat-label">Productos activos</div>
  </div>
  <div class="stat-card">
    <span class="stat-icon">🧾</span>
    <div class="stat-value"><?= $stats['ventas_hoy'] ?></div>
    <div class="stat-label">Ventas hoy</div>
  </div>
  <div class="stat-card">
    <span class="stat-icon">💰</span>
    <div class="stat-value" style="font-size:1.4rem"><?= formatMoney((float)$stats['total_hoy']) ?></div>
    <div class="stat-label">Total vendido hoy</div>
  </div>
  <?php if ($stats['stock_bajo'] > 0): ?>
  <div class="stat-card" style="box-shadow:0 0 0 1px rgba(243,156,18,.4),0 2px 12px rgba(0,0,0,.2)">
    <span class="stat-icon">⚠️</span>
    <div class="stat-value" style="color:var(--warning)"><?= $stats['stock_bajo'] ?></div>
    <div class="stat-label">Productos con stock bajo</div>
  </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-title">Últimas 5 ventas</div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Número</th><th>Cliente</th><th>Total</th><th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ventas_recientes as $v): ?>
        <tr>
          <td>
            <a href="/Taller-Aplicaciones-Web/modules/ventas/factura.php?numero=<?= $v['numero'] ?>"
               style="color:var(--accent);text-decoration:none;font-weight:600">
              <?= sanitize($v['numero']) ?>
            </a>
          </td>
          <td><?= sanitize($v['cliente']) ?></td>
          <td><?= formatMoney((float)$v['total']) ?></td>
          <td><?= formatDate($v['creado_en']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($ventas_recientes)): ?>
        <tr>
          <td colspan="4" style="text-align:center;color:var(--text-muted);padding:2rem">
            Sin ventas registradas aún.
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
