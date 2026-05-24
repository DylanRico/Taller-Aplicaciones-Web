<?php
$pageTitle = 'Productos';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$pdo    = getDB();
$buscar = trim($_GET['q'] ?? '');
$sql    = "SELECT p.*, c.nombre AS categoria
           FROM productos p JOIN categorias c ON c.id = p.categoria_id WHERE p.activo=1";
$params = [];
if ($buscar) {
    $sql    .= " AND (p.nombre LIKE ? OR p.codigo LIKE ?)";
    $params  = ["%$buscar%", "%$buscar%"];
}
$sql .= " ORDER BY p.nombre ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
  <h2>Productos / Inventario</h2>
  <a href="crear.php" class="btn btn-primary">+ Nuevo producto</a>
</div>
<div class="card">
  <form method="GET" style="display:flex;gap:.5rem;margin-bottom:1rem">
    <input type="text" name="q" value="<?= sanitize($buscar) ?>" class="form-control" placeholder="Buscar por nombre o codigo..." style="max-width:340px">
    <button class="btn btn-secondary">Buscar</button>
    <?php if ($buscar): ?><a href="index.php" class="btn btn-secondary">Limpiar</a><?php endif; ?>
  </form>
  <div class="table-wrapper">
    <table>
      <thead><tr><th>Codigo</th><th>Nombre</th><th>Categoria</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($productos)): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--text-muted)">Sin productos.</td></tr>
        <?php endif; ?>
        <?php foreach ($productos as $p): ?>
        <tr>
          <td><?= sanitize($p['codigo']) ?></td>
          <td><?= sanitize($p['nombre']) ?></td>
          <td><?= sanitize($p['categoria']) ?></td>
          <td><?= formatMoney((float)$p['precio']) ?></td>
          <td>
            <span class="<?= $p['stock'] <= $p['stock_minimo'] ? 'badge-stock-low' : 'badge-stock-ok' ?>">
              <?= $p['stock'] ?>
            </span>
          </td>
          <td style="display:flex;gap:.4rem">
            <a href="editar.php?id=<?= $p['id'] ?>" class="btn btn-secondary">Editar</a>
            <form method="POST" action="eliminar.php" onsubmit="return confirmDelete()">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button class="btn btn-danger">Eliminar</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
