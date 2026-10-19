<?php
require_once __DIR__ . '/_layouts/layout.php';

$items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    // Una sola consulta trae todos los productos del carrito (sin N+1).
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare(
        "SELECT p.id, p.name, p.price, p.image_url,
                c.name AS category_name, c.icon AS category_icon
         FROM products p
         JOIN categories c ON c.id = p.category_id
         WHERE p.id IN ($placeholders)"
    );
    $stmt->execute($ids);

    $found = [];
    foreach ($stmt->fetchAll() as $p) {
        $qty      = (int) $_SESSION['cart'][$p['id']];
        $subtotal = $p['price'] * $qty;
        $total   += $subtotal;
        $items[]  = ['producto' => $p, 'qty' => $qty, 'subtotal' => $subtotal];
        $found[$p['id']] = $qty;
    }

    // Descartamos del carrito los ids que ya no existen en la base (productos borrados).
    $_SESSION['cart'] = $found;
}
$maxQty = (int) config('cart.max_qty_per_item');
?>

<h1 class="h3 mb-4">Tu carrito</h1>

<?php if (empty($items)): ?>
  <p class="text-secondary">Todavía no agregaste productos. <a href="<?= e(url('/src/views/products.php')) ?>">Ver productos</a></p>
<?php else: ?>
  <div class="table-responsive mb-4">
    <table class="table table-dark align-middle">
      <thead>
        <tr>
          <th>Producto</th>
          <th>Categoría</th>
          <th>Precio</th>
          <th>Cantidad</th>
          <th>Subtotal</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): $p = $item['producto']; ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-3">
                <?php if (!empty($p['image_url'])): ?>
                  <div class="cart-thumb">
                    <img src="<?= e(url($p['image_url'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
                  </div>
                <?php else: ?>
                  <div class="cart-thumb no-image"><?= e($p['category_icon']) ?></div>
                <?php endif; ?>
                <span><?= e($p['name']) ?></span>
              </div>
            </td>
            <td><?= e($p['category_name']) ?></td>
            <td><?= e(format_price($p['price'])) ?></td>
            <td>
              <form action="<?= e(url('/src/controllers/cart/update.php')) ?>" method="POST" class="d-flex gap-1">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= e($p['id']) ?>">
                <input type="number" name="qty" value="<?= (int) $item['qty'] ?>" min="1" max="<?= $maxQty ?>"
                       class="form-control form-control-sm" style="width:70px" aria-label="Cantidad">
                <button type="submit" class="btn btn-outline-accent btn-sm">Actualizar</button>
              </form>
            </td>
            <td class="price"><?= e(format_price($item['subtotal'])) ?></td>
            <td>
              <form action="<?= e(url('/src/controllers/cart/remove.php')) ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= e($p['id']) ?>">
                <button type="submit" class="btn btn-outline-danger btn-sm">Quitar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="d-flex justify-content-between align-items-center">
    <h2 class="h5 mb-0">Total: <span class="price"><?= e(format_price($total)) ?></span></h2>
    <form action="<?= e(url('/src/controllers/checkout/create_preference.php')) ?>" method="POST">
      <?= csrf_field() ?>
      <button type="submit" class="btn btn-accent btn-lg">Pagar con Mercado Pago</button>
    </form>
  </div>

  <?php if (config('mercadopago.test_unit_price') !== null): ?>
    <p class="text-secondary small mt-3">
      * Modo prueba: cada producto se cobra a <?= e(format_price(config('mercadopago.test_unit_price'))) ?> en Mercado Pago (sandbox).
    </p>
  <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/_layouts/footer.php'; ?>
