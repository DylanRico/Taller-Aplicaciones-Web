<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    redirect('/distribuciones-caribe/dashboard.php');
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
                header('Location: /distribuciones-caribe/dashboard.php');
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
  <title>Login - Distribuciones Caribe</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap">
  <link rel="stylesheet" href="/distribuciones-caribe/assets/css/login.css">
</head>
<body>
  <main>
    <div class="contenedor__todo">
      <div class="caja__trasera">
        <div class="caja__trasera-login">
          <h3>¿Ya tienes una cuenta?</h3>
          <p>Inicia sesión para entrar en la página.</p>
          <button type="button" id="btn__iniciar-sesion">Iniciar Sesión</button>
        </div>
        <div class="caja__trasera-register">
          <h3>¿Aún no tienes una cuenta?</h3>
          <p>Regístrate para que puedas iniciar sesión.</p>
          <button type="button" id="btn__registrarse">Regístrarse</button>
        </div>
      </div>

      <div class="contenedor__login-register">
        <form method="POST" class="formulario__login">
          <h2>Iniciar Sesión</h2>
          <?php if ($error && ($_POST['action'] ?? 'login') !== 'register'): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
          <?php elseif ($success && ($_POST['action'] ?? '') === 'register'): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
          <?php endif; ?>
          <input type="hidden" name="action" value="login">
          <input type="email" name="email" placeholder="Correo Electronico" class="form-control" required autofocus>
          <input type="password" name="password" placeholder="Contraseña" class="form-control" required>
          <button type="submit">Entrar</button>
        </form>

        <form method="POST" class="formulario__register">
          <h2>Regístrarse</h2>
          <?php if ($error && ($_POST['action'] ?? '') === 'register'): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>
          <input type="hidden" name="action" value="register">
          <input type="text" name="nombre" placeholder="Nombre completo" class="form-control" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
          <input type="text" name="email" placeholder="Correo Electronico" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          <input type="text" name="usuario" placeholder="Usuario" class="form-control" value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>">
          <input type="password" name="password" placeholder="Contraseña" class="form-control" required>
          <select name="rol" id="register-rol" class="form-control">
            <option value="vendedor" <?= (($_POST['rol'] ?? '') === 'vendedor') ? 'selected' : '' ?>>Usuario</option>
            <option value="administrador" <?= (($_POST['rol'] ?? '') === 'administrador') ? 'selected' : '' ?>>Administrador</option>
          </select>
          <div class="rol-datos" id="datos-usuario">
            <input type="text" name="direccion" placeholder="Dirección o teléfono" class="form-control" value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>">
          </div>
          <div class="rol-datos" id="datos-admin" style="display: none;">
            <input type="text" name="admin_code" placeholder="Código de administrador" class="form-control" value="<?= htmlspecialchars($_POST['admin_code'] ?? '') ?>">
          </div>
          <button type="submit">Regístrarse</button>
        </form>
      </div>
    </div>
  </main>
  <script src="/distribuciones-caribe/assets/js/login.js"></script>
  <?php if ($openRegister): ?>
    <script>window.addEventListener('load', register);</script>
  <?php endif; ?>
</body>
</html>
