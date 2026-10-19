<?php
/**
 * Layout de las pantallas de login/registro. Si ya hay sesión, manda al inicio.
 * Muestra los errores flash y deja $old (datos previos del formulario, sin contraseñas).
 */
require_once __DIR__ . '/../../config/bootstrap.php';

if (is_logged_in()) {
    redirect('/src/views/index.php');
}

$old = (array) flash_get('old', []);
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?= e(url('/assets/css/bootstrap.min.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('/assets/css/theme.css')) ?>">
  <title><?= e(config('app.name')) ?> · Autenticación</title>
</head>
<body>

  <div class="auth-wrapper d-flex align-items-center justify-content-center">
    <div class="card auth-card shadow-sm m-3" style="width: 100%; max-width: 400px;">
      <div class="card-body p-4">
        <?php render_errors((array) flash_get('errors', [])); ?>
