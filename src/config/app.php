<?php
/**
 * Configuración de la aplicación.
 *
 * Todo lo que antes estaba "fijo" dentro de funciones vive acá y puede
 * sobrescribirse desde el .env. Se lee con config('seccion.clave').
 */
return [
    'app' => [
        'name'     => env('APP_NAME', 'MLA Tech'),
        'timezone' => env('APP_TIMEZONE', 'America/Argentina/Buenos_Aires'),
        'site_url' => rtrim(env('SITE_URL', 'http://localhost:8000'), '/'),
        'debug'    => env_bool('APP_DEBUG', false), // true = muestra detalles técnicos al usuario
    ],

    'auth' => [
        'password_min'  => 8,
        'password_max'  => 72,   // bcrypt ignora todo lo que pase de 72 bytes
        'login_attempts' => 5,   // intentos de login...
        'login_window'   => 300, // ...por ventana de N segundos
    ],

    'cart' => [
        'max_qty_per_item' => 10,
    ],

    'mercadopago' => [
        'access_token'    => env('MP_ACCESS_TOKEN', ''),
        'currency'        => env('MP_CURRENCY', 'ARS'),
        // Vacío = se cobra el precio real de la DB. Un número (ej: 1) fuerza ese precio en pruebas.
        'test_unit_price' => env('MP_TEST_UNIT_PRICE'),
        'use_sandbox'     => env_bool('MP_SANDBOX', true),
        'api_url'         => 'https://api.mercadopago.com/checkout/preferences',
        'back_urls'       => [
            'success' => '/src/views/checkout/success.php',
            'failure' => '/src/views/checkout/failure.php',
            'pending' => '/src/views/checkout/pending.php',
        ],
    ],

    'ai' => [
        'api_key'    => env('AI_API_KEY', ''),
        'api_url'    => env('AI_API_URL', 'https://api.anthropic.com/v1/messages'),
        'model'      => env('AI_MODEL', 'claude-haiku-4-5-20251001'),
        'max_tokens' => (int) env('AI_MAX_TOKENS', '300'),
        'timeout'    => 15,
        'max_chars'  => 500,  // largo máximo del mensaje del usuario
        'rate_max'   => 10,   // mensajes...
        'rate_window' => 60,  // ...por minuto
        'system_prompt' => 'Sos el asistente de soporte de ' . env('APP_NAME', 'MLA Tech') . ', una tienda online de '
            . 'componentes de PC, periféricos y accesorios. Respondé en español, de forma breve y amable '
            . '(máximo 3 líneas). Si te preguntan algo que no tiene que ver con la tienda, respondé igual '
            . 'con amabilidad pero aclarando que sos un asistente de soporte de ' . env('APP_NAME', 'MLA Tech') . '.',
    ],

    'contact' => [
        'name_max'    => 100,
        'email_max'   => 150,
        'subject_max' => 150,
        'message_max' => 2000,
        'rate_max'    => 5,
        'rate_window' => 600,
    ],

    /*
     * Armador de PC ("Arma tu PC"). Cada "slot" es un componente que necesita una PC.
     *   key       identificador (se usa en el formulario)
     *   category  nombre EXACTO de la categoría de la base de la que salen las opciones
     *   required  true = hay que elegirlo para poder finalizar (se ignora si la categoría no tiene stock)
     *   multiple  true = se pueden elegir varios (checkbox); false = uno solo (radio)
     * Para agregar un componente nuevo solo hay que sumar una entrada acá.
     */
    'builder' => [
        'slots' => [
            ['key' => 'cpu',         'label' => 'Procesador',        'icon' => '🧠', 'category' => 'Procesadores',     'required' => true,  'multiple' => false, 'hint' => 'El cerebro de tu PC. Fijate el socket: tiene que coincidir con la motherboard.'],
            ['key' => 'motherboard', 'label' => 'Motherboard',       'icon' => '🧩', 'category' => 'Motherboards',     'required' => true,  'multiple' => false, 'hint' => 'Conecta todo. Verificá socket (AM4, AM5, LGA1700) y tipo de memoria (DDR4/DDR5).'],
            ['key' => 'ram',         'label' => 'Memoria RAM',       'icon' => '💾', 'category' => 'Memorias RAM',     'required' => true,  'multiple' => false, 'hint' => 'Tiene que ser del mismo tipo que admite tu motherboard.'],
            ['key' => 'gpu',         'label' => 'Placa de video',    'icon' => '🎮', 'category' => 'Placas de video',  'required' => false, 'multiple' => false, 'hint' => 'Opcional si tu procesador trae video integrado. Imprescindible para gaming.'],
            ['key' => 'storage',     'label' => 'Almacenamiento',    'icon' => '💽', 'category' => 'Almacenamiento',   'required' => true,  'multiple' => false, 'hint' => 'Donde se guardan el sistema y tus archivos.'],
            ['key' => 'psu',         'label' => 'Fuente de poder',   'icon' => '⚡', 'category' => 'Fuentes de poder', 'required' => true,  'multiple' => false, 'hint' => 'Elegí potencia de sobra, sobre todo si sumás placa de video.'],
            ['key' => 'case',        'label' => 'Gabinete',          'icon' => '🖥️', 'category' => 'Gabinetes',        'required' => true,  'multiple' => false, 'hint' => 'Tiene que ser compatible con el formato de tu motherboard (ATX / micro-ATX).'],
            ['key' => 'peripherals', 'label' => 'Perifericos',       'icon' => '⌨️', 'category' => 'Perifericos',      'required' => false, 'multiple' => true,  'hint' => 'Opcional: teclado, mouse, monitor. Podés elegir varios.'],
        ],
    ],

    'search' => [
        'max_words'   => 6,
        'max_results' => 100,
    ],
];
