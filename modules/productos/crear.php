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
    redirect('/Taller-Aplicaciones-Web/modules/productos/index.php');
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
  <h2>Nuevo Producto</h2>
  <a href="index.php" class="btn btn-secondary">← Volver</a>
</div>

<div class="card" style="max-width:720px">
  <form method="POST">
    <div class="form-row">
      <div class="form-group">
        <label>Código único *</label>
        <input type="text" name="codigo" class="form-control" required placeholder="Ej: PROD-001">
      </div>
      <div class="form-group">
        <label>Categoría *</label>
        <select name="categoria_id" class="form-control" required>
          <option value="">Seleccionar…</option>
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
      <label>Descripción</label>
      <textarea name="descripcion" class="form-control" rows="3" placeholder="Descripción opcional…"></textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Precio (COP) *</label>
        <input type="number" step="0.01" name="precio" class="form-control" required placeholder="0.00">
      </div>
      <div class="form-group">
        <label>Stock inicial *</label>
        <input type="number" name="stock" class="form-control" value="0" required>
      </div>
      <div class="form-group">
        <label>Stock mínimo alerta</label>
        <input type="number" name="stock_minimo" class="form-control" value="5">
      </div>
    </div>
    <div style="display:flex;gap:.5rem;padding-top:.25rem">
      <button class="btn btn-primary">Guardar producto</button>
      <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
