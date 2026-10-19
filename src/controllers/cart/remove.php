<?php
/**
 * Quita un producto del carrito.
 * Antes era un link GET (cualquiera podía quitar productos con solo hacerte abrir una URL);
 * ahora es POST con token CSRF.
 */
require_once __DIR__ . '/../../config/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require(false, '/src/views/cart.php');
    $productId = input_string($_POST, 'product_id', 36);
    unset($_SESSION['cart'][$productId]);
}

redirect('/src/views/cart.php');
