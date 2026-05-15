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
    'social_instagram' => 'Instagram',
    'social_facebook' => 'Facebook',
    'social_tiktok' => 'TikTok',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'site_settings';

    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $user = current_user();

        try {
            if (strlen($newPassword) < 8) {
                throw new RuntimeException('La nueva contraseña debe tener al menos 8 caracteres.');
            }

            if ($newPassword !== $confirmPassword) {
                throw new RuntimeException('La confirmación no coincide con la nueva contraseña.');
            }

            $stmt = db()->prepare('SELECT password_hash FROM usuarios WHERE id = ? LIMIT 1');
            $stmt->execute([$user['id']]);
            $row = $stmt->fetch();

            if (!$row || !password_verify($currentPassword, $row['password_hash'])) {
                throw new RuntimeException('La contraseña actual no es correcta.');
            }

            $stmt = db()->prepare('UPDATE usuarios SET password_hash = ? WHERE id = ?');
            $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $user['id']]);
            flash('success', 'Contraseña actualizada correctamente.');
        } catch (Throwable $exception) {
            flash('error', $exception->getMessage());
        }

        redirect('admin/settings.php');
    }

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
    <input type="hidden" name="action" value="site_settings">

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

    <div class="panel-subsection">
      <h3>Redes sociales</h3>
      <p class="muted-text">Agrega solo las redes que quieras mostrar en la página pública. Si dejas un campo vacío, no aparecerá.</p>
    </div>

    <label>
      Instagram
      <input type="url" name="social_instagram" value="<?= e($settings['social_instagram']) ?>" placeholder="https://instagram.com/falex">
    </label>

    <label>
      Facebook
      <input type="url" name="social_facebook" value="<?= e($settings['social_facebook']) ?>" placeholder="https://facebook.com/falex">
    </label>

    <label>
      TikTok
      <input type="url" name="social_tiktok" value="<?= e($settings['social_tiktok']) ?>" placeholder="https://tiktok.com/@falex">
    </label>

    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Guardar configuración</button>
    </div>
  </form>
</section>

<section class="admin-panel narrow-panel">
  <div class="panel-heading">
    <div>
      <h2>Cambiar contraseña</h2>
      <p class="muted-text">Actualiza tu clave de acceso al panel interno cuando lo necesites.</p>
    </div>
  </div>

  <form class="admin-form" method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="change_password">
    <div class="form-grid">
      <label>Contraseña actual
        <input type="password" name="current_password" required autocomplete="current-password">
      </label>
      <label>Nueva contraseña
        <input type="password" name="new_password" minlength="8" required autocomplete="new-password">
      </label>
      <label>Confirmar nueva contraseña
        <input type="password" name="confirm_password" minlength="8" required autocomplete="new-password">
      </label>
    </div>
    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Actualizar contraseña</button>
    </div>
  </form>
</section>
<?php admin_footer(); ?>
