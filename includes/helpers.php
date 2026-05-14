<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    $sessionPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'sessions';
    if (!is_dir($sessionPath)) {
        @mkdir($sessionPath, 0775, true);
    }

    if (is_dir($sessionPath) && is_writable($sessionPath)) {
        session_save_path($sessionPath);
    }

    session_start();
}

const SITE_NAME = 'FALEX';
const WHATSAPP_NUMBER = '593999999999';
const CONTACT_EMAIL = 'ventas@falextextil.com';
const CONTACT_PHONE = '+593 99 999 9999';
const CONTACT_ADDRESS = 'Quito, Ecuador';

function default_site_settings(): array
{
    return [
        'whatsapp_number' => WHATSAPP_NUMBER,
        'contact_phone' => CONTACT_PHONE,
        'contact_email' => CONTACT_EMAIL,
        'contact_address' => CONTACT_ADDRESS,
    ];
}

function site_setting(string $key): string
{
    static $settings = null;
    $defaults = default_site_settings();

    if (!array_key_exists($key, $defaults)) {
        return '';
    }

    if ($settings === null) {
        $settings = $defaults;

        try {
            require_once __DIR__ . '/../config/database.php';
            $rows = db()->query('SELECT clave, valor FROM configuraciones')->fetchAll();
            foreach ($rows as $row) {
                if (array_key_exists($row['clave'], $settings) && trim((string) $row['valor']) !== '') {
                    $settings[$row['clave']] = (string) $row['valor'];
                }
            }
        } catch (Throwable) {
            $settings = $defaults;
        }
    }

    return $settings[$key];
}

function whatsapp_number(): string
{
    return preg_replace('/\D+/', '', site_setting('whatsapp_number'));
}

function contact_phone(): string
{
    return site_setting('contact_phone');
}

function contact_email(): string
{
    return site_setting('contact_email');
}

function contact_address(): string
{
    return site_setting('contact_address');
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if (str_contains($base, '/admin')) {
        $base = dirname($base);
    }

    return ($base === '/' ? '' : $base) . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Token de seguridad inválido.');
    }
}

function whatsapp_link(string $message): string
{
    return 'https://wa.me/' . whatsapp_number() . '?text=' . rawurlencode($message);
}

function product_image_url(?string $image): string
{
    if ($image) {
        return url('uploads/products/' . $image);
    }

    return url('assets/product-placeholder.svg');
}

function upload_product_image(array $file, ?string $current = null): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $current;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('No se pudo subir la imagen.');
    }

    if ($file['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('La imagen no debe superar 3 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($extensions[$mime])) {
        throw new RuntimeException('Solo se permiten imágenes JPG, PNG o WEBP.');
    }

    $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'products';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $filename = uniqid('producto_', true) . '.' . $extensions[$mime];
    $target = $uploadDir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('No se pudo guardar la imagen.');
    }

    return $filename;
}
