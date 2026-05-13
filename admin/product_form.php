<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_layout.php';

$id = (int) ($_GET['id'] ?? 0);
$product = [
    'nombre' => '',
    'descripcion' => '',
    'precio' => '',
    'categoria_id' => '',
    'imagen' => null,
    'estado' => 'activo',
];

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM productos WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        flash('error', 'Producto no encontrado.');
        redirect('admin/products.php');
    }
}

$categories = db()->query('SELECT id, nombre FROM categorias ORDER BY nombre')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float) ($_POST['precio'] ?? 0);
    $categoriaId = (int) ($_POST['categoria_id'] ?? 0);
    $estado = ($_POST['estado'] ?? 'activo') === 'inactivo' ? 'inactivo' : 'activo';

    try {
        if ($nombre === '' || $descripcion === '' || $categoriaId <= 0) {
            throw new RuntimeException('Completa nombre, descripción y categoría.');
        }

        $image = upload_product_image($_FILES['imagen'] ?? [], $product['imagen'] ?? null);

        if ($id > 0) {
            $stmt = db()->prepare('UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria_id = ?, imagen = ?, estado = ? WHERE id = ?');
            $stmt->execute([$nombre, $descripcion, $precio, $categoriaId, $image, $estado, $id]);
            flash('success', 'Producto actualizado correctamente.');
        } else {
            $stmt = db()->prepare('INSERT INTO productos (nombre, descripcion, precio, categoria_id, imagen, estado, creado_por) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$nombre, $descripcion, $precio, $categoriaId, $image, $estado, current_user()['id']]);
            flash('success', 'Producto creado correctamente.');
        }

        redirect('admin/products.php');
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
        $product = compact('nombre', 'descripcion', 'precio', 'categoriaId', 'estado') + $product;
        $product['categoria_id'] = $categoriaId;
    }
}

admin_header($id > 0 ? 'Editar producto' : 'Crear producto');
?>
<section class="admin-panel narrow-panel">
  <form class="admin-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label>Nombre del producto<input type="text" name="nombre" value="<?= e($product['nombre']) ?>" required></label>
    <label>Descripción<textarea name="descripcion" rows="5" required><?= e($product['descripcion']) ?></textarea></label>
    <div class="form-grid">
      <label>Precio<input type="number" name="precio" min="0" step="0.01" value="<?= e((string) $product['precio']) ?>" required></label>
      <label>Categoría
        <select name="categoria_id" required>
          <option value="">Selecciona una categoría</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= (int) $category['id'] ?>" <?= (int) $product['categoria_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Estado
        <select name="estado">
          <option value="activo" <?= $product['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
          <option value="inactivo" <?= $product['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>
      </label>
      <label>Imagen principal<input type="file" name="imagen" accept="image/jpeg,image/png,image/webp"><small>Los productos activos con imagen aparecerán automáticamente en la galería pública.</small></label>
    </div>
    <?php if (!empty($product['imagen'])): ?>
      <img class="form-preview" src="<?= e(product_image_url($product['imagen'])) ?>" alt="Imagen actual">
    <?php endif; ?>
    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Guardar producto</button>
      <a class="btn btn-secondary" href="<?= e(url('admin/products.php')) ?>">Cancelar</a>
    </div>
  </form>
</section>
<?php admin_footer(); ?>
