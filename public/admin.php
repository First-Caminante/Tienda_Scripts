<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// Incluir el autoloader de Composer
require_once '../vendor/autoload.php';

// Usar la clase Connection directamente
use Database\PDO\Connection;

session_start();
//dd($_SESSION);


if ($_SESSION['rol'] != "admin") {
  header('location:login.php');
}



// Obtener la conexión PDO
$connection = Connection::getInstance()->getConnection();

// Función para obtener todos los registros de una tabla
function getTableData($connection, $tableName)
{
  try {
    $stmt = $connection->prepare("SELECT * FROM $tableName");
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  } catch (\PDOException $e) {
    echo "Error al obtener datos de la tabla $tableName: " . $e->getMessage();
    return [];
  }
}

// Obtenemos los datos de cada tabla
$usuarios = getTableData($connection, 'usuarios');
$solicitudes = getTableData($connection, 'solicitudes');
$respuestas = getTableData($connection, 'respuestas');
$pagos = getTableData($connection, 'pagos');




///////////backup 



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $source_dir = realpath(__DIR__ . '/../');
  $backup_dir = $source_dir . '/backups';

  if (!is_dir($backup_dir)) {
    if (!mkdir($backup_dir, 0777, true)) {
      die("❌ No se pudo crear la carpeta de backups.");
    }
  }

  $zip_filename = 'backup_' . date('Ymd_His') . '.zip';
  $zip_path = $backup_dir . DIRECTORY_SEPARATOR . $zip_filename;

  $zip = new ZipArchive();
  if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("❌ No se pudo crear el archivo ZIP.");
  }

  function agregarDirectorio($zip, $folder, $base)
  {
    $files = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
      if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($base) + 1);
        $zip->addFile($filePath, $relativePath);
      }
    }
  }

  agregarDirectorio($zip, $source_dir, $source_dir);
  $zip->close();

  if (!file_exists($zip_path)) {
    die("❌ ERROR: No se pudo crear el archivo ZIP en: $zip_path");
  } else {
    $zip_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/../backups/' . $zip_filename;
    echo "✅ Backup creado correctamente.<br>";
    echo "📁 Ruta del archivo: <code>$zip_path</code><br>";
    echo "⬇️ <a href=\"$zip_url\" download>Descargar backup</a><br><br>";
  }
}




?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Administrativo - TiendaScripts</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome para iconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <style>
    .nav-tabs .nav-link.active {
      font-weight: bold;
      background-color: #f8f9fa;
    }

    .table-responsive {
      margin-top: 20px;
    }

    .badge {
      font-size: 0.8em;
    }
  </style>
</head>

