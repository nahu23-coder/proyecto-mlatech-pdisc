<?php
include(__DIR__ . '/../_layouts/layout.php');
?>

<header class="row">
  <div class="col">
    <h1>Catálogo de productos</h1>
    <p>Componentes, periféricos y accesorios para tu PC</p>
  </div>
  <div class="col d-flex justify-content-end align-items-center">

  </div>
</header>

<main>
  <?php if (!empty($products)): ?>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php foreach ($products as $product): ?>
        <div class="col">
          <article class="card h-100">
            <?php if (!empty($product['image_url'])): ?>
              <img src="<?= $product['image_url'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <span class="badge text-bg-secondary mb-2 align-self-start">
                <?= $product['category_icon'] ?> <?= $product['category_name'] ?>
              </span>

              <h2 class="card-title h5">
                <a href="/src/controllers/products/show.php?id=<?= $product['id'] ?>"><?= $product['name'] ?></a>
              </h2>

              <p class="card-text text-truncate"><?= $product['description'] ?></p>

              <div class="mt-auto d-flex justify-content-between align-items-center">
                <strong>$<?= number_format($product['price'], 2, ',', '.') ?></strong>
                <small class="text-muted">Stock: <?= $product['stock'] ?></small>
              </div>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>No hay productos todavía.</p>
  <?php endif; ?>
</main>