<?php
/**
 * Conexión PDO. Deja disponible la variable global $pdo.
 * Las credenciales salen del .env; los errores técnicos se registran en el log
 * y nunca se muestran al usuario (podrían revelar host, usuario o estructura).
 */
require_once __DIR__ . '/env.php';

$dbName = env('DB_NAME');
if ($dbName === null) {
    error_log('[db] Falta DB_NAME en el .env');
    http_response_code(500);
    exit('Error de configuración del servidor.');
}

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
    env('DB_HOST', 'localhost'),
    env('DB_PORT', '3306'),
    $dbName,
    env('DB_CHARSET', 'utf8mb4')
);

try {
    $pdo = new PDO($dsn, env('DB_USER', 'root'), env('DB_PASS', ''), [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // prepared statements reales del motor
    ]);
} catch (PDOException $e) {
    error_log('[db] Conexión fallida: ' . $e->getMessage());
    http_response_code(500);
    exit('No pudimos conectar con la base de datos. Intentá de nuevo más tarde.');
}
