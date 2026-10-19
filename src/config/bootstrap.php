<?php
/**
 * Bootstrap global. Se incluye al principio de cada entry point
 * (index.php, vistas y controladores) y deja listo:
 *   - configuración (config()), helpers y utilidades de seguridad
 *   - sesión endurecida y cabeceras de seguridad HTTP
 *   - conexión a la base como $pdo
 */
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/security.php';
require_once __DIR__ . '/../lib/http.php';

date_default_timezone_set(config('app.timezone'));

// Ruta base del sitio (si se sirve desde una subcarpeta, ej: http://localhost/mlatech)
define('BASE_PATH', rtrim((string) parse_url(config('app.site_url'), PHP_URL_PATH), '/'));

// ---- Cabeceras de seguridad -------------------------------------------------
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    // Todo el JS/CSS es propio (sin scripts inline), así que se puede bloquear el resto.
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; "
         . "style-src 'self' 'unsafe-inline'; script-src 'self'; "
         . "base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
}

// ---- Sesión endurecida ------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    ini_set('session.use_strict_mode', '1');   // rechaza IDs de sesión que el servidor no creó
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,                    // el JS no puede leer la cookie
        'samesite' => 'Lax',                   // Lax (no Strict) para no perder la sesión al volver de Mercado Pago
    ]);
    session_start();
}

require_once __DIR__ . '/database.php';
