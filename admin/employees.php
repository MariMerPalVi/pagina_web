<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_layout.php';
require_admin();

$editId = (int) ($_GET['edit'] ?? 0);
$editing = null;

if ($editId > 0) {
    $stmt = db()->prepare('SELECT id, nombre, correo, usuario, rol, estado FROM usuarios WHERE id = ? AND rol = "empleado" LIMIT 1');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'save';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'toggle' && $id > 0) {
        $stmt = db()->prepare('UPDATE usuarios SET estado = IF(estado = "activo", "inactivo", "activo") WHERE id = ? AND rol = "empleado"');
        $stmt->execute([$id]);
        flash('success', 'Estado del empleado actualizado.');
        redirect('admin/employees.php');
    }

    if ($action === 'delete' && $id > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM usuarios WHERE id = ? AND rol = "empleado"');
            $stmt->execute([$id]);
            flash('success', 'Empleado eliminado correctamente.');
        } catch (Throwable) {
            flash('error', 'No se pudo eliminar el empleado porque tiene registros asociados. Puedes desactivarlo.');
        }
        redirect('admin/employees.php');
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $estado = ($_POST['estado'] ?? 'activo') === 'inactivo' ? 'inactivo' : 'activo';

    try {
        if ($nombre === '' || $correo === '' || $usuario === '') {
            throw new RuntimeException('Completa nombre, correo y usuario.');
        }

        if ($id > 0) {
            if ($password !== '') {
                $stmt = db()->prepare('UPDATE usuarios SET nombre = ?, correo = ?, usuario = ?, password_hash = ?, estado = ? WHERE id = ? AND rol = "empleado"');
                $stmt->execute([$nombre, $correo, $usuario, password_hash($password, PASSWORD_DEFAULT), $estado, $id]);
            } else {
                $stmt = db()->prepare('UPDATE usuarios SET nombre = ?, correo = ?, usuario = ?, estado = ? WHERE id = ? AND rol = "empleado"');
                $stmt->execute([$nombre, $correo, $usuario, $estado, $id]);
            }
            flash('success', 'Empleado actualizado.');
        } else {
            if ($password === '') {
                throw new RuntimeException('La contraseña es obligatoria para crear un empleado.');
            }
            $stmt = db()->prepare('INSERT INTO usuarios (nombre, correo, usuario, password_hash, rol, estado) VALUES (?, ?, ?, ?, "empleado", ?)');
            $stmt->execute([$nombre, $correo, $usuario, password_hash($password, PASSWORD_DEFAULT), $estado]);
            flash('success', 'Empleado creado.');
        }

        redirect('admin/employees.php');
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
        redirect('admin/employees.php' . ($id ? '?edit=' . $id : ''));
    }
}

$employees = db()->query('SELECT id, nombre, correo, usuario, estado, fecha_creacion FROM usuarios WHERE rol = "empleado" ORDER BY fecha_creacion DESC')->fetchAll();
$showModal = isset($_GET['create']) || $editing;

admin_header('Empleados');
?>
<section class="admin-panel">
    <div class="panel-heading">
      <div>
        <h2>Listado de empleados</h2>
        <p class="muted-text">Gestiona los usuarios internos que pueden administrar el catálogo.</p>
      </div>
      <a class="btn btn-primary" href="<?= e(url('admin/employees.php?create=1')) ?>">Crear empleado</a>
    </div>
    <div class="table-wrap">
      <table class="admin-table">
        <thead><tr><th>Empleado</th><th>Usuario</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
          <?php foreach ($employees as $employee): ?>
            <tr>
              <td data-label="Empleado"><strong><?= e($employee['nombre']) ?></strong><small><?= e($employee['correo']) ?></small></td>
              <td data-label="Usuario"><?= e($employee['usuario']) ?></td>
              <td data-label="Estado"><span class="status <?= e($employee['estado']) ?>"><?= e($employee['estado']) ?></span></td>
              <td class="actions" data-label="Acciones">
                <a class="btn btn-small" href="<?= e(url('admin/employees.php?edit=' . (int) $employee['id'])) ?>" title="Editar">Editar</a>
                <form method="post">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= (int) $employee['id'] ?>">
                  <input type="hidden" name="action" value="toggle">
                  <button class="btn btn-small" type="submit" title="<?= $employee['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>"><?= $employee['estado'] === 'activo' ? 'Inactivar' : 'Activar' ?></button>
                </form>
                <form method="post" onsubmit="return confirm('¿Eliminar este empleado? Esta acción no se puede deshacer.')">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= (int) $employee['id'] ?>">
                  <input type="hidden" name="action" value="delete">
                  <button class="btn btn-danger btn-small" type="submit" title="Eliminar">Eliminar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
</section>

<?php if ($showModal): ?>
  <div class="admin-modal" role="dialog" aria-modal="true" aria-labelledby="employee-modal-title">
    <a class="admin-modal-backdrop" href="<?= e(url('admin/employees.php')) ?>" aria-label="Cerrar"></a>
    <article class="admin-modal-card">
      <div class="modal-heading">
        <div>
          <p class="eyebrow">Empleados</p>
          <h2 id="employee-modal-title"><?= $editing ? 'Editar empleado' : 'Crear empleado' ?></h2>
        </div>
        <a class="modal-close" href="<?= e(url('admin/employees.php')) ?>" aria-label="Cerrar">&times;</a>
      </div>
      <form class="admin-form" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
        <label>Nombre<input type="text" name="nombre" value="<?= e($editing['nombre'] ?? '') ?>" required autofocus></label>
        <label>Correo<input type="email" name="correo" value="<?= e($editing['correo'] ?? '') ?>" required></label>
        <label>Usuario<input type="text" name="usuario" value="<?= e($editing['usuario'] ?? '') ?>" required></label>
        <label>Contraseña<input type="password" name="password" placeholder="<?= $editing ? 'Dejar vacío para no cambiar' : 'Contraseña inicial' ?>" <?= $editing ? '' : 'required' ?>></label>
        <label>Estado
          <select name="estado">
            <option value="activo" <?= ($editing['estado'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= ($editing['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
          </select>
        </label>
        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Guardar empleado</button>
          <a class="btn btn-secondary" href="<?= e(url('admin/employees.php')) ?>">Cancelar</a>
        </div>
      </form>
    </article>
  </div>
<?php endif; ?>
<?php admin_footer(); ?>
