<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/public_layout.php';
require_once __DIR__ . '/includes/public_data.php';

$services = falex_services();

public_header('Servicios', 'servicios');
?>
<main>
  <?php page_hero('Servicios', 'Producción textil para proyectos de todo tamaño', 'Elige el servicio que necesitas y escríbenos para definir cantidades, tallas, telas, colores, bordados, estampados y sublimación.'); ?>

  <section class="section">
    <div class="card-grid services-grid">
      <?php foreach ($services as $index => $service): ?>
        <article class="service-card">
          <div class="service-image" aria-hidden="true"><span><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span></div>
          <h3><?= e($service[0]) ?></h3>
          <p><?= e($service[1]) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="section process-section">
    <div class="section-heading">
      <p class="eyebrow">Proceso</p>
      <h2>Del diseño a la entrega con una ruta clara</h2>
      <p>Trabajamos cada pedido con pasos simples para que sepas qué se produce, cómo se verá y cuándo estará listo.</p>
    </div>
    <div class="process-grid">
      <article><span>01</span><h3>Revisamos tu necesidad</h3><p>Cantidad, tallas, tipo de prenda, colores, logos, presupuesto y fecha de entrega.</p></article>
      <article><span>02</span><h3>Preparamos la propuesta</h3><p>Definimos materiales, técnica de personalización y detalles de acabado para aprobar producción.</p></article>
      <article><span>03</span><h3>Confeccionamos y personalizamos</h3><p>Corte, confección, bordado, estampado o sublimación con revisión de calidad.</p></article>
      <article><span>04</span><h3>Entregamos tu pedido</h3><p>Prendas terminadas, organizadas y listas para institución, empresa, equipo o evento.</p></article>
    </div>
  </section>

  <?php public_cta(); ?>
</main>
<?php public_footer(); ?>
