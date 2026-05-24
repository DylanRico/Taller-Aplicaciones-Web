const btnIniciar             = document.getElementById('btn__iniciar-sesion');
const btnRegistrarse         = document.getElementById('btn__registrarse');
const formularioLogin        = document.querySelector('.formulario__login');
const formularioRegister     = document.querySelector('.formulario__register');
const contenedorLR           = document.querySelector('.contenedor__login-register');
const cajaTraseraLogin       = document.querySelector('.caja__trasera-login');
const cajaTraseraRegister    = document.querySelector('.caja__trasera-register');
const datosAdmin             = document.getElementById('datos-admin');
const rolOptions             = document.querySelectorAll('.rol-option');

// ── Ripple effect on submit buttons ──────────────────────────
document.querySelectorAll('.btn-submit').forEach(btn => {
  btn.addEventListener('click', function(e) {
    const ripple = document.createElement('span');
    ripple.classList.add('ripple-effect');
    const rect = btn.getBoundingClientRect();
    ripple.style.left = (e.clientX - rect.left) + 'px';
    ripple.style.top  = (e.clientY - rect.top)  + 'px';
    btn.appendChild(ripple);
    ripple.addEventListener('animationend', () => ripple.remove());
  });
});

// ── Switch to login ───────────────────────────────────────────
function iniciarSesion() {
  if (window.innerWidth > 768) {
    contenedorLR.style.left = '0';
    cajaTraseraLogin.style.opacity  = '0';
    cajaTraseraLogin.style.pointerEvents = 'none';
    cajaTraseraRegister.style.opacity = '1';
    cajaTraseraRegister.style.pointerEvents = 'all';
  }
  formularioRegister.style.display = 'none';
  formularioRegister.classList.remove('visible');
  formularioLogin.style.display = 'flex';
  formularioLogin.classList.add('visible');
}

// ── Switch to register ────────────────────────────────────────
function register() {
  if (window.innerWidth > 768) {
    contenedorLR.style.left = '50%';
    cajaTraseraRegister.style.opacity = '0';
    cajaTraseraRegister.style.pointerEvents = 'none';
    cajaTraseraLogin.style.opacity  = '1';
    cajaTraseraLogin.style.pointerEvents = 'all';
  }
  formularioLogin.style.display = 'none';
  formularioLogin.classList.remove('visible');
  formularioRegister.style.display = 'flex';
  formularioRegister.classList.add('visible');
}

// ── Responsive reset ─────────────────────────────────────────
function anchoPage() {
  if (window.innerWidth <= 768) {
    contenedorLR.style.left = '0';
    cajaTraseraLogin.style.opacity  = '1';
    cajaTraseraRegister.style.opacity = '1';
    cajaTraseraLogin.style.pointerEvents  = 'all';
    cajaTraseraRegister.style.pointerEvents = 'all';
  }
}

// ── Rol pill switch ───────────────────────────────────────────
function ajustarCamposRol(value) {
  if (!datosAdmin) return;
  rolOptions.forEach(opt => opt.classList.toggle('active', opt.querySelector('input').value === value));
  datosAdmin.style.display = (value === 'administrador') ? 'block' : 'none';
}

// ── Events ───────────────────────────────────────────────────
btnIniciar.addEventListener('click', iniciarSesion);
btnRegistrarse.addEventListener('click', register);
window.addEventListener('resize', anchoPage);

rolOptions.forEach(opt => {
  opt.addEventListener('click', () => {
    const radio = opt.querySelector('input[type="radio"]');
    radio.checked = true;
    ajustarCamposRol(radio.value);
  });
});

// Init with currently selected radio value
const checkedRadio = document.querySelector('.rol-option input[type="radio"]:checked');
if (checkedRadio) ajustarCamposRol(checkedRadio.value);

// ── Init: show login by default ───────────────────────────────
iniciarSesion();
