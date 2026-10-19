<?php
/**
 * Cliente HTTP mínimo (cURL) compartido por Mercado Pago y el asistente de IA.
 */

/**
 * Hace un POST con cuerpo JSON y devuelve la respuesta decodificada.
 *
 * @param string $url     URL destino.
 * @param array  $headers Cabeceras extra (ej: ['Authorization: Bearer ...']).
 * @param array  $body    Datos que se envían como JSON.
 * @param int    $timeout Segundos máximos de espera.
 * @return array ['status' => int, 'data' => array|null, 'error' => string|null]
 */
function http_post_json($url, array $headers, array $body, $timeout = 15)
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => array_merge(['Content-Type: application/json'], $headers),
        CURLOPT_POSTFIELDS     => json_encode($body, JSON_UNESCAPED_UNICODE),
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $raw    = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error  = curl_error($ch);

    if ($raw === false) {
        return ['status' => 0, 'data' => null, 'error' => $error ?: 'Error de red'];
    }

    $data = json_decode($raw, true);
    return ['status' => $status, 'data' => is_array($data) ? $data : null, 'error' => null];
}
