<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'delete' && $id > 0) {
        require_admin();
        $stmt = db()->prepare('DELETE FROM productos WHERE id = ?');
        $stmt->execute([$id]);
        flash('success', 'Producto eliminado correctamente.');
        redirect('admin/products.php');
    }

    if ($action === 'toggle' && $id > 0) {
        $stmt = db()->prepare('UPDATE productos SET estado = IF(estado = "activo", "inactivo", "activo") WHERE id = ?');
        $stmt->execute([$id]);
        flash('success', 'Estado del producto actualizado.');
        redirect('admin/products.php');
    }
}

$products = db()->query('SELECT p.*, c.nombre AS categoria, u.nombre AS creador FROM productos p INNER JOIN categorias c ON c.id = p.categoria_id LEFT JOIN usuarios u ON u.id = p.creado_por ORDER BY p.fecha_creacion DESC')->fetchAll();
$categories = db()->query('SELECT id, nombre FROM categorias WHERE estado = "activo" ORDER BY nombre')->fetchAll();
$showCreateModal = isset($_GET['create']);

admin_header('Productos');
?>
<section class="admin-panel">
  <div class="panel-heading">
    <div>
      <h2>Catálogo administrable</h2>
      <p class="muted-text">Crea, edita y controla los productos visibles en el sitio público.</p>
    </div>
    <a class="btn btn-primary" href="<?= e(url('admin/products.php?create=1')) ?>">Crear producto</a>
  </div>
  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th>Imagen</th><th>Producto</th><th>Categoría</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach ($products as $product): ?>
          <tr>
            <td data-label="Imagen"><img class="table-image" src="<?= e(product_image_url($product['imagen'])) ?>" alt="<?= e($product['nombre']) ?>"></td>
            <td data-label="Producto"><strong><?= e($product['nombre']) ?></strong><small><?= e($product['creador'] ?? 'Sin creador') ?></small></td>
            <td data-label="Categoría"><?= e($product['categoria']) ?></td>
            <td data-label="Precio">$<?= number_format((float) $product['precio'], 2) ?></td>
            <td data-label="Estado"><span class="status <?= e($product['estado']) ?>"><?= e($product['estado']) ?></span></td>
            <td class="actions" data-label="Acciones">
              <a class="btn btn-small" href="<?= e(url('admin/product_form.php?id=' . (int) $product['id'])) ?>">Editar</a>
              <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                <input type="hidden" name="action" value="toggle">
                <button class="btn btn-small" type="submit"><?= $product['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?></button>
              </form>
              <?php if (is_admin()): ?>
                <form method="post" onsubmit="return confirm('¿Eliminar este producto?')">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                  <input type="hidden" name="action" value="delete">
                  <button class="btn btn-danger btn-small" type="submit">Eliminar</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$products): ?>
          <tr><td colspan="6">No hay productos registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php if ($showCreateModal): ?>
  <div class="admin-modal" role="dialog" aria-modal="true" aria-labelledby="product-modal-title">
    <a class="admin-modal-backdrop" href="<?= e(url('admin/products.php')) ?>" aria-label="Cerrar"></a>
    <article class="admin-modal-card admin-modal-wide">
      <div class="modal-heading">
        <div>
          <p class="eyebrow">Productos</p>
          <h2 id="product-modal-title">Crear producto</h2>
        </div>
        <a class="modal-close" href="<?= e(url('admin/products.php')) ?>" aria-label="Cerrar">&times;</a>
      </div>
      <form class="admin-form" method="post" action="<?= e(url('admin/product_form.php')) ?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>Nombre del producto<input type="text" name="nombre" required autofocus></label>
        <label>Descripción<textarea name="descripcion" rows="4" required></textarea></label>
        <div class="form-grid">
          <label>Precio<input type="number" name="precio" min="0" step="0.01" value="0.00" required></label>
          <label>Categoría
            <select name="categoria_id" required>
              <option value="">Selecciona una categoría</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>"><?= e($category['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Estado
            <select name="estado">
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
            </select>
          </label>
          <label>Imagen principal<input type="file" name="imagen" accept="image/jpeg,image/png,image/webp"><small>Los productos activos con imagen aparecerán en la galería pública.</small></label>
        </div>
        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Guardar producto</button>
          <a class="btn btn-secondary" href="<?= e(url('admin/products.php')) ?>">Cancelar</a>
        </div>
      </form>
    </article>
  </div>
<?php endif; ?>
<?php admin_footer(); ?>
