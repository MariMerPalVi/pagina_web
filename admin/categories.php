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

    if ($action === 'delete' && $id > 0) {
        require_admin();

        try {
            $stmt = db()->prepare('DELETE FROM categorias WHERE id = ?');
            $stmt->execute([$id]);
            flash('success', 'Categoría eliminada.');
        } catch (PDOException) {
            flash('error', 'No se puede eliminar una categoría con productos asociados. Desactívala o cambia esos productos de categoría primero.');
        }

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
$showModal = isset($_GET['create']) || $editing;

admin_header('Categorías');
?>
<section class="admin-panel">
    <div class="panel-heading">
      <div>
        <h2>Listado de categorías</h2>
        <p class="muted-text">Organiza los filtros que verá el cliente en el catálogo público.</p>
      </div>
      <a class="btn btn-primary" href="<?= e(url('admin/categories.php?create=1')) ?>">Crear categoría</a>
    </div>
    <div class="table-wrap">
      <table class="admin-table">
        <thead><tr><th>Categoría</th><th>Productos</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
          <?php foreach ($categories as $category): ?>
            <tr>
              <td data-label="Categoría"><strong><?= e($category['nombre']) ?></strong><small><?= e($category['descripcion']) ?></small></td>
              <td data-label="Productos"><?= (int) $category['productos'] ?></td>
              <td data-label="Estado"><span class="status <?= e($category['estado']) ?>"><?= e($category['estado']) ?></span></td>
              <td class="actions" data-label="Acciones">
                <a class="btn btn-small" href="<?= e(url('admin/categories.php?edit=' . (int) $category['id'])) ?>" title="Editar">Editar</a>
                <form method="post">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                  <input type="hidden" name="action" value="toggle">
                  <button class="btn btn-small" type="submit" title="<?= $category['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>"><?= $category['estado'] === 'activo' ? 'Inactivar' : 'Activar' ?></button>
                </form>
                <?php if (is_admin()): ?>
                  <form method="post" onsubmit="return confirm('¿Eliminar esta categoría?')">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button class="btn btn-danger btn-small" type="submit" title="Eliminar">Eliminar</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
</section>

<?php if ($showModal): ?>
  <div class="admin-modal" role="dialog" aria-modal="true" aria-labelledby="category-modal-title">
    <a class="admin-modal-backdrop" href="<?= e(url('admin/categories.php')) ?>" aria-label="Cerrar"></a>
    <article class="admin-modal-card">
      <div class="modal-heading">
        <div>
          <p class="eyebrow">Categorías</p>
          <h2 id="category-modal-title"><?= $editing ? 'Editar categoría' : 'Crear categoría' ?></h2>
        </div>
        <a class="modal-close" href="<?= e(url('admin/categories.php')) ?>" aria-label="Cerrar">&times;</a>
      </div>
      <form class="admin-form" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
        <label>Nombre<input type="text" name="nombre" value="<?= e($editing['nombre'] ?? '') ?>" required autofocus></label>
        <label>Descripción<textarea name="descripcion" rows="4"><?= e($editing['descripcion'] ?? '') ?></textarea></label>
        <label>Estado
          <select name="estado">
            <option value="activo" <?= ($editing['estado'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= ($editing['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
          </select>
        </label>
        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Guardar categoría</button>
          <a class="btn btn-secondary" href="<?= e(url('admin/categories.php')) ?>">Cancelar</a>
        </div>
      </form>
    </article>
  </div>
<?php endif; ?>
<?php admin_footer(); ?>
