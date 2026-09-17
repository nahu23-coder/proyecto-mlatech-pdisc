<?php
require_once __DIR__ . '/../../config/bootstrap.php';

try {
    $query = '
        SELECT * 
        from products;
    ';

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id' => $_GET['id'],
    ]);

    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    require_once __DIR__ . '/../../views/posts/show.php';
} catch (PDOException $e) {
    exit;
}