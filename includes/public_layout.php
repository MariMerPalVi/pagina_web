<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

function public_header(string $title, string $active = 'inicio', string $description = ''): void
{
    $description = $description ?: 'FALEX, fábrica textil especializada en cuellos, bordados, sublimación, estampados, uniformes y camisetas personalizadas.';
    $items = [
        'inicio' => ['Inicio', 'index.php'],
        'nosotros' => ['Nosotros', 'nosotros.php'],
        'servicios' => ['Servicios', 'servicios.php'],
        'catalogo' => ['Catálogo', 'catalogo.php'],
        'galeria' => ['Galería', 'galeria.php'],
        'contacto' => ['Contacto', 'contacto.php'],
    ];
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($description) ?>">
  <title><?= e($title) ?> | FALEX Fábrica Textil</title>
  <link rel="stylesheet" href="<?= e(url('css/styles.css')) ?>">
</head>
<body>
  <header class="site-header">
    <div class="top-contact-bar">
      <div>
        <a href="tel:<?= e(str_replace(' ', '', CONTACT_PHONE)) ?>"><?= e(CONTACT_PHONE) ?></a>
        <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a>
      </div>
      <a href="<?= e(url('catalogo.php')) ?>">Ver productos</a>
    </div>
    <nav class="navbar" aria-label="Navegación principal">
      <a class="brand" href="<?= e(url('index.php')) ?>">
        <img src="<?= e(url('assets/logo-falex.svg')) ?>" alt="FALEX Fábrica Textil" width="72" height="72">
        <span><strong>FALEX</strong><small>Fábrica Textil</small></span>
      </a>
      <button class="menu-toggle" type="button" aria-label="Abrir menú" aria-expanded="false"><span></span><span></span><span></span></button>
      <div class="nav-links">
        <?php foreach ($items as $key => [$label, $href]): ?>
          <a class="<?= $active === $key ? 'active' : '' ?>" href="<?= e(url($href)) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
      </div>
      <a class="btn btn-primary nav-cta" href="<?= e(whatsapp_link('Hola FALEX, quiero solicitar una cotización.')) ?>" target="_blank" rel="noopener">Solicitar cotización</a>
    </nav>
  </header>
    <?php
}

function page_hero(string $eyebrow, string $title, string $copy): void
{
    ?>
  <section class="page-hero">
    <div>
      <p class="eyebrow"><?= e($eyebrow) ?></p>
      <h1><?= e($title) ?></h1>
      <p><?= e($copy) ?></p>
    </div>
  </section>
    <?php
}

function public_cta(): void
{
    ?>
  <section class="cta-section">
    <div>
      <p class="eyebrow">Cotizaciones</p>
      <h2>Haz realidad el diseño que tienes en mente. Solicita tu cotización hoy mismo.</h2>
    </div>
    <div class="cta-actions">
      <a class="btn btn-light" href="<?= e(whatsapp_link('Hola FALEX, quiero cotizar un proyecto textil.')) ?>" target="_blank" rel="noopener">WhatsApp</a>
      <a class="btn btn-outline-light" href="tel:<?= e(str_replace(' ', '', CONTACT_PHONE)) ?>">Llamar</a>
      <a class="btn btn-outline-light" href="mailto:<?= e(CONTACT_EMAIL) ?>">Correo</a>
    </div>
  </section>
    <?php
}

function public_footer(): void
{
    ?>
  <a class="whatsapp-float" href="<?= e(whatsapp_link('Hola FALEX, quiero información sobre sus servicios.')) ?>" target="_blank" rel="noopener" aria-label="Contactar por WhatsApp">WhatsApp</a>

  <footer class="site-footer">
    <p>© <?= date('Y') ?> FALEX Fábrica Textil. Confección y personalización de prendas.</p>
    <a href="<?= e(url('login.php')) ?>">Acceso interno</a>
  </footer>

  <script src="<?= e(url('js/main.js')) ?>"></script>
</body>
</html>
    <?php
}
