<?php
$pageTitle = 'Editar Producto';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$pdo = getDB();
$id  = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM productos WHERE id=?');
$stmt->execute([$id]);
$prod = $stmt->fetch();
if (!$prod) redirect('/distribuciones-caribe/modules/productos/index.php');
$categorias = $pdo->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare(
        'UPDATE productos SET codigo=?,nombre=?,descripcion=?,categoria_id=?,precio=?,stock=?,stock_minimo=? WHERE id=?'
    )->execute([
        $_POST['codigo'], $_POST['nombre'], $_POST['descripcion'],
        $_POST['categoria_id'], $_POST['precio'], $_POST['stock'],
        $_POST['stock_minimo'], $id
    ]);
    flashMessage('success', 'Producto actualizado.');
    redirect('/distribuciones-caribe/modules/productos/index.php');
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<h2 style="margin-bottom:1rem">Editar Producto</h2>
<div class="card">
<form method="POST">
  <div class="form-row">
    <div class="form-group">
      <label>Codigo *</label>
      <input type="text" name="codigo" class="form-control" value="<?= sanitize($prod['codigo']) ?>" required>
    </div>
    <div class="form-group">
      <label>Categoria *</label>
      <select name="categoria_id" class="form-control" required>
        <?php foreach ($categorias as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $prod['categoria_id']==$c['id']?'selected':'' ?>><?= sanitize($c['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label>Nombre *</label>
    <input type="text" name="nombre" class="form-control" value="<?= sanitize($prod['nombre']) ?>" required>
  </div>
  <div class="form-group">
    <label>Descripcion</label>
    <textarea name="descripcion" class="form-control" rows="3"><?= sanitize($prod['descripcion'] ?? '') ?></textarea>
  </div>
  <div class="form-row">
    <div class="form-group">
      <label>Precio *</label>
      <input type="number" step="0.01" name="precio" class="form-control" value="<?= $prod['precio'] ?>" required>
    </div>
    <div class="form-group">
      <label>Stock actual</label>
      <input type="number" name="stock" class="form-control" value="<?= $prod['stock'] ?>" required>
    </div>
    <div class="form-group">
      <label>Stock minimo</label>
      <input type="number" name="stock_minimo" class="form-control" value="<?= $prod['stock_minimo'] ?>">
    </div>
  </div>
  <div style="display:flex;gap:.5rem">
    <button class="btn btn-primary">Actualizar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
  </div>
</form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
