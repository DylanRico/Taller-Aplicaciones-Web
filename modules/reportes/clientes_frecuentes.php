<?php
$pageTitle = 'Clientes Frecuentes';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$pdo  = getDB();
$rows = $pdo->query(
    "SELECT c.tipo_doc, c.documento, c.nombre, c.apellido, c.ciudad,
            COUNT(v.id) AS num_compras, SUM(v.total) AS total_comprado
     FROM clientes c JOIN ventas v ON v.cliente_id=c.id AND v.estado='activa'
     GROUP BY c.id ORDER BY num_compras DESC, total_comprado DESC LIMIT 20"
)->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
  <h2>Clientes Frecuentes</h2>
  <a href="index.php" class="btn btn-secondary">Volver</a>
</div>
<div class="card">
  <div class="table-wrapper">
    <table>
      <thead><tr><th>#</th><th>Documento</th><th>Cliente</th><th>Ciudad</th><th>Compras</th><th>Total comprado</th></tr></thead>
      <tbody>
        <?php if (empty($rows)): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--text-muted)">Sin datos.</td></tr>
        <?php endif; ?>
        <?php foreach ($rows as $i => $r): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= sanitize($r['tipo_doc'].' '.$r['documento']) ?></td>
          <td><?= sanitize($r['nombre'].' '.$r['apellido']) ?></td>
          <td><?= sanitize($r['ciudad'] ?? '-') ?></td>
          <td><?= $r['num_compras'] ?></td>
          <td><?= formatMoney((float)$r['total_comprado']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
