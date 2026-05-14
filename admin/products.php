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

admin_header('Productos');
?>
<section class="admin-panel">
  <div class="panel-heading">
    <h2>Catálogo administrable</h2>
    <a class="btn btn-primary" href="<?= e(url('admin/product_form.php')) ?>">Crear producto</a>
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
<?php admin_footer(); ?>
