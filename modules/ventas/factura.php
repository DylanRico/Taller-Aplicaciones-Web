<?php
$pageTitle = 'Factura';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

$numero = $_GET['numero'] ?? '';
$pdo    = getDB();
$stmt   = $pdo->prepare(
    "SELECT v.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido,
            c.documento, c.tipo_doc, c.email AS cli_email, c.ciudad,
            u.nombre AS vendedor
     FROM ventas v
     JOIN clientes c ON c.id=v.cliente_id
     JOIN usuarios u ON u.id=v.usuario_id
     WHERE v.numero=?"
);
$stmt->execute([$numero]);
$venta = $stmt->fetch();
if (!$venta) redirect('/distribuciones-caribe/modules/ventas/index.php');

$detalle = $pdo->prepare(
    "SELECT dv.*, p.nombre AS producto, p.codigo
     FROM detalle_ventas dv JOIN productos p ON p.id=dv.producto_id
     WHERE dv.venta_id=?"
);
$detalle->execute([$venta['id']]);
$items = $detalle->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>
<div class="no-print" style="margin-bottom:1rem;display:flex;gap:.5rem">
  <button onclick="window.print()" class="btn btn-primary">Imprimir Factura</button>
  <a href="index.php" class="btn btn-secondary">Volver</a>
</div>

<div class="factura">
  <div style="display:flex;justify-content:space-between;margin-bottom:1.5rem">
    <div>
      <h2>Distribuciones Caribe S.A.S</h2>
      <p>NIT: 900.000.000-1</p>
    </div>
    <div style="text-align:right">
      <h3>FACTURA DE VENTA</h3>
      <p><strong><?= sanitize($venta['numero']) ?></strong></p>
      <p><?= formatDate($venta['creado_en']) ?></p>
    </div>
  </div>

  <div style="margin-bottom:1.5rem">
    <strong>Cliente:</strong> <?= sanitize($venta['tipo_doc'].' '.$venta['documento'].' - '.$venta['cli_nombre'].' '.$venta['cli_apellido']) ?><br>
    <strong>Ciudad:</strong> <?= sanitize($venta['ciudad'] ?? '') ?><br>
    <strong>Vendedor:</strong> <?= sanitize($venta['vendedor']) ?>
  </div>

  <table style="width:100%;border-collapse:collapse;margin-bottom:1rem">
    <thead>
      <tr style="background:#1a1d27;color:#fff">
        <th style="padding:.5rem;border:1px solid #ccc">Codigo</th>
        <th style="padding:.5rem;border:1px solid #ccc">Producto</th>
        <th style="padding:.5rem;border:1px solid #ccc">Cant.</th>
        <th style="padding:.5rem;border:1px solid #ccc">Precio unit.</th>
        <th style="padding:.5rem;border:1px solid #ccc">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td style="padding:.4rem;border:1px solid #ccc"><?= sanitize($item['codigo']) ?></td>
        <td style="padding:.4rem;border:1px solid #ccc"><?= sanitize($item['producto']) ?></td>
        <td style="padding:.4rem;border:1px solid #ccc;text-align:center"><?= $item['cantidad'] ?></td>
        <td style="padding:.4rem;border:1px solid #ccc;text-align:right"><?= formatMoney((float)$item['precio_unit']) ?></td>
        <td style="padding:.4rem;border:1px solid #ccc;text-align:right"><?= formatMoney((float)$item['subtotal']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr><td colspan="4" style="text-align:right;padding:.4rem;border:1px solid #ccc"><strong>Subtotal:</strong></td><td style="text-align:right;padding:.4rem;border:1px solid #ccc"><?= formatMoney((float)$venta['subtotal']) ?></td></tr>
      <tr><td colspan="4" style="text-align:right;padding:.4rem;border:1px solid #ccc"><strong>IVA (19%):</strong></td><td style="text-align:right;padding:.4rem;border:1px solid #ccc"><?= formatMoney((float)$venta['iva']) ?></td></tr>
      <tr style="font-size:1.1rem"><td colspan="4" style="text-align:right;padding:.4rem;border:1px solid #ccc"><strong>TOTAL:</strong></td><td style="text-align:right;padding:.4rem;border:1px solid #ccc;font-weight:700"><?= formatMoney((float)$venta['total']) ?></td></tr>
    </tfoot>
  </table>

  <?php if ($venta['notas']): ?>
  <p><strong>Notas:</strong> <?= sanitize($venta['notas']) ?></p>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
