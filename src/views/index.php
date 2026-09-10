<?php
include('./_layouts/layout.php');
?>

<!-- Hero -->
<section class="hero text-white mb-5">
  <h1 class="display-6">Armá tu PC con los mejores componentes</h1>
  <p class="lead text-secondary">
    Procesadores, placas de video, memorias, almacenamiento y periféricos al mejor precio, con envíos a todo el país.
  </p>
  <a href="#productos" class="btn btn-accent btn-lg mt-2">Ver productos</a>
</section>

<!-- Categorías -->
<section id="categorias" class="mb-5">
  <h2 class="h4 mb-3">Categorías</h2>
  <div class="row g-3">
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card category-card">
        <span class="category-icon">🧠</span>
        <span>Procesadores</span>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card category-card">
        <span class="category-icon">🖥️</span>
        <span>Placas de video</span>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card category-card">
        <span class="category-icon">💾</span>
        <span>Memorias RAM</span>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card category-card">
        <span class="category-icon">🗄️</span>
        <span>Almacenamiento</span>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card category-card">
        <span class="category-icon">⌨️</span>
        <span>Periféricos</span>
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card category-card">
        <span class="category-icon">🎧</span>
        <span>Accesorios</span>
      </div>
    </div>
  </div>
</section>

<!-- Productos destacados -->
<section id="productos" class="mb-5">
  <h2 class="h4 mb-3">Productos destacados</h2>
  <div class="row g-3">

    <div class="col-6 col-md-4 col-lg-3">
      <div class="card product-card">
        <div class="product-thumb">🧠</div>
        <div class="card-body">
          <span class="badge badge-stock mb-2">En stock</span>
          <h3 class="h6 card-title mb-1">Procesador Ryzen 5 5600</h3>
          <p class="price mb-2">$ 120.000</p>
          <button class="btn btn-accent btn-sm w-100">Agregar al carrito</button>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-4 col-lg-3">
      <div class="card product-card">
        <div class="product-thumb">🖥️</div>
        <div class="card-body">
          <span class="badge badge-stock mb-2">En stock</span>
          <h3 class="h6 card-title mb-1">Placa de video RTX 4060</h3>
          <p class="price mb-2">$ 480.000</p>
          <button class="btn btn-accent btn-sm w-100">Agregar al carrito</button>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-4 col-lg-3">
      <div class="card product-card">
        <div class="product-thumb">💾</div>
        <div class="card-body">
          <span class="badge badge-stock mb-2">En stock</span>
          <h3 class="h6 card-title mb-1">Memoria RAM 16GB DDR4</h3>
          <p class="price mb-2">$ 45.000</p>
          <button class="btn btn-accent btn-sm w-100">Agregar al carrito</button>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-4 col-lg-3">
      <div class="card product-card">
        <div class="product-thumb">⌨️</div>
        <div class="card-body">
          <span class="badge badge-stock mb-2">En stock</span>
          <h3 class="h6 card-title mb-1">Teclado mecánico RGB</h3>
          <p class="price mb-2">$ 38.000</p>
          <button class="btn btn-accent btn-sm w-100">Agregar al carrito</button>
        </div>
      </div>
    </div>

  </div>
  <p class="text-secondary small mt-3">
    * Productos de ejemplo — reemplazar por los datos reales cuando esté conectada la base de datos.
  </p>
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
      <p class="text-secondary small">Tarjetas, transferencia y más.</p>
    </div>
  </div>
</section>

<?php include('./_layouts/footer.php'); ?>
