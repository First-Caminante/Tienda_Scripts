<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once '../vendor/autoload.php';
//require_once 'App/Controllers/Functions.php';




use App\Controllers\Functions;
use TCPDF;

session_start();

if ($_SESSION['rol'] != "admin") {
  header('location:login.php');
}




$functions = new Functions();

// Manejo de descargas PDF
if (isset($_GET['download']) && isset($_GET['type'])) {
  $reportType = $_GET['type'];

  // Crear PDF
  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $pdf->SetCreator(PDF_CREATOR);
  $pdf->SetAuthor('TiendaScripts');
  $pdf->SetTitle('Reporte - ' . ucfirst($reportType));
  $pdf->SetHeaderData('', 0, 'TiendaScripts', 'Reporte de ' . ucfirst($reportType));
  $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
  $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
  $pdf->SetFont('helvetica', '', 10);
  $pdf->AddPage();

  $html = '';

  switch ($reportType) {
    case 'usuarios':
      $usuarios = $functions->getUsuarios();
      $html = '<h2 style="color: #092c1f;">Reporte de Usuarios</h2>';
      $html .= '<table border="1" cellpadding="5">';
      $html .= '<tr style="background-color: #092c1f; color: white;"><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Fecha Registro</th></tr>';

      foreach ($usuarios as $usuario) {
        $html .= '<tr>';
        $html .= '<td>' . $usuario['id'] . '</td>';
        $html .= '<td>' . $usuario['nombre'] . '</td>';
        $html .= '<td>' . $usuario['email'] . '</td>';
        $html .= '<td>' . $usuario['rol'] . '</td>';
        $html .= '<td>' . $usuario['fecha_registro'] . '</td>';
        $html .= '</tr>';
      }
      $html .= '</table>';

      // Predicción simple
      $totalUsuarios = count($usuarios);
      $clientesCount = count(array_filter($usuarios, function ($u) {
        return $u['rol'] == 'cliente';
      }));
      $desarrolladoresCount = count(array_filter($usuarios, function ($u) {
        return $u['rol'] == 'desarrollador';
      }));

      $html .= '<br><h3 style="color: #092c1f;">Predicciones</h3>';
      $html .= '<p><strong>Total de usuarios:</strong> ' . $totalUsuarios . '</p>';
      $html .= '<p><strong>Crecimiento proyectado (próximo mes):</strong> ' . round($totalUsuarios * 1.15) . ' usuarios</p>';
      $html .= '<p><strong>Distribución:</strong> ' . round(($clientesCount / $totalUsuarios) * 100, 1) . '% Clientes, ' . round(($desarrolladoresCount / $totalUsuarios) * 100, 1) . '% Desarrolladores</p>';
      break;

    case 'solicitudes':
      $solicitudes = $functions->getSolicitudes();
      $html = '<h2 style="color: #092c1f;">Reporte de Solicitudes</h2>';
      $html .= '<table border="1" cellpadding="5">';
      $html .= '<tr style="background-color: #092c1f; color: white;"><th>ID</th><th>Usuario ID</th><th>Título</th><th>Estado</th><th>Fecha</th></tr>';

      foreach ($solicitudes as $solicitud) {
        $html .= '<tr>';
        $html .= '<td>' . $solicitud['id'] . '</td>';
        $html .= '<td>' . $solicitud['usuario_id'] . '</td>';
        $html .= '<td>' . substr($solicitud['titulo'], 0, 30) . '...</td>';
        $html .= '<td>' . $solicitud['estado'] . '</td>';
        $html .= '<td>' . $solicitud['fecha_creacion'] . '</td>';
        $html .= '</tr>';
      }
      $html .= '</table>';

      // Predicción
      $pendientes = count(array_filter($solicitudes, function ($s) {
        return $s['estado'] == 'pendiente';
      }));
      $completadas = count(array_filter($solicitudes, function ($s) {
        return $s['estado'] == 'completado';
      }));

      $html .= '<br><h3 style="color: #092c1f;">Predicciones</h3>';
      $html .= '<p><strong>Solicitudes pendientes:</strong> ' . $pendientes . '</p>';
      $html .= '<p><strong>Tiempo estimado de resolución:</strong> ' . ($pendientes * 2) . ' días</p>';
      $html .= '<p><strong>Tasa de completado:</strong> ' . round(($completadas / count($solicitudes)) * 100, 1) . '%</p>';
      break;

    case 'pagos':
      $pagos = $functions->getPagos();
      $html = '<h2 style="color: #092c1f;">Reporte de Pagos</h2>';
      $html .= '<table border="1" cellpadding="5">';
      $html .= '<tr style="background-color: #092c1f; color: white;"><th>ID</th><th>Usuario ID</th><th>Solicitud ID</th><th>Monto</th><th>Estado</th><th>Fecha</th></tr>';

      $totalIngresos = 0;
      foreach ($pagos as $pago) {
        if ($pago['estado'] == 'pagado') {
          $totalIngresos += $pago['monto'];
        }
        $html .= '<tr>';
        $html .= '<td>' . $pago['id'] . '</td>';
        $html .= '<td>' . $pago['usuario_id'] . '</td>';
        $html .= '<td>' . $pago['solicitud_id'] . '</td>';
        $html .= '<td>$' . number_format($pago['monto'], 2) . '</td>';
        $html .= '<td>' . $pago['estado'] . '</td>';
        $html .= '<td>' . $pago['fecha_pago'] . '</td>';
        $html .= '</tr>';
      }
      $html .= '</table>';

      // Predicción
      $pagosPendientes = array_filter($pagos, function ($p) {
        return $p['estado'] == 'pendiente';
      });
      $ingresosPotenciales = array_sum(array_column($pagosPendientes, 'monto'));

      $html .= '<br><h3 style="color: #092c1f;">Predicciones Financieras</h3>';
      $html .= '<p><strong>Ingresos actuales:</strong> $' . number_format($totalIngresos, 2) . '</p>';
      $html .= '<p><strong>Ingresos potenciales:</strong> $' . number_format($ingresosPotenciales, 2) . '</p>';
      $html .= '<p><strong>Proyección mensual:</strong> $' . number_format($totalIngresos * 1.2, 2) . '</p>';
      break;
  }

  $pdf->writeHTML($html, true, false, true, false, '');
  $pdf->Output('reporte_' . $reportType . '.pdf', 'D');
  exit;
}

