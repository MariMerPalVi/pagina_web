<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function admin_header(string $title): void
{
    require_login();
    $user = current_user();
    $flashes = get_flashes();
    $current = basename($_SERVER['SCRIPT_NAME']);
    $menu = [
        ['Dashboard', 'index.php', ['index.php']],
        ['Productos', 'products.php', ['products.php', 'product_form.php']],
        ['Categorías', 'categories.php', ['categories.php']],
    ];

    if (is_admin()) {
        $menu[] = ['Cotizaciones', 'quotes.php', ['quotes.php']];
        $menu[] = ['Empleados', 'employees.php', ['employees.php']];
        $menu[] = ['Configuración', 'settings.php', ['settings.php']];
    }
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?> | Panel FALEX</title>
  <link rel="stylesheet" href="<?= e(url('css/styles.css?v=' . filemtime(__DIR__ . '/../css/styles.css'))) ?>">
  <link rel="stylesheet" href="<?= e(url('css/admin.css?v=' . filemtime(__DIR__ . '/../css/admin.css'))) ?>">
</head>
<body class="admin-body">
  <aside class="admin-sidebar">
    <a class="brand admin-brand" href="<?= e(url('admin/index.php')) ?>">
      <img src="<?= e(url('assets/logo-falex.svg')) ?>" alt="FALEX" width="62" height="62">
      <span><strong>FALEX</strong><small><?= e($user['rol']) ?></small></span>
    </a>
    <nav class="admin-menu" aria-label="Panel administrativo">
      <?php foreach ($menu as [$label, $href, $matches]): ?>
        <a class="<?= in_array($current, $matches, true) ? 'active' : '' ?>" href="<?= e(url('admin/' . $href)) ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar-footer">
      <a href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener">Ver sitio público</a>
      <a href="<?= e(url('logout.php')) ?>">Cerrar sesión</a>
    </div>
  </aside>
  <main class="admin-main">
    <header class="admin-mobilebar">
      <a class="brand" href="<?= e(url('admin/index.php')) ?>">
        <img src="<?= e(url('assets/logo-falex.svg')) ?>" alt="FALEX" width="48" height="48">
        <span><strong>FALEX</strong><small>Panel interno</small></span>
      </a>
      <a class="btn btn-secondary btn-small" href="<?= e(url('logout.php')) ?>">Salir</a>
    </header>
    <nav class="admin-mobile-menu" aria-label="Navegación móvil del panel">
      <?php foreach ($menu as [$label, $href, $matches]): ?>
        <a class="<?= in_array($current, $matches, true) ? 'active' : '' ?>" href="<?= e(url('admin/' . $href)) ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
      <a href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener">Ver sitio</a>
    </nav>
    <header class="admin-topbar">
      <div>
        <p class="eyebrow">Panel interno</p>
        <h1><?= e($title) ?></h1>
      </div>
      <div class="admin-topbar-actions">
        <a class="btn btn-secondary" href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener">Ver sitio</a>
        <div class="admin-user">
          <strong><?= e($user['nombre']) ?></strong>
          <span><?= e($user['rol']) ?></span>
        </div>
      </div>
    </header>
    <?php foreach ($flashes as $flash): ?>
      <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endforeach; ?>
    <?php
}

function admin_footer(): void
{
    ?>
  </main>
</body>
</html>
    <?php
}
