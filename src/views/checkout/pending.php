<?php require_once __DIR__ . '/../_layouts/layout.php'; ?>

<div class="text-center py-5">
  <div style="font-size:4rem;">⏳</div>
  <h1 class="h3 mt-3">Pago pendiente</h1>
  <p class="text-secondary">
    Mercado Pago todavía está procesando el pago (por ejemplo, si se pagó con un medio offline).
  </p>
  <a href="<?= e(url('/src/views/products.php')) ?>" class="btn btn-accent mt-3">Volver a la tienda</a>
</div>

<?php require_once __DIR__ . '/../_layouts/footer.php'; ?>
