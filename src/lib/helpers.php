<?php
/**
 * Helpers generales: configuración, escape de salida, lectura segura de
 * entradas, redirecciones, respuestas JSON y mensajes flash.
 */

// ---------------------------------------------------------------------------
// Compatibilidad: si PHP no tiene la extensión mbstring (pasa en algunas instalaciones
// de Windows/XAMPP), definimos versiones mínimas para que el sitio no se caiga.
// Lo recomendado igual es activarla en php.ini (extension=mbstring).
// ---------------------------------------------------------------------------
if (!function_exists('mb_substr')) {
    function mb_substr($string, $start, $length = null, $encoding = null)
    {
        $chars = preg_split('//u', (string) $string, -1, PREG_SPLIT_NO_EMPTY);
        return implode('', array_slice($chars ?: [], $start, $length));
    }
}
if (!function_exists('mb_strlen')) {
    function mb_strlen($string, $encoding = null)
    {
        return (int) preg_match_all('/./us', (string) $string);
    }
}
if (!function_exists('mb_strtolower')) {
    function mb_strtolower($string, $encoding = null)
    {
        return strtolower((string) $string); // solo ASCII; suficiente para el FAQ
    }
}

/**
 * Lee un valor de src/config/app.php con notación de puntos: config('cart.max_qty_per_item').
 */
function config($key, $default = null)
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require __DIR__ . '/../config/app.php';
    }

    $value = $cfg;
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

/** Escapa un valor para imprimirlo en HTML (previene XSS). Usar SIEMPRE al mostrar datos. */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Arma una URL interna respetando la ruta base del sitio. */
function url($path)
{
    return BASE_PATH . $path;
}

/**
 * Lee un campo de texto de $_GET/$_POST de forma segura.
 * Devuelve '' si falta o si no es string (ej: ?q[]=x), recorta espacios y limita el largo.
 */
function input_string(array $source, $key, $maxLength = 255)
{
    $value = $source[$key] ?? '';
    if (!is_string($value)) {
        return '';
    }
    return mb_substr(trim($value), 0, $maxLength);
}

/** Valida que un texto sea un UUID (formato de los ids de la base). */
function is_uuid($value)
{
    return is_string($value)
        && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value) === 1;
}

/** Genera un UUID v4 criptográficamente seguro. */
function uuid_v4()
{
    $b = random_bytes(16);
    $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
    $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
}

/** Redirige a una ruta interna y corta la ejecución. */
function redirect($path)
{
    header('Location: ' . url($path));
    exit;
}

/** Responde JSON y corta la ejecución. */
function json_response(array $data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Guarda un mensaje que se muestra UNA sola vez (en la próxima página). */
function flash_set($key, $value)
{
    $_SESSION['flash'][$key] = $value;
}

/** Lee y borra un mensaje flash. */
function flash_get($key, $default = null)
{
    $value = $_SESSION['flash'][$key] ?? $default;
    unset($_SESSION['flash'][$key]);
    return $value;
}

/** Imprime una lista de errores como alerta de Bootstrap (ya escapada). */
function render_errors(array $errors)
{
    if (!$errors) {
        return;
    }
    echo '<div class="alert alert-danger" role="alert"><ul class="mb-0 ps-3">';
    foreach ($errors as $error) {
        echo '<li>' . e($error) . '</li>';
    }
    echo '</ul></div>';
}

/** Formatea un precio como "$ 1.234" (formato argentino). */
function format_price($amount)
{
    return '$ ' . number_format((float) $amount, 0, ',', '.');
}

/** Imprime un aviso simple (éxito por defecto) ya escapado. No imprime nada si el mensaje está vacío. */
function render_notice($message, $type = 'success')
{
    if (!$message) {
        return;
    }
    echo '<div class="alert alert-' . e($type) . '" role="status">' . e($message) . '</div>';
}
