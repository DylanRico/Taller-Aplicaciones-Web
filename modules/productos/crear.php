<?php
$pageTitle = 'Nuevo Producto';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
$pdo        = getDB();
$categorias = $pdo->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare(
        'INSERT INTO productos (codigo,nombre,descripcion,categoria_id,precio,stock,stock_minimo)
         VALUES (?,?,?,?,?,?,?)'
    );
    $stmt->execute([
        $_POST['codigo'],   $_POST['nombre'],     $_POST['descripcion'],
        $_POST['categoria_id'], $_POST['precio'], $_POST['stock'],
        $_POST['stock_minimo']
    ]);
    flashMessage('success', 'Producto registrado.');
    redirect('/distribuciones-caribe/modules/productos/index.php');
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<h2 style="margin-bottom:1rem">Nuevo Producto</h2>
<div class="card">
<form method="POST">
  <div class="form-row">
    <div class="form-group">
      <label>Codigo unico *</label>
      <input type="text" name="codigo" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Categoria *</label>
      <select name="categoria_id" class="form-control" required>
        <option value="">Seleccionar...</option>
        <?php foreach ($categorias as $c): ?>
        <option value="<?= $c['id'] ?>"><?= sanitize($c['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label>Nombre del producto *</label>
    <input type="text" name="nombre" class="form-control" required>
  </div>
  <div class="form-group">
    <label>Descripcion</label>
    <textarea name="descripcion" class="form-control" rows="3"></textarea>
  </div>
  <div class="form-row">
    <div class="form-group">
      <label>Precio (COP) *</label>
      <input type="number" step="0.01" name="precio" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Stock inicial *</label>
      <input type="number" name="stock" class="form-control" value="0" required>
    </div>
    <div class="form-group">
      <label>Stock minimo alerta</label>
      <input type="number" name="stock_minimo" class="form-control" value="5">
    </div>
  </div>
  <div style="display:flex;gap:.5rem">
    <button class="btn btn-primary">Guardar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
  </div>
</form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
