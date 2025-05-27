<?php
session_start();
require('../vendor/autoload.php');

use App\Controllers\Functions;

// Verificar si el usuario está logueado
if (!isset($_SESSION['id']) || !isset($_SESSION['rol'])) {
  header('Location: login.php');
  exit;
}

$funciones = new Functions();
$mensaje = '';
$error = '';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action'])) {
    switch ($_POST['action']) {
      case 'crear_solicitud':
        $titulo = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($titulo) || empty($descripcion)) {
          $error = 'El título y la descripción son obligatorios.';
        } else {
          $data = [
            'usuario_id' => $_SESSION['id'],
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'estado' => 'pendiente'
          ];

          $result = $funciones->crearSolicitud($data);

          if ($result['success']) {
            $mensaje = 'Solicitud creada exitosamente.';
          } else {
            $error = $result['message'];
          }
        }
        break;

      case 'eliminar_solicitud':
        $solicitud_id = intval($_POST['solicitud_id'] ?? 0);

        if ($solicitud_id > 0) {
          // Solo permitir eliminar solicitudes propias
          $result = $funciones->eliminarSolicitud($solicitud_id, $_SESSION['id']);

          if ($result['success']) {
            $mensaje = 'Solicitud eliminada exitosamente.';
          } else {
            $error = $result['message'];
          }
        } else {
          $error = 'ID de solicitud inválido.';
        }
        break;
    }
  }
}

// Obtener solicitudes del usuario
$solicitudes = $funciones->getSolicitudesByUsuario($_SESSION['id']);
$estadisticas = $funciones->getEstadisticasSolicitudesUsuario($_SESSION['id']);

// Función para formatear fecha
function formatearFecha($fecha)
{
  return date('d/m/Y H:i', strtotime($fecha));
}

// Función para obtener clase CSS del estado
function getEstadoClass($estado)
{
  switch ($estado) {
    case 'pendiente':
      return 'estado-pendiente';
    case 'en proceso':
      return 'estado-proceso';
    case 'completado':
      return 'estado-completado';
    case 'rechazado':
      return 'estado-rechazado';
    default:
      return 'estado-pendiente';
  }
}

