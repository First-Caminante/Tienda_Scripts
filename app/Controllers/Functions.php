<?php

namespace App\Controllers;

use Database\PDO\Connection;

define('BASE_DIR', dirname(__DIR__, 2)); // /EISPDM_PROJECTS/Tienda_Scripts


class Functions
{


  private $connection;
  private $dbHost = 'localhost';
  private $dbName = 'TiendaScripts';
  private $dbUser = 'root';
  private $dbPass = 'caminante';
  private $backupPath;


  // define('BASE_DIR', dirname(__DIR__, 2)); // /EISPDM_PROJECTS/Tienda_Scripts

  public function __construct()
  {
    $this->connection = Connection::getInstance()->getConnection();
    //$this->backupPath = realpath(__DIR__ . '/../../public/backup') . '/';
    $this->backupPath = BASE_DIR . '/public/backup/';
  }


  ///aqui para pagos   
  public function getSolicitudPorId($solicitud_id): array
  {
    try {
      $sql = "SELECT s.*, u.nombre as cliente_nombre, u.email as cliente_email,
                       r.archivo_script, r.mensaje as respuesta_mensaje,
                       d.nombre as desarrollador_nombre
                FROM solicitudes s 
                INNER JOIN usuarios u ON s.usuario_id = u.id
                LEFT JOIN respuestas r ON s.id = r.solicitud_id
                LEFT JOIN usuarios d ON r.desarrollador_id = d.id
                WHERE s.id = ?";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute([$solicitud_id]);
      $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

      return $resultado ? $resultado : ['error' => 'Solicitud no encontrada'];
    } catch (\PDOException $e) {
      return ['error' => 'Error al obtener solicitud: ' . $e->getMessage()];
    }
  }

  /**
   * Crear un nuevo pago
   */
  public function crearPago($usuario_id, $solicitud_id, $monto, $datos_pago = []): array
  {
    try {
      // Validar datos de pago simulados
      $estado_pago = $this->validarDatosPago($datos_pago);

      $sql = "INSERT INTO pagos (usuario_id, solicitud_id, monto, estado) VALUES (?, ?, ?, ?)";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute([$usuario_id, $solicitud_id, $monto, $estado_pago]);

      $pago_id = $this->connection->lastInsertId();

      return [
        'success' => true,
        'pago_id' => $pago_id,
        'estado' => $estado_pago,
        'message' => $this->getMensajePago($estado_pago)
      ];
    } catch (\PDOException $e) {
      return [
        'success' => false,
        'error' => 'Error al procesar pago: ' . $e->getMessage()
      ];
    }
  }

  /**
   * Validar datos de pago simulados
   */
  private function validarDatosPago($datos): string
  {
    // Validaciones simuladas realistas

    // Validar número de tarjeta
    $numero_tarjeta = preg_replace('/\s+/', '', $datos['numero_tarjeta'] ?? '');
    if (strlen($numero_tarjeta) < 16 || !is_numeric($numero_tarjeta)) {
      return 'fallido';
    }

    // Validar CVV
    $cvv = $datos['cvv'] ?? '';
    if (strlen($cvv) < 3 || !is_numeric($cvv)) {
      return 'fallido';
    }

    // Validar fecha de expiración
    $mes = $datos['mes'] ?? '';
    $año = $datos['año'] ?? '';
    if (empty($mes) || empty($año) || $mes < 1 || $mes > 12) {
      return 'fallido';
    }

    $fecha_actual = new \DateTime();
    $fecha_expiracion = new \DateTime("$año-$mes-01");
    if ($fecha_expiracion < $fecha_actual) {
      return 'fallido';
    }

    // Validar nombre del titular
    $nombre = trim($datos['nombre_titular'] ?? '');
    if (strlen($nombre) < 3) {
      return 'fallido';
    }

    // Validar email
    $email = $datos['email'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return 'fallido';
    }

    // Simulación de probabilidad de éxito (95% éxito)
    $probabilidad = rand(1, 100);
    if ($probabilidad <= 5) {
      return 'fallido';
    }

    // Simulación de procesamiento (5% pendiente, 95% pagado)
    $estado_final = rand(1, 100);
    return $estado_final <= 95 ? 'pagado' : 'pendiente';
  }

