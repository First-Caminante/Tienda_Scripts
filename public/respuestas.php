<?php
session_start();
require('../vendor/autoload.php');

use App\Controllers\Functions;

// Verificar si el usuario está logueado y es desarrollador
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'desarrollador') {
  header('Location: login.php');
  exit;
}

$funciones = new Functions();
$desarrollador_id = $_SESSION['id'];
$mensaje = '';
$tipo_mensaje = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['accion'])) {
    switch ($_POST['accion']) {
      case 'responder':
        $solicitud_id = (int)$_POST['solicitud_id'];
        $mensaje_respuesta = trim($_POST['mensaje']);
        $archivo_script = trim($_POST['archivo_script']);
        $nuevo_estado = $_POST['nuevo_estado'];

        if (empty($mensaje_respuesta)) {
          $mensaje = 'El mensaje no puede estar vacío';
          $tipo_mensaje = 'error';
        } else {
          // Insertar respuesta
          $resultado_respuesta = $funciones->insertarRespuesta(
            $solicitud_id,
            $desarrollador_id,
            $mensaje_respuesta,
            !empty($archivo_script) ? $archivo_script : null
          );

          if ($resultado_respuesta['success']) {
            // Cambiar estado de la solicitud
            $resultado_estado = $funciones->cambiarEstadoSolicitud($solicitud_id, $nuevo_estado);

            if ($resultado_estado['success']) {
              $mensaje = 'Respuesta enviada y estado actualizado correctamente';
              $tipo_mensaje = 'success';
            } else {
              $mensaje = 'Respuesta enviada pero error al actualizar estado: ' . $resultado_estado['error'];
              $tipo_mensaje = 'warning';
            }
          } else {
            $mensaje = $resultado_respuesta['error'];
            $tipo_mensaje = 'error';
          }
        }
        break;

      case 'cambiar_estado':
        $solicitud_id = (int)$_POST['solicitud_id'];
        $nuevo_estado = $_POST['nuevo_estado'];

        $resultado = $funciones->cambiarEstadoSolicitud($solicitud_id, $nuevo_estado);

        if ($resultado['success']) {
          $mensaje = $resultado['message'];
          $tipo_mensaje = 'success';
        } else {
          $mensaje = $resultado['error'];
          $tipo_mensaje = 'error';
        }
        break;
    }
  }
}

