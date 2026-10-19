<?php
/**
 * Cierre de sesión. Solo POST + CSRF (un simple <img src="logout.php"> de un sitio
 * ajeno no puede desloguear al usuario).
 */
require_once __DIR__ . '/../../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    logout_user();
    redirect('/src/views/auth/login.php');
}

redirect('/src/views/index.php');
