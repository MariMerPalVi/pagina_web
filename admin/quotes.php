<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_layout.php';
require_admin();

function ensure_quotes_table(): void
{
    db()->exec(
        'CREATE TABLE IF NOT EXISTS solicitudes_cotizacion (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(160) NOT NULL,
            telefono VARCHAR(60) NOT NULL,
            correo VARCHAR(160) NULL,
            producto VARCHAR(160) NOT NULL,
            mensaje TEXT NOT NULL,
            respuesta TEXT NULL,
            estado ENUM("pendiente", "respondida", "no_respondida") NOT NULL DEFAULT "pendiente",
            respondido_por INT NULL,
            fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            fecha_respuesta TIMESTAMP NULL DEFAULT NULL,
            CONSTRAINT fk_solicitudes_usuario FOREIGN KEY (respondido_por) REFERENCES usuarios(id) ON DELETE SET NULL
        ) ENGINE=InnoDB'
    );
}

ensure_quotes_table();

try {
    db()->exec('UPDATE solicitudes_cotizacion SET estado = "no_respondida" WHERE estado = "cerrada"');
    db()->exec('ALTER TABLE solicitudes_cotizacion MODIFY estado ENUM("pendiente", "respondida", "no_respondida") NOT NULL DEFAULT "pendiente"');
} catch (Throwable) {
}

$viewId = (int) ($_GET['view'] ?? 0);
$selected = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'update_status' && $id > 0) {
        $estado = $_POST['estado'] ?? 'pendiente';
        $nota = trim($_POST['respuesta'] ?? '');

        if (!in_array($estado, ['pendiente', 'respondida', 'no_respondida'], true)) {
            $estado = 'pendiente';
        }

        $stmt = db()->prepare('UPDATE solicitudes_cotizacion SET respuesta = ?, estado = ?, respondido_por = ?, fecha_respuesta = NOW() WHERE id = ?');
        $stmt->execute([$nota ?: null, $estado, current_user()['id'], $id]);

        flash('success', 'Estado de la solicitud actualizado.');
        redirect('admin/quotes.php?view=' . $id);
    }
}

if ($viewId > 0) {
    $stmt = db()->prepare('SELECT s.*, u.nombre AS respondido_por_nombre FROM solicitudes_cotizacion s LEFT JOIN usuarios u ON u.id = s.respondido_por WHERE s.id = ? LIMIT 1');
    $stmt->execute([$viewId]);
    $selected = $stmt->fetch();
}

$quotes = db()->query('SELECT * FROM solicitudes_cotizacion ORDER BY fecha_creacion DESC')->fetchAll();

admin_header('Cotizaciones');
?>
<section class="admin-panel">
  <div class="panel-heading">
    <div>
      <h2>Solicitudes recibidas</h2>
      <p class="muted-text">Controla los mensajes enviados desde el formulario público y registra el estado de atención.</p>
    </div>
  </div>

  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Cliente</th><th>Producto</th><th>Fecha</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach ($quotes as $quote): ?>
          <tr>
            <td data-label="Cliente"><strong><?= e($quote['nombre']) ?></strong><small><?= e($quote['correo'] ?: $quote['telefono']) ?></small></td>
            <td data-label="Producto"><?= e($quote['producto']) ?></td>
            <td data-label="Fecha"><?= e(date('d/m/Y H:i', strtotime($quote['fecha_creacion']))) ?></td>
            <td data-label="Estado"><span class="status <?= e($quote['estado']) ?>"><?= e(str_replace('_', ' ', $quote['estado'])) ?></span></td>
            <td class="actions" data-label="Acciones">
              <a class="btn btn-small" href="<?= e(url('admin/quotes.php?view=' . (int) $quote['id'])) ?>">Ver / gestionar</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$quotes): ?>
          <tr><td colspan="5">Aún no hay solicitudes registradas.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php if ($selected): ?>
  <div class="admin-modal" role="dialog" aria-modal="true" aria-labelledby="quote-modal-title">
    <a class="admin-modal-backdrop" href="<?= e(url('admin/quotes.php')) ?>" aria-label="Cerrar"></a>
    <article class="admin-modal-card admin-modal-wide">
      <div class="modal-heading">
        <div>
          <p class="eyebrow">Solicitud #<?= (int) $selected['id'] ?></p>
          <h2 id="quote-modal-title"><?= e($selected['nombre']) ?></h2>
        </div>
        <a class="modal-close" href="<?= e(url('admin/quotes.php')) ?>" aria-label="Cerrar">&times;</a>
      </div>

      <div class="quote-detail">
        <p><strong>Teléfono:</strong> <a href="tel:<?= e(preg_replace('/\s+/', '', $selected['telefono'])) ?>"><?= e($selected['telefono']) ?></a></p>
        <p><strong>Correo:</strong> <?= $selected['correo'] ? '<a href="mailto:' . e($selected['correo']) . '">' . e($selected['correo']) . '</a>' : 'No registrado' ?></p>
        <p><strong>Producto:</strong> <?= e($selected['producto']) ?></p>
        <p><strong>Fecha:</strong> <?= e(date('d/m/Y H:i', strtotime($selected['fecha_creacion']))) ?></p>
        <p><strong>Mensaje:</strong></p>
        <div class="quote-message"><?= nl2br(e($selected['mensaje'])) ?></div>
      </div>

      <?php if (!empty($selected['respuesta'])): ?>
        <div class="quote-response">
          <strong>Nota interna</strong>
          <p><?= nl2br(e($selected['respuesta'])) ?></p>
          <?php if (!empty($selected['fecha_respuesta'])): ?>
            <small><?= e(date('d/m/Y H:i', strtotime($selected['fecha_respuesta']))) ?><?= $selected['respondido_por_nombre'] ? ' · ' . e($selected['respondido_por_nombre']) : '' ?></small>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <form class="admin-form" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int) $selected['id'] ?>">
        <input type="hidden" name="action" value="update_status">
        <label>Estado de atención
          <select name="estado" required>
            <option value="pendiente" <?= $selected['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
            <option value="respondida" <?= $selected['estado'] === 'respondida' ? 'selected' : '' ?>>Respondida</option>
            <option value="no_respondida" <?= $selected['estado'] === 'no_respondida' ? 'selected' : '' ?>>No respondida</option>
          </select>
        </label>
        <label>Nota interna opcional
          <textarea name="respuesta" rows="5" placeholder="Ejemplo: Se contactó por WhatsApp, falta confirmar tallas o cliente no respondió."><?= e($selected['respuesta'] ?? '') ?></textarea>
        </label>
        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Guardar estado</button>
          <a class="btn btn-secondary" href="<?= e(url('admin/quotes.php')) ?>">Cerrar</a>
        </div>
      </form>
    </article>
  </div>
<?php endif; ?>
<?php admin_footer(); ?>
