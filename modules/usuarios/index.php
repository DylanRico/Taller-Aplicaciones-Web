<?php
$pageTitle = 'Usuarios';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$pdo      = getDB();
$usuarios = $pdo->query('SELECT id,nombre,email,rol,activo,creado_en FROM usuarios ORDER BY nombre')->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
  <h2>Usuarios del Sistema</h2>
  <a href="crear.php" class="btn btn-primary">+ Nuevo usuario</a>
</div>
<div class="card">
  <div class="table-wrapper">
    <table>
      <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Registrado</th></tr></thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><?= sanitize($u['nombre']) ?></td>
          <td><?= sanitize($u['email']) ?></td>
          <td><span class="badge-rol"><?= sanitize($u['rol']) ?></span></td>
          <td><?= $u['activo'] ? '<span class="badge-stock-ok">Activo</span>' : '<span class="badge-stock-low">Inactivo</span>' ?></td>
          <td><?= formatDate($u['creado_en']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
