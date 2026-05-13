<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_layout.php';
require_admin();

admin_header('Configuración');
?>
<section class="admin-panel narrow-panel">
  <h2>Datos principales del sitio</h2>
  <p class="muted-text">Para cambiar WhatsApp, correo, teléfono o dirección edita las constantes en <strong>includes/helpers.php</strong>. Esta sección queda preparada para futuras configuraciones administrables desde base de datos.</p>
  <div class="settings-list">
    <div><span>WhatsApp</span><strong><?= e(WHATSAPP_NUMBER) ?></strong></div>
    <div><span>Teléfono</span><strong><?= e(CONTACT_PHONE) ?></strong></div>
    <div><span>Correo</span><strong><?= e(CONTACT_EMAIL) ?></strong></div>
    <div><span>Dirección</span><strong><?= e(CONTACT_ADDRESS) ?></strong></div>
  </div>
</section>
<?php admin_footer(); ?>
