<?php
/**
 * Login: valida credenciales y abre la sesión.
 *
 * Seguridad aplicada:
 *  - Solo POST + token CSRF.
 *  - Consulta preparada (sin inyección SQL).
 *  - Mensaje de error genérico (no revela si el email existe) y verificación a tiempo
 *    constante aunque el usuario no exista (evita enumeración por tiempos).
 *  - Límite de intentos por sesión y regeneración del ID de sesión al entrar.
 */
require_once __DIR__ . '/../../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/src/views/auth/login.php');
}
csrf_require(false, '/src/views/auth/login.php');

$email    = input_string($_POST, 'email', 150);
$password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

// Hash descartable: se compara contra él cuando el email no existe.
const DUMMY_HASH = '$2y$10$2BdCAbDxok8Dg.DbwwDIfOO.FkWmvydj8vTbwd58rGrFrFbXZ1SPq';

$fail = function ($message) use ($email) {
    flash_set('errors', [$message]);
    flash_set('old', ['email' => $email]);
    redirect('/src/views/auth/login.php');
};

if (!rate_limit_allow('login', config('auth.login_attempts'), config('auth.login_window'))) {
    $fail('Demasiados intentos. Esperá unos minutos e intentá de nuevo.');
}

if ($email === '' || $password === '' || strlen($password) > 1024) {
    $fail('Ingresá tu email y tu contraseña.');
}

try {
    $stmt = $pdo->prepare('SELECT id, name, password FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    $hash  = $user ? $user['password'] : DUMMY_HASH;
    $valid = password_verify($password, $hash) && $user;

    if (!$valid) {
        $fail('Email o contraseña incorrectos.');
    }

    // Si PHP mejoró el algoritmo/costo por defecto, actualizamos el hash guardado.
    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
        $upd = $pdo->prepare('UPDATE users SET password = :p WHERE id = :id');
        $upd->execute([':p' => password_hash($password, PASSWORD_DEFAULT), ':id' => $user['id']]);
    }

    login_user($user);
    redirect('/src/views/index.php');
} catch (PDOException $e) {
    error_log('[login] ' . $e->getMessage());
    $fail('Ocurrió un error. Intentá de nuevo en unos minutos.');
}
