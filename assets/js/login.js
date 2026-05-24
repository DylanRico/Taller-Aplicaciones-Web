// Login/Register form switch logic
const btnIniciar = document.getElementById('btn__iniciar-sesion');
const btnRegistrarse = document.getElementById('btn__registrarse');
const formularioLogin = document.querySelector('.formulario__login');
const formularioRegister = document.querySelector('.formulario__register');
const contenedorLoginRegister = document.querySelector('.contenedor__login-register');
const cajaTraseraLogin = document.querySelector('.caja__trasera-login');
const cajaTraseraRegister = document.querySelector('.caja__trasera-register');
const registerRol = document.getElementById('register-rol');
const datosAdmin = document.getElementById('datos-admin');
const datosUsuario = document.getElementById('datos-usuario');

function anchoPage(){
    if (window.innerWidth > 850){
        cajaTraseraRegister.style.display = 'block';
        cajaTraseraLogin.style.display = 'block';
    }else{
        cajaTraseraRegister.style.display = 'block';
        cajaTraseraRegister.style.opacity = '1';
        cajaTraseraLogin.style.display = 'none';
        formularioLogin.style.display = 'block';
        contenedorLoginRegister.style.left = '0px';
        formularioRegister.style.display = 'none';   
    }
}

function iniciarSesion(){
    if (window.innerWidth > 850){
        formularioLogin.style.display = 'block';
        contenedorLoginRegister.style.left = '10px';
        formularioRegister.style.display = 'none';
        cajaTraseraRegister.style.opacity = '1';
        cajaTraseraLogin.style.opacity = '0';
    }else{
        formularioLogin.style.display = 'block';
        contenedorLoginRegister.style.left = '0px';
        formularioRegister.style.display = 'none';
        cajaTraseraRegister.style.display = 'block';
        cajaTraseraLogin.style.display = 'none';
    }
}

function register(){
    if (window.innerWidth > 850){
        formularioRegister.style.display = 'block';
        contenedorLoginRegister.style.left = '410px';
        formularioLogin.style.display = 'none';
        cajaTraseraRegister.style.opacity = '0';
        cajaTraseraLogin.style.opacity = '1';
    }else{
        formularioRegister.style.display = 'block';
        contenedorLoginRegister.style.left = '0px';
        formularioLogin.style.display = 'none';
        cajaTraseraRegister.style.display = 'none';
        cajaTraseraLogin.style.display = 'block';
        cajaTraseraLogin.style.opacity = '1';
    }
}

function ajustarCamposRol() {
    if (registerRol && registerRol.value === 'administrador') {
        datosAdmin.style.display = 'block';
        datosUsuario.style.display = 'none';
    } else {
        datosAdmin.style.display = 'none';
        datosUsuario.style.display = 'block';
    }
}

window.addEventListener('resize', anchoPage);
btnIniciar.addEventListener('click', iniciarSesion);
btnRegistrarse.addEventListener('click', register);

if (registerRol) {
    registerRol.addEventListener('change', ajustarCamposRol);
    ajustarCamposRol();
}

anchoPage();
