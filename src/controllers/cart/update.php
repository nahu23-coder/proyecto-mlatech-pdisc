<?php
/**
 * Cambia la cantidad de un producto del carrito (formulario POST).
 */
require_once __DIR__ . '/../../config/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require(false, '/src/views/cart.php');

    $productId = input_string($_POST, 'product_id', 36);
    $qty       = max(1, (int) ($_POST['qty'] ?? 1));

    if (is_uuid($productId) && isset($_SESSION['cart'][$productId])) {
        $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = :id');
        $stmt->execute([':id' => $productId]);
        $stock = (int) $stmt->fetchColumn();

        $limit = min($stock, config('cart.max_qty_per_item'));
        if ($limit < 1) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId] = min($qty, $limit);
            if ($qty > $limit) {
                flash_set('errors', ["Solo hay $limit unidades disponibles."]);
            }
        }
    }
}

redirect('/src/views/cart.php');
