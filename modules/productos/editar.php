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
if (!$prod) redirect('/Taller-Aplicaciones-Web/modules/productos/index.php');
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
    redirect('/Taller-Aplicaciones-Web/modules/productos/index.php');
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
  <h2>Editar Producto</h2>
  <a href="index.php" class="btn btn-secondary">← Volver</a>
</div>

<div class="card" style="max-width:720px">
  <form method="POST">
    <div class="form-row">
      <div class="form-group">
        <label>Código *</label>
        <input type="text" name="codigo" class="form-control" value="<?= sanitize($prod['codigo']) ?>" required>
      </div>
      <div class="form-group">
        <label>Categoría *</label>
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
      <label>Descripción</label>
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
        <label>Stock mínimo</label>
        <input type="number" name="stock_minimo" class="form-control" value="<?= $prod['stock_minimo'] ?>">
      </div>
    </div>
    <div style="display:flex;gap:.5rem;padding-top:.25rem">
      <button class="btn btn-primary">Actualizar</button>
      <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
