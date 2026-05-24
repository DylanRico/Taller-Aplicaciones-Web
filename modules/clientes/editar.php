<?php
$pageTitle = 'Editar Cliente';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$pdo = getDB();
$id  = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM clientes WHERE id = ?');
$stmt->execute([$id]);
$cliente = $stmt->fetch();
if (!$cliente) redirect('/Taller-Aplicaciones-Web/modules/clientes/index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare(
        'UPDATE clientes SET tipo_doc=?,documento=?,nombre=?,apellido=?,email=?,telefono=?,ciudad=?,direccion=? WHERE id=?'
    )->execute([
        $_POST['tipo_doc'], $_POST['documento'], $_POST['nombre'],
        $_POST['apellido'], $_POST['email'], $_POST['telefono'],
        $_POST['ciudad'], $_POST['direccion'], $id
    ]);
    flashMessage('success', 'Cliente actualizado.');
    redirect('/Taller-Aplicaciones-Web/modules/clientes/index.php');
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
  <h2>Editar Cliente</h2>
  <a href="index.php" class="btn btn-secondary">← Volver</a>
</div>

<div class="card" style="max-width:720px">
  <form method="POST">
    <div class="form-row">
      <div class="form-group">
        <label>Tipo de documento</label>
        <select name="tipo_doc" class="form-control">
          <?php foreach(['CC','NIT','CE','PP'] as $t): ?>
          <option <?= $cliente['tipo_doc']===$t?'selected':'' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Documento *</label>
        <input type="text" name="documento" class="form-control" value="<?= sanitize($cliente['documento']) ?>" required>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Nombre *</label>
        <input type="text" name="nombre" class="form-control" value="<?= sanitize($cliente['nombre']) ?>" required>
      </div>
      <div class="form-group">
        <label>Apellido</label>
        <input type="text" name="apellido" class="form-control" value="<?= sanitize($cliente['apellido'] ?? '') ?>">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= sanitize($cliente['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="<?= sanitize($cliente['telefono'] ?? '') ?>">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Ciudad</label>
        <input type="text" name="ciudad" class="form-control" value="<?= sanitize($cliente['ciudad'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Dirección</label>
        <input type="text" name="direccion" class="form-control" value="<?= sanitize($cliente['direccion'] ?? '') ?>">
      </div>
    </div>
    <div style="display:flex;gap:.5rem;padding-top:.25rem">
      <button class="btn btn-primary">Actualizar</button>
      <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
