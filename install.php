<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 3,
        ]);

        $schema = file_get_contents(__DIR__ . '/database/schema.sql');
        foreach (array_filter(array_map('trim', explode(';', $schema))) as $statement) {
            $pdo->exec($statement);
        }

        require_once __DIR__ . '/config/database.php';
        $db = db();

        $categories = [
            ['Cuellos escolares', 'Cuellos para uniformes escolares personalizados.'],
            ['Cuellos empresariales', 'Cuellos para prendas corporativas y uniformes empresariales.'],
            ['Camisetas personalizadas', 'Camisetas para eventos, campañas, grupos y marcas.'],
            ['Equipos deportivos', 'Uniformes sublimados y estampados para equipos.'],
            ['Prendas para hombre', 'Prendas en telas de punto para hombres.'],
            ['Prendas para mujer', 'Prendas en telas de punto para mujeres.'],
            ['Prendas para niños', 'Prendas cómodas y personalizadas para niños.'],
            ['Bordados institucionales', 'Sellos, nombres y logotipos bordados.'],
            ['Sublimados', 'Diseños sublimados con acabado profesional.'],
            ['Estampados', 'Estampados textiles para prendas personalizadas.'],
        ];

        $stmt = $db->prepare('INSERT INTO categorias (nombre, descripcion, estado) SELECT ?, ?, "activo" WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE nombre = ?)');
        foreach ($categories as $category) {
            $stmt->execute([$category[0], $category[1], $category[0]]);
        }

        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $employeePassword = password_hash('empleado123', PASSWORD_DEFAULT);

        $stmt = $db->prepare('INSERT INTO usuarios (nombre, correo, usuario, password_hash, rol, estado) SELECT ?, ?, ?, ?, ?, "activo" WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE usuario = ?)');
        $stmt->execute(['Administrador FALEX', CONTACT_EMAIL, 'admin', $adminPassword, 'administrador', 'admin']);
        $stmt->execute(['Empleado Demo', 'empleado@falextextil.com', 'empleado', $employeePassword, 'empleado', 'empleado']);

        $message = 'Instalación completada. Usuario admin: admin / admin123. Usuario empleado: empleado / empleado123.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Instalar FALEX</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body class="install-page">
  <main class="install-card">
    <img src="assets/logo-falex.svg" alt="FALEX" width="90" height="90">
    <h1>Instalar sistema FALEX</h1>
    <p>Este instalador crea la base de datos, tablas, categorías iniciales y usuarios de prueba para XAMPP.</p>

    <?php if ($message): ?>
      <div class="alert success"><?= e($message) ?></div>
      <a class="btn btn-primary" href="login.php">Ir al login</a>
      <a class="btn btn-secondary" href="index.php">Ver página pública</a>
    <?php else: ?>
      <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
      <?php endif; ?>
      <form method="post">
        <button class="btn btn-primary" type="submit">Crear base de datos e instalar</button>
      </form>
    <?php endif; ?>
  </main>
</body>
</html>
