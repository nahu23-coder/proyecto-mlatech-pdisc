<?php
<<<<<<< HEAD
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {  // Si la solicitud no es POST, volves al register
  header('Location: /src/views/auth/login.php');
  exit;
}

$data = [
  'email'           => trim($_POST['email'] ?? ''),
  'password'        => $_POST['password'] ?? '',

];
?>
=======
require_once __DIR__ . '/../../config/bootstrap.php';

// Paso clave #1: Validar tipo de solicitud ----------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {  // Si la solicitud no es POST, volves al register
  header('Location: /src/views/auth/register.php');
  exit;
}

// Paso clave #2: Tomar datos -----------------------------------
$data = [
  'email'           => trim($_POST['email'] ?? ''), // trim(str) saca los espacios al inicio y al final
  'password'        => $_POST['password'] ?? ''
];

try{

} 
>>>>>>> cbcc50a2f577fdafe91bf09022e7a70297ce2405
