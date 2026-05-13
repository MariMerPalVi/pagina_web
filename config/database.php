<?php

declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_PORT = '3306';
const DB_NAME = 'falex_textil';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    ensure_mysql_is_ready();

    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    ini_set('mysqlnd.net_read_timeout', '3');

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 3,
    ]);

    return $pdo;
}

function ensure_mysql_is_ready(): void
{
    $socket = @fsockopen(DB_HOST, (int) DB_PORT, $errno, $error, 1.0);

    if (!$socket) {
        throw new PDOException('MySQL no está disponible en ' . DB_HOST . ':' . DB_PORT . '. Inicia o reinicia MySQL desde XAMPP.');
    }

    stream_set_timeout($socket, 1);
    $handshake = fread($socket, 1);
    $meta = stream_get_meta_data($socket);
    fclose($socket);

    if ($handshake === '' || !empty($meta['timed_out'])) {
        throw new PDOException('MySQL está abierto en el puerto ' . DB_PORT . ', pero no responde. Reinicia MySQL desde XAMPP.');
    }
}
