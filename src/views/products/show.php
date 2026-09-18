<?php
include(__DIR__ . '/../_layouts/layout.php');
?>

<header class="row mb-4">
  <div class="col">
    <h1>Detalle del producto</h1>
  </div>
  <div class="col d-flex justify-content-end align-items-center">
    <a class="btn btn-primary" href="/src/controllers/products/index.php">Volver al catálogo</a>
  </div>
</header>

<main>
  <?php if (!empty($product)): ?>
    <article class="card border-0">
      <div class="row g-4">
        <?php if (!empty($product['image_url'])): ?>
          <div class="col-md-5">
            <img src="<?= $product['image_url'] ?>" class="img-fluid rounded" alt="<?= $product['name'] ?>">
          </div>
        <?php endif; ?>

        <div class="col">
          <span class="badge text-bg-secondary mb-2">
            <?= $product['category_icon'] ?> <?= $product['category_name'] ?>
          </span>

          <h2 class="card-title"><?= $product['name'] ?></h2>

          <p class="card-text"><?= nl2br($product['description']) ?></p>

          <p class="fs-4 fw-bold">$<?= number_format($product['price'], 2, ',', '.') ?></p>

          <p class="card-text">
            <strong>Stock disponible:</strong>
            <?php if ($product['stock'] > 0): ?>
              <?= $product['stock'] ?> unidades
            <?php else: ?>
              <span class="text-danger">Sin stock</span>
            <?php endif; ?>
          </p>

          <p class="text-muted">Publicado el <?= $product['created_at'] ?></p>

          <button class="btn btn-success" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
            🛒 Agregar al carrito
          </button>
        </div>
      </div>
    </article>
  <?php else: ?>
    <p>No se encontró el producto solicitado.</p>
  <?php endif; ?>
</main>