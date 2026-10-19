<?php
require_once __DIR__ . '/_layouts/layout.php';

// Chips de filtro. La búsqueda real la hace search.php y products-search.js dibuja el resultado;
// el texto y la categoría iniciales se leen desde la URL (?q= / ?categoria=).
$categorias = $pdo->query('SELECT name, icon FROM categories ORDER BY name')->fetchAll();
?>

<h1 class="h3 mb-4">Productos</h1>

<div class="d-flex flex-wrap gap-2 mb-4" id="filterChips">
  <button type="button" class="btn btn-sm filter-chip active" data-categoria="">Todas</button>
  <?php foreach ($categorias as $cat): ?>
    <button type="button" class="btn btn-sm filter-chip" data-categoria="<?= e($cat['name']) ?>">
      <?= e($cat['icon']) ?> <?= e($cat['name']) ?>
    </button>
  <?php endforeach; ?>
</div>

<!-- Indicador de carga: lo muestra/oculta products-search.js mientras espera al servidor -->
<div id="loadingIndicator" class="text-center my-4 d-none" role="status">
  <div class="spinner-border text-warning" aria-hidden="true"></div>
  <span class="visually-hidden">Cargando productos...</span>
</div>

<div class="row g-3" id="productsGrid" aria-live="polite"></div>

<p id="noResults" class="text-secondary text-center mt-4 d-none">No se encontraron productos.</p>

<?php
$pageScripts = ['/assets/js/products-search.js'];
require_once __DIR__ . '/_layouts/footer.php';
