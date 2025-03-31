<?php

require('../vendor/autoload.php');

use App\Controllers\Functions;

$funciones = new Functions();


if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $action = $_POST['action'] ?? '';
  switch ($action) {
    case 'addUser':
      $data = [
        ':nombre' => $_POST['nombre'],
        ':email' => $_POST['email'],
        ':password_hash' => $_POST['password_hash']
      ];
      #dd($data);
      $funciones->addUser($data);
      break;
    case 'loginUser':
      $data = [
        'email' => $_POST['email'],
        'password_hash' => $_POST['password_hash']
      ];

      $user = $funciones->loginUser($data);

      if ($user['success']) {
        header('location:/EISPDM_PROJECTS/Tienda_Scripts/public/user.php');
      } else {
        echo "error al iniciar session";
      }

      break;
    default:
      echo "error";
  };
} else {
  echo "error no method post aaaaaaaaaa";
}
