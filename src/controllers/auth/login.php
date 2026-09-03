<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {  // Si la solicitud no es POST, volves al register
  header('Location: /src/views/auth/login.php');
  exit;
}

$data = [
  'email'           => trim($_POST['email'] ?? ''),
  'password'        => $_POST['password'] ?? '',

];
?>