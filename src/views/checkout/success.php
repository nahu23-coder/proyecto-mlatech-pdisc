<?php
require_once __DIR__ . '/../_layouts/layout.php';

// Mercado Pago vuelve con ?status=approved&payment_id=... Solo vaciamos el carrito si el pago figura aprobado.
// OJO: estos parámetros vienen del navegador y se pueden falsificar. Para confirmar de verdad un pago
// hay que usar un webhook (notification_url) o consultar GET /v1/payments/{id} con el access token.
$status    = input_string($_GET, 'status', 20);
$paymentId = input_string($_GET, 'payment_id', 30);
$approved  = ($status === 'approved');

if ($approved) {
    $_SESSION['cart'] = [];
}
?>

<div class="text-center py-5">
  <?php if ($approved): ?>
    <div style="font-size:4rem;">✅</div>
    <h1 class="h3 mt-3">¡Pago aprobado!</h1>
    <p class="text-secondary">
      Tu compra se procesó correctamente con Mercado Pago.
      Payment ID: <?= e($paymentId !== '' ? $paymentId : '—') ?>
    </p>
  <?php else: ?>
    <div style="font-size:4rem;">ℹ️</div>
    <h1 class="h3 mt-3">No encontramos un pago aprobado</h1>
    <p class="text-secondary">Si ya pagaste, puede demorar unos minutos en acreditarse.</p>
  <?php endif; ?>
  <a href="<?= e(url('/src/views/products.php')) ?>" class="btn btn-accent mt-3">Seguir comprando</a>
</div>

<?php require_once __DIR__ . '/../_layouts/footer.php'; ?>
