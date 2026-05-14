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
        <a href="tel:<?= e(str_replace(' ', '', contact_phone())) ?>"><?= e(contact_phone()) ?></a>
        <a href="mailto:<?= e(contact_email()) ?>"><?= e(contact_email()) ?></a>
        <a class="top-social-link" href="<?= e(whatsapp_link('Hola FALEX, quiero información.')) ?>" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp"><?= icon_svg('whatsapp') ?></a>
        <?php foreach (social_links() as $name => $link): ?>
          <a class="top-social-link" href="<?= e($link) ?>" target="_blank" rel="noopener" aria-label="<?= e($name) ?>" title="<?= e($name) ?>"><?= icon_svg(strtolower($name)) ?></a>
        <?php endforeach; ?>
      </div>
      <a href="<?= e(url('catalogo.php')) ?>">Ver productos</a>
    </div>
    <nav class="navbar" aria-label="Navegación principal">
      <a class="brand" href="<?= e(url('index.php')) ?>">
        <img src="<?= e(url('assets/LOGO.png')) ?>" alt="FALEX Fábrica Textil" width="72" height="72">
        <span><strong>FALEX</strong><small>Fábrica Textil</small></span>
      </a>
      <button class="menu-toggle" type="button" aria-label="Abrir menú" aria-expanded="false"><span></span><span></span><span></span></button>
      <div class="nav-links">
        <?php foreach ($items as $key => [$label, $href]): ?>
          <a class="<?= $active === $key ? 'active' : '' ?>" href="<?= e(url($href)) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
      </div>
      <a class="btn btn-primary nav-cta" href="<?= e(url('contacto.php#cotizacion')) ?>">Solicitar cotización</a>
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
      <a class="btn btn-light" href="<?= e(url('contacto.php#cotizacion')) ?>">Solicitar cotización</a>
      <a class="btn btn-outline-light" href="tel:<?= e(str_replace(' ', '', contact_phone())) ?>">Llamar</a>
      <a class="btn btn-outline-light" href="mailto:<?= e(contact_email()) ?>">Correo</a>
    </div>
  </section>
    <?php
}

function public_footer(): void
{
    $socialLinks = social_links();
    $footerLinks = [
        ['Inicio', 'index.php', 'home'],
        ['Nosotros', 'nosotros.php', 'factory'],
        ['Servicios', 'servicios.php', 'services'],
        ['Productos', 'catalogo.php', 'catalog'],
        ['Cotización', 'contacto.php#cotizacion', 'contact'],
        ['Contacto', 'contacto.php', 'contact'],
    ];
    ?>
  <a class="whatsapp-float" href="<?= e(whatsapp_link('Hola FALEX, quiero información sobre sus servicios.')) ?>" target="_blank" rel="noopener" aria-label="Contactar por WhatsApp">WhatsApp</a>

  <footer class="site-footer">
    <div class="footer-brand">
      <strong>FALEX Fábrica Textil</strong>
      <p><?= e(contact_phone()) ?> · <?= e(contact_email()) ?> · <?= e(contact_address()) ?></p>
      <p>© <?= date('Y') ?> Confección y personalización de prendas.</p>
    </div>
    <div class="footer-nav">
      <div class="footer-page-links">
        <?php foreach ($footerLinks as [$label, $href, $icon]): ?>
          <a href="<?= e(url($href)) ?>"><?= icon_svg($icon) ?><span><?= e($label) ?></span></a>
        <?php endforeach; ?>
      </div>
      <div class="footer-links">
        <?php foreach ($socialLinks as $name => $link): ?>
          <a class="footer-icon-link" href="<?= e($link) ?>" target="_blank" rel="noopener" aria-label="<?= e($name) ?>" title="<?= e($name) ?>"><?= icon_svg(strtolower($name)) ?></a>
        <?php endforeach; ?>
        <a class="footer-internal-link" href="<?= e(url('login.php')) ?>"><?= icon_svg('lock') ?><span>Acceso interno</span></a>
      </div>
    </div>
  </footer>

  <script src="<?= e(url('js/main.js')) ?>"></script>
</body>
</html>
    <?php
}
