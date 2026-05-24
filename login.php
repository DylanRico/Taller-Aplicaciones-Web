<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    redirect('/Taller-Aplicaciones-Web/dashboard.php');
}

$error = '';
$success = '';
$openRegister = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'register') {
        $openRegister = true;
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $rol = trim($_POST['rol'] ?? 'vendedor');
        $adminCode = trim($_POST['admin_code'] ?? '');

        if (!$nombre || !$email || !$password) {
            $error = 'Complete todos los campos del registro.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Correo electrónico inválido.';
        } elseif (!in_array($rol, ['vendedor', 'administrador'], true)) {
            $error = 'Rol no válido.';
        } elseif ($rol === 'administrador' && $adminCode !== 'CARIBE2026') {
            $error = 'Código de administrador incorrecto.';
        } else {
            $pdo = getDB();
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'El correo ya está registrado.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO usuarios (nombre,email,password,rol) VALUES (?,?,?,?)');
                $stmt->execute([$nombre, $email, $hash, $rol]);
                $success = 'Registro completado. Ahora puedes iniciar sesión.';
                $openRegister = false;
            }
        }
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email && $password) {
            $pdo = getDB();
            $stmt = $pdo->prepare('SELECT id, nombre, email, password, rol, activo FROM usuarios WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && $user['activo'] && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_rol'] = $user['rol'];
                header('Location: /Taller-Aplicaciones-Web/dashboard.php');
                exit;
            }
            $error = 'Credenciales incorrectas o usuario inactivo.';
        } else {
            $error = 'Complete todos los campos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Distribuciones Caribe — Acceso</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800&display=swap">
  <link rel="stylesheet" href="/Taller-Aplicaciones-Web/assets/css/login.css">
</head>
<body>

  <!-- ░░ iPad Air wallpaper background ░░ -->
  <div class="sky-layer">
    <div class="slug slug-1"></div>
    <div class="slug slug-2"></div>
    <div class="slug slug-3"></div>
    <div class="slug slug-4"></div>
    <div class="slug slug-5"></div>
    <div class="slug slug-6"></div>
    <div class="stripe stripe-1"></div>
    <div class="stripe stripe-2"></div>
    <div class="stripe stripe-3"></div>
  </div>

  <main>
    <div class="contenedor__todo">

      <!-- Back panel with toggle buttons -->
      <div class="caja__trasera">
        <div class="caja__trasera-login">
          <h3>¿Ya tienes cuenta?</h3>
          <p>Ingresa tus credenciales para acceder al sistema de Distribuciones Caribe.</p>
          <button type="button" id="btn__iniciar-sesion">Iniciar sesión</button>
        </div>
        <div class="caja__trasera-register">
          <h3>¿Nuevo por aquí?</h3>
          <p>Crea tu cuenta en segundos y empieza a gestionar tus ventas.</p>
          <button type="button" id="btn__registrarse">Crear cuenta</button>
        </div>
      </div>

      <!-- Form container -->
      <div class="contenedor__login-register">

        <!-- Login form -->
        <form method="POST" class="formulario__login">
          <div class="brand-mark">
            <div class="logo-icon">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 7H4C2.9 7 2 7.9 2 9v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zm-9 8H7v-2h4v2zm6 0h-4v-2h4v2zM4 7l8-4 8 4H4z"/>
              </svg>
            </div>
            <span>Distribuciones Caribe S.A.S</span>
          </div>

          <h2>Bienvenido</h2>
          <p class="form-subtitle">Inicia sesión para continuar</p>

          <?php if ($error && ($_POST['action'] ?? 'login') !== 'register'): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
          <?php elseif ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
          <?php endif; ?>

          <input type="hidden" name="action" value="login">

          <div class="input-group">
            <label for="login-email">Correo electrónico</label>
            <div class="input-wrapper">
              <input type="email" id="login-email" name="email" placeholder="tu@correo.com" class="form-control" required autofocus>
              <span class="input-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
              </span>
            </div>
          </div>

          <div class="input-group">
            <label for="login-password">Contraseña</label>
            <div class="input-wrapper">
              <input type="password" id="login-password" name="password" placeholder="••••••••" class="form-control" required>
              <span class="input-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
              </span>
            </div>
          </div>

          <button type="submit" class="btn-submit">Entrar</button>
        </form>

        <!-- Register form -->
        <form method="POST" class="formulario__register">
          <div class="brand-mark">
            <div class="logo-icon">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 7H4C2.9 7 2 7.9 2 9v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zm-9 8H7v-2h4v2zm6 0h-4v-2h4v2zM4 7l8-4 8 4H4z"/>
              </svg>
            </div>
            <span>Distribuciones Caribe S.A.S</span>
          </div>

          <h2>Crear cuenta</h2>
          <p class="form-subtitle">Completa los datos para registrarte</p>

          <?php if ($error && ($_POST['action'] ?? '') === 'register'): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <input type="hidden" name="action" value="register">

          <div class="input-group">
            <label for="reg-nombre">Nombre completo</label>
            <div class="input-wrapper">
              <input type="text" id="reg-nombre" name="nombre" placeholder="Juan Pérez" class="form-control" required
                     value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
              <span class="input-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                </svg>
              </span>
            </div>
          </div>

          <div class="input-group">
            <label for="reg-email">Correo electrónico</label>
            <div class="input-wrapper">
              <input type="email" id="reg-email" name="email" placeholder="tu@correo.com" class="form-control" required
                     value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
              <span class="input-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
              </span>
            </div>
          </div>

          <div class="input-group">
            <label for="reg-password">Contraseña</label>
            <div class="input-wrapper">
              <input type="password" id="reg-password" name="password" placeholder="••••••••" class="form-control" required>
              <span class="input-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
              </span>
            </div>
          </div>

          <div class="input-group">
            <label>Tipo de cuenta</label>
            <div class="rol-selector">
              <label class="rol-option <?= (($_POST['rol'] ?? 'vendedor') === 'vendedor') ? 'active' : '' ?>" id="opt-vendedor">
                <input type="radio" name="rol" value="vendedor" <?= (($_POST['rol'] ?? 'vendedor') === 'vendedor') ? 'checked' : '' ?>>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                Vendedor
              </label>
              <label class="rol-option <?= (($_POST['rol'] ?? '') === 'administrador') ? 'active' : '' ?>" id="opt-administrador">
                <input type="radio" name="rol" value="administrador" <?= (($_POST['rol'] ?? '') === 'administrador') ? 'checked' : '' ?>>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Administrador
              </label>
            </div>
          </div>

          <div class="rol-datos" id="datos-admin" style="<?= (($_POST['rol'] ?? '') === 'administrador') ? '' : 'display:none' ?>">
            <div class="input-group">
              <label for="reg-admin-code">Código de administrador</label>
              <div class="input-wrapper">
                <input type="text" id="reg-admin-code" name="admin_code" placeholder="Código secreto" class="form-control"
                       value="<?= htmlspecialchars($_POST['admin_code'] ?? '') ?>">
                <span class="input-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                  </svg>
                </span>
              </div>
            </div>
          </div>

          <button type="submit" class="btn-submit">Crear cuenta</button>
        </form>

      </div><!-- /.contenedor__login-register -->
    </div><!-- /.contenedor__todo -->
  </main>

  <script src="/Taller-Aplicaciones-Web/assets/js/login.js"></script>
  <?php if ($openRegister): ?>
    <script>window.addEventListener('load', register);</script>
  <?php endif; ?>
</body>
</html>