// Obtener datos
$solicitudes = $funciones->getSolicitudesConUsuario();
$respuestas = $funciones->getRespuestasCompletas();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Respuestas - Desarrollador</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #092c1f;
      color: #ffffff;
      line-height: 1.6;
    }

    .header {
      background-color: #0a3d2b;
      padding: 1rem 2rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    .header h1 {
      color: #4ade80;
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }

    .user-info {
      color: #a3a3a3;
      font-size: 0.9rem;
    }

    .container {
      max-width: 1400px;
      margin: 2rem auto;
      padding: 0 2rem;
    }

    .mensaje {
      padding: 1rem;
      margin-bottom: 2rem;
      border-radius: 8px;
      font-weight: 500;
    }

    .mensaje.success {
      background-color: rgba(34, 197, 94, 0.1);
      border: 1px solid #22c55e;
      color: #4ade80;
    }

    .mensaje.error {
      background-color: rgba(239, 68, 68, 0.1);
      border: 1px solid #ef4444;
      color: #f87171;
    }

    .mensaje.warning {
      background-color: rgba(245, 158, 11, 0.1);
      border: 1px solid #f59e0b;
      color: #fbbf24;
    }

    .section {
      background-color: #0f3d2f;
      border-radius: 12px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .section h2 {
      color: #4ade80;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      border-bottom: 2px solid #4ade80;
      padding-bottom: 0.5rem;
    }

    .solicitud-card {
      background-color: #1a5c42;
      border-radius: 8px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      border-left: 4px solid #4ade80;
    }

    .solicitud-header {
      display: flex;
      justify-content: between;
      align-items: flex-start;
      margin-bottom: 1rem;
    }

    .solicitud-titulo {
      color: #ffffff;
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .solicitud-meta {
      display: flex;
      gap: 1rem;
      margin-bottom: 1rem;
      font-size: 0.9rem;
      color: #a3a3a3;
    }

    .estado {
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
    }

    .estado.pendiente {
      background-color: #f59e0b;
      color: #000;
    }

    .estado.en-proceso {
      background-color: #3b82f6;
      color: #fff;
    }

    .estado.completado {
      background-color: #22c55e;
      color: #fff;
    }

    .estado.rechazado {
      background-color: #ef4444;
      color: #fff;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      color: #4ade80;
      font-weight: 500;
    }

    .form-control {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #374151;
      border-radius: 6px;
      background-color: #1f2937;
      color: #ffffff;
      font-size: 1rem;
    }

    .form-control:focus {
      outline: none;
      border-color: #4ade80;
      box-shadow: 0 0 0 2px rgba(74, 222, 128, 0.2);
    }

    textarea.form-control {
      resize: vertical;
      min-height: 100px;
    }

    select.form-control {
      cursor: pointer;
    }

    .btn {
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background-color: #4ade80;
      color: #000;
    }

    .btn-primary:hover {
      background-color: #22c55e;
      transform: translateY(-1px);
    }

    .btn-secondary {
      background-color: #6b7280;
      color: #fff;
    }

    .btn-secondary:hover {
      background-color: #4b5563;
    }

    .respuesta-item {
      background-color: #1a5c42;
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      border-left: 3px solid #4ade80;
    }

    .respuesta-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.5rem;
    }

    .tabla-responsive {
      overflow-x: auto;
    }

    .tabla {
      width: 100%;
      border-collapse: collapse;
      background-color: #1f2937;
      border-radius: 8px;
      overflow: hidden;
    }

    .tabla th,
    .tabla td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #374151;
    }

    .tabla th {
      background-color: #0f3d2f;
      color: #4ade80;
      font-weight: 600;
    }

    .tabla tr:hover {
      background-color: #374151;
    }

    @media (max-width: 768px) {
      .container {
        padding: 0 1rem;
      }

      .header {
        padding: 1rem;
      }

      .section {
        padding: 1rem;
      }
    }
  </style>
</head>

<body>
  <div class="header">
    <h1>Gestión de Respuestas</h1>
    <div class="user-info">
      Desarrollador: <?php echo htmlspecialchars($_SESSION['nombre']); ?>
      (<?php echo htmlspecialchars($_SESSION['email']); ?>)
    </div>
  </div>

  <div class="container">
    <?php if (!empty($mensaje)): ?>
      <div class="mensaje <?php echo $tipo_mensaje; ?>">
        <?php echo htmlspecialchars($mensaje); ?>
      </div>
    <?php endif; ?>

    <!-- Sección de Solicitudes Pendientes -->
    <div class="section">
      <h2>Solicitudes Pendientes</h2>

      <?php
      $solicitudes_pendientes = array_filter($solicitudes, function ($sol) {
        return $sol['estado'] === 'pendiente' || $sol['estado'] === 'en proceso';
      });

      if (empty($solicitudes_pendientes)): ?>
        <p style="color: #a3a3a3; text-align: center; padding: 2rem;">
          No hay solicitudes pendientes en este momento.
        </p>
      <?php else: ?>
        <?php foreach ($solicitudes_pendientes as $solicitud): ?>
          <div class="solicitud-card">
            <div class="solicitud-header">
              <div>
                <div class="solicitud-titulo"><?php echo htmlspecialchars($solicitud['titulo']); ?></div>
                <div class="solicitud-meta">
                  <span>Cliente: <?php echo htmlspecialchars($solicitud['usuario_nombre']); ?></span>
                  <span>Email: <?php echo htmlspecialchars($solicitud['usuario_email']); ?></span>
                  <span>Fecha: <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_creacion'])); ?></span>
                </div>
              </div>
              <span class="estado <?php echo str_replace(' ', '-', $solicitud['estado']); ?>">
                <?php echo ucfirst($solicitud['estado']); ?>
              </span>
            </div>

            <div style="margin-bottom: 1rem;">
              <strong>Descripción:</strong><br>
              <?php echo nl2br(htmlspecialchars($solicitud['descripcion'])); ?>
            </div>

            <form method="POST" style="margin-top: 1.5rem;">
              <input type="hidden" name="accion" value="responder">
              <input type="hidden" name="solicitud_id" value="<?php echo $solicitud['id']; ?>">

              <div class="form-group">
                <label for="mensaje_<?php echo $solicitud['id']; ?>">Mensaje de Respuesta *</label>
                <textarea
                  name="mensaje"
                  id="mensaje_<?php echo $solicitud['id']; ?>"
                  class="form-control"
                  placeholder="Escribe tu respuesta al cliente..."
                  required></textarea>
              </div>

              <div class="form-group">
                <label for="archivo_<?php echo $solicitud['id']; ?>">Archivo/Script (Opcional)</label>
                <input
                  type="text"
                  name="archivo_script"
                  id="archivo_<?php echo $solicitud['id']; ?>"
                  class="form-control"
                  placeholder="Ruta del archivo o nombre del script...">
              </div>

              <div class="form-group">
                <label for="estado_<?php echo $solicitud['id']; ?>">Cambiar Estado a:</label>
                <select name="nuevo_estado" id="estado_<?php echo $solicitud['id']; ?>" class="form-control" required>
                  <option value="en proceso" <?php echo $solicitud['estado'] === 'en proceso' ? 'selected' : ''; ?>>En Proceso</option>
                  <option value="completado">Completado</option>
                  <option value="rechazado">Rechazado</option>
                  <option value="pendiente" <?php echo $solicitud['estado'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                </select>
              </div>

              <button type="submit" class="btn btn-primary">Enviar Respuesta</button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Sección de Todas las Solicitudes -->
    <div class="section">
      <h2>Todas las Solicitudes</h2>

      <div class="tabla-responsive">
        <table class="tabla">
          <thead>
            <tr>
              <th>ID</th>
              <th>Título</th>
              <th>Cliente</th>
              <th>Estado</th>
              <th>Fecha</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($solicitudes as $solicitud): ?>
              <tr>
                <td><?php echo $solicitud['id']; ?></td>
                <td><?php echo htmlspecialchars($solicitud['titulo']); ?></td>
                <td><?php echo htmlspecialchars($solicitud['usuario_nombre']); ?></td>
                <td>
                  <span class="estado <?php echo str_replace(' ', '-', $solicitud['estado']); ?>">
                    <?php echo ucfirst($solicitud['estado']); ?>
                  </span>
                </td>
                <td><?php echo date('d/m/Y', strtotime($solicitud['fecha_creacion'])); ?></td>
                <td>
                  <form method="POST" style="display: inline;">
                    <input type="hidden" name="accion" value="cambiar_estado">
                    <input type="hidden" name="solicitud_id" value="<?php echo $solicitud['id']; ?>">
                    <select name="nuevo_estado" class="form-control" style="width: auto; display: inline-block;" onchange="this.form.submit()">
                      <option value="">Cambiar estado...</option>
                      <option value="pendiente">Pendiente</option>
                      <option value="en proceso">En Proceso</option>
                      <option value="completado">Completado</option>
                      <option value="rechazado">Rechazado</option>
                    </select>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Sección de Respuestas Enviadas -->
    <div class="section">
      <h2>Historial de Respuestas</h2>

      <?php if (empty($respuestas)): ?>
        <p style="color: #a3a3a3; text-align: center; padding: 2rem;">
          No hay respuestas registradas aún.
        </p>
      <?php else: ?>
        <?php foreach ($respuestas as $respuesta): ?>
          <div class="respuesta-item">
            <div class="respuesta-header">
              <strong>Solicitud: <?php echo htmlspecialchars($respuesta['solicitud_titulo']); ?></strong>
              <small><?php echo date('d/m/Y H:i', strtotime($respuesta['fecha_respuesta'])); ?></small>
            </div>
            <div style="margin-bottom: 0.5rem;">
              <strong>Cliente:</strong> <?php echo htmlspecialchars($respuesta['cliente_nombre']); ?>
              <strong>Desarrollador:</strong> <?php echo htmlspecialchars($respuesta['desarrollador_nombre']); ?>
            </div>
            <div style="margin-bottom: 0.5rem;">
              <strong>Mensaje:</strong><br>
              <?php echo nl2br(htmlspecialchars($respuesta['mensaje'])); ?>
            </div>
            <?php if (!empty($respuesta['archivo_script'])): ?>
              <div>
                <strong>Archivo:</strong> <?php echo htmlspecialchars($respuesta['archivo_script']); ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</body>

</html>
