<?php
/**
 * Lectura centralizada del archivo .env (se parsea UNA sola vez por request).
 *
 * Se usa INI_SCANNER_RAW para que valores como "yes", "null" o contraseñas con
 * caracteres especiales (! ( ) { } | & ~ ^) no sean reinterpretados por el parser INI.
 */

/**
 * Devuelve una variable de entorno (primero .env, luego variables reales del sistema).
 *
 * @param string      $key     Nombre de la variable.
 * @param string|null $default Valor si no existe o está vacía.
 */
function env($key, $default = null)
{
    static $vars = null;

    if ($vars === null) {
        $file = dirname(__DIR__, 2) . '/.env';
        $parsed = is_readable($file) ? parse_ini_file($file, false, INI_SCANNER_RAW) : [];
        $vars = is_array($parsed) ? $parsed : [];
    }

    $value = $vars[$key] ?? getenv($key);

    return ($value === false || $value === null || $value === '') ? $default : (string) $value;
}

/** Interpreta "true/1/yes/on" como booleano. */
function env_bool($key, $default = false)
{
    $value = env($key);
    return $value === null ? $default : in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
}
