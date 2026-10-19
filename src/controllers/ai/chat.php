<?php
/**
 * Asistente virtual. Recibe un mensaje y responde JSON {"reply": "..."}.
 *
 * Estrategia en 3 niveles:
 *   1) API de IA (si hay AI_API_KEY en el .env)
 *   2) FAQ por palabras clave (src/config/faq.php)
 *   3) Mensaje genérico que deriva a Contacto
 *
 * La clave de la API vive solo en el servidor; el navegador nunca la ve.
 */
require_once __DIR__ . '/../../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['reply' => 'Método no permitido.'], 405);
}
require_login(true);
csrf_require(true);

$maxChars = config('ai.max_chars');
$message  = input_string($_POST, 'message', $maxChars);

if ($message === '') {
    json_response(['reply' => 'Escribime tu consulta y te ayudo.']);
}

// Cada llamada a la IA cuesta dinero: limitamos la frecuencia por sesión.
if (!rate_limit_allow('chat', config('ai.rate_max'), config('ai.rate_window'))) {
    json_response(['reply' => 'Estás escribiendo muy rápido. Esperá un momento y volvé a intentar.'], 429);
}

// Liberamos el bloqueo de la sesión: la llamada externa puede tardar y no debe trabar
// el resto de las pestañas/requests del mismo usuario.
session_write_close();

/** Nivel 2: respuesta por palabras clave (coincidencia de palabra completa). */
function faq_reply($message)
{
    $text = mb_strtolower($message);

    foreach (require __DIR__ . '/../../config/faq.php' as $entry) {
        foreach ($entry['keywords'] as $keyword) {
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/u', $text)) {
                return $entry['respuesta'];
            }
        }
    }
    return null;
}

/** Nivel 1: respuesta de la API de IA. Devuelve null si no está configurada o falla. */
function ai_reply($message)
{
    $cfg = config('ai');
    if ($cfg['api_key'] === '') {
        return null;
    }

    $res = http_post_json(
        $cfg['api_url'],
        ['x-api-key: ' . $cfg['api_key'], 'anthropic-version: 2023-06-01'],
        [
            'model'      => $cfg['model'],
            'max_tokens' => $cfg['max_tokens'],
            'system'     => $cfg['system_prompt'],
            'messages'   => [['role' => 'user', 'content' => $message]],
        ],
        $cfg['timeout']
    );

    if ($res['error'] !== null || $res['status'] < 200 || $res['status'] >= 300) {
        error_log('[chat] IA no disponible (HTTP ' . $res['status'] . ') ' . ($res['error'] ?? ''));
        return null;
    }

    return $res['data']['content'][0]['text'] ?? null;
}

$reply = ai_reply($message)
    ?? faq_reply($message)
    ?? 'No tengo una respuesta puntual para eso, pero podés escribirnos desde la sección Contacto y nuestro equipo te responde a la brevedad.';

json_response(['reply' => $reply]);
