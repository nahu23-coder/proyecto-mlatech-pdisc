<?php
/**
 * Lógica del armador de PC ("Arma tu PC").
 * La definición de los componentes vive en config('builder.slots') (src/config/app.php).
 */

/**
 * Trae de la base todos los productos de las categorías que usa el armador,
 * agrupados por nombre de categoría y ordenados de menor a mayor precio.
 * Una sola consulta para todo el armador (sin N+1).
 *
 * @return array [ 'Procesadores' => [ [id, name, description, price, stock, image_url], ... ], ... ]
 */
function builder_load_options(PDO $pdo)
{
    $names = array_values(array_unique(array_column(config('builder.slots'), 'category')));
    $placeholders = implode(',', array_fill(0, count($names), '?'));

    $stmt = $pdo->prepare(
        "SELECT p.id, p.name, p.description, p.price, p.stock, p.image_url, c.name AS category_name
         FROM products p
         JOIN categories c ON c.id = p.category_id
         WHERE c.name IN ($placeholders)
         ORDER BY p.price ASC"
    );
    $stmt->execute($names);

    $grouped = [];
    foreach ($stmt->fetchAll() as $row) {
        $grouped[$row['category_name']][] = $row;
    }
    
    return $grouped;
}

/** ¿Hay al menos un producto con stock en la lista de opciones? */
function builder_has_stock(array $products)
{
    foreach ($products as $p) {
        if ((int) $p['stock'] > 0) {
            return true;
        }
    }
    return false;
}

/**
 * Un componente es obligatorio solo si está marcado como required Y hay algo para elegir.
 * (Evita que el armador quede trabado si una categoría todavía no tiene productos con stock.)
 */
function builder_slot_is_required(array $slot, array $options)
{
    return !empty($slot['required']) && builder_has_stock($options[$slot['category']] ?? []);
}
