<?php
/**
 * "Finalizar" del armador de PC: valida la configuración elegida, agrega los componentes
 * al carrito y redirige al carrito.
 *
 * Seguridad: solo POST + CSRF + sesión. NUNCA se confía en lo que manda el navegador:
 *   - cada id debe ser un UUID válido y existir en la base,
 *   - el producto tiene que pertenecer a la categoría del componente donde se eligió
 *     (no se puede colar un mouse como "Procesador"),
 *   - tiene que haber stock,
 *   - los componentes obligatorios no pueden faltar.
 * Es idempotente: si el producto ya estaba en el carrito, no se duplica la cantidad.
 */
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../lib/builder.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/src/views/builder.php');
}
csrf_require(false, '/src/views/builder.php');

$slots   = config('builder.slots');
$options = builder_load_options($pdo);
$posted  = is_array($_POST['components'] ?? null) ? $_POST['components'] : [];

// id => producto (con su categoría), para validar contra la base en O(1)
$byId = [];
foreach ($options as $products) {
    foreach ($products as $p) {
        $byId[$p['id']] = $p;
    }
}

$errors    = [];
$selection = [];   // slot => [ids válidos]  (se devuelve al formulario si hay errores)

foreach ($slots as $slot) {
    $raw = $posted[$slot['key']] ?? [];
    $raw = is_array($raw) ? $raw : [$raw];

    $ids = [];
    foreach ($raw as $id) {
        if (is_string($id) && is_uuid($id)) {   // el "Ninguno" (valor vacío) y la basura se descartan acá
            $ids[$id] = true;
        }
    }
    $ids = array_keys($ids);
    if (empty($slot['multiple'])) {
        $ids = array_slice($ids, 0, 1);
    }

    foreach ($ids as $id) {
        if (!isset($byId[$id]) || $byId[$id]['category_name'] !== $slot['category']) {
            $errors[] = "La opción elegida en «{$slot['label']}» no es válida.";
        } elseif ((int) $byId[$id]['stock'] < 1) {
            $errors[] = "«{$byId[$id]['name']}» se quedó sin stock. Elegí otra opción en {$slot['label']}.";
        } else {
            $selection[$slot['key']][] = $id;
        }
    }

    if (empty($selection[$slot['key']]) && builder_slot_is_required($slot, $options)) {
        $errors[] = "Falta elegir: {$slot['label']}.";
    }
}

if (!$errors && !$selection) {
    $errors[] = 'Elegí al menos un componente para armar tu PC.';
}

if ($errors) {
    flash_set('errors', array_values(array_unique($errors)));
    flash_set('build_old', $selection);        // conserva lo que el usuario ya había elegido
    redirect('/src/views/builder.php');
}

$count = 0;
foreach ($selection as $ids) {
    foreach ($ids as $id) {
        $_SESSION['cart'][$id] = max((int) ($_SESSION['cart'][$id] ?? 0), 1);
        $count++;
    }
}

flash_set('notice', "Agregamos $count componente" . ($count === 1 ? '' : 's') . ' de tu PC al carrito. Revisá el resumen y pagá cuando quieras.');
redirect('/src/views/cart.php');
