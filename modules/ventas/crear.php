<?php
$pageTitle = 'Nueva Venta';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$pdo      = getDB();
$clientes = $pdo->query('SELECT id, documento, nombre, apellido FROM clientes ORDER BY nombre')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = (int)$_POST['cliente_id'];
    $productos  = $_POST['producto_id'] ?? [];
    $cantidades = $_POST['cantidad']    ?? [];
    $precios    = $_POST['precio']      ?? [];

    if (!$cliente_id || empty($productos)) {
        flashMessage('danger', 'Seleccione un cliente y al menos un producto.');
        redirect('/Taller-Aplicaciones-Web/modules/ventas/crear.php');
    }

    $subtotal = (float)$_POST['h_subtotal'];
    $iva      = (float)$_POST['h_iva'];
    $total    = (float)$_POST['h_total'];
    $numero   = generateInvoiceNumber();
    $user     = currentUser();

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO ventas (numero,cliente_id,usuario_id,subtotal,iva,total,notas) VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->execute([$numero, $cliente_id, $user['id'], $subtotal, $iva, $total, $_POST['notas'] ?? '']);
        $venta_id = $pdo->lastInsertId();

        $detalle  = $pdo->prepare(
            'INSERT INTO detalle_ventas (venta_id,producto_id,cantidad,precio_unit,subtotal) VALUES (?,?,?,?,?)'
        );
        $updStock = $pdo->prepare('UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?');

        foreach ($productos as $i => $pid) {
            $pid   = (int)$pid;
            $cant  = (int)$cantidades[$i];
            $precio= (float)$precios[$i];
            $sub   = $cant * $precio;

            $updStock->execute([$cant, $pid, $cant]);
            if ($updStock->rowCount() === 0) {
                throw new Exception("Stock insuficiente para el producto ID $pid.");
            }
            $detalle->execute([$venta_id, $pid, $cant, $precio, $sub]);
        }
        $pdo->commit();
        flashMessage('success', "Venta $numero registrada exitosamente.");
        redirect("/Taller-Aplicaciones-Web/modules/ventas/factura.php?numero=$numero");
    } catch (Exception $e) {
        $pdo->rollBack();
        flashMessage('danger', 'Error: ' . $e->getMessage());
        redirect('/Taller-Aplicaciones-Web/modules/ventas/crear.php');
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
  <h2>Nueva Venta</h2>
  <a href="index.php" class="btn btn-secondary">← Volver</a>
</div>

<div class="card">
  <form method="POST" id="formVenta">
    <div class="form-group">
      <label>Cliente *</label>
      <select name="cliente_id" class="form-control" required>
        <option value="">Seleccionar cliente…</option>
        <?php foreach ($clientes as $c): ?>
        <option value="<?= $c['id'] ?>"><?= sanitize($c['nombre'].' '.$c['apellido'].' — '.$c['documento']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="margin:1.25rem 0 .75rem;display:flex;align-items:center;gap:.75rem">
      <span style="font-weight:600;font-size:1rem">Productos</span>
      <div style="flex:1;height:1px;background:var(--border)"></div>
    </div>

    <div style="margin-bottom:.875rem;position:relative">
      <input type="text" id="buscaProd" class="form-control" placeholder="Buscar producto por nombre o código…" style="max-width:440px">
      <div id="sugerencias" class="suggestions-dropdown" style="display:none;max-width:440px"></div>
    </div>

    <div id="productosContainer"></div>

    <div class="card" style="margin-top:1rem;background:rgba(255,255,255,.03)">
      <div class="totals-box">
        <div class="totals-row">
          <span class="totals-label">Subtotal</span>
          <span class="totals-val" id="subtotal">$0.00</span>
        </div>
        <div class="totals-row">
          <span class="totals-label">IVA (19%)</span>
          <span class="totals-val" id="iva">$0.00</span>
        </div>
        <div class="totals-row" style="margin-top:.35rem;padding-top:.5rem;border-top:1px solid var(--border)">
          <span class="totals-label" style="font-size:1rem;color:var(--text-main);font-weight:600">TOTAL</span>
          <span class="totals-val totals-total" id="total">$0.00</span>
        </div>
      </div>
      <input type="hidden" name="h_subtotal" id="h_subtotal" value="0">
      <input type="hidden" name="h_iva"      id="h_iva"      value="0">
      <input type="hidden" name="h_total"    id="h_total"    value="0">
    </div>

    <div class="form-group" style="margin-top:1rem">
      <label>Notas</label>
      <textarea name="notas" class="form-control" rows="2" placeholder="Observaciones opcionales…"></textarea>
    </div>

    <div style="display:flex;gap:.5rem;padding-top:.25rem">
      <button type="submit" class="btn btn-success">Registrar Venta</button>
      <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<script>
document.getElementById('buscaProd').addEventListener('input', function() {
  const q = this.value.trim();
  const div = document.getElementById('sugerencias');
  if (q.length < 2) { div.style.display = 'none'; return; }
  fetch('/Taller-Aplicaciones-Web/modules/ventas/api_productos.php?q=' + encodeURIComponent(q))
    .then(r => r.json())
    .then(data => {
      if (!data.length) { div.style.display = 'none'; return; }
      div.innerHTML = data.map(p =>
        '<div class="suggestion-item" onclick="agregarProducto(' + p.id + ',\'' +
        p.codigo + '\',\'' + p.nombre.replace(/'/g,"\\'") + '\',' + p.precio + ',' + p.stock + ')">' +
        '<strong>' + p.nombre + '</strong> &nbsp;<span>' + p.codigo + ' &mdash; $' + Number(p.precio).toLocaleString('es-CO') + ' &mdash; Stock: ' + p.stock + '</span>' +
        '</div>'
      ).join('');
      div.style.display = 'block';
    });
});

document.addEventListener('click', function(e) {
  if (!e.target.closest('#buscaProd') && !e.target.closest('#sugerencias')) {
    document.getElementById('sugerencias').style.display = 'none';
  }
});

function agregarProducto(id, codigo, nombre, precio, stock) {
  document.getElementById('sugerencias').style.display = 'none';
  document.getElementById('buscaProd').value = '';
  const div = document.createElement('div');
  div.className = 'fila-producto';
  div.innerHTML =
    '<input type="hidden" name="producto_id[]" value="' + id + '">' +
    '<input type="hidden" name="precio[]" class="precio" value="' + precio + '">' +
    '<span style="flex:1;min-width:180px;font-weight:500">' + nombre + '</span>' +
    '<span style="color:var(--text-muted);font-size:.8125rem">' + codigo + '</span>' +
    '<span style="color:var(--text-muted)">$' + Number(precio).toLocaleString('es-CO') + '</span>' +
    '<input type="number" name="cantidad[]" class="cant form-control" value="1" min="1" max="' + stock + '" style="width:80px" oninput="calcularTotales()">' +
    '<span class="sub-linea" style="min-width:90px;text-align:right;font-weight:600;font-variant-numeric:tabular-nums">$' + Number(precio).toLocaleString('es-CO') + '</span>' +
    '<button type="button" onclick="this.closest(\'.fila-producto\').remove();calcularTotales()" class="btn btn-danger" style="padding:.3rem .6rem">✕</button>';
  document.getElementById('productosContainer').appendChild(div);
  calcularTotales();
}
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
