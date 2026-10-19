<?php
/**
 * Crea la preferencia de pago desde el carrito y redirige a Mercado Pago.
 * Los precios se toman SIEMPRE de la base de datos (nunca del navegador).
 */
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/mercadopago.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/src/views/cart.php');
}
csrf_require(false, '/src/views/cart.php');

if (empty($_SESSION['cart'])) {
    redirect('/src/views/cart.php');
}

$cart = $_SESSION['cart'];
$ids  = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);

$testPrice = config('mercadopago.test_unit_price'); // null = precio real
$items = [];
foreach ($stmt->fetchAll() as $p) {
    $items[] = [
        'title'      => $p['name'],
        'quantity'   => (int) $cart[$p['id']],
        'unit_price' => $testPrice !== null ? (float) $testPrice : (float) $p['price'],
    ];
}

if (!$items) {
    redirect('/src/views/cart.php');
}

$result = mp_create_preference($items, current_user_id());

if ($result['ok']) {
    // El carrito se vacía recién al volver con pago aprobado (ver success.php).
    header('Location: ' . $result['init_point']);
    exit;
}

flash_set('mp_error', $result['error']);
redirect('/src/views/checkout/failure.php');
