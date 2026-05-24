// Sidebar toggle (responsive)
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
}

// Confirmar eliminacion
function confirmDelete(msg) {
  return confirm(msg || 'Esta seguro de que desea eliminar este registro?');
}

// Resaltar sidebar link activo
document.addEventListener('DOMContentLoaded', function () {
  const links = document.querySelectorAll('.sidebar a');
  links.forEach(link => {
    if (window.location.href.includes(link.getAttribute('href'))) {
      link.classList.add('active');
    }
  });

  // Auto-ocultar alertas despues de 4s
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(a => setTimeout(() => {
    a.classList.add('fade-out');
    a.addEventListener('transitionend', () => a.remove(), { once: true });
  }, 4000));
});

// Validar formularios antes de enviar
function validateForm(formId, rules) {
  const form = document.getElementById(formId);
  if (!form) return true;
  let valid = true;
  rules.forEach(({ field, message }) => {
    const el = form.querySelector(`[name="${field}"]`);
    if (el && !el.value.trim()) {
      el.style.borderColor = 'var(--danger)';
      alert(message);
      valid = false;
    }
  });
  return valid;
}

// Calcular totales en formulario de venta
function calcularTotales() {
  const filas = document.querySelectorAll('.fila-producto');
  let subtotal = 0;
  filas.forEach(fila => {
    const cant  = parseFloat(fila.querySelector('.cant')?.value  || 0);
    const precio= parseFloat(fila.querySelector('.precio')?.value || 0);
    const sub   = cant * precio;
    const subEl = fila.querySelector('.sub-linea');
    if (subEl) subEl.textContent = '$' + sub.toFixed(2);
    subtotal += sub;
  });
  const iva   = subtotal * 0.19;
  const total = subtotal + iva;
  const el = id => document.getElementById(id);
  if (el('subtotal')) el('subtotal').textContent = '$' + subtotal.toFixed(2);
  if (el('iva'))      el('iva').textContent      = '$' + iva.toFixed(2);
  if (el('total'))    el('total').textContent    = '$' + total.toFixed(2);
  if (el('h_subtotal')) el('h_subtotal').value   = subtotal.toFixed(2);
  if (el('h_iva'))      el('h_iva').value        = iva.toFixed(2);
  if (el('h_total'))    el('h_total').value      = total.toFixed(2);
}