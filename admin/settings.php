<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_layout.php';
require_admin();

function ensure_settings_table(): void
{
    db()->exec(
        'CREATE TABLE IF NOT EXISTS configuraciones (
            id INT AUTO_INCREMENT PRIMARY KEY,
            clave VARCHAR(80) NOT NULL UNIQUE,
            valor TEXT NOT NULL,
            fecha_actualizacion TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB'
    );
}

ensure_settings_table();

$labels = [
    'whatsapp_number' => 'WhatsApp',
    'contact_phone' => 'Teléfono',
    'contact_email' => 'Correo',
    'contact_address' => 'Dirección',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $stmt = db()->prepare(
        'INSERT INTO configuraciones (clave, valor)
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE valor = VALUES(valor)'
    );

    foreach ($labels as $key => $label) {
        $stmt->execute([$key, trim($_POST[$key] ?? '')]);
    }

    flash('success', 'Configuración actualizada correctamente.');
    redirect('admin/settings.php');
}

$settings = [];
$rows = db()->query('SELECT clave, valor FROM configuraciones')->fetchAll();
foreach ($rows as $row) {
    $settings[$row['clave']] = $row['valor'];
}

foreach (default_site_settings() as $key => $value) {
    $settings[$key] = $settings[$key] ?? $value;
}

admin_header('Configuración');
?>
<section class="admin-panel narrow-panel">
  <div class="panel-heading">
    <div>
      <h2>Datos principales del sitio</h2>
      <p class="muted-text">Estos datos se muestran en la web pública, botones de contacto y enlaces de WhatsApp.</p>
    </div>
  </div>

  <form class="admin-form" method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <label>
      WhatsApp
      <input type="text" name="whatsapp_number" value="<?= e($settings['whatsapp_number']) ?>" placeholder="593999999999" required>
      <small>Usa formato internacional, sin + ni espacios. Ejemplo: 593999999999.</small>
    </label>

    <label>
      Teléfono visible
      <input type="text" name="contact_phone" value="<?= e($settings['contact_phone']) ?>" placeholder="+593 99 999 9999" required>
    </label>

    <label>
      Correo
      <input type="email" name="contact_email" value="<?= e($settings['contact_email']) ?>" placeholder="ventas@falextextil.com" required>
    </label>

    <label>
      Dirección
      <input type="text" name="contact_address" value="<?= e($settings['contact_address']) ?>" placeholder="Ciudad, país" required>
    </label>

    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Guardar configuración</button>
    </div>
  </form>
</section>
<?php admin_footer(); ?>
