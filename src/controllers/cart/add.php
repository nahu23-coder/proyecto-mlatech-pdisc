<?php
/**
 * Agrega un producto al carrito (AJAX, responde JSON).
 * Valida login, CSRF, formato del id, existencia del producto y stock disponible.
 */
require_once __DIR__ . '/../../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Método no permitido'], 405);
}
require_login(true);
csrf_require(true);

$productId = input_string($_POST, 'product_id', 36);
$qty       = max(1, (int) ($_POST['qty'] ?? 1));

if (!is_uuid($productId)) {
    json_response(['success' => false, 'error' => 'Producto inválido'], 422);
}

$stmt = $pdo->prepare('SELECT stock FROM products WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $productId]);
$product = $stmt->fetch();

if (!$product) {
    json_response(['success' => false, 'error' => 'El producto no existe'], 404);
}

$limit    = min((int) $product['stock'], config('cart.max_qty_per_item'));
$newTotal = ($_SESSION['cart'][$productId] ?? 0) + $qty;

if ($limit < 1) {
    json_response(['success' => false, 'error' => 'Producto sin stock'], 409);
}
if ($newTotal > $limit) {
    json_response(['success' => false, 'error' => "Máximo disponible por compra: $limit unidades"], 409);
}

$_SESSION['cart'][$productId] = $newTotal;

json_response(['success' => true, 'cart_count' => array_sum($_SESSION['cart'])]);
