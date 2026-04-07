<?php
require_once __DIR__ . '/config.php';

/**
 * Retorna uma conexão única com o banco usando mysqli.
 */
function db(): mysqli
{
    static $conn = null;

    if ($conn instanceof mysqli) {
        return $conn;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        $conn->set_charset('utf8mb4');
    } catch (mysqli_sql_exception $e) {
        die('Erro na conexão com o banco de dados: ' . $e->getMessage());
    }

    return $conn;
}