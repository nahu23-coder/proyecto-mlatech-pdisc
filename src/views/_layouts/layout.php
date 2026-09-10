<?php
require_once __DIR__ . '/../../config/bootstrap.php'; # Acá linkea las configuraciones de bootstrap.php

# Si no estoy logueado, me saca
if (!isset($_SESSION['user'])) {
  header('Location: /src/views/auth/login.php');
  exit;
}

function logout() {
  session_destroy();
  header('Location: /src/views/auth/login.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href=<?= '/assets/css/bootstrap.min.css' ?> >
  <link rel="stylesheet" href=<?= '/assets/css/theme.css' ?> >
  <script src=<?= '/assets/js/bootstrap.min.js' ?>></script>
  <title>MLA Tech</title>
</head>
<body>
  <nav class="navbar navbar-expand-lg bg-dark-subtle border-bottom shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="/src/views/index.php">
        MLA <span class="brand-accent">TECH</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" href="/src/views/index.php">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#categorias">Categorías</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#productos">Productos</a>
          </li>
        </ul>

        <div class="d-flex align-items-center gap-2">
          <a href="#" class="btn btn-outline-light btn-sm position-relative">
            🛒 Carrito
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-warning">0</span>
          </a>
          <a href="/src/controllers/auth/logout.php" class="btn btn-outline-danger btn-sm">Salir</a>
        </div>
      </div>
    </div>
  </nav>

  <main class="container my-4">
    