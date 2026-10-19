<?php
/**
 * Layout privado: protege la página (requiere sesión), evita que el navegador la
 * guarde en caché (botón "Atrás" tras cerrar sesión) y dibuja la barra de navegación.
 * Cada vista lo incluye al principio y termina incluyendo footer.php.
 */
require_once __DIR__ . '/../../config/bootstrap.php';

require_login();
send_no_cache_headers();

// Unidades en el carrito (para el contador de la navbar)
$cartCount   = array_sum(array_map('intval', $_SESSION['cart'] ?? []));
$currentPage = basename($_SERVER['SCRIPT_NAME']);
$appName     = config('app.name');

/** Marca el link activo del menú. */
$navActive = function ($page) use ($currentPage) {
    return $currentPage === $page ? 'active' : '';
};
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
  <meta name="base-path" content="<?= e(BASE_PATH) ?>">
  <link rel="stylesheet" href="<?= e(url('/assets/css/bootstrap.min.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('/assets/css/theme.css')) ?>">
  <title><?= e($appName) ?></title>
</head>
<body>
  <nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="<?= e(url('/src/views/index.php')) ?>">
        <img src="<?= e(url('/assets/img/logo.jpg')) ?>" alt="<?= e($appName) ?>">
        MLA <span class="brand-accent">TECH</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"
              aria-controls="navMenu" aria-expanded="false" aria-label="Mostrar menú">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link <?= $navActive('index.php') ?>" href="<?= e(url('/src/views/index.php')) ?>">Inicio</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive('products.php') ?>" href="<?= e(url('/src/views/products.php')) ?>">Productos</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive('builder.php') ?>" href="<?= e(url('/src/views/builder.php')) ?>">Arma tu PC</a></li>
          <li class="nav-item"><a class="nav-link <?= $navActive('contact.php') ?>" href="<?= e(url('/src/views/contact.php')) ?>">Contacto</a></li>
        </ul>

        <form action="<?= e(url('/src/views/products.php')) ?>" method="GET" class="d-flex me-2" role="search">
          <input type="search" name="q" id="searchInput" class="form-control form-control-sm"
                 placeholder="Buscar productos..." autocomplete="off" maxlength="100"
                 value="<?= e(input_string($_GET, 'q', 100)) ?>">
        </form>

        <div class="d-flex align-items-center gap-2">
          <a href="<?= e(url('/src/views/cart.php')) ?>" class="btn btn-outline-accent btn-sm position-relative">
            🛒 Carrito
            <span id="cartBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-warning"><?= (int) $cartCount ?></span>
          </a>
          <form action="<?= e(url('/src/controllers/auth/logout.php')) ?>" method="POST" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-danger btn-sm">Salir</button>
          </form>
        </div>
      </div>
    </div>
  </nav>

  <main class="container my-4">
    <?php render_errors((array) flash_get('errors', [])); ?>
    <?php render_notice(flash_get('notice')); ?>