// Función para obtener iniciales del nombre
function getIniciales($nombre)
{
  $palabras = explode(' ', $nombre);
  $iniciales = '';
  foreach ($palabras as $palabra) {
    if (!empty($palabra)) {
      $iniciales .= strtoupper($palabra[0]);
    }
  }
  return substr($iniciales, 0, 2);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Solicitudes - Tienda Scripts</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #092c1f 0%, #0f3d2b 50%, #092c1f 100%);
      min-height: 100vh;
      color: #ffffff;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 20px;
      border-radius: 15px;
      margin-bottom: 30px;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .header h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
      background: linear-gradient(45deg, #4ade80, #22c55e);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .user-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 15px;
    }

    .user-details {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .user-avatar {
      width: 50px;
      height: 50px;
      background: linear-gradient(45deg, #4ade80, #22c55e);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      font-weight: bold;
      color: #092c1f;
    }

    .alert {
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-weight: 500;
    }

    .alert-success {
      background: rgba(34, 197, 94, 0.2);
      color: #22c55e;
      border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .alert-error {
      background: rgba(239, 68, 68, 0.2);
      color: #ef4444;
      border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 20px;
      border-radius: 15px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(74, 222, 128, 0.3);
    }

    .stat-number {
      font-size: 2rem;
      font-weight: bold;
      color: #4ade80;
      margin-bottom: 5px;
    }

    .main-content {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 30px;
    }

    .form-section {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 25px;
      border-radius: 15px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      height: fit-content;
    }

    .form-section h2 {
      margin-bottom: 20px;
      color: #4ade80;
      font-size: 1.5rem;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #ffffff;
      font-weight: 500;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 2px solid rgba(255, 255, 255, 0.2);
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.1);
      color: #ffffff;
      font-size: 16px;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #4ade80;
      box-shadow: 0 0 10px rgba(74, 222, 128, 0.3);
    }

    .form-group textarea {
      resize: vertical;
      min-height: 120px;
    }

    .btn {
      padding: 12px 25px;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }

    .btn-primary {
      background: linear-gradient(45deg, #4ade80, #22c55e);
      color: #092c1f;
    }

    .btn-primary:hover {
      background: linear-gradient(45deg, #22c55e, #16a34a);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(74, 222, 128, 0.4);
    }

    .btn-danger {
      background: linear-gradient(45deg, #ef4444, #dc2626);
      color: white;
      padding: 8px 16px;
      font-size: 14px;
    }

    .btn-danger:hover {
      background: linear-gradient(45deg, #dc2626, #b91c1c);
    }

    .btn-secondary {
      background: rgba(255, 255, 255, 0.1);
      color: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    .solicitudes-section {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 25px;
      border-radius: 15px;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .solicitudes-section h2 {
      margin-bottom: 20px;
      color: #4ade80;
      font-size: 1.5rem;
    }

    .solicitud-card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }

    .solicitud-card:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateX(5px);
    }

    .solicitud-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .solicitud-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: #ffffff;
      margin: 0;
    }

    .solicitud-fecha {
      color: #a1a1aa;
      font-size: 0.9rem;
    }

    .solicitud-descripcion {
      color: #d4d4d8;
      margin-bottom: 15px;
      line-height: 1.5;
    }

    .solicitud-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
    }

    .estado-badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
    }

    .estado-pendiente {
      background: rgba(234, 179, 8, 0.2);
      color: #fbbf24;
      border: 1px solid rgba(251, 191, 36, 0.3);
    }

    .estado-proceso {
      background: rgba(59, 130, 246, 0.2);
      color: #3b82f6;
      border: 1px solid rgba(59, 130, 246, 0.3);
    }

    .estado-completado {
      background: rgba(34, 197, 94, 0.2);
      color: #22c55e;
      border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .estado-rechazado {
      background: rgba(239, 68, 68, 0.2);
      color: #ef4444;
      border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #a1a1aa;
    }

    .empty-state h3 {
      margin-bottom: 10px;
      color: #ffffff;
    }

    @media (max-width: 768px) {
      .main-content {
        grid-template-columns: 1fr;
      }

      .stats-cards {
        grid-template-columns: repeat(2, 1fr);
      }

      .user-info {
        flex-direction: column;
        text-align: center;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h1>Mis Solicitudes</h1>
      <div class="user-info">
        <div class="user-details">
          <div class="user-avatar"><?php echo getIniciales($_SESSION['nombre']); ?></div>
          <div>
            <div><strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></div>
            <div style="color: #a1a1aa;"><?php echo htmlspecialchars($_SESSION['email']); ?></div>
            <div style="color: #4ade80; font-size: 0.9rem;"><?php echo ucfirst($_SESSION['rol']); ?></div>
          </div>
        </div>
        <div>
          <?php if ($_SESSION['rol'] === 'admin'): ?>
            <a href="admin.php" class="btn btn-secondary">← Panel Admin</a>
          <?php else: ?>
            <a href="user.php" class="btn btn-secondary">← Dashboard</a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php if ($mensaje): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-number"><?php echo $estadisticas['total']; ?></div>
        <div>Total Solicitudes</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $estadisticas['pendientes']; ?></div>
        <div>Pendientes</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $estadisticas['en_proceso']; ?></div>
        <div>En Proceso</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $estadisticas['completadas']; ?></div>
        <div>Completadas</div>
      </div>
    </div>

    <div class="main-content">
      <div class="form-section">
        <h2>Nueva Solicitud</h2>
        <form method="POST" action="">
          <input type="hidden" name="action" value="crear_solicitud">

          <div class="form-group">
            <label for="titulo">Título *</label>
            <input type="text" id="titulo" name="titulo" required maxlength="255"
              placeholder="Título de tu solicitud...">
          </div>

          <div class="form-group">
            <label for="descripcion">Descripción *</label>
            <textarea id="descripcion" name="descripcion" required
              placeholder="Describe detalladamente tu solicitud..."></textarea>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%;">
            Enviar Solicitud
          </button>
        </form>
      </div>

      <div class="solicitudes-section">
        <h2>Historial de Solicitudes</h2>

        <?php if (empty($solicitudes)): ?>
          <div class="empty-state">
            <h3>No tienes solicitudes</h3>
            <p>Crea tu primera solicitud usando el formulario de la izquierda</p>
          </div>
        <?php else: ?>
          <?php foreach ($solicitudes as $solicitud): ?>
            <div class="solicitud-card">
              <div class="solicitud-header">
                <h3 class="solicitud-title"><?php echo htmlspecialchars($solicitud['titulo']); ?></h3>
                <span class="solicitud-fecha"><?php echo formatearFecha($solicitud['fecha_creacion']); ?></span>
              </div>
              <p class="solicitud-descripcion"><?php echo nl2br(htmlspecialchars($solicitud['descripcion'])); ?></p>
              <div class="solicitud-footer">
                <span class="estado-badge <?php echo getEstadoClass($solicitud['estado']); ?>">
                  <?php echo ucfirst($solicitud['estado']); ?>
                </span>
                <?php if ($solicitud['estado'] === 'pendiente'): ?>
                  <form method="POST" action="" style="display: inline;"
                    onsubmit="return confirm('¿Estás seguro de eliminar esta solicitud?');">
                    <input type="hidden" name="action" value="eliminar_solicitud">
                    <input type="hidden" name="solicitud_id" value="<?php echo $solicitud['id']; ?>">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        setTimeout(() => {
          alert.style.opacity = '0';
          alert.style.transform = 'translateY(-20px)';
          setTimeout(() => {
            alert.remove();
          }, 300);
        }, 5000);
      });
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
      const titulo = document.getElementById('titulo').value.trim();
      const descripcion = document.getElementById('descripcion').value.trim();

      if (!titulo || !descripcion) {
        e.preventDefault();
        alert('Por favor, completa todos los campos obligatorios.');
        return false;
      }

      if (titulo.length < 5) {
        e.preventDefault();
        alert('El título debe tener al menos 5 caracteres.');
        return false;
      }

      if (descripcion.length < 10) {
        e.preventDefault();
        alert('La descripción debe tener al menos 10 caracteres.');
        return false;
      }
    });
  </script>
</body>

</html>
