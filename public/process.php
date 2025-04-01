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
    case 'editUser':
      // Verificar que el ID exista
      if (isset($_POST['id'])) {
        $userId = $_POST['id'];
        $data = [];

        // Recoger los datos del formulario
        if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
          $data['nombre'] = $_POST['nombre'];
        }

        if (isset($_POST['rol']) && !empty($_POST['rol'])) {
          $data['rol'] = $_POST['rol'];
        }

        // Solo actualizar la contraseña si se proporcionó una nueva
        if (isset($_POST['password_hash']) && !empty($_POST['password_hash'])) {
          $data['password_hash'] = $_POST['password_hash'];
          // Aquí podrías añadir un hash a la contraseña si lo necesitas
          // $data['password_hash'] = password_hash($_POST['password_hash'], PASSWORD_DEFAULT);
        }

        //dd($data, $userId);

        // Llamar a la función editUser
        $result = $funciones->editUser($userId, $data);

        header('Location:/EISPDM_PROJECTS/Tienda_Scripts/public/admin.php');

        // Redirigir con mensaje según el resultado
        /*  if ($result['success']) {*/
        /*    header('Location: index.php?tab=usuarios&msg=Usuario actualizado correctamente');*/
        /*  } else {*/
        /*    header('Location: index.php?tab=usuarios&error=' . urlencode($result['message']));*/
        /*  }*/
        /*} else {*/
        /*  header('Location: index.php?tab=usuarios&error=ID de usuario no especificado');*/
      }
      break;
    default:
      echo "error";
  };
} else {
  echo "error no method post aaaaaaaaaa";
}