<body>
  <div class="container-fluid py-4">
    <h1 class="mb-4">Panel Administrativo - TiendaScripts</h1>

    <form method="POST" action="backup_restore.php">
      <button type="submit" name="backupa">Crear Backup Completo</button>
    </form>
    <form action="logout.php" method="POST">
      <button type="submit" name="">Cerrar Session</button>
    </form>
    <form action="genera_reportes.php">
      <button type="submit" name="">GENERAR REPORTES</button>
    </form>


    <!-- Navegación por pestañas -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab" aria-controls="usuarios" aria-selected="true">
          <i class="fas fa-users"></i> Usuarios
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="solicitudes-tab" data-bs-toggle="tab" data-bs-target="#solicitudes" type="button" role="tab" aria-controls="solicitudes" aria-selected="false">
          <i class="fas fa-file-alt"></i> Solicitudes
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="respuestas-tab" data-bs-toggle="tab" data-bs-target="#respuestas" type="button" role="tab" aria-controls="respuestas" aria-selected="false">
          <i class="fas fa-reply"></i> Respuestas
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="pagos-tab" data-bs-toggle="tab" data-bs-target="#pagos" type="button" role="tab" aria-controls="pagos" aria-selected="false">
          <i class="fas fa-credit-card"></i> Pagos
        </button>
      </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="myTabContent">
      <!-- Tabla de Usuarios -->
      <div class="tab-pane fade show active" id="usuarios" role="tabpanel" aria-labelledby="usuarios-tab">
        <div class="table-responsive">
          <h3>Usuarios Registrados</h3>
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($usuarios)): ?>
                <tr>
                  <td colspan="6" class="text-center">No hay usuarios registrados</td>
                </tr>
              <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                  <tr>
                    <td><?= $usuario['id'] ?></td>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td>
                      <?php
                      $rolClass = '';
                      switch ($usuario['rol']) {
                        case 'admin':
                          $rolClass = 'bg-danger';
                          break;
                        case 'desarrollador':
                          $rolClass = 'bg-warning';
                          break;
                        default:
                          $rolClass = 'bg-info';
                      }
                      ?>
                      <span class="badge <?= $rolClass ?>"><?= $usuario['rol'] ?></span>
                    </td>
                    <td><?= $usuario['fecha_registro'] ?></td>
                    <td>
                      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal<?= $usuario['id'] ?>"><i class="fas fa-edit"></i></button>


                      <form action="process.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="deleteUser">
                        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>


                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Modales para editar usuarios -->
        <?php foreach ($usuarios as $usuario): ?>
          <!-- Modal para editar usuario -->
          <div class="modal fade" id="editarUsuarioModal<?= $usuario['id'] ?>" tabindex="-1" aria-labelledby="editarUsuarioModalLabel<?= $usuario['id'] ?>" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editarUsuarioModalLabel<?= $usuario['id'] ?>">Editar Usuario</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="process.php" method="POST">
                  <div class="modal-body">
                    <input type="hidden" name="action" value="editUser">
                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

                    <div class="mb-3">
                      <label for="nombre<?= $usuario['id'] ?>" class="form-label">Nombre</label>
                      <input type="text" class="form-control" id="nombre<?= $usuario['id'] ?>" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                    </div>

                    <div class="mb-3">
                      <label for="rol<?= $usuario['id'] ?>" class="form-label">Rol</label>
                      <select class="form-select" id="rol<?= $usuario['id'] ?>" name="rol" required>
                        <option value="cliente" <?= $usuario['rol'] == 'cliente' ? 'selected' : '' ?>>Cliente</option>
                        <option value="desarrollador" <?= $usuario['rol'] == 'desarrollador' ? 'selected' : '' ?>>Desarrollador</option>
                        <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                      </select>
                    </div>

                    <div class="mb-3">
                      <label for="password<?= $usuario['id'] ?>" class="form-label">Nueva Contraseña</label>
                      <input type="password" class="form-control" id="password<?= $usuario['id'] ?>" name="password_hash" placeholder="Dejar en blanco para mantener la actual">
                      <div class="form-text">Solo complete este campo si desea cambiar la contraseña.</div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Tabla de Solicitudes -->
      <div class="tab-pane fade" id="solicitudes" role="tabpanel" aria-labelledby="solicitudes-tab">
        <div class="table-responsive">
          <h3>Solicitudes</h3>
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Fecha Creación</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($solicitudes)): ?>
                <tr>
                  <td colspan="7" class="text-center">No hay solicitudes registradas</td>
                </tr>
              <?php else: ?>
                <?php foreach ($solicitudes as $solicitud): ?>
                  <?php
                  // Obtener nombre de usuario
                  $nombreUsuario = '';
                  foreach ($usuarios as $u) {
                    if ($u['id'] == $solicitud['usuario_id']) {
                      $nombreUsuario = $u['nombre'];
                      break;
                    }
                  }

                  // Determinar clase de estado
                  $estadoClass = '';
                  switch ($solicitud['estado']) {
                    case 'pendiente':
                      $estadoClass = 'bg-warning';
                      break;
                    case 'en proceso':
                      $estadoClass = 'bg-info';
                      break;
                    case 'completado':
                      $estadoClass = 'bg-success';
                      break;
                    case 'rechazado':
                      $estadoClass = 'bg-danger';
                      break;
                  }
                  ?>
                  <tr>
                    <td><?= $solicitud['id'] ?></td>
                    <td><?= htmlspecialchars($nombreUsuario) ?></td>
                    <td><?= htmlspecialchars($solicitud['titulo']) ?></td>
                    <td>
                      <?php
                      $descripcion = $solicitud['descripcion'];
                      echo strlen($descripcion) > 50 ? htmlspecialchars(substr($descripcion, 0, 50)) . '...' : htmlspecialchars($descripcion);
                      ?>
                      <button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#descripcionModal<?= $solicitud['id'] ?>">Ver más</button>
                    </td>
                    <td><span class="badge <?= $estadoClass ?>"><?= $solicitud['estado'] ?></span></td>
                    <td><?= $solicitud['fecha_creacion'] ?></td>
                    <td>
                      <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                      <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                  </tr>

                  <!-- Modal para mostrar descripción completa -->
                  <div class="modal fade" id="descripcionModal<?= $solicitud['id'] ?>" tabindex="-1" aria-labelledby="descripcionModalLabel<?= $solicitud['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="descripcionModalLabel<?= $solicitud['id'] ?>">Descripción Completa</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <?= nl2br(htmlspecialchars($solicitud['descripcion'])) ?>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tabla de Respuestas -->
      <div class="tab-pane fade" id="respuestas" role="tabpanel" aria-labelledby="respuestas-tab">
        <div class="table-responsive">
          <h3>Respuestas</h3>
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Solicitud</th>
                <th>Desarrollador</th>
                <th>Mensaje</th>
                <th>Archivo Script</th>
                <th>Fecha</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($respuestas)): ?>
                <tr>
                  <td colspan="7" class="text-center">No hay respuestas registradas</td>
                </tr>
              <?php else: ?>
                <?php foreach ($respuestas as $respuesta): ?>
                  <?php
                  // Obtener título de solicitud
                  $tituloSolicitud = '';
                  foreach ($solicitudes as $s) {
                    if ($s['id'] == $respuesta['solicitud_id']) {
                      $tituloSolicitud = $s['titulo'];
                      break;
                    }
                  }

                  // Obtener nombre de desarrollador
                  $nombreDesarrollador = '';
                  foreach ($usuarios as $u) {
                    if ($u['id'] == $respuesta['desarrollador_id']) {
                      $nombreDesarrollador = $u['nombre'];
                      break;
                    }
                  }
                  ?>
                  <tr>
                    <td><?= $respuesta['id'] ?></td>
                    <td title="<?= htmlspecialchars($tituloSolicitud) ?>">
                      <?= strlen($tituloSolicitud) > 30 ? htmlspecialchars(substr($tituloSolicitud, 0, 30)) . '...' : htmlspecialchars($tituloSolicitud) ?>
                    </td>
                    <td><?= htmlspecialchars($nombreDesarrollador) ?></td>
                    <td>
                      <?php
                      $mensaje = $respuesta['mensaje'];
                      echo strlen($mensaje) > 50 ? htmlspecialchars(substr($mensaje, 0, 50)) . '...' : htmlspecialchars($mensaje);
                      ?>
                      <button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#mensajeModal<?= $respuesta['id'] ?>">Ver más</button>
                    </td>
                    <td>
                      <?php if (!empty($respuesta['archivo_script'])): ?>
                        <a href="<?= htmlspecialchars($respuesta['archivo_script']) ?>" class="btn btn-sm btn-success" download>
                          <i class="fas fa-download"></i> Descargar
                        </a>
                      <?php else: ?>
                        <span class="text-muted">Sin archivo</span>
                      <?php endif; ?>
                    </td>
                    <td><?= $respuesta['fecha_respuesta'] ?></td>
                    <td>
                      <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                      <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                  </tr>

                  <!-- Modal para mostrar mensaje completo -->
                  <div class="modal fade" id="mensajeModal<?= $respuesta['id'] ?>" tabindex="-1" aria-labelledby="mensajeModalLabel<?= $respuesta['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="mensajeModalLabel<?= $respuesta['id'] ?>">Mensaje Completo</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <?= nl2br(htmlspecialchars($respuesta['mensaje'])) ?>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tabla de Pagos -->
      <div class="tab-pane fade" id="pagos" role="tabpanel" aria-labelledby="pagos-tab">
        <div class="table-responsive">
          <h3>Pagos</h3>
          <table class="table table-striped table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Solicitud</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($pagos)): ?>
                <tr>
                  <td colspan="7" class="text-center">No hay pagos registrados</td>
                </tr>
              <?php else: ?>
                <?php foreach ($pagos as $pago): ?>
                  <?php
                  // Obtener nombre de usuario
                  $nombreUsuario = '';
                  foreach ($usuarios as $u) {
                    if ($u['id'] == $pago['usuario_id']) {
                      $nombreUsuario = $u['nombre'];
                      break;
                    }
                  }

                  // Obtener título de solicitud
                  $tituloSolicitud = '';
                  foreach ($solicitudes as $s) {
                    if ($s['id'] == $pago['solicitud_id']) {
                      $tituloSolicitud = $s['titulo'];
                      break;
                    }
                  }

                  // Determinar clase de estado
                  $estadoClass = '';
                  switch ($pago['estado']) {
                    case 'pendiente':
                      $estadoClass = 'bg-warning';
                      break;
                    case 'pagado':
                      $estadoClass = 'bg-success';
                      break;
                    case 'fallido':
                      $estadoClass = 'bg-danger';
                      break;
                  }
                  ?>
                  <tr>
                    <td><?= $pago['id'] ?></td>
                    <td><?= htmlspecialchars($nombreUsuario) ?></td>
                    <td title="<?= htmlspecialchars($tituloSolicitud) ?>">
                      <?= strlen($tituloSolicitud) > 30 ? htmlspecialchars(substr($tituloSolicitud, 0, 30)) . '...' : htmlspecialchars($tituloSolicitud) ?>
                    </td>
                    <td><?= number_format($pago['monto'], 2) ?> €</td>
                    <td><span class="badge <?= $estadoClass ?>"><?= $pago['estado'] ?></span></td>
                    <td><?= $pago['fecha_pago'] ?></td>
                    <td>
                      <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                      <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS y Popper -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

  <!-- Script para manejar pestañas y alertas -->
  <script>
    // Activar la pestaña correspondiente según el parámetro de URL
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const tab = urlParams.get('tab');

      if (tab) {
        const tabElement = document.querySelector(`#${tab}-tab`);
        if (tabElement) {
          const bsTab = new bootstrap.Tab(tabElement);
          bsTab.show();
        }
      }

      // Mostrar mensajes de éxito o error si existen
      const msg = urlParams.get('msg');
      const error = urlParams.get('error');

      if (msg) {
        showAlert(msg, 'success');
      }

      if (error) {
        showAlert(error, 'danger');
      }
    });

    // Función para mostrar alertas
    function showAlert(message, type) {
      const alertDiv = document.createElement('div');
      alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
      alertDiv.setAttribute('role', 'alert');
      alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;

      document.querySelector('.container-fluid').prepend(alertDiv);

      // Auto cerrar después de 5 segundos
      setTimeout(() => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alertDiv);
        bsAlert.close();
      }, 5000);
    }
  </script>
</body>

</html>
