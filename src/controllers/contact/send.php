<?php
/**
 * Guarda un mensaje del formulario de contacto.
 * Valida y limita cada campo, exige CSRF y limita el envío masivo.
 */
require_once __DIR__ . '/../../config/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/src/views/contact.php');
}
csrf_require(false, '/src/views/contact.php');

$cfg  = config('contact');
$data = [
    'name'    => input_string($_POST, 'name', $cfg['name_max']),
    'email'   => input_string($_POST, 'email', $cfg['email_max']),
    'subject' => input_string($_POST, 'subject', $cfg['subject_max']),
    'message' => input_string($_POST, 'message', $cfg['message_max']),
];

$errors = [];
if ($data['name'] === '')    { $errors[] = 'El nombre es obligatorio.'; }
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { $errors[] = 'Ingresá un email válido.'; }
if ($data['subject'] === '') { $errors[] = 'El asunto es obligatorio.'; }
if ($data['message'] === '') { $errors[] = 'El mensaje es obligatorio.'; }

if (!$errors && !rate_limit_allow('contact', $cfg['rate_max'], $cfg['rate_window'])) {
    $errors[] = 'Enviaste muchos mensajes seguidos. Probá de nuevo en unos minutos.';
}

if ($errors) {
    flash_set('errors', $errors);
    flash_set('old', $data);
    redirect('/src/views/contact.php');
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO contact_messages (name, email, subject, message)
         VALUES (:name, :email, :subject, :message)'
    );
    $stmt->execute([
        ':name'    => $data['name'],
        ':email'   => $data['email'],
        ':subject' => $data['subject'],
        ':message' => $data['message'],
    ]);
} catch (PDOException $e) {
    error_log('[contact] ' . $e->getMessage());
    flash_set('errors', ['No pudimos guardar tu mensaje. Intentá de nuevo.']);
    flash_set('old', $data);
    redirect('/src/views/contact.php');
}

redirect('/src/views/contact.php?ok=1');