  /**
   * Obtener mensaje según el estado del pago
   */
  private function getMensajePago($estado): string
  {
    switch ($estado) {
      case 'pagado':
        return 'Pago procesado exitosamente. ¡Gracias por tu compra!';
      case 'pendiente':
        return 'Pago en proceso de verificación. Te notificaremos cuando se complete.';
      case 'fallido':
        return 'Error en el procesamiento del pago. Verifica tus datos e intenta nuevamente.';
      default:
        return 'Estado de pago desconocido.';
    }
  }

  /**
   * Obtener historial de pagos de un usuario
   */
  public function getPagosUsuario($usuario_id): array
  {
    try {
      $sql = "SELECT p.*, s.titulo as solicitud_titulo, s.descripcion
                FROM pagos p 
                INNER JOIN solicitudes s ON p.solicitud_id = s.id
                WHERE p.usuario_id = ?
                ORDER BY p.fecha_pago DESC";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute([$usuario_id]);
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      return ['error' => 'Error al obtener historial de pagos: ' . $e->getMessage()];
    }
  }

  /**
   * Generar precio simulado basado en la solicitud
   */
  public function generarPrecioScript($solicitud_id): float
  {
    // Generar precio basado en el ID y características simuladas
    $precios_base = [15.99, 25.99, 35.99, 45.99, 55.99, 75.99, 99.99];
    $index = $solicitud_id % count($precios_base);
    return $precios_base[$index];
  }
  ///aqui terminan pagos 
  ///notificaciones para el usuario 

  public function getSolicitudesUsuario($usuario_id): array
  {
    try {
      $sql = "SELECT s.*, 
                       r.mensaje as respuesta_mensaje,
                       r.archivo_script,
                       r.fecha_respuesta,
                       u.nombre as desarrollador_nombre
                FROM solicitudes s 
                LEFT JOIN respuestas r ON s.id = r.solicitud_id
                LEFT JOIN usuarios u ON r.desarrollador_id = u.id
                WHERE s.usuario_id = ?
                ORDER BY s.fecha_creacion DESC";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute([$usuario_id]);
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      return ['error' => 'Error al obtener solicitudes del usuario: ' . $e->getMessage()];
    }
  }

