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

  public function deleteUser($id)
  {
    try {
      $stmt = $this->connection->prepare("CALL EliminarUsuario(:id)");
      $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
      $stmt->execute();
      $stmt->closeCursor();
    } catch (\PDOException $e) {
      echo "error delete" . $e->getMessage();
    }
  }


  function loginUsuarionone($email, $password)
  {
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

  function loginUsuario($email, $password)
  {
    try {
      $stmt = $this->connection->prepare("SELECT id, nombre, email, password_hash, rol FROM usuarios WHERE email = ?");
      $stmt->execute([$email]);
      $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

      if ($usuario && password_verify($password, $usuario['password_hash'])) {
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



  #aqui las funciones para reportes graficos 
  #
  #
  // Añadir estas funciones a la clase Functions existente

  /**
   * Obtiene las estadísticas de solicitudes por estado
   * @return array Datos para el reporte de solicitudes por estado
   */
  public function getSolicitudesPorEstado()
  {
    try {
      $sql = "SELECT estado, COUNT(*) as cantidad FROM solicitudes GROUP BY estado";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      return [
        'success' => false,
        'message' => 'Error al obtener estadísticas de solicitudes: ' . $e->getMessage()
      ];
    }
  }

  /**
   * Obtiene las estadísticas de pagos por mes
   * @param int $año El año para el que se quieren obtener las estadísticas
   * @return array Datos para el reporte de pagos mensuales
   */
  public function getPagosMensuales($año = null)
  {
    try {
      if ($año === null) {
        $año = date('Y'); // Año actual si no se especifica
      }

      $sql = "SELECT 
              MONTH(fecha_pago) as mes, 
              SUM(monto) as total_pagos,
              COUNT(*) as cantidad_pagos 
            FROM pagos 
            WHERE YEAR(fecha_pago) = :año AND estado = 'pagado'
            GROUP BY MONTH(fecha_pago)
            ORDER BY MONTH(fecha_pago)";

      $stmt = $this->connection->prepare($sql);
      $stmt->bindParam(':año', $año, \PDO::PARAM_INT);
      $stmt->execute();

      $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);

      // Asegurar que todos los meses estén representados
      $meses_completos = [];
      $nombres_meses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
      ];

      foreach ($nombres_meses as $num => $nombre) {
        $encontrado = false;
        foreach ($resultados as $resultado) {
          if ((int)$resultado['mes'] === $num) {
            $meses_completos[] = [
              'mes' => $num,
              'nombre_mes' => $nombre,
              'total_pagos' => $resultado['total_pagos'],
              'cantidad_pagos' => $resultado['cantidad_pagos']
            ];
            $encontrado = true;
            break;
          }
        }

        if (!$encontrado) {
          $meses_completos[] = [
            'mes' => $num,
            'nombre_mes' => $nombre,
            'total_pagos' => 0,
            'cantidad_pagos' => 0
          ];
        }
      }

      return $meses_completos;
    } catch (\PDOException $e) {
      return [
        'success' => false,
        'message' => 'Error al obtener estadísticas de pagos: ' . $e->getMessage()
      ];
    }
  }

  /**
   * Obtiene estadísticas de usuarios por rol
   * @return array Datos para el reporte de usuarios por rol
   */
  public function getUsuariosPorRol()
  {
    try {
      $sql = "SELECT rol, COUNT(*) as cantidad FROM usuarios GROUP BY rol";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      return [
        'success' => false,
        'message' => 'Error al obtener estadísticas de usuarios: ' . $e->getMessage()
      ];
    }
  }

  /**
   * Obtiene estadísticas generales del sistema
   * @return array Estadísticas generales
   */
  public function getEstadisticasGenerales()
  {
    try {
      $stats = [];

      // Total de usuarios
      $sql = "SELECT COUNT(*) as total FROM usuarios";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();
      $stats['total_usuarios'] = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

      // Total de solicitudes
      $sql = "SELECT COUNT(*) as total FROM solicitudes";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();
      $stats['total_solicitudes'] = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

      // Total de pagos
      $sql = "SELECT COUNT(*) as total, SUM(monto) as monto_total FROM pagos WHERE estado = 'pagado'";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();
      $pagos = $stmt->fetch(\PDO::FETCH_ASSOC);
      $stats['total_pagos'] = $pagos['total'];
      $stats['monto_total_pagos'] = $pagos['monto_total'] ?? 0;

      return $stats;
    } catch (\PDOException $e) {
      return [
        'success' => false,
        'message' => 'Error al obtener estadísticas generales: ' . $e->getMessage()
      ];
    }
  }
}
