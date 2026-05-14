<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/public_layout.php';
require_once __DIR__ . '/includes/public_data.php';

$categories = public_categories();

public_header('Contacto', 'contacto');
?>
<main>
  <?php page_hero('Contacto', 'Cuéntanos qué necesitas confeccionar', 'Te asesoramos con telas, tallas, cantidades, bordados, estampados, sublimación y tiempos de entrega.'); ?>

  <section class="section contact-section">
    <div class="contact-info">
      <p class="eyebrow">Datos de contacto</p>
      <h2>Solicita información o cotiza tu proyecto</h2>
      <p>Escríbenos por WhatsApp, llamada, correo o formulario. Mientras más detalles compartas, mejor podremos orientarte.</p>
      <ul class="contact-list">
        <li><strong>WhatsApp:</strong> <a href="<?= e(whatsapp_link('Hola FALEX, necesito información.')) ?>" target="_blank" rel="noopener"><?= e(contact_phone()) ?></a></li>
        <li><strong>Correo:</strong> <a href="mailto:<?= e(contact_email()) ?>"><?= e(contact_email()) ?></a></li>
        <li><strong>Dirección:</strong> <?= e(contact_address()) ?></li>
        <li><strong>Redes:</strong> <a href="#">Instagram</a> · <a href="#">Facebook</a></li>
      </ul>
    </div>
    <form class="contact-form" action="mailto:<?= e(contact_email()) ?>" method="post" enctype="text/plain">
      <label>Nombre<input type="text" name="nombre" placeholder="Tu nombre o institución" required></label>
      <label>Teléfono<input type="tel" name="telefono" placeholder="+593..." required></label>
      <label>Correo electrónico<input type="email" name="correo" placeholder="correo@ejemplo.com"></label>
      <label>Tipo de producto requerido
        <select name="producto" required>
          <option value="">Selecciona una opción</option>
          <?php foreach ($categories as $category): ?>
            <option><?= e($category['nombre']) ?></option>
          <?php endforeach; ?>
          <option>Otro proyecto textil</option>
        </select>
      </label>
      <label class="full">Mensaje<textarea name="mensaje" rows="5" placeholder="Indica cantidad, tallas, colores, fecha de entrega y detalles del diseño."></textarea></label>
      <button class="btn btn-primary full" type="submit">Enviar solicitud</button>
    </form>
  </section>
</main>
<?php public_footer(); ?>
