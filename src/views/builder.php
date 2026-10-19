<?php
/**
 * "Arma tu PC": una sección por cada componente (definidos en config('builder.slots')).
 * El usuario elige uno por sección y "Finalizar" manda todo a cart/add_build.php, que lo
 * valida, lo agrega al carrito y abre el carrito.
 *
 * Funciona aunque falle el JS (son radios/checkbox dentro de un form normal);
 * assets/js/builder.js solo suma el resumen en vivo y el total.
 */
require_once __DIR__ . '/_layouts/layout.php';
require_once __DIR__ . '/../lib/builder.php';

$slots   = config('builder.slots');
$options = builder_load_options($pdo);

// Si el servidor rechazó la configuración, se vuelve a marcar lo que ya había elegido.
$selected = [];
foreach ((array) flash_get('build_old', []) as $ids) {
    foreach ((array) $ids as $id) {
        $selected[$id] = true;
    }
}
?>

<h1 class="h3 mb-2">Arma tu PC</h1>
<p class="text-secondary mb-4">
  Elegí cada componente y mirá el total en tiempo real. Al finalizar, se abre el carrito con el resumen de tu configuración.
</p>

<div class="alert alert-info small" role="note">
  <strong>Tip de compatibilidad:</strong> el socket del procesador (AM4, AM5, LGA1700) tiene que coincidir con el de la
  motherboard, y la RAM tiene que ser del tipo que admite (DDR4 / DDR5). Cada producto indica sus datos en la descripción.
</div>

<form id="builderForm" action="<?= e(url('/src/controllers/cart/add_build.php')) ?>" method="POST" novalidate>
  <?= csrf_field() ?>

  <div class="row g-4">
    <!-- Componentes -->
    <div class="col-lg-8">
      <?php foreach ($slots as $n => $slot):
          $list     = $options[$slot['category']] ?? [];
          $required = builder_slot_is_required($slot, $options);
          $multiple = !empty($slot['multiple']);
          $type     = $multiple ? 'checkbox' : 'radio';
          $field    = 'components[' . $slot['key'] . ']' . ($multiple ? '[]' : '');

          $hasSelection = false;
          foreach ($list as $p) {
              if (isset($selected[$p['id']])) { $hasSelection = true; break; }
          }
      ?>
        <section class="card build-slot mb-4" id="slot-<?= e($slot['key']) ?>" data-slot-section
                 data-key="<?= e($slot['key']) ?>" data-label="<?= e($slot['label']) ?>"
                 data-required="<?= $required ? '1' : '0' ?>">
          <div class="card-header d-flex align-items-center gap-2 flex-wrap">
            <span class="slot-icon" aria-hidden="true"><?= e($slot['icon']) ?></span>
            <h2 class="h5 mb-0"><?= $n + 1 ?>. <?= e($slot['label']) ?></h2>
            <span class="badge <?= $required ? 'text-bg-warning' : 'badge-stock' ?>"><?= $required ? 'Obligatorio' : 'Opcional' ?></span>
            <span class="slot-done ms-auto small text-warning d-none">✓ Elegido</span>
          </div>

          <div class="card-body">
            <p class="text-secondary small mb-3"><?= e($slot['hint']) ?></p>

            <?php if (!$list): ?>
              <p class="text-secondary mb-0">Todavía no cargamos productos de esta categoría.</p>
            <?php else: ?>
              <div class="row g-3">
                <?php if (!$required && !$multiple): ?>
                  <div class="col-12">
                    <label class="build-option build-none">
                      <input type="radio" class="build-input visually-hidden" name="<?= e($field) ?>" value=""
                             <?= $hasSelection ? '' : 'checked' ?>>
                      <span class="build-card py-2">Sin este componente</span>
                    </label>
                  </div>
                <?php endif; ?>

                <?php foreach ($list as $p):
                    $out = (int) $p['stock'] < 1;
                    $img = $p['image_url'] ? url($p['image_url']) : '';
                ?>
                  <div class="col-12 col-md-6">
                    <label class="build-option<?= $out ? ' is-out' : '' ?>">
                      <input type="<?= $type ?>" class="build-input visually-hidden"
                             name="<?= e($field) ?>" value="<?= e($p['id']) ?>"
                             data-name="<?= e($p['name']) ?>" data-price="<?= e($p['price']) ?>"
                             <?= isset($selected[$p['id']]) ? 'checked' : '' ?> <?= $out ? 'disabled' : '' ?>>
                      <span class="build-card">
                        <span class="cart-thumb<?= $img === '' ? ' no-image' : '' ?>" data-icon="<?= e($slot['icon']) ?>">
                          <?php if ($img !== ''): ?>
                            <img src="<?= e($img) ?>" alt="" loading="lazy">
                          <?php else: ?>
                            <?= e($slot['icon']) ?>
                          <?php endif; ?>
                        </span>
                        <span class="build-info">
                          <span class="build-name"><?= e($p['name']) ?></span>
                          <span class="build-desc"><?= e($p['description']) ?></span>
                          <span class="d-flex align-items-center gap-2 mt-1">
                            <span class="price"><?= e(format_price($p['price'])) ?></span>
                            <span class="badge badge-stock"><?= $out ? 'Sin stock' : 'En stock' ?></span>
                          </span>
                        </span>
                        <span class="build-check" aria-hidden="true">✓</span>
                      </span>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </section>
      <?php endforeach; ?>
    </div>

    <!-- Resumen (lo completa builder.js) -->
    <div class="col-lg-4">
      <aside class="card build-summary" aria-live="polite">
        <div class="card-body">
          <h2 class="h5 mb-3">Tu configuración</h2>

          <ul id="summaryList" class="list-unstyled mb-3"></ul>
          <p id="summaryMissing" class="small text-secondary mb-3">Elegí tus componentes para ver el resumen.</p>

          <div class="d-flex justify-content-between align-items-center border-top border-secondary-subtle pt-3 mb-3">
            <strong>Total</strong>
            <span class="price fs-5" id="summaryTotal">$ 0</span>
          </div>

          <button type="submit" id="finishBtn" class="btn btn-accent w-100">Finalizar y ver carrito</button>
          <button type="button" id="clearBtn" class="btn btn-link btn-sm w-100 mt-2 text-secondary">Limpiar selección</button>
        </div>
      </aside>
    </div>
  </div>
</form>

<?php
$pageScripts = ['/assets/js/builder.js'];
require_once __DIR__ . '/_layouts/footer.php';
