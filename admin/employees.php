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

admin_header('Empleados');
?>
<section class="admin-grid">
  <article class="admin-panel">
    <div class="panel-heading"><h2><?= $editing ? 'Editar empleado' : 'Crear empleado' ?></h2></div>
    <form class="admin-form" method="post">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
      <label>Nombre<input type="text" name="nombre" value="<?= e($editing['nombre'] ?? '') ?>" required></label>
      <label>Correo<input type="email" name="correo" value="<?= e($editing['correo'] ?? '') ?>" required></label>
      <label>Usuario<input type="text" name="usuario" value="<?= e($editing['usuario'] ?? '') ?>" required></label>
      <label>Contraseña<input type="password" name="password" placeholder="<?= $editing ? 'Dejar vacío para no cambiar' : 'Contraseña inicial' ?>"></label>
      <label>Estado
        <select name="estado">
          <option value="activo" <?= ($editing['estado'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option>
          <option value="inactivo" <?= ($editing['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>
      </label>
      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Guardar empleado</button>
        <?php if ($editing): ?><a class="btn btn-secondary" href="<?= e(url('admin/employees.php')) ?>">Cancelar</a><?php endif; ?>
      </div>
    </form>
  </article>

  <article class="admin-panel">
    <div class="panel-heading"><h2>Listado de empleados</h2></div>
    <div class="table-wrap">
      <table class="admin-table">
        <thead><tr><th>Empleado</th><th>Usuario</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
          <?php foreach ($employees as $employee): ?>
            <tr>
              <td><strong><?= e($employee['nombre']) ?></strong><small><?= e($employee['correo']) ?></small></td>
              <td><?= e($employee['usuario']) ?></td>
              <td><span class="status <?= e($employee['estado']) ?>"><?= e($employee['estado']) ?></span></td>
              <td class="actions">
                <a class="btn btn-small" href="<?= e(url('admin/employees.php?edit=' . (int) $employee['id'])) ?>">Editar</a>
                <form method="post">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= (int) $employee['id'] ?>">
                  <input type="hidden" name="action" value="toggle">
                  <button class="btn btn-small" type="submit"><?= $employee['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?></button>
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
