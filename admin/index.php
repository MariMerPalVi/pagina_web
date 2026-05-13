<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_layout.php';

$stats = [
    'productos' => 0,
    'productos_activos' => 0,
    'categorias' => 0,
    'empleados' => 0,
];

$stats['productos'] = (int) db()->query('SELECT COUNT(*) FROM productos')->fetchColumn();
$stats['productos_activos'] = (int) db()->query('SELECT COUNT(*) FROM productos WHERE estado = "activo"')->fetchColumn();
$stats['categorias'] = (int) db()->query('SELECT COUNT(*) FROM categorias')->fetchColumn();
$stats['empleados'] = (int) db()->query('SELECT COUNT(*) FROM usuarios WHERE rol = "empleado"')->fetchColumn();

$stats['productos_inactivos'] = max(0, $stats['productos'] - $stats['productos_activos']);
$stats['con_imagen'] = (int) db()->query('SELECT COUNT(*) FROM productos WHERE estado = "activo" AND imagen IS NOT NULL AND imagen <> ""')->fetchColumn();
$galleryPercent = $stats['productos_activos'] > 0 ? min(100, round(($stats['con_imagen'] / $stats['productos_activos']) * 100)) : 0;

$latestProducts = db()->query('SELECT p.nombre, p.precio, p.estado, p.imagen, c.nombre AS categoria FROM productos p INNER JOIN categorias c ON c.id = p.categoria_id ORDER BY p.fecha_creacion DESC LIMIT 6')->fetchAll();

admin_header('Dashboard');
?>
<section class="dashboard-summary">
  <div>
    <h2>Resumen del sistema</h2>
    <p>Control rápido del catálogo, productos publicados y galería pública.</p>
  </div>
  <div class="quick-actions">
    <a class="btn btn-primary" href="<?= e(url('admin/product_form.php')) ?>">Crear producto</a>
    <a class="btn btn-secondary" href="<?= e(url('catalogo.php')) ?>" target="_blank" rel="noopener">Catálogo público</a>
  </div>
</section>

<section class="admin-stats">
  <article><span>Total productos</span><strong><?= $stats['productos'] ?></strong><small>Registrados en catálogo</small></article>
  <article><span>Activos</span><strong><?= $stats['productos_activos'] ?></strong><small>Visibles al público</small></article>
  <article><span>Categorías</span><strong><?= $stats['categorias'] ?></strong><small>Filtros disponibles</small></article>
  <article><span>Empleados</span><strong><?= $stats['empleados'] ?></strong><small>Usuarios operativos</small></article>
</section>

<section class="dashboard-grid">
  <article class="admin-panel catalog-health">
    <div class="panel-heading">
      <h2>Estado del catálogo</h2>
    </div>
    <div class="health-row"><span>Activos</span><strong><?= $stats['productos_activos'] ?></strong></div>
    <div class="health-row"><span>Inactivos</span><strong><?= $stats['productos_inactivos'] ?></strong></div>
    <div class="progress-label"><span>Galería con imágenes</span><strong><?= $galleryPercent ?>%</strong></div>
    <div class="progress-track"><span style="width: <?= $galleryPercent ?>%"></span></div>
    <p><?= $stats['con_imagen'] ?> producto<?= $stats['con_imagen'] === 1 ? '' : 's' ?> activo<?= $stats['con_imagen'] === 1 ? '' : 's' ?> con imagen visible en la galería.</p>
  </article>

  <article class="admin-panel">
    <div class="panel-heading">
      <h2>Accesos rápidos</h2>
    </div>
    <div class="shortcut-list">
      <a href="<?= e(url('admin/products.php')) ?>"><strong>Productos</strong><span>Crear, editar, activar o desactivar productos.</span></a>
      <a href="<?= e(url('admin/categories.php')) ?>"><strong>Categorías</strong><span>Organizar filtros del catálogo público.</span></a>
      <?php if (is_admin()): ?>
        <a href="<?= e(url('admin/employees.php')) ?>"><strong>Empleados</strong><span>Gestionar usuarios internos y accesos.</span></a>
      <?php endif; ?>
    </div>
  </article>
</section>

<section class="admin-panel recent-products-panel">
  <div class="panel-heading">
    <h2>Productos recientes</h2>
    <a class="btn btn-secondary" href="<?= e(url('admin/products.php')) ?>">Ver todos</a>
  </div>
  <?php if ($latestProducts): ?>
    <div class="recent-products-list">
      <?php foreach ($latestProducts as $product): ?>
        <article>
          <img src="<?= e(product_image_url($product['imagen'])) ?>" alt="<?= e($product['nombre']) ?>">
          <div>
            <strong><?= e($product['nombre']) ?></strong>
            <span><?= e($product['categoria']) ?> · $<?= number_format((float) $product['precio'], 2) ?></span>
          </div>
          <span class="status <?= e($product['estado']) ?>"><?= e($product['estado']) ?></span>
        </article>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <div>
        <span>Catálogo</span>
        <h3>Aún no hay productos registrados</h3>
        <p>Crea el primer producto para empezar a llenar el catálogo y la galería pública.</p>
      </div>
      <a class="btn btn-primary" href="<?= e(url('admin/product_form.php')) ?>">Crear producto</a>
    </div>
  <?php endif; ?>
</section>
<?php admin_footer(); ?>
