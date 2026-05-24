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
        redirect('/distribuciones-caribe/modules/ventas/crear.php');
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
        redirect("/distribuciones-caribe/modules/ventas/factura.php?numero=$numero");
    } catch (Exception $e) {
        $pdo->rollBack();
        flashMessage('danger', 'Error: ' . $e->getMessage());
        redirect('/distribuciones-caribe/modules/ventas/crear.php');
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<h2 style="margin-bottom:1rem">Nueva Venta</h2>
<div class="card">
<form method="POST" id="formVenta">
  <div class="form-group">
    <label>Cliente *</label>
    <select name="cliente_id" class="form-control" required>
      <option value="">Seleccionar cliente...</option>
      <?php foreach ($clientes as $c): ?>
      <option value="<?= $c['id'] ?>"><?= sanitize($c['nombre'].' '.$c['apellido'].' - '.$c['documento']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <h3 style="margin:1rem 0 .5rem">Productos</h3>
  <div style="margin-bottom:.75rem;position:relative">
    <input type="text" id="buscaProd" class="form-control" placeholder="Buscar producto por nombre o codigo..." style="max-width:400px">
    <div id="sugerencias" style="background:var(--bg-card);border:1px solid var(--border);border-radius:6px;display:none;position:absolute;z-index:50;width:400px;top:100%"></div>
  </div>

  <div id="productosContainer"></div>

  <div class="card" style="margin-top:1rem">
    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.3rem">
      <span>Subtotal: <strong id="subtotal">$0.00</strong></span>
      <span>IVA (19%): <strong id="iva">$0.00</strong></span>
      <span style="font-size:1.2rem">TOTAL: <strong id="total">$0.00</strong></span>
    </div>
    <input type="hidden" name="h_subtotal" id="h_subtotal" value="0">
    <input type="hidden" name="h_iva"      id="h_iva"      value="0">
    <input type="hidden" name="h_total"    id="h_total"    value="0">
  </div>

  <div class="form-group" style="margin-top:1rem">
    <label>Notas</label>
    <textarea name="notas" class="form-control" rows="2"></textarea>
  </div>

  <div style="display:flex;gap:.5rem">
    <button type="submit" class="btn btn-success">Registrar Venta</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
  </div>
</form>
</div>

<script>
let filaIdx = 0;

document.getElementById('buscaProd').addEventListener('input', function() {
  const q = this.value.trim();
  if (q.length < 2) { document.getElementById('sugerencias').style.display='none'; return; }
  fetch('/distribuciones-caribe/modules/ventas/api_productos.php?q=' + encodeURIComponent(q))
    .then(r => r.json())
    .then(data => {
      const div = document.getElementById('sugerencias');
      if (!data.length) { div.style.display='none'; return; }
      div.innerHTML = data.map(p =>
        '<div onclick="agregarProducto(' + p.id + ',\'' + p.codigo + '\',\'' + p.nombre.replace(/'/g,"\\'") + '\',' + p.precio + ',' + p.stock + ')"' +
        ' style="padding:.5rem 1rem;cursor:pointer;border-bottom:1px solid var(--border)">' +
        '<strong>' + p.nombre + '</strong> &mdash; ' + p.codigo + ' &mdash; $' + p.precio + ' (Stock: ' + p.stock + ')' +
        '</div>'
      ).join('');
      div.style.display='block';
    });
});

function agregarProducto(id, codigo, nombre, precio, stock) {
  document.getElementById('sugerencias').style.display='none';
  document.getElementById('buscaProd').value = '';
  const div = document.createElement('div');
  div.className = 'fila-producto';
  div.style.cssText = 'display:flex;gap:.5rem;align-items:center;margin-bottom:.5rem;flex-wrap:wrap';
  div.innerHTML =
    '<input type="hidden" name="producto_id[]" value="' + id + '">' +
    '<input type="hidden" name="precio[]" class="precio" value="' + precio + '">' +
    '<span style="flex:1;min-width:200px">' + nombre + ' (' + codigo + ')</span>' +
    '<span>$' + precio + '</span>' +
    '<input type="number" name="cantidad[]" class="cant form-control" value="1" min="1" max="' + stock + '" style="width:80px" oninput="calcularTotales()">' +
    '<span class="sub-linea" style="min-width:80px;text-align:right">$' + precio + '</span>' +
    '<button type="button" onclick="this.closest(\'.fila-producto\').remove();calcularTotales()" class="btn btn-danger">x</button>';
  document.getElementById('productosContainer').appendChild(div);
  calcularTotales();
}
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
