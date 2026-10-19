<?php
/**
 * Preguntas frecuentes del asistente (modo sin IA).
 * Cada entrada: palabras clave -> respuesta. Editar acá, no en chat.php.
 */
return [
    ['keywords' => ['envio', 'envío', 'entrega', 'demora'],
     'respuesta' => 'Hacemos envíos a todo el país. El tiempo de entrega estimado es de 2 a 5 días hábiles según tu ubicación.'],
    ['keywords' => ['garantia', 'garantía'],
     'respuesta' => 'Todos nuestros productos tienen garantía oficial de 6 a 12 meses según el fabricante.'],
    ['keywords' => ['pago', 'mercado pago', 'tarjeta', 'cuotas'],
     'respuesta' => 'Podés pagar con Mercado Pago (tarjetas de crédito/débito y otros medios).'],
    ['keywords' => ['cambio', 'devolucion', 'devolución'],
     'respuesta' => 'Tenés 10 días desde la recepción del producto para solicitar un cambio o devolución, siempre que esté sin uso.'],
    ['keywords' => ['stock', 'disponible', 'hay'],
     'respuesta' => 'Podés ver el stock disponible de cada producto en su ficha, dentro de la sección Productos.'],
    ['keywords' => ['horario', 'atencion', 'atención', 'contacto', 'humano'],
     'respuesta' => 'Nuestro equipo humano atiende de lunes a viernes de 9 a 18hs. También podés escribirnos desde la sección Contacto.'],
    ['keywords' => ['hola', 'buenas', 'buenos dias', 'buenas tardes'],
     'respuesta' => '¡Hola! ¿En qué te puedo ayudar? Puedo responderte sobre envíos, garantía, pagos y productos.'],
];
