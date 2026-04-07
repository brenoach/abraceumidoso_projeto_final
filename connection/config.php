<?php
/**
 * Configuração geral do sistema e do banco de dados.
 * Ajustado para InfinityFree.
 */

declare(strict_types=1);

if (!defined('APP_NAME')) {
    define('APP_NAME', 'Abrace Um Idoso');
}

$hostAtual = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLocal = in_array($hostAtual, ['localhost', '127.0.0.1'], true);

if ($isLocal) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'if0_41248576_bd_abraceumidoso');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_PORT', 3306);
} else {
    define('DB_HOST', 'sql211.infinityfree.com');
    define('DB_NAME', 'if0_41248576_bd_abraceumidoso');
    define('DB_USER', 'if0_41248576');
    define('DB_PASS', '305079Mo');
    define('DB_PORT', 3306);
}
