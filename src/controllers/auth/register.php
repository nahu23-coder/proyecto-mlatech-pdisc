<?php
/**
 * Registro de usuario.
 *
 * - Valida y sanea cada campo en el servidor (el HTML "required" no alcanza).
 * - La contraseña se guarda con password_hash (bcrypt), nunca en texto plano.
 * - El email duplicado se detecta por la restricción UNIQUE de la base (código 1062),
 *   lo cual es seguro ante registros simultáneos (no hay "check y después insert").
 */
require_once __DIR__ . '/../../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/src/views/auth/register.php');
}
csrf_require(false, '/src/views/auth/register.php');

$name           = input_string($_POST, 'name', 50);
$email          = input_string($_POST, 'email', 150);
$password       = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
$repeatPassword = is_string($_POST['repeatPassword'] ?? null) ? $_POST['repeatPassword'] : '';

$min = config('auth.password_min');
$max = config('auth.password_max');

$errors = [];

if (mb_strlen($name) < 2) {
    $errors[] = 'El nombre es obligatorio (mínimo 2 caracteres).';
}
if ($email === '') {
    $errors[] = 'El email es obligatorio.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El email no tiene un formato válido.';
}
if (strlen($password) < $min) {
    $errors[] = "La contraseña debe tener al menos $min caracteres.";
} elseif (strlen($password) > $max) {
    $errors[] = "La contraseña no puede superar los $max caracteres.";
}
if ($password !== $repeatPassword) {
    $errors[] = 'Las contraseñas no coinciden.';
}

if ($errors) {
    flash_set('errors', $errors);
    flash_set('old', ['name' => $name, 'email' => $email]); // nunca se devuelve la contraseña
    redirect('/src/views/auth/register.php');
}

try {
    $userId = uuid_v4(); // la tabla usa UUID: lastInsertId() devolvería 0, por eso lo generamos acá

    $stmt = $pdo->prepare('INSERT INTO users (id, name, email, password) VALUES (:id, :name, :email, :password)');
    $stmt->execute([
        ':id'       => $userId,
        ':name'     => $name,
        ':email'    => $email,
        ':password' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    login_user(['id' => $userId, 'name' => $name]);
    redirect('/src/views/index.php');
} catch (PDOException $e) {
    $duplicate = isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062;
    if (!$duplicate) {
        error_log('[register] ' . $e->getMessage());
    }

    flash_set('errors', [$duplicate ? 'Ese email ya está registrado.' : 'No pudimos crear la cuenta. Intentá de nuevo.']);
    flash_set('old', ['name' => $name, 'email' => $email]);
    redirect('/src/views/auth/register.php');
}
