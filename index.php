<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/public_layout.php';

public_header('Inicio', 'inicio');
?>
<main>
  <section class="hero">
    <div class="hero-content">
      <p class="eyebrow">Fábrica textil especializada</p>
      <h1>Confeccionamos identidad textil para instituciones, empresas y equipos.</h1>
      <p class="hero-copy">Somos una fábrica textil enfocada en crear prendas personalizadas con buenos acabados, asesoría cercana y capacidad para atender pedidos institucionales, empresariales, deportivos y particulares.</p>
      <div class="hero-actions">
        <a class="btn btn-primary" href="<?= e(url('catalogo.php')) ?>">Ver catálogo</a>
        <a class="btn btn-secondary" href="<?= e(whatsapp_link('Hola FALEX, deseo solicitar una cotización.')) ?>" target="_blank" rel="noopener">Solicitar por WhatsApp</a>
        <a class="btn btn-ghost" href="<?= e(url('contacto.php')) ?>">Contactar</a>
      </div>
      <div class="hero-metrics" aria-label="Capacidades de FALEX">
        <div><strong>9</strong><span>líneas de servicio</span></div>
        <div><strong>100%</strong><span>personalizable</span></div>
        <div><strong>24h</strong><span>respuesta comercial</span></div>
      </div>
    </div>
    <div class="hero-panel" aria-label="Identidad FALEX">
      <div class="fabric-card">
        <img class="hero-logo" src="<?= e(url('assets/logo-falex.svg')) ?>" alt="Logo FALEX Fábrica Textil" width="420" height="420">
      </div>
      <div class="hero-tags" aria-hidden="true">
        <span>Bordados</span>
        <span>Sublimación</span>
        <span>Uniformes</span>
      </div>
      <div class="production-card">
        <span>Catálogo administrable</span>
        <strong>Productos, precios e imágenes desde el panel interno</strong>
      </div>
    </div>
  </section>

  <section class="trust-strip" aria-label="Especialidades de FALEX">
    <div><span>01</span><strong>Uniformes escolares</strong></div>
    <div><span>02</span><strong>Ropa corporativa</strong></div>
    <div><span>03</span><strong>Equipos deportivos</strong></div>
    <div><span>04</span><strong>Bordados y sellos</strong></div>
  </section>

  <section class="section split-section">
    <div>
      <p class="eyebrow">Soluciones textiles</p>
      <h2>Todo lo que necesitas para vestir tu identidad</h2>
    </div>
    <div class="text-block">
      <p>FALEX produce cuellos escolares y empresariales, uniformes, camisetas personalizadas, equipos deportivos, bordados institucionales, estampados y sublimados.</p>
      <p>Explora cada módulo para conocer la empresa, ver servicios, consultar el catálogo, revisar trabajos y solicitar una cotización.</p>
    </div>
  </section>

  <section class="section module-grid-section">
    <div class="module-grid">
      <a href="<?= e(url('nosotros.php')) ?>"><span>Nosotros</span><strong>Conoce la fábrica</strong></a>
      <a href="<?= e(url('servicios.php')) ?>"><span>Servicios</span><strong>Lo que producimos</strong></a>
      <a href="<?= e(url('catalogo.php')) ?>"><span>Catálogo</span><strong>Productos activos</strong></a>
      <a href="<?= e(url('galeria.php')) ?>"><span>Galería</span><strong>Trabajos realizados</strong></a>
      <a href="<?= e(url('contacto.php')) ?>"><span>Contacto</span><strong>Cotiza tu pedido</strong></a>
    </div>
  </section>

  <?php public_cta(); ?>
</main>
<?php public_footer(); ?>
