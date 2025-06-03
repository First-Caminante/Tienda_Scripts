<?php
session_start();
require('../vendor/autoload.php');

use App\Controllers\Functions;

// Verificar si el usuario está logueado y es cliente
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'cliente') {
  header('Location: login.php');
  exit;
}

$funciones = new Functions();
$usuario_id = $_SESSION['id'];

// Obtener datos del usuario
$solicitudes = $funciones->getSolicitudesUsuario($usuario_id);
$estadisticas = $funciones->getEstadisticasUsuario($usuario_id);



//dd($_SESSION, $solicitudes, $estadisticas);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Solicitudes - Cliente</title>
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
      min-height: 100vh;
    }

    .header {
      background: linear-gradient(135deg, #0a3d2b, #0f4f36);
      padding: 2rem;
      text-align: center;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .header h1 {
      color: #4ade80;
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
      font-weight: 700;
    }

    .welcome-text {
      color: #a3a3a3;
      font-size: 1.1rem;
    }

    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 2rem;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

    .stat-card {
      background: linear-gradient(135deg, #0f3d2f, #1a5c42);
      padding: 1.5rem;
      border-radius: 12px;
      text-align: center;
      border: 1px solid rgba(74, 222, 128, 0.2);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 30px rgba(74, 222, 128, 0.1);
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: bold;
      color: #4ade80;
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: #a3a3a3;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .section {
      background-color: #0f3d2f;
      border-radius: 12px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(74, 222, 128, 0.1);
    }

    .section h2 {
      color: #4ade80;
      margin-bottom: 2rem;
      font-size: 1.8rem;
      border-bottom: 3px solid #4ade80;
      padding-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .solicitud-card {
      background: linear-gradient(135deg, #1a5c42, #0f3d2f);
      border-radius: 10px;
      padding: 2rem;
      margin-bottom: 2rem;
      border-left: 5px solid;
      position: relative;
      transition: transform 0.3s ease;
    }

    .solicitud-card:hover {
      transform: translateX(5px);
    }

    .solicitud-card.pendiente {
      border-left-color: #f59e0b;
    }

    .solicitud-card.en-proceso {
      border-left-color: #3b82f6;
    }

    .solicitud-card.completado {
      border-left-color: #22c55e;
    }

    .solicitud-card.rechazado {
      border-left-color: #ef4444;
    }

    .solicitud-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .solicitud-titulo {
      color: #ffffff;
      font-size: 1.4rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .solicitud-fecha {
      color: #a3a3a3;
      font-size: 0.9rem;
    }

    .estado {
      padding: 0.5rem 1rem;
      border-radius: 25px;
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .estado.pendiente {
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #000;
    }

    .estado.en-proceso {
      background: linear-gradient(135deg, #3b82f6, #2563eb);
      color: #fff;
    }

    .estado.completado {
      background: linear-gradient(135deg, #22c55e, #16a34a);
      color: #fff;
    }

    .estado.rechazado {
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: #fff;
    }

    .descripcion {
      color: #d1d5db;
      margin-bottom: 1.5rem;
      padding: 1rem;
      background-color: rgba(0, 0, 0, 0.2);
      border-radius: 8px;
      border-left: 3px solid #4ade80;
    }

    .respuesta-section {
      background-color: rgba(74, 222, 128, 0.05);
      padding: 1.5rem;
      border-radius: 8px;
      border: 1px solid rgba(74, 222, 128, 0.2);
      margin-top: 1rem;
    }

    .respuesta-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .script-info {
      background: linear-gradient(135deg, #4ade80, #22c55e);
      color: #000;
      padding: 1rem;
      border-radius: 8px;
      margin-top: 1rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn {
      padding: 1rem 2rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.3s ease;
      font-size: 1rem;
    }

    .btn-comprar {
      background: linear-gradient(135deg, #4ade80, #22c55e);
      color: #000;
      box-shadow: 0 4px 15px rgba(74, 222, 128, 0.3);
    }

    .btn-comprar:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(74, 222, 128, 0.4);
    }

    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: #a3a3a3;
    }

    .empty-state h3 {
      color: #4ade80;
      margin-bottom: 1rem;
      font-size: 1.5rem;
    }

    .icon {
      width: 20px;
      height: 20px;
      display: inline-block;
    }

    @media (max-width: 768px) {
      .container {
        padding: 0 1rem;
      }

      .header {
        padding: 1.5rem;
      }

      .header h1 {
        font-size: 2rem;
      }

      .section {
        padding: 1.5rem;
      }

      .solicitud-card {
        padding: 1.5rem;
      }

      .solicitud-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
  </style>
</head>

<body>
  <div class="header">
    <h1>🚀 Mis Solicitudes</h1>
    <div class="welcome-text">
      Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong>
    </div>
  </div>

  <div class="container">
    <!-- Estadísticas -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number"><?php echo $estadisticas['total_solicitudes'] ?? 0; ?></div>
        <div class="stat-label">Total Solicitudes</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $estadisticas['pendientes'] ?? 0; ?></div>
        <div class="stat-label">Pendientes</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $estadisticas['en_proceso'] ?? 0; ?></div>
        <div class="stat-label">En Proceso</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $estadisticas['completados'] ?? 0; ?></div>
        <div class="stat-label">Completados</div>
      </div>
    </div>

    <!-- Lista de Solicitudes -->
    <div class="section">
      <h2>📋 Estado de mis Solicitudes</h2>

      <?php if (empty($solicitudes) || isset($solicitudes['error'])): ?>
        <div class="empty-state">
          <h3>🔍 No tienes solicitudes aún</h3>
          <p>Cuando realices solicitudes de scripts, aparecerán aquí con su estado actual.</p>
        </div>
      <?php else: ?>
        <?php foreach ($solicitudes as $solicitud): ?>
          <div class="solicitud-card <?php echo str_replace(' ', '-', $solicitud['estado']); ?>">
            <div class="solicitud-header">
              <div>
                <div class="solicitud-titulo">
                  <?php echo htmlspecialchars($solicitud['titulo']); ?>
                </div>
                <div class="solicitud-fecha">
                  📅 Solicitado el <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_creacion'])); ?>
                </div>
              </div>
              <span class="estado <?php echo str_replace(' ', '-', $solicitud['estado']); ?>">
                <?php
                $estados_iconos = [
                  'pendiente' => '⏳ Pendiente',
                  'en proceso' => '🔄 En Proceso',
                  'completado' => '✅ Completado',
                  'rechazado' => '❌ Rechazado'
                ];
                echo $estados_iconos[$solicitud['estado']] ?? ucfirst($solicitud['estado']);
                ?>
              </span>
            </div>

            <div class="descripcion">
              <strong>📝 Descripción de tu solicitud:</strong><br>
              <?php echo nl2br(htmlspecialchars($solicitud['descripcion'])); ?>
            </div>

            <?php if (!empty($solicitud['respuesta_mensaje'])): ?>
              <div class="respuesta-section">
                <div class="respuesta-header">
                  <strong>💬 Respuesta del Desarrollador</strong>
                  <?php if (!empty($solicitud['desarrollador_nombre'])): ?>
                    <small>👨‍💻 Por: <?php echo htmlspecialchars($solicitud['desarrollador_nombre']); ?></small>
                  <?php endif; ?>
                </div>

                <p style="color: #d1d5db; margin-bottom: 1rem;">
                  <?php echo nl2br(htmlspecialchars($solicitud['respuesta_mensaje'])); ?>
                </p>

                <?php if (!empty($solicitud['fecha_respuesta'])): ?>
                  <small style="color: #a3a3a3;">
                    📅 Respondido el <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_respuesta'])); ?>
                  </small>
                <?php endif; ?>

                <?php if (!empty($solicitud['archivo_script'])): ?>
                  <div class="script-info">
                    <span class="icon">🎯</span>
                    <strong>Script disponible: <?php echo htmlspecialchars($solicitud['archivo_script']); ?></strong>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <?php if ($solicitud['estado'] === 'completado' && !empty($solicitud['archivo_script'])): ?>
              <div style="margin-top: 1.5rem; text-align: center;">
                <a href="comprar-script.php?solicitud_id=<?php echo $solicitud['id']; ?>&usuario_id=<?php echo $solicitud['usuario_id']; ?>" class="btn btn-comprar">
                  <span class="icon">🛒</span>
                  Comprar Script
                </a>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Sección de Scripts Disponibles -->
    <?php
    $scripts_completados = array_filter($solicitudes, function ($sol) {
      return $sol['estado'] === 'completado' && !empty($sol['archivo_script']);
    });
    ?>

    <?php if (!empty($scripts_completados)): ?>
      <div class="section">
        <h2>🎯 Mis Scripts Listos</h2>

        <?php foreach ($scripts_completados as $script): ?>
          <div class="solicitud-card completado" style="border-left-color: #4ade80;">
            <div class="solicitud-header">
              <div>
                <div class="solicitud-titulo">
                  🎯 <?php echo htmlspecialchars($script['titulo']); ?>
                </div>
                <div class="script-info" style="margin-top: 0.5rem;">
                  <span class="icon">📁</span>
                  Archivo: <strong><?php echo htmlspecialchars($script['archivo_script']); ?></strong>
                </div>
              </div>
              <a href="comprar-script.php?solicitud_id=<?php echo $script['id']; ?>" class="btn btn-comprar">
                <span class="icon">🛒</span>
                Comprar Ahora
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>

</html>
