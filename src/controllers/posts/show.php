<?php
require_once __DIR__ . '/../../config/bootstrap.php';

try {
    $query = '
        SELECT
            p.*,
            c.id AS category_id,
            c.name AS category_name,
            c.icon AS category_icon
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.id = :id
    ';

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id' => $_GET['id'],
    ]);

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    require_once __DIR__ . '/../../views/products/show.php';
} catch (PDOException $e) {
    exit('Error al consultar el producto');
}