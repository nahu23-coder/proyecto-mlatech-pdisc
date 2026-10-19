<?php
/**
 * Seguridad: CSRF, autenticación/sesión y limitación de intentos.
 */

// ---------------------------------------------------------------- CSRF -----

/** Token CSRF de la sesión actual (se crea la primera vez que se pide). */
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Campo oculto para incluir en cada <form method="POST">. */
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** ¿El token enviado (campo POST o cabecera X-CSRF-Token) coincide con el de la sesión? */
function csrf_valid()
{
    $sent = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return is_string($sent) && hash_equals(csrf_token(), $sent);
}

/**
 * Exige un token válido. Si falla: JSON 419 (AJAX) o redirección con mensaje (formularios).
 *
 * @param bool   $json       true si el endpoint responde JSON.
 * @param string $backTo     ruta a la que volver si es un formulario.
 */
function csrf_require($json = false, $backTo = '/src/views/index.php')
{
    if (csrf_valid()) {
        return;
    }
    if ($json) {
        json_response(['success' => false, 'error' => 'Sesión vencida, recargá la página.'], 419);
    }
    flash_set('errors', ['La sesión expiró. Volvé a intentarlo.']);
    redirect($backTo);
}

// ---------------------------------------------------------- Autenticación --

/** ¿Hay un usuario logueado? */
function is_logged_in()
{
    return !empty($_SESSION['user']);
}

/** Id (UUID) del usuario logueado o null. */
function current_user_id()
{
    return is_logged_in() ? $_SESSION['user'] : null;
}

/**
 * Protege una página o endpoint.
 * Páginas: redirige al login. Endpoints JSON: responde 401.
 */
function require_login($json = false)
{
    if (is_logged_in()) {
        return;
    }
    if ($json) {
        json_response(['success' => false, 'error' => 'No autorizado'], 401);
    }
    redirect('/src/views/auth/login.php');
}

/**
 * Inicia sesión para un usuario. Regenera el ID de sesión (evita session fixation).
 */
function login_user(array $user)
{
    session_regenerate_id(true);
    $_SESSION['user']      = $user['id'];
    $_SESSION['user_name'] = $user['name'] ?? '';
    unset($_SESSION['csrf_token']); // token nuevo para la sesión nueva
}

/** Cierra sesión por completo: datos, cookie y sesión en el servidor. */
function logout_user()
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/** Evita que el navegador guarde en caché la página (botón "Atrás" tras cerrar sesión). */
function send_no_cache_headers()
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

// ------------------------------------------------------- Rate limiting -----

/**
 * Limitador simple por sesión: permite $max eventos cada $window segundos.
 * Devuelve false si ya se superó el límite.
 * (Frena el uso casual; para ataques reales hay que limitar también por IP en el servidor.)
 */
function rate_limit_allow($bucket, $max, $window)
{
    $now  = time();
    $hits = array_filter($_SESSION['rate'][$bucket] ?? [], function ($t) use ($now, $window) {
        return $t > $now - $window;
    });

    if (count($hits) >= $max) {
        $_SESSION['rate'][$bucket] = array_values($hits);
        return false;
    }

    $hits[] = $now;
    $_SESSION['rate'][$bucket] = array_values($hits);
    return true;
}
