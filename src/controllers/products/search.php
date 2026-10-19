<?php
/**
 * Búsqueda de productos. Responde JSON (no escribe nada en disco).
 *
 * GET: q (texto libre), categoria (nombre exacto de la categoría)
 *
 * Cada palabra del texto debe aparecer en nombre, descripción o categoría (AND entre palabras),
 * así "placa video" o "ryzen 5" encuentran lo esperado.
 */
require_once __DIR__ . '/../../config/bootstrap.php';

require_login(true);

$q         = input_string($_GET, 'q', 100);
$categoria = input_string($_GET, 'categoria', 50);

$sql = 'SELECT p.id, p.name, p.description, p.price, p.stock, p.image_url,
               c.name AS category_name, c.icon AS category_icon
        FROM products p
        JOIN categories c ON c.id = p.category_id
        WHERE 1 = 1';
$params = [];

if ($q !== '') {
    $words = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY);
    $words = array_slice($words, 0, config('search.max_words'));

    foreach ($words as $i => $word) {
        // Plural -> singular simple ("procesadores" -> "procesador")
        $len = strlen($word);
        if ($len > 4 && substr($word, -2) === 'es') {
            $word = substr($word, 0, -2);
        } elseif ($len > 3 && substr($word, -1) === 's') {
            $word = substr($word, 0, -1);
        }

        // Escapamos \ % _ para que el usuario no pueda usarlos como comodines.
        $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $word) . '%';

        // Una sola comparación por palabra sobre las 3 columnas concatenadas
        // (antes eran 3 parámetros y 3 LIKE por palabra).
        $sql .= " AND CONCAT_WS(' ', p.name, p.description, c.name) LIKE :w{$i}";
        $params[":w{$i}"] = $like;
    }
}

if ($categoria !== '') {
    $sql .= ' AND c.name = :categoria';
    $params[':categoria'] = $categoria;
}

$sql .= ' ORDER BY c.name, p.name LIMIT ' . (int) config('search.max_results');

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('[search] ' . $e->getMessage());
    json_response(['success' => false, 'error' => 'No se pudo realizar la búsqueda.'], 500);
}

// PDO devuelve DECIMAL/INT como string: los pasamos a número y armamos la URL de la imagen.
foreach ($products as &$p) {
    $p['price']     = (float) $p['price'];
    $p['stock']     = (int) $p['stock'];
    $p['image_url'] = $p['image_url'] ? url($p['image_url']) : null;
}
unset($p);

json_response([
    'success'   => true,
    'query'     => $q,
    'categoria' => $categoria,
    'total'     => count($products),
    'products'  => $products,
]);
