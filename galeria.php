<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/public_layout.php';
require_once __DIR__ . '/includes/public_data.php';

$galleryItems = public_gallery_items();

public_header('Galería', 'galeria');
?>
<main>
  <?php page_hero('Galería', 'Trabajos realizados por FALEX', 'Un espacio para mostrar bordados, camisetas, uniformes, equipos deportivos, cuellos escolares y cuellos empresariales.'); ?>

  <section class="section gallery-section">
    <?php if ($galleryItems): ?>
      <div class="gallery-grid dynamic-gallery">
        <?php foreach ($galleryItems as $item): ?>
          <article class="gallery-photo-card">
            <img src="<?= e(product_image_url($item['imagen'])) ?>" alt="<?= e($item['nombre']) ?>" loading="lazy">
            <div>
              <span><?= e($item['categoria']) ?></span>
              <h3><?= e($item['nombre']) ?></h3>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <div>
          <span>Galería administrable</span>
          <h3>Aún no hay imágenes publicadas</h3>
          <p>Sube imágenes en el módulo Productos del panel administrativo. Los productos activos con imagen aparecerán automáticamente en esta galería.</p>
        </div>
        <a class="btn btn-primary" href="<?= e(url('login.php')) ?>">Ir al panel</a>
      </div>
    <?php endif; ?>
  </section>

  <?php public_cta(); ?>
</main>
<?php public_footer(); ?>
