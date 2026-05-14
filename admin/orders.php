<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_layout.php';

function ensure_orders_quotes_table(): void
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

function ensure_orders_table(): void
{
    db()->exec(
        'CREATE TABLE IF NOT EXISTS ordenes_trabajo (
            id INT AUTO_INCREMENT PRIMARY KEY,
            solicitud_id INT NULL,
            cliente_nombre VARCHAR(160) NOT NULL,
            cliente_telefono VARCHAR(60) NOT NULL,
            cliente_correo VARCHAR(160) NULL,
            producto VARCHAR(160) NOT NULL,
            cantidad VARCHAR(80) NULL,
            tallas VARCHAR(255) NULL,
            detalles TEXT NOT NULL,
            fecha_entrega DATE NULL,
            asignado_a INT NULL,
            estado ENUM("pendiente", "en_proceso", "finalizada") NOT NULL DEFAULT "pendiente",
            creado_por INT NULL,
            fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_ordenes_solicitud FOREIGN KEY (solicitud_id) REFERENCES solicitudes_cotizacion(id) ON DELETE SET NULL,
            CONSTRAINT fk_ordenes_asignado FOREIGN KEY (asignado_a) REFERENCES usuarios(id) ON DELETE SET NULL,
            CONSTRAINT fk_ordenes_creador FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL
        ) ENGINE=InnoDB'
    );

    db()->exec(
        'CREATE TABLE IF NOT EXISTS ordenes_trabajo_imagenes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            orden_id INT NOT NULL,
            imagen VARCHAR(255) NOT NULL,
            fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_ordenes_imagenes_orden FOREIGN KEY (orden_id) REFERENCES ordenes_trabajo(id) ON DELETE CASCADE
        ) ENGINE=InnoDB'
    );
}

ensure_orders_quotes_table();
ensure_orders_table();

$viewId = (int) ($_GET['view'] ?? 0);
$selected = null;
$selectedImages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $id = (int) ($_POST['id'] ?? 0);
    $estado = $_POST['estado'] ?? 'pendiente';

    if ($id > 0) {
        if (!in_array($estado, ['pendiente', 'en_proceso', 'finalizada'], true)) {
            $estado = 'pendiente';
        }

        $stmt = db()->prepare('UPDATE ordenes_trabajo SET estado = ? WHERE id = ?');
        $stmt->execute([$estado, $id]);
        flash('success', 'Estado de la orden actualizado.');
        redirect('admin/orders.php?view=' . $id);
    }
}

if ($viewId > 0) {
    $stmt = db()->prepare(
        'SELECT o.*, a.nombre AS empleado, c.nombre AS creador
         FROM ordenes_trabajo o
         LEFT JOIN usuarios a ON a.id = o.asignado_a
         LEFT JOIN usuarios c ON c.id = o.creado_por
         WHERE o.id = ?
         LIMIT 1'
    );
    $stmt->execute([$viewId]);
    $selected = $stmt->fetch();

    if ($selected) {
        $stmt = db()->prepare('SELECT imagen FROM ordenes_trabajo_imagenes WHERE orden_id = ? ORDER BY id');
        $stmt->execute([$viewId]);
        $selectedImages = $stmt->fetchAll();
    }
}

$orders = db()->query(
    'SELECT o.*, u.nombre AS empleado
     FROM ordenes_trabajo o
     LEFT JOIN usuarios u ON u.id = o.asignado_a
     ORDER BY o.fecha_creacion DESC'
)->fetchAll();

