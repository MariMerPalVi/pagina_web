<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $user = null;
    if ($user !== null) {
        return $user;
    }

    try {
        $stmt = db()->prepare('SELECT id, nombre, correo, usuario, rol, estado FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    } catch (PDOException) {
        return null;
    }

    if (!$user || $user['estado'] !== 'activo') {
        logout();
        return null;
    }

    return $user;
}

function login(string $username, string $password): bool
{
    try {
        $stmt = db()->prepare('SELECT * FROM usuarios WHERE usuario = ? AND estado = "activo" LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
    } catch (PDOException) {
        return false;
    }

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];

    return true;
}

function logout(): void
{
    unset($_SESSION['user_id']);
}

function require_login(): void
{
    if (!current_user()) {
        redirect('login.php');
    }
}

function require_admin(): void
{
    require_login();

    if ((current_user()['rol'] ?? '') !== 'administrador') {
        http_response_code(403);
        exit('No tienes permisos para acceder a esta sección.');
    }
}

function is_admin(): bool
{
    return (current_user()['rol'] ?? '') === 'administrador';
}
