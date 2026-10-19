<?php
require_once __DIR__ . '/_layouts/layout.php';

// Categorías (cards del home)
$categorias = $pdo->query('SELECT name, icon FROM categories ORDER BY name')->fetchAll();

// Productos destacados: los últimos 4 cargados
$destacados = $pdo->query(
    'SELECT p.id, p.name, p.description, p.price, p.stock, p.image_url,
            c.name AS category_name, c.icon AS category_icon
     FROM products p
     JOIN categories c ON c.id = p.category_id
     ORDER BY p.created_at DESC
     LIMIT 4'
)->fetchAll();
?>

<!-- Hero / Banner -->
<section class="hero mb-5">
  <img src="<?= e(url('/assets/img/banner.jpg')) ?>" alt="<?= e(config('app.name')) ?> - Tu tienda de hardware">
  <div class="hero-caption">
    <h1 class="display-6 text-white">Armá tu PC con los mejores componentes</h1>
    <p class="lead text-secondary">
      Procesadores, placas de video, memorias, almacenamiento y periféricos al mejor precio, con envíos a todo el país.
    </p>
    <a href="<?= e(url('/src/views/products.php')) ?>" class="btn btn-accent btn-lg mt-2">Ver productos</a>
    <a href="<?= e(url('/src/views/builder.php')) ?>" class="btn btn-outline-accent btn-lg mt-2 ms-2">Armá tu PC</a>
  </div>
</section>

<!-- Categorías -->
<section id="categorias" class="mb-5">
  <h2 class="h4 mb-3">Categorías</h2>
  <div class="row g-3">
    <?php foreach ($categorias as $cat): ?>
      <div class="col-6 col-md-4 col-lg-2">
        <a href="<?= e(url('/src/views/products.php?categoria=' . urlencode($cat['name']))) ?>" class="card category-card text-decoration-none text-body">
          <span class="category-icon"><?= e($cat['icon']) ?></span>
          <span><?= e($cat['name']) ?></span>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Productos destacados -->
<section id="productos" class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0">Productos destacados</h2>
    <a href="<?= e(url('/src/views/products.php')) ?>" class="btn btn-outline-accent btn-sm">Ver todos</a>
  </div>
  <div class="row g-3">
    <?php foreach ($destacados as $p): $img = $p['image_url'] ? url($p['image_url']) : ''; ?>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="card product-card" data-product-card
             data-id="<?= e($p['id']) ?>"
             data-name="<?= e($p['name']) ?>"
             data-category="<?= e($p['category_name']) ?>"
             data-price="<?= e($p['price']) ?>"
             data-description="<?= e($p['description']) ?>"
             data-icon="<?= e($p['category_icon']) ?>"
             data-image="<?= e($img) ?>">
          <div class="product-thumb<?= $img !== '' ? ' has-image' : '' ?>">
            <?php if ($img !== ''): ?>
              <img src="<?= e($img) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
            <?php else: ?>
              <?= e($p['category_icon']) ?>
            <?php endif; ?>
          </div>
          <div class="card-body">
            <span class="badge badge-stock mb-2"><?= $p['stock'] > 0 ? 'En stock' : 'Sin stock' ?></span>
            <h3 class="h6 card-title mb-1"><?= e($p['name']) ?></h3>
            <p class="price mb-2"><?= e(format_price($p['price'])) ?></p>
            <button type="button" class="btn btn-accent btn-sm w-100" data-add-to-cart <?= $p['stock'] > 0 ? '' : 'disabled' ?>>
              Agregar al carrito
            </button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Por qué elegirnos -->
<section class="mb-5">
  <div class="row g-3 text-center">
    <div class="col-md-4">
      <div class="feature-icon mb-2">🚚</div>
      <h3 class="h6">Envíos a todo el país</h3>
      <p class="text-secondary small">Recibí tus componentes donde estés.</p>
    </div>
    <div class="col-md-4">
      <div class="feature-icon mb-2">🛡️</div>
      <h3 class="h6">Garantía oficial</h3>
      <p class="text-secondary small">Todos los productos con garantía.</p>
    </div>
    <div class="col-md-4">
      <div class="feature-icon mb-2">💳</div>
      <h3 class="h6">Medios de pago</h3>
      <p class="text-secondary small">Pagá con Mercado Pago de forma simple y segura.</p>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/_layouts/footer.php'; ?>