  /**
   * Obtener estadísticas del usuario
   */
  public function getEstadisticasUsuario($usuario_id): array
  {
    try {
      $sql = "SELECT 
                    COUNT(*) as total_solicitudes,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'en proceso' THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completados,
                    SUM(CASE WHEN estado = 'rechazado' THEN 1 ELSE 0 END) as rechazados
                FROM solicitudes 
                WHERE usuario_id = ?";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute([$usuario_id]);
      return $stmt->fetch(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      return ['error' => 'Error al obtener estadísticas: ' . $e->getMessage()];
    }
  }

  ///aqui acaba sus notify 


  ///aqui van para las respuestas


  public function insertarRespuesta($solicitud_id, $desarrollador_id, $mensaje, $archivo_script = null): array
  {
    try {
      $sql = "INSERT INTO respuestas (solicitud_id, desarrollador_id, mensaje, archivo_script) VALUES (?, ?, ?, ?)";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute([$solicitud_id, $desarrollador_id, $mensaje, $archivo_script]);

      return [
        'success' => true,
        'message' => 'Respuesta insertada correctamente',
        'id' => $this->connection->lastInsertId()
      ];
    } catch (\PDOException $e) {
      return [
        'success' => false,
        'error' => 'Error al insertar respuesta: ' . $e->getMessage()
      ];
    }
  }

  /**
   * Cambiar el estado de una solicitud
   */
  public function cambiarEstadoSolicitud($solicitud_id, $nuevo_estado): array
  {
    try {
      $estados_validos = ['pendiente', 'en proceso', 'completado', 'rechazado'];

      if (!in_array($nuevo_estado, $estados_validos)) {
        return [
          'success' => false,
          'error' => 'Estado no válido'
        ];
      }

      $sql = "UPDATE solicitudes SET estado = ? WHERE id = ?";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute([$nuevo_estado, $solicitud_id]);

      if ($stmt->rowCount() > 0) {
        return [
          'success' => true,
          'message' => 'Estado actualizado correctamente'
        ];
      } else {
        return [
          'success' => false,
          'error' => 'No se encontró la solicitud o no se realizó ningún cambio'
        ];
      }
    } catch (\PDOException $e) {
      return [
        'success' => false,
        'error' => 'Error al cambiar estado: ' . $e->getMessage()
      ];
    }
  }

  /**
   * Obtener solicitudes con información del usuario
   */
  public function getSolicitudesConUsuario(): array
  {
    try {
      $sql = "SELECT s.*, u.nombre as usuario_nombre, u.email as usuario_email 
                FROM solicitudes s 
                INNER JOIN usuarios u ON s.usuario_id = u.id 
                ORDER BY s.fecha_creacion DESC";
      $stmt = $this->connection->query($sql);
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      return ['error' => 'Error al obtener solicitudes: ' . $e->getMessage()];
    }
  }

  /**
   * Obtener respuestas con información de solicitud y desarrollador
   */
  public function getRespuestasCompletas(): array
  {
    try {
      $sql = "SELECT r.*, s.titulo as solicitud_titulo, s.descripcion as solicitud_descripcion,
                       u.nombre as desarrollador_nombre, u.email as desarrollador_email,
                       uc.nombre as cliente_nombre, uc.email as cliente_email
                FROM respuestas r 
                INNER JOIN solicitudes s ON r.solicitud_id = s.id
                INNER JOIN usuarios u ON r.desarrollador_id = u.id
                INNER JOIN usuarios uc ON s.usuario_id = uc.id
                ORDER BY r.fecha_respuesta DESC";
      $stmt = $this->connection->query($sql);
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      return ['error' => 'Error al obtener respuestas: ' . $e->getMessage()];
    }
  }


  ///aqui acaban las para las respuestas

  ///////////aqui iran las funciones para solicitudes
  //

  /**
   * Crear una nueva solicitud
   */
  public function crearSolicitud($data)
  {
    try {
      $sql = "INSERT INTO solicitudes (usuario_id, titulo, descripcion, estado) VALUES (:usuario_id, :titulo, :descripcion, :estado)";
      $stmt = $this->connection->prepare($sql);

      $result = $stmt->execute([
        ':usuario_id' => $data['usuario_id'],
        ':titulo' => trim($data['titulo']),
        ':descripcion' => trim($data['descripcion']),
        ':estado' => $data['estado'] ?? 'pendiente'
      ]);

      if ($result) {
        return [
          'success' => true,
          'message' => 'Solicitud creada exitosamente',
          'id' => $this->connection->lastInsertId()
        ];
      } else {
        return ['success' => false, 'message' => 'Error al crear la solicitud'];
      }
    } catch (Exception $e) {
      return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
  }

  /**
   * Obtener solicitudes por usuario
   */
  public function getSolicitudesByUsuario($usuario_id)
  {
    try {
      $sql = "SELECT * FROM solicitudes WHERE usuario_id = :usuario_id ORDER BY fecha_creacion DESC";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute([':usuario_id' => $usuario_id]);

      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_log('Error en getSolicitudesByUsuario: ' . $e->getMessage());
      return [];
    }
  }

  /**
   * Obtener todas las solicitudes con información del usuario (para admin)
   */
  public function getAllSolicitudes()
  {
    try {
      $sql = "SELECT s.*, u.nombre as usuario_nombre, u.email as usuario_email 
                FROM solicitudes s 
                JOIN usuarios u ON s.usuario_id = u.id 
                ORDER BY s.fecha_creacion DESC";
      $stmt = $this->connection->prepare($sql);
      $stmt->execute();

      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_log('Error en getAllSolicitudes: ' . $e->getMessage());
      return [];
    }
  }

  /**
   * Actualizar estado de solicitud
   */
  public function actualizarEstadoSolicitud($id, $estado)
  {
    try {
      $estados_validos = ['pendiente', 'en proceso', 'completado', 'rechazado'];

      if (!in_array($estado, $estados_validos)) {
        return ['success' => false, 'message' => 'Estado no válido'];
      }

      $sql = "UPDATE solicitudes SET estado = :estado WHERE id = :id";
      $stmt = $this->connection->prepare($sql);

      $result = $stmt->execute([
        ':estado' => $estado,
        ':id' => $id
      ]);

      if ($result && $stmt->rowCount() > 0) {
        return ['success' => true, 'message' => 'Estado actualizado exitosamente'];
      } else {
        return ['success' => false, 'message' => 'No se encontró la solicitud o no se realizaron cambios'];
      }
    } catch (Exception $e) {
      return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
  }

  /**
   * Eliminar solicitud (solo el propietario o admin)
   */
  public function eliminarSolicitud($id, $usuario_id = null)
  {
    try {
      // Si se proporciona usuario_id, verificar que la solicitud pertenezca al usuario
      if ($usuario_id) {
        $sql = "DELETE FROM solicitudes WHERE id = :id AND usuario_id = :usuario_id";
        $params = [':id' => $id, ':usuario_id' => $usuario_id];
      } else {
        // Solo admin puede eliminar sin verificar usuario
        $sql = "DELETE FROM solicitudes WHERE id = :id";
        $params = [':id' => $id];
      }

      $stmt = $this->connection->prepare($sql);
      $result = $stmt->execute($params);

      if ($result && $stmt->rowCount() > 0) {
        return ['success' => true, 'message' => 'Solicitud eliminada exitosamente'];
      } else {
        return ['success' => false, 'message' => 'No se pudo eliminar la solicitud'];
      }
    } catch (Exception $e) {
      return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
  }

  /**
   * Obtener estadísticas de solicitudes por estado para un usuario específico
   */
  public function getEstadisticasSolicitudesUsuario($usuario_id)
  {
    try {
      $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'en proceso' THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completadas,
                    SUM(CASE WHEN estado = 'rechazado' THEN 1 ELSE 0 END) as rechazadas
                FROM solicitudes 
                WHERE usuario_id = :usuario_id";

      $stmt = $this->connection->prepare($sql);
      $stmt->execute([':usuario_id' => $usuario_id]);

      $result = $stmt->fetch(\PDO::FETCH_ASSOC);

      return [
        'total' => (int)$result['total'],
        'pendientes' => (int)$result['pendientes'],
        'en_proceso' => (int)$result['en_proceso'],
        'completadas' => (int)$result['completadas'],
        'rechazadas' => (int)$result['rechazadas']
      ];
    } catch (Exception $e) {
      error_log('Error en getEstadisticasSolicitudesUsuario: ' . $e->getMessage());
      return [
        'total' => 0,
        'pendientes' => 0,
        'en_proceso' => 0,
        'completadas' => 0,
        'rechazadas' => 0
      ];
    }
  }

  /**
   * Obtener una solicitud específica
   */
  public function getSolicitudById($id, $usuario_id = null)
  {
    try {
      if ($usuario_id) {
        // Verificar que la solicitud pertenezca al usuario
        $sql = "SELECT * FROM solicitudes WHERE id = :id AND usuario_id = :usuario_id";
        $params = [':id' => $id, ':usuario_id' => $usuario_id];
      } else {
        $sql = "SELECT * FROM solicitudes WHERE id = :id";
        $params = [':id' => $id];
      }

      $stmt = $this->connection->prepare($sql);
      $stmt->execute($params);

      return $stmt->fetch(\PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      error_log('Error en getSolicitudById: ' . $e->getMessage());
      return false;
    }
  }

  // aqui acaban las funciones solicitudes


  /*
   *
   *aqui iran los respaldos de la bd 
   *
   * */


  /*public function crearBackup(): string
  {
    $fecha = date('Y-m-d_H-i-s');
    $archivo = "backup_{$fecha}.sql";
    $rutaCompleta = $this->backupPath . $archivo;

    $comando = "mysqldump -h {$this->dbHost} -u {$this->dbUser} " .
      ($this->dbPass ? "-p{$this->dbPass} " : "") .
      "{$this->dbName} > \"{$rutaCompleta}\"";

    exec($comando, $output, $resultado);

    return $resultado === 0 ? "Backup creado: $archivo" : "Error al crear backup.";
  }*/

  public function crearBackup(): string
  {
    $fecha = date('Y-m-d_H-i-s');
    $archivo = "backup_{$fecha}.sql";
    $rutaCompleta = $this->backupPath . $archivo;

    // Verifica si el directorio existe
    if (!is_dir($this->backupPath)) {
      return "Error: el directorio de backups no existe: {$this->backupPath}";
    }

    // Verifica permisos de escritura
    if (!is_writable($this->backupPath)) {
      return "Error: no se puede escribir en el directorio: {$this->backupPath}";
    }

    // Construcción del comando mysqldump
    $comando = "mysqldump -h {$this->dbHost} -u {$this->dbUser} " .
      ($this->dbPass ? "-p\"{$this->dbPass}\" " : "") .
      "{$this->dbName} > \"{$rutaCompleta}\"";

    // Ejecuta el comando y captura salida
    exec($comando . ' 2>&1', $output, $resultado);

    if ($resultado === 0) {
      return "✅ Backup creado exitosamente: $archivo";
    } else {
      return "❌ Error al crear backup.\nComando ejecutado:\n$comando\n\nSalida:\n" . implode("\n", $output);
    }
  }


  public function listarBackups(): array
  {
    $archivos = array_filter(scandir($this->backupPath), function ($f) {
      return pathinfo($f, PATHINFO_EXTENSION) === 'sql';
    });
    return array_values($archivos);
  }

  /*public function restaurarBackup(string $archivo): string
  {
    $archivo = basename($archivo);
    $rutaCompleta = $this->backupPath . $archivo;

    if (!file_exists($rutaCompleta)) {
      return "Archivo no encontrado.";
    }

    $comando = "mysql -h {$this->dbHost} -u {$this->dbUser} " .
      ($this->dbPass ? "-p{$this->dbPass} " : "") .
      "{$this->dbName} < \"{$rutaCompleta}\"";

    exec($comando, $output, $resultado);

    return $resultado === 0 ? "Backup restaurado: $archivo" : "Error al restaurar backup.";
  }*/


  public function restaurarBackup(string $archivo): string
  {
    $rutaArchivo = $this->backupPath . $archivo;

    if (!file_exists($rutaArchivo)) {
      return "❌ Error: El archivo no existe → $rutaArchivo";
    }

    // Verifica mysqldump
    $mysqlPath = trim(shell_exec("which mysql"));
    if (!$mysqlPath) {
      return "❌ Error: El comando 'mysql' no está disponible. Instálalo con: sudo apt install mysql-client";
    }

    // Comando de restauración
    $comando = "$mysqlPath -h {$this->dbHost} -u {$this->dbUser} " .
      ($this->dbPass ? "-p\"{$this->dbPass}\" " : "") .
      "{$this->dbName} < \"$rutaArchivo\"";

    exec($comando . " 2>&1", $output, $resultado);

    if ($resultado === 0) {
      return "✅ Base de datos restaurada correctamente desde: $archivo";
    } else {
      return "❌ Error al restaurar.\n\n🧠 COMANDO:\n$comando\n\n🧵 SALIDA:\n" . implode("\n", $output);
    }
  }



  /*
   *
   *aqui acaban los respaldo 
   * */




  /*funciones que retornan toda la bd 
   *
   *


  */



  public function getUsuarios(): array
  {
    try {
      $stmt = $this->connection->query("SELECT * FROM usuarios");
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      return ['error' => 'Error al obtener usuarios: ' . $e->getMessage()];
    }
  }

  public function getSolicitudes(): array
  {
    try {
      $stmt = $this->connection->query("SELECT * FROM solicitudes");
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      return ['error' => 'Error al obtener solicitudes: ' . $e->getMessage()];
    }
  }

  public function getRespuestas(): array
  {
    try {
      $stmt = $this->connection->query("SELECT * FROM respuestas");
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      return ['error' => 'Error al obtener respuestas: ' . $e->getMessage()];
    }
  }

  public function getPagos(): array
  {
    try {
      $stmt = $this->connection->query("SELECT * FROM pagos");
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      return ['error' => 'Error al obtener pagos: ' . $e->getMessage()];
    }
  }

  /*
  aqui acaban 
   */
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
  public function getSolicitudesPorEstadopast()
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
