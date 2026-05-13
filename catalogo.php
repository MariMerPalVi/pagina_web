<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/public_layout.php';
require_once __DIR__ . '/includes/public_data.php';

$selectedCategory = isset($_GET['categoria']) ? (int) $_GET['categoria'] : 0;
$order = $_GET['orden'] ?? 'recientes';
$categories = public_categories();
$products = public_products($selectedCategory);
$selectedCategoryName = 'Productos';

foreach ($categories as $category) {
    if ((int) $category['id'] === $selectedCategory) {
        $selectedCategoryName = $category['nombre'];
        break;
    }
}

usort($products, static function (array $a, array $b) use ($order): int {
    return match ($order) {
        'precio_asc' => ((float) $a['precio']) <=> ((float) $b['precio']),
        'precio_desc' => ((float) $b['precio']) <=> ((float) $a['precio']),
        'nombre' => strcasecmp($a['nombre'], $b['nombre']),
        default => strcmp((string) $b['fecha_creacion'], (string) $a['fecha_creacion']),
    };
});

public_header('Catálogo', 'catalogo');
?>
<main>
  <section class="shop-hero">
    <div class="breadcrumbs">
      <a href="<?= e(url('index.php')) ?>">Inicio</a>
      <span>/</span>
      <strong><?= e($selectedCategoryName) ?></strong>
    </div>
    <h1><?= e($selectedCategoryName) ?></h1>
    <p>Catálogo de productos textiles FALEX. Consulta prendas, categorías, precios referenciales y solicita información por WhatsApp.</p>
  </section>

  <section class="shop-layout">
    <aside class="shop-sidebar">
      <h2>Categorías</h2>
      <nav class="shop-categories" aria-label="Categorías del catálogo">
        <a class="<?= $selectedCategory === 0 ? 'active' : '' ?>" href="<?= e(url('catalogo.php')) ?>">Todos los productos</a>
        <?php foreach ($categories as $category): ?>
          <a class="<?= $selectedCategory === (int) $category['id'] ? 'active' : '' ?>" href="<?= e(url('catalogo.php?categoria=' . (int) $category['id'])) ?>"><?= e($category['nombre']) ?></a>
        <?php endforeach; ?>
      </nav>
      <div class="sidebar-cta">
        <strong>¿Buscas algo a medida?</strong>
        <p>Envíanos el diseño, cantidades y tallas para cotizar.</p>
        <a class="btn btn-primary" href="<?= e(whatsapp_link('Hola FALEX, quiero cotizar un producto personalizado.')) ?>" target="_blank" rel="noopener">Cotizar</a>
      </div>
    </aside>

    <section class="shop-products">
      <div class="shop-toolbar">
        <p>Mostrando <strong><?= count($products) ?></strong> resultado<?= count($products) === 1 ? '' : 's' ?></p>
        <form method="get">
          <?php if ($selectedCategory > 0): ?>
            <input type="hidden" name="categoria" value="<?= $selectedCategory ?>">
          <?php endif; ?>
          <select name="orden" onchange="this.form.submit()" aria-label="Ordenar productos">
            <option value="recientes" <?= $order === 'recientes' ? 'selected' : '' ?>>Ordenar por los últimos</option>
            <option value="nombre" <?= $order === 'nombre' ? 'selected' : '' ?>>Ordenar por nombre</option>
            <option value="precio_asc" <?= $order === 'precio_asc' ? 'selected' : '' ?>>Precio: bajo a alto</option>
            <option value="precio_desc" <?= $order === 'precio_desc' ? 'selected' : '' ?>>Precio: alto a bajo</option>
          </select>
        </form>
      </div>

      <?php if ($products): ?>
        <div class="product-grid shop-product-grid">
          <?php foreach ($products as $product): ?>
            <article class="product-card shop-card">
              <a class="product-image-link" href="<?= e(whatsapp_link('Hola, estoy interesado/a en el producto ' . $product['nombre'] . ' de FALEX. ¿Me pueden brindar más información?')) ?>" target="_blank" rel="noopener">
                <img src="<?= e(product_image_url($product['imagen'])) ?>" alt="<?= e($product['nombre']) ?>" loading="lazy">
              </a>
              <div class="product-body">
                <span class="product-category"><?= e($product['categoria']) ?></span>
                <h3><?= e($product['nombre']) ?></h3>
                <p><?= e($product['descripcion']) ?></p>
                <strong class="price">$<?= number_format((float) $product['precio'], 2) ?></strong>
                <a class="btn btn-primary" href="<?= e(whatsapp_link('Hola, estoy interesado/a en el producto ' . $product['nombre'] . ' de FALEX. ¿Me pueden brindar más información?')) ?>" target="_blank" rel="noopener">Solicitar información</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <div>
            <span>Catálogo</span>
            <h3>Aún no hay productos activos en esta categoría</h3>
            <p>Ingresa al panel administrativo para agregar productos con imagen, descripción, precio y categoría.</p>
          </div>
          <a class="btn btn-primary" href="<?= e(whatsapp_link('Hola FALEX, quiero consultar sobre productos disponibles.')) ?>" target="_blank" rel="noopener">Consultar disponibilidad</a>
        </div>
      <?php endif; ?>
    </section>
  </section>
</main>
<?php public_footer(); ?>
