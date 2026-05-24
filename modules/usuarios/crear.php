<?php
$pageTitle = 'Nuevo Usuario';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $rol      = $_POST['rol'] ?? '';
    $roles_validos = ['vendedor', 'administrador'];

    if (!$nombre || !$email || !$password) {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El formato del email no es válido.';
    } elseif (!in_array($rol, $roles_validos, true)) {
        $error = 'Rol no válido.';
    } else {
        try {
            $pdo  = getDB();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare('INSERT INTO usuarios (nombre,email,password,rol) VALUES (?,?,?,?)')
                ->execute([$nombre, $email, $hash, $rol]);
            flashMessage('success', 'Usuario creado exitosamente.');
            redirect('/Taller-Aplicaciones-Web/modules/usuarios/index.php');
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'Ya existe un usuario con ese email.';
            } else {
                $error = 'Error al crear el usuario. Intente de nuevo.';
            }
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
  <h2>Nuevo Usuario</h2>
  <a href="index.php" class="btn btn-secondary">← Volver</a>
</div>

<div class="card" style="max-width:560px">
  <?php if ($error): ?>
  <div class="alert alert-danger" style="margin:0 0 1rem"><?= sanitize($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Nombre completo *</label>
      <input type="text" name="nombre" class="form-control" required value="<?= sanitize($_POST['nombre'] ?? '') ?>">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" class="form-control" required value="<?= sanitize($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Rol *</label>
        <select name="rol" class="form-control">
          <option value="vendedor"      <?= (($_POST['rol'] ?? '') === 'vendedor')      ? 'selected' : '' ?>>Vendedor</option>
          <option value="administrador" <?= (($_POST['rol'] ?? '') === 'administrador') ? 'selected' : '' ?>>Administrador</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label>Contraseña * <span style="color:var(--text-muted);font-weight:400">(mínimo 6 caracteres)</span></label>
      <input type="password" name="password" class="form-control" required minlength="6">
    </div>
    <div style="display:flex;gap:.5rem;padding-top:.25rem">
      <button class="btn btn-primary">Crear usuario</button>
      <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
