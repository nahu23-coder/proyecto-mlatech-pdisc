<?php
/**
 * Integración con Checkout Pro de Mercado Pago (API REST vía cURL, sin SDK).
 * Toda la configuración (token, moneda, URLs, modo prueba) sale de config('mercadopago').
 */

/**
 * Crea una preferencia de pago.
 *
 * @param array  $items       Lista de ['title' => string, 'quantity' => int, 'unit_price' => float].
 * @param string $externalRef Referencia propia (id del usuario) para poder identificar el pago después.
 * @return array ['ok' => bool, 'init_point' => string|null, 'error' => string|null]
 */
function mp_create_preference(array $items, $externalRef)
{
    $cfg = config('mercadopago');

    if ($cfg['access_token'] === '') {
        return ['ok' => false, 'init_point' => null, 'error' => 'Falta configurar MP_ACCESS_TOKEN en el .env'];
    }

    $mpItems = array_map(function ($item) use ($cfg) {
        return [
            'title'       => $item['title'],
            'quantity'    => (int) $item['quantity'],
            'unit_price'  => (float) $item['unit_price'],
            'currency_id' => $cfg['currency'],
        ];
    }, $items);

    $siteUrl = config('app.site_url');
    $body = [
        'items'              => $mpItems,
        'external_reference' => $externalRef,
        'back_urls'          => array_map(function ($path) use ($siteUrl) {
            return $siteUrl . $path;
        }, $cfg['back_urls']),
        'auto_return'        => 'approved',
    ];

    $res = http_post_json($cfg['api_url'], ['Authorization: Bearer ' . $cfg['access_token']], $body);

    if ($res['error'] !== null) {
        error_log('[mercadopago] ' . $res['error']);
        return ['ok' => false, 'init_point' => null, 'error' => 'No se pudo conectar con Mercado Pago.'];
    }

    $data = $res['data'];
    if ($res['status'] >= 200 && $res['status'] < 300 && isset($data['init_point'])) {
        // sandbox_init_point sirve para probar con usuarios/tarjetas de prueba
        $point = ($cfg['use_sandbox'] && !empty($data['sandbox_init_point'])) ? $data['sandbox_init_point'] : $data['init_point'];
        return ['ok' => true, 'init_point' => $point, 'error' => null];
    }

    error_log('[mercadopago] HTTP ' . $res['status'] . ': ' . json_encode($data));
    return ['ok' => false, 'init_point' => null, 'error' => $data['message'] ?? 'Mercado Pago rechazó la preferencia.'];
}
