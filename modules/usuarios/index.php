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
<div class="page-header">
  <h2>Usuarios del Sistema</h2>
  <a href="crear.php" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Nuevo usuario
  </a>
</div>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Registrado</th></tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
          <td style="font-weight:500"><?= sanitize($u['nombre']) ?></td>
          <td style="color:var(--text-muted)"><?= sanitize($u['email']) ?></td>
          <td><span class="badge-rol"><?= sanitize($u['rol']) ?></span></td>
          <td>
            <?php if ($u['activo']): ?>
              <span class="badge-stock-ok">Activo</span>
            <?php else: ?>
              <span class="badge-stock-low">Inactivo</span>
            <?php endif; ?>
          </td>
          <td><?= formatDate($u['creado_en']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
