<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    redirect('admin/index.php');
}

$error = '';
$dbError = '';

try {
    db();
} catch (PDOException $exception) {
    $dbError = 'No se pudo conectar con MySQL. Revisa en config/database.php el host, nombre de base de datos, usuario y contraseña entregados por InfinityFree.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$dbError && login($username, $password)) {
        redirect('admin/index.php');
    }

    $error = $dbError ?: 'Usuario o contraseña incorrectos, o usuario inactivo.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login interno | FALEX</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body class="login-page">
  <main class="login-card">
    <a class="brand center-brand" href="index.php">
      <img src="assets/logo-falex.svg" alt="FALEX" width="76" height="76">
      <span><strong>FALEX</strong><small>Panel interno</small></span>
    </a>
    <h1>Iniciar sesión</h1>
    <p>Accede para gestionar catálogo, productos y usuarios según tu rol.</p>

    <?php if ($dbError): ?>
      <div class="alert error"><?= e($dbError) ?></div>
    <?php elseif ($error): ?>
      <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="auth-form">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <label>Usuario<input type="text" name="usuario" required autocomplete="username"></label>
      <label>Contraseña<input type="password" name="password" required autocomplete="current-password"></label>
      <button class="btn btn-primary" type="submit">Entrar al panel</button>
    </form>
  </main>
</body>
</html>
