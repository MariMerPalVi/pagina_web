<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_layout.php';

$editId = (int) ($_GET['edit'] ?? 0);
$editing = null;

if ($editId > 0) {
    $stmt = db()->prepare('SELECT * FROM categorias WHERE id = ? LIMIT 1');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'save';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'toggle' && $id > 0) {
        $stmt = db()->prepare('UPDATE categorias SET estado = IF(estado = "activo", "inactivo", "activo") WHERE id = ?');
        $stmt->execute([$id]);
        flash('success', 'Estado de la categoría actualizado.');
        redirect('admin/categories.php');
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado = ($_POST['estado'] ?? 'activo') === 'inactivo' ? 'inactivo' : 'activo';

    if ($nombre === '') {
        flash('error', 'El nombre de la categoría es obligatorio.');
        redirect('admin/categories.php');
    }

    if ($id > 0) {
        $stmt = db()->prepare('UPDATE categorias SET nombre = ?, descripcion = ?, estado = ? WHERE id = ?');
        $stmt->execute([$nombre, $descripcion, $estado, $id]);
        flash('success', 'Categoría actualizada.');
    } else {
        $stmt = db()->prepare('INSERT INTO categorias (nombre, descripcion, estado) VALUES (?, ?, ?)');
        $stmt->execute([$nombre, $descripcion, $estado]);
        flash('success', 'Categoría creada.');
    }

    redirect('admin/categories.php');
}

$categories = db()->query('SELECT c.*, COUNT(p.id) AS productos FROM categorias c LEFT JOIN productos p ON p.categoria_id = c.id GROUP BY c.id ORDER BY c.nombre')->fetchAll();

admin_header('Categorías');
?>
<section class="admin-grid">
  <article class="admin-panel">
    <div class="panel-heading"><h2><?= $editing ? 'Editar categoría' : 'Crear categoría' ?></h2></div>
    <form class="admin-form" method="post">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
      <label>Nombre<input type="text" name="nombre" value="<?= e($editing['nombre'] ?? '') ?>" required></label>
      <label>Descripción<textarea name="descripcion" rows="4"><?= e($editing['descripcion'] ?? '') ?></textarea></label>
      <label>Estado
        <select name="estado">
          <option value="activo" <?= ($editing['estado'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option>
          <option value="inactivo" <?= ($editing['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>
      </label>
      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <?php if ($editing): ?><a class="btn btn-secondary" href="<?= e(url('admin/categories.php')) ?>">Cancelar</a><?php endif; ?>
      </div>
    </form>
  </article>

  <article class="admin-panel">
    <div class="panel-heading"><h2>Listado</h2></div>
    <div class="table-wrap">
      <table class="admin-table">
        <thead><tr><th>Categoría</th><th>Productos</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
          <?php foreach ($categories as $category): ?>
            <tr>
              <td><strong><?= e($category['nombre']) ?></strong><small><?= e($category['descripcion']) ?></small></td>
              <td><?= (int) $category['productos'] ?></td>
              <td><span class="status <?= e($category['estado']) ?>"><?= e($category['estado']) ?></span></td>
              <td class="actions">
                <a class="btn btn-small" href="<?= e(url('admin/categories.php?edit=' . (int) $category['id'])) ?>">Editar</a>
                <form method="post">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                  <input type="hidden" name="action" value="toggle">
                  <button class="btn btn-small" type="submit"><?= $category['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </article>
</section>
<?php admin_footer(); ?>
