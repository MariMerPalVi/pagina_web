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
const SOCIAL_INSTAGRAM = '';
const SOCIAL_FACEBOOK = '';
const SOCIAL_TIKTOK = '';
const FOOTER_LINK_1_LABEL = 'Inicio';
const FOOTER_LINK_1_URL = 'index.php';
const FOOTER_LINK_2_LABEL = 'Catálogo';
const FOOTER_LINK_2_URL = 'catalogo.php';
const FOOTER_LINK_3_LABEL = 'Contacto';
const FOOTER_LINK_3_URL = 'contacto.php';

function default_site_settings(): array
{
    return [
        'whatsapp_number' => WHATSAPP_NUMBER,
        'contact_phone' => CONTACT_PHONE,
        'contact_email' => CONTACT_EMAIL,
        'contact_address' => CONTACT_ADDRESS,
        'social_instagram' => SOCIAL_INSTAGRAM,
        'social_facebook' => SOCIAL_FACEBOOK,
        'social_tiktok' => SOCIAL_TIKTOK,
        'footer_link_1_label' => FOOTER_LINK_1_LABEL,
        'footer_link_1_url' => FOOTER_LINK_1_URL,
        'footer_link_2_label' => FOOTER_LINK_2_LABEL,
        'footer_link_2_url' => FOOTER_LINK_2_URL,
        'footer_link_3_label' => FOOTER_LINK_3_LABEL,
        'footer_link_3_url' => FOOTER_LINK_3_URL,
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
                if (array_key_exists($row['clave'], $settings)) {
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

function social_links(): array
{
    return array_filter([
        'Instagram' => site_setting('social_instagram'),
        'Facebook' => site_setting('social_facebook'),
        'TikTok' => site_setting('social_tiktok'),
    ], static fn (string $url): bool => trim($url) !== '');
}

function footer_links(): array
{
    $links = [];

    for ($i = 1; $i <= 3; $i++) {
        $label = trim(site_setting('footer_link_' . $i . '_label'));
        $url = trim(site_setting('footer_link_' . $i . '_url'));

        if ($label !== '' && $url !== '') {
            $links[] = ['label' => $label, 'url' => $url];
        }
    }

    return $links;
}

function icon_svg(string $name): string
{
    $icons = [
        'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1"></circle></svg>',
        'facebook' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3c-3 0-5 2-5 5v2H6v4h3v5h4v-5h3l1-4h-4V9c0-.6.4-1 1-1Z"></path></svg>',
        'tiktok' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 4c.4 3 2.2 4.8 5 5v4c-1.8 0-3.4-.6-5-1.6V16a5 5 0 1 1-5-5c.4 0 .7 0 1 .1v4.1c-.3-.1-.6-.2-1-.2a1.9 1.9 0 1 0 1.9 1.9V4h3.1Z"></path></svg>',
        'whatsapp' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19.2 6.1 16A8 8 0 1 1 9 18.2L5 19.2Z"></path><path d="M9.5 8.8c.2 2 1.8 3.7 3.8 4l1-1.1 2 .9c-.2 1.1-1 1.8-2.1 1.8-3.4-.1-6.2-2.8-6.3-6.2 0-1.1.7-1.9 1.8-2.1l.9 2-1.1.7Z"></path></svg>',
        'lock' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg>',
        'home' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 11 12 4l8 7"></path><path d="M6 10v10h12V10"></path></svg>',
        'link' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7 0l2-2a5 5 0 0 0-7-7l-1 1"></path><path d="M14 11a5 5 0 0 0-7 0l-2 2a5 5 0 0 0 7 7l1-1"></path></svg>',
        'school' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-5 9 5-9 5-9-5Z"></path><path d="M7 12v4c3 2 7 2 10 0v-4"></path></svg>',
        'briefcase' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"></rect><path d="M9 7V5h6v2M3 12h18"></path></svg>',
        'shirt' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 4 5 6 3 11l4 2 1-2v9h8v-9l1 2 4-2-2-5-3-2-2 2h-4L8 4Z"></path></svg>',
        'badge' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-5"></path></svg>',
        'factory' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 21V9l6 4V9l6 4V6h6v15H3Z"></path><path d="M7 17h2M12 17h2M17 17h2"></path></svg>',
        'services' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10"></path><circle cx="7" cy="7" r="2"></circle><circle cx="17" cy="12" r="2"></circle></svg>',
        'catalog' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="7" height="7" rx="1"></rect><rect x="13" y="4" width="7" height="7" rx="1"></rect><rect x="4" y="13" width="7" height="7" rx="1"></rect><rect x="13" y="13" width="7" height="7" rx="1"></rect></svg>',
        'gallery' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><circle cx="8" cy="10" r="2"></circle><path d="m21 15-5-5L6 19"></path></svg>',
        'contact' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4z"></path><path d="m4 7 8 6 8-6"></path></svg>',
    ];

    return $icons[$name] ?? '';
}

function send_email(string $to, string $subject, string $message, ?string $replyTo = null): bool
{
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: FALEX <' . contact_email() . '>',
    ];

    if ($replyTo) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    return @mail($to, $subject, $message, implode("\r\n", $headers));
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

function upload_order_images(array $files): array
{
    if (empty($files['name']) || !is_array($files['name'])) {
        return [];
    }

    $saved = [];
    $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'orders';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    foreach ($files['name'] as $index => $name) {
        if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($files['error'][$index] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No se pudo subir una de las imágenes.');
        }

        if ($files['size'][$index] > 3 * 1024 * 1024) {
            throw new RuntimeException('Cada imagen debe pesar máximo 3 MB.');
        }

        $mime = $finfo->file($files['tmp_name'][$index]);
        if (!isset($extensions[$mime])) {
            throw new RuntimeException('Solo se permiten imágenes JPG, PNG o WEBP.');
        }

        $filename = uniqid('orden_', true) . '.' . $extensions[$mime];
        if (!move_uploaded_file($files['tmp_name'][$index], $uploadDir . DIRECTORY_SEPARATOR . $filename)) {
            throw new RuntimeException('No se pudo guardar una de las imágenes.');
        }

        $saved[] = $filename;
    }

    return $saved;
}

function order_image_url(string $image): string
{
    return url('uploads/orders/' . $image);
}
