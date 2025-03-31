<?php

namespace App\Controllers;

use Database\PDO\Connection;

class Functions
{

  private $connection;

  public function __construct()
  {
    $this->connection = Connection::getInstance()->getConnection();
  }

  public function addUser($data)
  {
    try {
      $stmt = $this->connection->prepare('insert into usuarios(nombre,email,password_hash) 
      values(:nombre,:email,:password_hash);');
      $stmt->execute($data);
    } catch (\PDOException $e) {
      echo "error al ingresar usuario: " . $e->getMessage();
    }
  }


  function loginUser($data)
  {
    $sql = "SELECT id, nombre, password_hash FROM usuarios WHERE email = :email AND rol = 'cliente' LIMIT 1";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute(['email' => $data['email']]);

    $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);
    //    dd($usuario);
    if ($usuario && $data['password_hash'] === $usuario['password_hash']) {
      return [
        'success' => true,
        'user_id' => $usuario['id'],
        'nombre'  => $usuario['nombre']
      ];
    }

    return ['success' => false, 'message' => 'Usuario o contraseña incorrectos o no es un cliente.'];
  }
}
