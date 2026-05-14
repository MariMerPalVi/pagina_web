<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/public_layout.php';
require_once __DIR__ . '/includes/public_data.php';
require_once __DIR__ . '/config/database.php';

$categories = public_categories();
$socialLinks = social_links();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verify_csrf();

        $nombre = trim($_POST['nombre'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $producto = trim($_POST['producto'] ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');

        if ($nombre === '' || $telefono === '' || $correo === '' || $producto === '' || $mensaje === '') {
            throw new RuntimeException('Completa nombre, teléfono, correo, tipo de producto y mensaje.');
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Ingresa un correo electrónico válido.');
        }

        db()->exec(
            'CREATE TABLE IF NOT EXISTS solicitudes_cotizacion (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(160) NOT NULL,
                telefono VARCHAR(60) NOT NULL,
                correo VARCHAR(160) NULL,
                producto VARCHAR(160) NOT NULL,
                mensaje TEXT NOT NULL,
                respuesta TEXT NULL,
                estado ENUM("pendiente", "respondida", "cerrada") NOT NULL DEFAULT "pendiente",
                respondido_por INT NULL,
                fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                fecha_respuesta TIMESTAMP NULL DEFAULT NULL,
                CONSTRAINT fk_solicitudes_usuario FOREIGN KEY (respondido_por) REFERENCES usuarios(id) ON DELETE SET NULL
            ) ENGINE=InnoDB'
        );

        $stmt = db()->prepare('INSERT INTO solicitudes_cotizacion (nombre, telefono, correo, producto, mensaje) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$nombre, $telefono, $correo ?: null, $producto, $mensaje]);

        send_email(
            contact_email(),
            'Nueva solicitud de cotización FALEX',
            "Nueva solicitud recibida desde la web de FALEX.\n\nNombre: {$nombre}\nTeléfono: {$telefono}\nCorreo: " . ($correo ?: 'No indicado') . "\nProducto: {$producto}\n\nMensaje:\n{$mensaje}",
            $correo ?: null
        );

        flash('success', 'Tu solicitud fue enviada correctamente. Te contactaremos pronto.');
        redirect('contacto.php');
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
        redirect('contacto.php');
    }
}

public_header('Contacto', 'contacto');
$flashes = get_flashes();
?>
<main>
  <?php page_hero('Contacto', 'Cuéntanos qué necesitas confeccionar', 'Te asesoramos con telas, tallas, cantidades, bordados, estampados, sublimación y tiempos de entrega.'); ?>

  <section class="section contact-section">
    <div class="contact-info">
      <p class="eyebrow">Datos de contacto</p>
      <h2>Solicita información o cotiza tu proyecto</h2>
      <p>Escríbenos por WhatsApp, llamada, correo o formulario. Mientras más detalles compartas, mejor podremos orientarte.</p>
      <?php foreach ($flashes as $flash): ?>
        <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
      <?php endforeach; ?>
      <ul class="contact-list">
        <li><strong>WhatsApp:</strong> <a href="<?= e(whatsapp_link('Hola FALEX, necesito información.')) ?>" target="_blank" rel="noopener"><?= e(contact_phone()) ?></a></li>
        <li><strong>Correo:</strong> <a href="mailto:<?= e(contact_email()) ?>"><?= e(contact_email()) ?></a></li>
        <li><strong>Dirección:</strong> <?= e(contact_address()) ?></li>
        <?php if ($socialLinks): ?>
          <li><strong>Redes:</strong>
            <?php $socialIndex = 0; foreach ($socialLinks as $name => $link): ?>
              <?= $socialIndex++ > 0 ? ' · ' : '' ?><a href="<?= e($link) ?>" target="_blank" rel="noopener"><?= e($name) ?></a>
            <?php endforeach; ?>
          </li>
        <?php endif; ?>
      </ul>
    </div>
    <form class="contact-form" method="post">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <label>Nombre<input type="text" name="nombre" placeholder="Tu nombre o institución" required></label>
      <label>Teléfono<input type="tel" name="telefono" placeholder="+593..." required></label>
      <label>Correo electrónico<input type="email" name="correo" placeholder="correo@ejemplo.com" required></label>
      <label>Tipo de producto requerido
        <select name="producto" required>
          <option value="">Selecciona una opción</option>
          <?php foreach ($categories as $category): ?>
            <option><?= e($category['nombre']) ?></option>
          <?php endforeach; ?>
          <option>Otro proyecto textil</option>
        </select>
      </label>
      <label class="full">Mensaje<textarea name="mensaje" rows="5" placeholder="Indica cantidad, tallas, colores, fecha de entrega y detalles del diseño." required></textarea></label>
      <button class="btn btn-primary full" type="submit">Enviar solicitud</button>
    </form>
  </section>
</main>
<?php public_footer(); ?>
