<?php

declare(strict_types=1);

// InfinityFree: reemplaza estos valores con los datos reales del panel MySQL.
// Importante: en InfinityFree el host NO suele ser localhost ni 127.0.0.1.
// Ejemplo de host: sqlXXX.infinityfree.com
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
