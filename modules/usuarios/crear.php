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
        $error = 'El formato del email no es valido.';
    } elseif (!in_array($rol, $roles_validos, true)) {
        $error = 'Rol no valido.';
    } else {
        try {
            $pdo  = getDB();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare('INSERT INTO usuarios (nombre,email,password,rol) VALUES (?,?,?,?)')
                ->execute([$nombre, $email, $hash, $rol]);
            flashMessage('success', 'Usuario creado exitosamente.');
            redirect('/distribuciones-caribe/modules/usuarios/index.php');
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
<h2 style="margin-bottom:1rem">Nuevo Usuario</h2>
<div class="card">
<?php if ($error): ?>
<div class="alert alert-danger"><?= sanitize($error) ?></div>
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
        <option value="vendedor" <?= (($_POST['rol'] ?? '') === 'vendedor') ? 'selected' : '' ?>>Vendedor</option>
        <option value="administrador" <?= (($_POST['rol'] ?? '') === 'administrador') ? 'selected' : '' ?>>Administrador</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label>Contrasena * (minimo 6 caracteres)</label>
    <input type="password" name="password" class="form-control" required minlength="6">
  </div>
  <div style="display:flex;gap:.5rem">
    <button class="btn btn-primary">Crear usuario</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
  </div>
</form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
