<?php
/** Punto de entrada público: redirige a la home privada (que pide login si hace falta). */
require_once __DIR__ . '/src/config/bootstrap.php';

redirect('/src/views/index.php');
