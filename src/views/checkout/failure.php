<?php
require_once __DIR__ . '/../_layouts/layout.php';

$error = flash_get('mp_error'); // detalle técnico (se muestra solo en modo debug)
?>

<div class="text-center py-5">
  <div style="font-size:4rem;">❌</div>
  <h1 class="h3 mt-3">El pago no se pudo procesar</h1>
  <p class="text-secondary">
    Algo falló al intentar procesar el pago con Mercado Pago. Podés volver a intentarlo.
  </p>
  <?php if ($error && config('app.debug')): ?>
    <p class="text-danger small">Detalle técnico: <?= e($error) ?></p>
  <?php endif; ?>
  <a href="<?= e(url('/src/views/cart.php')) ?>" class="btn btn-accent mt-3">Volver al carrito</a>
</div>

<?php require_once __DIR__ . '/../_layouts/footer.php'; ?>
