<?php
$pageTitle = 'Clientes';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$pdo    = getDB();
$buscar = trim($_GET['q'] ?? '');
$sql    = "SELECT * FROM clientes WHERE 1=1";
$params = [];

if ($buscar) {
    $sql    .= " AND (nombre LIKE ? OR apellido LIKE ? OR documento LIKE ?)";
    $params  = ["%$buscar%", "%$buscar%", "%$buscar%"];
}
$sql .= " ORDER BY nombre ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clientes = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
  <h2>Clientes</h2>
  <a href="crear.php" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Nuevo cliente
  </a>
</div>

<div class="card">
  <form method="GET" class="search-bar">
    <input type="text" name="q" value="<?= sanitize($buscar) ?>" class="form-control" placeholder="Buscar por nombre o documento…">
    <button class="btn btn-secondary">Buscar</button>
    <?php if ($buscar): ?><a href="index.php" class="btn btn-secondary">Limpiar</a><?php endif; ?>
  </form>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>Documento</th><th>Nombre</th><th>Teléfono</th><th>Ciudad</th><th>Acciones</th></tr>
      </thead>
      <tbody>
        <?php if (empty($clientes)): ?>
        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:2rem">No se encontraron clientes.</td></tr>
        <?php endif; ?>
        <?php foreach ($clientes as $c): ?>
        <tr>
          <td style="color:var(--text-muted);font-size:.8125rem"><?= sanitize($c['tipo_doc']) ?> <?= sanitize($c['documento']) ?></td>
          <td style="font-weight:500"><?= sanitize($c['nombre']) ?> <?= sanitize($c['apellido'] ?? '') ?></td>
          <td><?= sanitize($c['telefono'] ?? '—') ?></td>
          <td><?= sanitize($c['ciudad'] ?? '—') ?></td>
          <td>
            <div style="display:flex;gap:.375rem">
              <a href="editar.php?id=<?= $c['id'] ?>" class="btn btn-secondary">Editar</a>
              <form method="POST" action="eliminar.php" onsubmit="return confirmDelete()">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <button class="btn btn-danger">Eliminar</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
