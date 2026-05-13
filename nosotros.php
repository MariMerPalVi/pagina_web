<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/public_layout.php';

public_header('Nosotros', 'nosotros');
?>
<main>
  <?php page_hero('Nosotros', 'Una fábrica textil especializada en prendas con identidad', 'FALEX combina experiencia, personalización y cuidado en los acabados para atender pedidos institucionales, empresariales, deportivos y particulares.'); ?>

  <section class="section split-section">
    <div>
      <p class="eyebrow">Quiénes somos</p>
      <h2>Confección, bordado, sublimación y estampado en un mismo lugar</h2>
    </div>
    <div class="text-block">
      <p>FALEX es una fábrica textil dedicada a producir prendas personalizadas para instituciones educativas, empresas, equipos deportivos y clientes particulares.</p>
      <p>Trabajamos cuellos escolares y empresariales, uniformes, camisetas, prendas en telas de punto, bordados de sellos institucionales, estampados y sublimados.</p>
      <p>Nuestro compromiso es ofrecer asesoría cercana, materiales adecuados, producción organizada y acabados profesionales en cada pedido.</p>
    </div>
  </section>

  <section class="section why-section">
    <div class="section-heading compact">
      <p class="eyebrow">Por qué elegir FALEX</p>
      <h2>Confianza para producir prendas con acabado profesional</h2>
    </div>
    <div class="benefits-grid">
      <div><strong>Calidad en cada prenda</strong><span>Controlamos detalles de confección, presentación y acabado.</span></div>
      <div><strong>Diseños personalizados</strong><span>Adaptamos colores, logotipos, tallas, nombres y estilos.</span></div>
      <div><strong>Atención integral</strong><span>Trabajamos con empresas, escuelas, colegios y equipos deportivos.</span></div>
      <div><strong>Variedad textil</strong><span>Cuellos, camisetas, uniformes, prendas de punto, bordados y sublimados.</span></div>
      <div><strong>Experiencia técnica</strong><span>Confección, bordado, estampado y sublimación en un mismo lugar.</span></div>
      <div><strong>Cumplimiento</strong><span>Responsabilidad y comunicación clara en cada pedido.</span></div>
    </div>
  </section>

  <?php public_cta(); ?>
</main>
<?php public_footer(); ?>