// Obtener datos para los reportes
$usuarios = $functions->getUsuarios();
$solicitudes = $functions->getSolicitudes();
$respuestas = $functions->getRespuestas();
$pagos = $functions->getPagos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Reportes - TiendaScripts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --primary-color: #092c1f;
      --primary-light: #0d3d2a;
      --primary-dark: #051a12;
    }

    .bg-primary-custom {
      background-color: var(--primary-color) !important;
    }

    .btn-primary-custom {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      color: white;
    }

    .btn-primary-custom:hover {
      background-color: var(--primary-light);
      border-color: var(--primary-light);
    }

    .text-primary-custom {
      color: var(--primary-color) !important;
    }

    .border-primary-custom {
      border-color: var(--primary-color) !important;
    }

    .navbar-brand {
      font-weight: bold;
      font-size: 1.5rem;
    }

    .card {
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
    }

    .table-custom {
      border: 2px solid var(--primary-color);
    }

    .table-custom th {
      background-color: var(--primary-color);
      color: white;
      border-color: var(--primary-color);
    }

    .prediction-box {
      background-color: #f8f9fa;
      border-left: 4px solid var(--primary-color);
      padding: 1rem;
      margin-top: 1rem;
    }

    .chart-container {
      position: relative;
      height: 400px;
      margin: 1rem 0;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-primary-custom">
    <div class="container">
      <a class="navbar-brand text-white" href="#">TiendaScripts - Reportes</a>
    </div>
  </nav>

  <div class="container mt-4">
    <div class="row mb-4">
      <div class="col-12">
        <h1 class="text-primary-custom">Sistema de Reportes</h1>
        <p class="lead">Análisis completo de la plataforma con predicciones y visualizaciones</p>
      </div>
    </div>

    <!-- Reportes Normales con Tablas -->
    <div class="row">
      <div class="col-12">
        <h2 class="text-primary-custom mb-4">📊 Reportes Normales</h2>
      </div>
    </div>

    <!-- Reporte 1: Usuarios -->
    <div class="card">
      <div class="card-header bg-primary-custom text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Reporte de Usuarios</h4>
        <a href="?download=1&type=usuarios" class="btn btn-light btn-sm">📥 Descargar PDF</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-custom">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Fecha Registro</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach (array_slice($usuarios, 0, 5) as $usuario): ?>
                <tr>
                  <td><?php echo $usuario['id']; ?></td>
                  <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                  <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                  <td><span class="badge bg-secondary"><?php echo $usuario['rol']; ?></span></td>
                  <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="prediction-box">
          <h5 class="text-primary-custom">🔮 Predicciones de Usuarios</h5>
          <?php
          $totalUsuarios = count($usuarios);
          $clientesCount = count(array_filter($usuarios, function ($u) {
            return $u['rol'] == 'cliente';
          }));
          $desarrolladoresCount = count(array_filter($usuarios, function ($u) {
            return $u['rol'] == 'desarrollador';
          }));
          ?>
          <div class="row">
            <div class="col-md-4">
              <strong>Total actual:</strong> <?php echo $totalUsuarios; ?> usuarios
            </div>
            <div class="col-md-4">
              <strong>Crecimiento proyectado:</strong> <?php echo round($totalUsuarios * 1.15); ?> usuarios (+15%)
            </div>
            <div class="col-md-4">
              <strong>Distribución:</strong> <?php echo round(($clientesCount / $totalUsuarios) * 100, 1); ?>% Clientes
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reporte 2: Solicitudes -->
    <div class="card">
      <div class="card-header bg-primary-custom text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Reporte de Solicitudes</h4>
        <a href="?download=1&type=solicitudes" class="btn btn-light btn-sm">📥 Descargar PDF</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-custom">
            <thead>
              <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Título</th>
                <th>Estado</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach (array_slice($solicitudes, 0, 5) as $solicitud): ?>
                <tr>
                  <td><?php echo $solicitud['id']; ?></td>
                  <td><?php echo $solicitud['usuario_id']; ?></td>
                  <td><?php echo htmlspecialchars(substr($solicitud['titulo'], 0, 40)) . '...'; ?></td>
                  <td>
                    <?php
                    $badgeClass = match ($solicitud['estado']) {
                      'pendiente' => 'bg-warning',
                      'en proceso' => 'bg-info',
                      'completado' => 'bg-success',
                      'rechazado' => 'bg-danger',
                      default => 'bg-secondary'
                    };
                    ?>
                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $solicitud['estado']; ?></span>
                  </td>
                  <td><?php echo date('d/m/Y', strtotime($solicitud['fecha_creacion'])); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="prediction-box">
          <h5 class="text-primary-custom">🔮 Predicciones de Solicitudes</h5>
          <?php
          $pendientes = count(array_filter($solicitudes, function ($s) {
            return $s['estado'] == 'pendiente';
          }));
          $completadas = count(array_filter($solicitudes, function ($s) {
            return $s['estado'] == 'completado';
          }));
          $totalSolicitudes = count($solicitudes);
          ?>
          <div class="row">
            <div class="col-md-4">
              <strong>Pendientes:</strong> <?php echo $pendientes; ?> solicitudes
            </div>
            <div class="col-md-4">
              <strong>Tiempo estimado:</strong> <?php echo ($pendientes * 2); ?> días
            </div>
            <div class="col-md-4">
              <strong>Tasa completado:</strong> <?php echo round(($completadas / $totalSolicitudes) * 100, 1); ?>%
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reporte 3: Pagos -->
    <div class="card">
      <div class="card-header bg-primary-custom text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Reporte de Pagos</h4>
        <a href="?download=1&type=pagos" class="btn btn-light btn-sm">📥 Descargar PDF</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-custom">
            <thead>
              <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Solicitud</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach (array_slice($pagos, 0, 5) as $pago): ?>
                <tr>
                  <td><?php echo $pago['id']; ?></td>
                  <td><?php echo $pago['usuario_id']; ?></td>
                  <td><?php echo $pago['solicitud_id']; ?></td>
                  <td><strong>$<?php echo number_format($pago['monto'], 2); ?></strong></td>
                  <td>
                    <?php
                    $badgeClass = match ($pago['estado']) {
                      'pendiente' => 'bg-warning',
                      'pagado' => 'bg-success',
                      'fallido' => 'bg-danger',
                      default => 'bg-secondary'
                    };
                    ?>
                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $pago['estado']; ?></span>
                  </td>
                  <td><?php echo date('d/m/Y', strtotime($pago['fecha_pago'])); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="prediction-box">
          <h5 class="text-primary-custom">🔮 Predicciones Financieras</h5>
          <?php
          $totalIngresos = array_sum(array_column(array_filter($pagos, function ($p) {
            return $p['estado'] == 'pagado';
          }), 'monto'));
          $pagosPendientes = array_filter($pagos, function ($p) {
            return $p['estado'] == 'pendiente';
          });
          $ingresosPotenciales = array_sum(array_column($pagosPendientes, 'monto'));
          ?>
          <div class="row">
            <div class="col-md-4">
              <strong>Ingresos actuales:</strong> $<?php echo number_format($totalIngresos, 2); ?>
            </div>
            <div class="col-md-4">
              <strong>Potenciales:</strong> $<?php echo number_format($ingresosPotenciales, 2); ?>
            </div>
            <div class="col-md-4">
              <strong>Proyección mensual:</strong> $<?php echo number_format($totalIngresos * 1.2, 2); ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reportes Gráficos -->
    <div class="row mt-5">
      <div class="col-12">
        <h2 class="text-primary-custom mb-4">📈 Reportes Gráficos</h2>
      </div>
    </div>

    <!-- Gráfico 1: Distribución de Usuarios por Rol -->
    <div class="card">
      <div class="card-header bg-primary-custom text-white">
        <h4 class="mb-0">Distribución de Usuarios por Rol</h4>
      </div>
      <div class="card-body">
        <div class="chart-container">
          <canvas id="usuariosChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Gráfico 2: Estado de Solicitudes -->
    <div class="card">
      <div class="card-header bg-primary-custom text-white">
        <h4 class="mb-0">Estado de Solicitudes</h4>
      </div>
      <div class="card-body">
        <div class="chart-container">
          <canvas id="solicitudesChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Gráfico 3: Ingresos por Estado de Pago -->
    <div class="card">
      <div class="card-header bg-primary-custom text-white">
        <h4 class="mb-0">Ingresos por Estado de Pago</h4>
      </div>
      <div class="card-body">
        <div class="chart-container">
          <canvas id="pagosChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Datos para los gráficos
    const primaryColor = '#092c1f';
    const colors = [primaryColor, '#0d3d2a', '#4a7c59', '#87ceeb', '#ffa500'];

    // Gráfico 1: Usuario por Rol
    <?php
    $rolesData = [];
    foreach ($usuarios as $usuario) {
      $rolesData[$usuario['rol']] = ($rolesData[$usuario['rol']] ?? 0) + 1;
    }
    ?>
    const usuariosCtx = document.getElementById('usuariosChart').getContext('2d');
    new Chart(usuariosCtx, {
      type: 'pie',
      data: {
        labels: <?php echo json_encode(array_keys($rolesData)); ?>,
        datasets: [{
          data: <?php echo json_encode(array_values($rolesData)); ?>,
          backgroundColor: colors,
          borderColor: primaryColor,
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          title: {
            display: true,
            text: 'Total de usuarios: <?php echo count($usuarios); ?>'
          }
        }
      }
    });

    // Gráfico 2: Estados de Solicitudes
    <?php
    $estadosData = [];
    foreach ($solicitudes as $solicitud) {
      $estadosData[$solicitud['estado']] = ($estadosData[$solicitud['estado']] ?? 0) + 1;
    }
    ?>
    const solicitudesCtx = document.getElementById('solicitudesChart').getContext('2d');
    new Chart(solicitudesCtx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode(array_keys($estadosData)); ?>,
        datasets: [{
          label: 'Cantidad de Solicitudes',
          data: <?php echo json_encode(array_values($estadosData)); ?>,
          backgroundColor: primaryColor,
          borderColor: primaryColor,
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Gráfico 3: Pagos por Estado
    <?php
    $pagosEstados = [];
    foreach ($pagos as $pago) {
      $estado = $pago['estado'];
      $pagosEstados[$estado] = ($pagosEstados[$estado] ?? 0) + floatval($pago['monto']);
    }
    ?>
    const pagosCtx = document.getElementById('pagosChart').getContext('2d');
    new Chart(pagosCtx, {
      type: 'doughnut',
      data: {
        labels: <?php echo json_encode(array_keys($pagosEstados)); ?>,
        datasets: [{
          data: <?php echo json_encode(array_values($pagosEstados)); ?>,
          backgroundColor: colors,
          borderColor: primaryColor,
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          title: {
            display: true,
            text: 'Total en pagos: $<?php echo number_format(array_sum($pagosEstados), 2); ?>'
          }
        }
      }
    });
  </script>
</body>

</html>
