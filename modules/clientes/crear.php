<?php
$pageTitle = 'Nuevo Cliente';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO clientes (tipo_doc,documento,nombre,apellido,email,telefono,ciudad,direccion)
         VALUES (?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([
        $_POST['tipo_doc'], $_POST['documento'], $_POST['nombre'],
        $_POST['apellido'], $_POST['email'], $_POST['telefono'],
        $_POST['ciudad'],   $_POST['direccion']
    ]);
    flashMessage('success', 'Cliente registrado exitosamente.');
    redirect('/Taller-Aplicaciones-Web/modules/clientes/index.php');
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
  <h2>Nuevo Cliente</h2>
  <a href="index.php" class="btn btn-secondary">← Volver</a>
</div>

<div class="card" style="max-width:720px">
  <form method="POST">
    <div class="form-row">
      <div class="form-group">
        <label>Tipo de documento</label>
        <select name="tipo_doc" class="form-control" required>
          <option>CC</option><option>NIT</option><option>CE</option><option>PP</option>
        </select>
      </div>
      <div class="form-group">
        <label>Número de documento *</label>
        <input type="text" name="documento" class="form-control" required>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Nombre *</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Apellido</label>
        <input type="text" name="apellido" class="form-control">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control">
      </div>
      <div class="form-group">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Ciudad</label>
        <input type="text" name="ciudad" class="form-control">
      </div>
      <div class="form-group">
        <label>Dirección</label>
        <input type="text" name="direccion" class="form-control">
      </div>
    </div>
    <div style="display:flex;gap:.5rem;padding-top:.25rem">
      <button class="btn btn-primary">Guardar cliente</button>
      <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
