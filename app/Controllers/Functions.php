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

  public function editUser($userId, $data)
  {
    try {
      // Preparamos la consulta SQL para actualizar los campos permitidos
      $sql = "UPDATE usuarios SET ";
      $params = [];

      // Verificamos qué campos se van a actualizar
      if (isset($data['nombre'])) {
        $sql .= "nombre = :nombre, ";
        $params['nombre'] = $data['nombre'];
      }

      if (isset($data['rol'])) {
        $sql .= "rol = :rol, ";
        $params['rol'] = $data['rol'];
      }

      if (isset($data['password_hash'])) {
        $sql .= "password_hash = :password_hash, ";
        $params['password_hash'] = $data['password_hash'];
      }

      // Eliminamos la última coma y espacio
      $sql = rtrim($sql, ", ");

      // Añadimos la condición WHERE
      $sql .= " WHERE id = :id";
      $params['id'] = $userId;

      // Si no hay nada que actualizar, retornamos false
      if (count($params) <= 1) {
        return [
          'success' => false,
          'message' => 'No se proporcionaron campos para actualizar'
        ];
      }

      // Preparamos y ejecutamos la consulta
      $stmt = $this->connection->prepare($sql);
      $result = $stmt->execute($params);

      if ($result) {
        return [
          'success' => true,
          'message' => 'Usuario actualizado correctamente'
        ];
      } else {
        return [
          'success' => false,
          'message' => 'No se pudo actualizar el usuario'
        ];
      }
    } catch (\PDOException $e) {
      return [
        'success' => false,
        'message' => 'Error al actualizar usuario: ' . $e->getMessage()
      ];
    }
  }

  public function deleteUser($id) {
      try{
        $stmt = $this->connection->prepare("CALL EliminarUsuario(:id)");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
      }catch(\PDOException $e){
        echo "error delete".$e->getMessage();
      }   
  }


  function loginUsuario($email, $password) {
    //require 'conexion.php'; // Asegúrate de tener el PDO aquí

    try {
        $stmt = $this->connection->prepare("CALL LoginUsuario(?, ?)");
        $stmt->execute([$email, $password]);
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($usuario) {
            return [
                'success' => true,
                'mensaje' => 'Inicio de sesión exitoso',
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'email' => $usuario['email'],
                'rol' => $usuario['rol']
            ];
        } else {
            return [
                'success' => false,
                'mensaje' => 'Correo o contraseña incorrectos'
            ];
        }
    } catch (\PDOException $e) {
        return [
            'success' => false,
            'mensaje' => 'Error de base de datos: ' . $e->getMessage()
        ];
    }
}


}
