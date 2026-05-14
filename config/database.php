<?php

declare(strict_types=1);

const DB_HOST = 'sql112.infinityfree.com';
const DB_PORT = '3306';
const DB_NAME = 'if0_41910792_falex';
const DB_USER = 'if0_41910792';
const DB_PASS = 'SVY78wl17l53l';
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
