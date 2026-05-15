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

    db()->exec(
        'CREATE TABLE IF NOT EXISTS ordenes_trabajo_responsables (
            orden_id INT NOT NULL,
            usuario_id INT NOT NULL,
            fecha_asignacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (orden_id, usuario_id),
            CONSTRAINT fk_ordenes_responsables_orden FOREIGN KEY (orden_id) REFERENCES ordenes_trabajo(id) ON DELETE CASCADE,
            CONSTRAINT fk_ordenes_responsables_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        ) ENGINE=InnoDB'
    );

    try {
        db()->exec('INSERT IGNORE INTO ordenes_trabajo_responsables (orden_id, usuario_id) SELECT id, asignado_a FROM ordenes_trabajo WHERE asignado_a IS NOT NULL');
    } catch (Throwable) {
    }
}

ensure_orders_quotes_table();
ensure_orders_table();

$viewId = (int) ($_GET['view'] ?? 0);
$editId = (int) ($_GET['edit'] ?? 0);
$creating = isset($_GET['create']);
$selected = null;
$selectedImages = [];
$selectedResponsibles = [];
$editing = null;
$editingResponsibles = [];

function sync_order_responsibles(int $orderId, array $responsibles): void
{
    $ids = array_values(array_unique(array_filter(array_map('intval', $responsibles), static fn (int $id): bool => $id > 0)));
    $delete = db()->prepare('DELETE FROM ordenes_trabajo_responsables WHERE orden_id = ?');
    $delete->execute([$orderId]);

    if (!$ids) {
        return;
    }

    $insert = db()->prepare('INSERT INTO ordenes_trabajo_responsables (orden_id, usuario_id) VALUES (?, ?)');
    foreach ($ids as $userId) {
        $insert->execute([$orderId, $userId]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['action'] ?? 'update_status';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'delete' && $id > 0) {
        require_admin();
        $stmt = db()->prepare('DELETE FROM ordenes_trabajo WHERE id = ?');
        $stmt->execute([$id]);
        flash('success', 'Orden eliminada correctamente.');
        redirect('admin/orders.php');
    }

    if ($action === 'save') {
        require_admin();
        $producto = trim($_POST['producto'] ?? '');
        $clienteNombre = trim($_POST['cliente_nombre'] ?? '');
        $clienteTelefono = trim($_POST['cliente_telefono'] ?? '');
        $clienteCorreo = trim($_POST['cliente_correo'] ?? '');
        $cantidad = trim($_POST['cantidad'] ?? '');
        $tallas = trim($_POST['tallas'] ?? '');
        $detalles = trim($_POST['detalles'] ?? '');
        $fechaEntrega = trim($_POST['fecha_entrega'] ?? '');
        $estado = $_POST['estado'] ?? 'pendiente';
        $responsables = $_POST['responsables'] ?? [];

        if (!in_array($estado, ['pendiente', 'en_proceso', 'finalizada'], true)) {
            $estado = 'pendiente';
        }

        if ($producto === '' || $clienteNombre === '' || $clienteTelefono === '' || $detalles === '') {
            flash('error', 'Completa cliente, teléfono, producto y detalles.');
            redirect('admin/orders.php' . ($id > 0 ? '?edit=' . $id : '?create=1'));
        }

        if ($id > 0) {
            $stmt = db()->prepare(
                'UPDATE ordenes_trabajo
                 SET cliente_nombre = ?, cliente_telefono = ?, cliente_correo = ?, producto = ?, cantidad = ?, tallas = ?, detalles = ?, fecha_entrega = ?, estado = ?
                 WHERE id = ?'
            );
            $stmt->execute([$clienteNombre, $clienteTelefono, $clienteCorreo ?: null, $producto, $cantidad ?: null, $tallas ?: null, $detalles, $fechaEntrega ?: null, $estado, $id]);
            $orderId = $id;
        } else {
            $stmt = db()->prepare(
                'INSERT INTO ordenes_trabajo (cliente_nombre, cliente_telefono, cliente_correo, producto, cantidad, tallas, detalles, fecha_entrega, estado, creado_por)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$clienteNombre, $clienteTelefono, $clienteCorreo ?: null, $producto, $cantidad ?: null, $tallas ?: null, $detalles, $fechaEntrega ?: null, $estado, current_user()['id']]);
            $orderId = (int) db()->lastInsertId();
        }

        sync_order_responsibles($orderId, is_array($responsables) ? $responsables : []);

        $images = upload_order_images($_FILES['imagenes'] ?? []);
        if ($images) {
            $imageStmt = db()->prepare('INSERT INTO ordenes_trabajo_imagenes (orden_id, imagen) VALUES (?, ?)');
            foreach ($images as $image) {
                $imageStmt->execute([$orderId, $image]);
            }
        }

        flash('success', 'Orden guardada correctamente.');
        redirect('admin/orders.php?view=' . $orderId);
    }

    if ($action === 'update_status' && $id > 0) {
        $estado = $_POST['estado'] ?? 'pendiente';
        if (!in_array($estado, ['pendiente', 'en_proceso', 'finalizada'], true)) {
            $estado = 'pendiente';
        }
        $stmt = db()->prepare('UPDATE ordenes_trabajo SET estado = ? WHERE id = ?');
        $stmt->execute([$estado, $id]);
        flash('success', 'Estado de la orden actualizado.');
        redirect('admin/orders.php?view=' . $id);
    }
}

if (is_admin() && $editId > 0) {
    $stmt = db()->prepare('SELECT * FROM ordenes_trabajo WHERE id = ? LIMIT 1');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch();

    if ($editing) {
        $stmt = db()->prepare('SELECT usuario_id FROM ordenes_trabajo_responsables WHERE orden_id = ?');
        $stmt->execute([$editId]);
        $editingResponsibles = array_map('intval', array_column($stmt->fetchAll(), 'usuario_id'));
    }
}

if ($viewId > 0) {
    $stmt = db()->prepare(
        'SELECT o.*, c.nombre AS creador
         FROM ordenes_trabajo o
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

        $stmt = db()->prepare(
            'SELECT u.nombre
             FROM ordenes_trabajo_responsables r
             INNER JOIN usuarios u ON u.id = r.usuario_id
             WHERE r.orden_id = ?
             ORDER BY u.nombre'
        );
        $stmt->execute([$viewId]);
        $selectedResponsibles = array_column($stmt->fetchAll(), 'nombre');
    }
}

$orders = db()->query(
    'SELECT o.*, GROUP_CONCAT(u.nombre ORDER BY u.nombre SEPARATOR ", ") AS responsables
     FROM ordenes_trabajo o
     LEFT JOIN ordenes_trabajo_responsables r ON r.orden_id = o.id
     LEFT JOIN usuarios u ON u.id = r.usuario_id
     GROUP BY o.id
     ORDER BY o.fecha_creacion DESC'
)->fetchAll();
$employees = db()->query('SELECT id, nombre FROM usuarios WHERE rol = "empleado" AND estado = "activo" ORDER BY nombre')->fetchAll();
$showForm = is_admin() && ($creating || $editing);

admin_header('Órdenes');
?>
<section class="admin-panel">
  <div class="panel-heading">
    <div>
      <h2>Órdenes de trabajo</h2>
      <p class="muted-text">Pedidos generados desde cotizaciones respondidas para seguimiento interno del equipo.</p>
    </div>
    <?php if (is_admin()): ?>
      <a class="btn btn-primary" href="<?= e(url('admin/orders.php?create=1')) ?>">Crear orden</a>
    <?php endif; ?>
  </div>

  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Orden</th><th>Cliente</th><th>Producto</th><th>Entrega</th><th>Responsables</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td data-label="Orden"><strong>#<?= (int) $order['id'] ?></strong><small><?= e(date('d/m/Y', strtotime($order['fecha_creacion']))) ?></small></td>
            <td data-label="Cliente"><strong><?= e($order['cliente_nombre']) ?></strong><small><?= e($order['cliente_telefono']) ?></small></td>
            <td data-label="Producto"><?= e($order['producto']) ?><?= $order['cantidad'] ? '<small>' . e($order['cantidad']) . '</small>' : '' ?></td>
            <td data-label="Entrega"><?= $order['fecha_entrega'] ? e(date('d/m/Y', strtotime($order['fecha_entrega']))) : '<span class="muted-text">Sin fecha</span>' ?></td>
            <td data-label="Responsables"><?= e($order['responsables'] ?: 'Sin asignar') ?></td>
            <td data-label="Estado"><span class="status <?= e($order['estado']) ?>"><?= e(str_replace('_', ' ', $order['estado'])) ?></span></td>
            <td class="actions" data-label="Acciones">
              <a class="btn btn-small" href="<?= e(url('admin/orders.php?view=' . (int) $order['id'])) ?>">Ver detalle</a>
              <?php if (is_admin()): ?>
                <a class="btn btn-small" href="<?= e(url('admin/orders.php?edit=' . (int) $order['id'])) ?>">Editar</a>
                <form method="post" onsubmit="return confirm('¿Eliminar esta orden?')">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                  <input type="hidden" name="action" value="delete">
                  <button class="btn btn-danger btn-small" type="submit">Eliminar</button>
                </form>
              <?php endif; ?>
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
        <div class="modal-tools">
          <button class="btn btn-small btn-secondary" type="button" onclick="window.print()">Imprimir</button>
          <a class="modal-close" href="<?= e(url('admin/orders.php')) ?>" aria-label="Cerrar">&times;</a>
        </div>
      </div>

      <div class="quote-detail">
        <p><strong>Cliente:</strong> <?= e($selected['cliente_nombre']) ?></p>
        <p><strong>Teléfono:</strong> <a href="tel:<?= e(preg_replace('/\s+/', '', $selected['cliente_telefono'])) ?>"><?= e($selected['cliente_telefono']) ?></a></p>
        <p><strong>Correo:</strong> <?= $selected['cliente_correo'] ? '<a href="mailto:' . e($selected['cliente_correo']) . '">' . e($selected['cliente_correo']) . '</a>' : 'No registrado' ?></p>
        <p><strong>Cantidad:</strong> <?= e($selected['cantidad'] ?: 'No indicada') ?></p>
        <p><strong>Tallas:</strong> <?= e($selected['tallas'] ?: 'No indicadas') ?></p>
        <p><strong>Fecha de entrega:</strong> <?= $selected['fecha_entrega'] ? e(date('d/m/Y', strtotime($selected['fecha_entrega']))) : 'Sin fecha' ?></p>
        <p><strong>Responsables:</strong> <?= e($selectedResponsibles ? implode(', ', $selectedResponsibles) : 'Sin asignar') ?></p>
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
<?php if ($showForm): ?>
  <div class="admin-modal" role="dialog" aria-modal="true" aria-labelledby="order-form-title">
    <a class="admin-modal-backdrop" href="<?= e(url('admin/orders.php')) ?>" aria-label="Cerrar"></a>
    <article class="admin-modal-card admin-modal-wide">
      <div class="modal-heading">
        <div>
          <p class="eyebrow">Órdenes</p>
          <h2 id="order-form-title"><?= $editing ? 'Editar orden' : 'Crear orden' ?></h2>
        </div>
        <a class="modal-close" href="<?= e(url('admin/orders.php')) ?>" aria-label="Cerrar">&times;</a>
      </div>
      <form class="admin-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
        <input type="hidden" name="action" value="save">
        <div class="form-grid">
          <label>Cliente<input type="text" name="cliente_nombre" value="<?= e($editing['cliente_nombre'] ?? '') ?>" required></label>
          <label>Teléfono<input type="text" name="cliente_telefono" value="<?= e($editing['cliente_telefono'] ?? '') ?>" required></label>
          <label>Correo<input type="email" name="cliente_correo" value="<?= e($editing['cliente_correo'] ?? '') ?>"></label>
          <label>Producto<input type="text" name="producto" value="<?= e($editing['producto'] ?? '') ?>" required></label>
          <label>Cantidad<input type="text" name="cantidad" value="<?= e($editing['cantidad'] ?? '') ?>"></label>
          <label>Tallas<input type="text" name="tallas" value="<?= e($editing['tallas'] ?? '') ?>"></label>
          <label>Fecha de entrega<input type="date" name="fecha_entrega" value="<?= e($editing['fecha_entrega'] ?? '') ?>"></label>
          <label>Estado
            <select name="estado" required>
              <?php $currentStatus = $editing['estado'] ?? 'pendiente'; ?>
              <option value="pendiente" <?= $currentStatus === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
              <option value="en_proceso" <?= $currentStatus === 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
              <option value="finalizada" <?= $currentStatus === 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
            </select>
          </label>
        </div>
        <label>Responsables
          <select name="responsables[]" multiple size="<?= max(3, min(6, count($employees))) ?>">
            <?php foreach ($employees as $employee): ?>
              <option value="<?= (int) $employee['id'] ?>" <?= in_array((int) $employee['id'], $editingResponsibles, true) ? 'selected' : '' ?>><?= e($employee['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
          <small>Mantén presionada la tecla Ctrl para seleccionar varios responsables.</small>
        </label>
        <label>Detalles de producción<textarea name="detalles" rows="6" required><?= e($editing['detalles'] ?? '') ?></textarea></label>
        <label>Imágenes de referencia
          <input type="file" name="imagenes[]" accept="image/jpeg,image/png,image/webp" multiple>
          <small>Al editar puedes agregar nuevas imágenes sin borrar las existentes.</small>
        </label>
        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Guardar orden</button>
          <a class="btn btn-secondary" href="<?= e(url('admin/orders.php')) ?>">Cancelar</a>
        </div>
      </form>
    </article>
  </div>
<?php endif; ?>
<?php admin_footer(); ?>
