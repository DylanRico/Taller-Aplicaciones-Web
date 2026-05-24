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
if (!$venta) redirect('/Taller-Aplicaciones-Web/modules/ventas/index.php');

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
<div class="no-print" style="margin-bottom:1.25rem;display:flex;gap:.5rem">
  <button onclick="window.print()" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    Imprimir
  </button>
  <a href="index.php" class="btn btn-secondary">← Volver</a>
</div>

<div class="factura">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:2px solid #e5e7eb">
    <div>
      <h2 style="font-size:1.4rem;margin-bottom:.25rem">Distribuciones Caribe S.A.S</h2>
      <p style="color:#6b7280;font-size:.875rem">NIT: 900.000.000-1</p>
    </div>
    <div style="text-align:right">
      <div style="background:#1a1d27;color:#fff;padding:.5rem 1rem;border-radius:8px;display:inline-block;margin-bottom:.5rem">
        <h3 style="font-size:.75rem;letter-spacing:.08em;text-transform:uppercase;opacity:.7;margin-bottom:.15rem">Factura de Venta</h3>
        <p style="font-size:1.1rem;font-weight:800;font-variant-numeric:tabular-nums"><?= sanitize($venta['numero']) ?></p>
      </div>
      <p style="color:#6b7280;font-size:.875rem"><?= formatDate($venta['creado_en']) ?></p>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
    <div>
      <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;margin-bottom:.5rem">Cliente</p>
      <p style="font-weight:600"><?= sanitize($venta['cli_nombre'].' '.$venta['cli_apellido']) ?></p>
      <p style="color:#6b7280;font-size:.875rem"><?= sanitize($venta['tipo_doc'].' '.$venta['documento']) ?></p>
      <?php if ($venta['ciudad']): ?>
      <p style="color:#6b7280;font-size:.875rem"><?= sanitize($venta['ciudad']) ?></p>
      <?php endif; ?>
    </div>
    <div>
      <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;margin-bottom:.5rem">Vendedor</p>
      <p style="font-weight:600"><?= sanitize($venta['vendedor']) ?></p>
    </div>
  </div>

  <table style="width:100%;border-collapse:collapse;margin-bottom:1.5rem;font-size:.875rem">
    <thead>
      <tr>
        <th style="padding:.625rem .75rem;background:#1a1d27;color:#fff;text-align:left;font-weight:600;font-size:.8rem;letter-spacing:.03em">Código</th>
        <th style="padding:.625rem .75rem;background:#1a1d27;color:#fff;text-align:left;font-weight:600;font-size:.8rem">Producto</th>
        <th style="padding:.625rem .75rem;background:#1a1d27;color:#fff;text-align:center;font-weight:600;font-size:.8rem">Cant.</th>
        <th style="padding:.625rem .75rem;background:#1a1d27;color:#fff;text-align:right;font-weight:600;font-size:.8rem">Precio unit.</th>
        <th style="padding:.625rem .75rem;background:#1a1d27;color:#fff;text-align:right;font-weight:600;font-size:.8rem">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr style="border-bottom:1px solid #e5e7eb">
        <td style="padding:.5rem .75rem;color:#6b7280;font-family:monospace;font-size:.8rem"><?= sanitize($item['codigo']) ?></td>
        <td style="padding:.5rem .75rem;font-weight:500"><?= sanitize($item['producto']) ?></td>
        <td style="padding:.5rem .75rem;text-align:center;font-variant-numeric:tabular-nums"><?= $item['cantidad'] ?></td>
        <td style="padding:.5rem .75rem;text-align:right;font-variant-numeric:tabular-nums"><?= formatMoney((float)$item['precio_unit']) ?></td>
        <td style="padding:.5rem .75rem;text-align:right;font-weight:600;font-variant-numeric:tabular-nums"><?= formatMoney((float)$item['subtotal']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="4" style="padding:.5rem .75rem;text-align:right;color:#6b7280;font-size:.875rem;border-top:1px solid #d1d5db">Subtotal:</td>
        <td style="padding:.5rem .75rem;text-align:right;font-variant-numeric:tabular-nums;border-top:1px solid #d1d5db"><?= formatMoney((float)$venta['subtotal']) ?></td>
      </tr>
      <tr>
        <td colspan="4" style="padding:.5rem .75rem;text-align:right;color:#6b7280;font-size:.875rem">IVA (19%):</td>
        <td style="padding:.5rem .75rem;text-align:right;font-variant-numeric:tabular-nums"><?= formatMoney((float)$venta['iva']) ?></td>
      </tr>
      <tr style="background:#f9fafb">
        <td colspan="4" style="padding:.625rem .75rem;text-align:right;font-weight:700;font-size:1rem;border-top:2px solid #1a1d27">TOTAL:</td>
        <td style="padding:.625rem .75rem;text-align:right;font-weight:800;font-size:1.1rem;font-variant-numeric:tabular-nums;border-top:2px solid #1a1d27"><?= formatMoney((float)$venta['total']) ?></td>
      </tr>
    </tfoot>
  </table>

  <?php if ($venta['notas']): ?>
  <div style="padding:.75rem 1rem;background:#f3f4f6;border-radius:8px;font-size:.875rem">
    <strong>Notas:</strong> <?= sanitize($venta['notas']) ?>
  </div>
  <?php endif; ?>

  <div style="margin-top:2rem;padding-top:1rem;border-top:1px solid #e5e7eb;text-align:center;color:#9ca3af;font-size:.75rem">
    Distribuciones Caribe S.A.S &mdash; Documento generado el <?= date('d/m/Y H:i') ?>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