admin_header('Órdenes');
?>
<section class="admin-panel">
  <div class="panel-heading">
    <div>
      <h2>Órdenes de trabajo</h2>
      <p class="muted-text">Pedidos generados desde cotizaciones respondidas para seguimiento interno del equipo.</p>
    </div>
  </div>

  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Orden</th><th>Cliente</th><th>Producto</th><th>Entrega</th><th>Empleado</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td data-label="Orden"><strong>#<?= (int) $order['id'] ?></strong><small><?= e(date('d/m/Y', strtotime($order['fecha_creacion']))) ?></small></td>
            <td data-label="Cliente"><strong><?= e($order['cliente_nombre']) ?></strong><small><?= e($order['cliente_telefono']) ?></small></td>
            <td data-label="Producto"><?= e($order['producto']) ?><?= $order['cantidad'] ? '<small>' . e($order['cantidad']) . '</small>' : '' ?></td>
            <td data-label="Entrega"><?= $order['fecha_entrega'] ? e(date('d/m/Y', strtotime($order['fecha_entrega']))) : '<span class="muted-text">Sin fecha</span>' ?></td>
            <td data-label="Empleado"><?= e($order['empleado'] ?? 'Sin asignar') ?></td>
            <td data-label="Estado"><span class="status <?= e($order['estado']) ?>"><?= e(str_replace('_', ' ', $order['estado'])) ?></span></td>
            <td class="actions" data-label="Acciones">
              <a class="btn btn-small" href="<?= e(url('admin/orders.php?view=' . (int) $order['id'])) ?>">Ver detalle</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$orders): ?>
          <tr><td colspan="7">Aún no hay órdenes de trabajo.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php if ($selected): ?>
  <div class="admin-modal" role="dialog" aria-modal="true" aria-labelledby="order-modal-title">
    <a class="admin-modal-backdrop" href="<?= e(url('admin/orders.php')) ?>" aria-label="Cerrar"></a>
    <article class="admin-modal-card admin-modal-wide">
      <div class="modal-heading">
        <div>
          <p class="eyebrow">Orden #<?= (int) $selected['id'] ?></p>
          <h2 id="order-modal-title"><?= e($selected['producto']) ?></h2>
        </div>
        <a class="modal-close" href="<?= e(url('admin/orders.php')) ?>" aria-label="Cerrar">&times;</a>
      </div>

      <div class="quote-detail">
        <p><strong>Cliente:</strong> <?= e($selected['cliente_nombre']) ?></p>
        <p><strong>Teléfono:</strong> <a href="tel:<?= e(preg_replace('/\s+/', '', $selected['cliente_telefono'])) ?>"><?= e($selected['cliente_telefono']) ?></a></p>
        <p><strong>Correo:</strong> <?= $selected['cliente_correo'] ? '<a href="mailto:' . e($selected['cliente_correo']) . '">' . e($selected['cliente_correo']) . '</a>' : 'No registrado' ?></p>
        <p><strong>Cantidad:</strong> <?= e($selected['cantidad'] ?: 'No indicada') ?></p>
        <p><strong>Tallas:</strong> <?= e($selected['tallas'] ?: 'No indicadas') ?></p>
        <p><strong>Fecha de entrega:</strong> <?= $selected['fecha_entrega'] ? e(date('d/m/Y', strtotime($selected['fecha_entrega']))) : 'Sin fecha' ?></p>
        <p><strong>Asignado a:</strong> <?= e($selected['empleado'] ?? 'Sin asignar') ?></p>
        <p><strong>Creada por:</strong> <?= e($selected['creador'] ?? 'No registrado') ?></p>
        <p><strong>Detalles de producción:</strong></p>
        <div class="quote-message"><?= nl2br(e($selected['detalles'])) ?></div>
      </div>

      <?php if ($selectedImages): ?>
        <div class="order-images">
          <?php foreach ($selectedImages as $image): ?>
            <a href="<?= e(order_image_url($image['imagen'])) ?>" target="_blank" rel="noopener">
              <img src="<?= e(order_image_url($image['imagen'])) ?>" alt="Imagen de referencia de la orden">
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="admin-form" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int) $selected['id'] ?>">
        <label>Estado de la orden
          <select name="estado" required>
            <option value="pendiente" <?= $selected['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
            <option value="en_proceso" <?= $selected['estado'] === 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
            <option value="finalizada" <?= $selected['estado'] === 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
          </select>
        </label>
        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Guardar estado</button>
          <a class="btn btn-secondary" href="<?= e(url('admin/orders.php')) ?>">Cerrar</a>
        </div>
      </form>
    </article>
  </div>
<?php endif; ?>
<?php admin_footer(); ?>
