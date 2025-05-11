<?php
/*session_start();
// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
  header('Location: login.php');
  exit;
}*/

require_once '../vendor/autoload.php';

use App\Controllers\Functions;

$funciones = new Functions();

// Obtener estadísticas generales
$estadisticas = $funciones->getEstadisticasGenerales();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reportes - Tienda Scripts</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome para los iconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Chart.js para las gráficas -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- jsPDF para generar PDF -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

  <script src="reports_utils.js"></script>
  <style>
    .chart-container {
      position: relative;
      height: 300px;
      width: 100%;
      margin-bottom: 30px;
    }

    .report-card {
      transition: all 0.3s ease;
    }

    .report-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
    }

    .stats-card {
      border-left: 4px solid #0d6efd;
      transition: all 0.3s ease;
    }

    .stats-card:hover {
      background-color: #f8f9fa;
    }
  </style>
</head>

<body>
  <div class="container py-5">
    <div class="row mb-4">
      <div class="col-12">
        <h1 class="text-center mb-4">Panel de Reportes</h1>
        <div class="d-flex justify-content-between align-items-center">
          <a href="admin.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Panel
          </a>
          <div>
            <button id="generateAllReports" class="btn btn-primary">
              <i class="fas fa-file-pdf"></i> Generar Todos los Reportes
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-header bg-white">
            <h4 class="mb-0">Estadísticas Generales</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3 mb-3">
                <div class="card stats-card">
                  <div class="card-body">
                    <h5 class="card-title text-muted">Total Usuarios</h5>
                    <h2 class="mb-0"><?= $estadisticas['total_usuarios'] ?? 0 ?></h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3 mb-3">
                <div class="card stats-card">
                  <div class="card-body">
                    <h5 class="card-title text-muted">Total Solicitudes</h5>
                    <h2 class="mb-0"><?= $estadisticas['total_solicitudes'] ?? 0 ?></h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3 mb-3">
                <div class="card stats-card">
                  <div class="card-body">
                    <h5 class="card-title text-muted">Total Pagos</h5>
                    <h2 class="mb-0"><?= $estadisticas['total_pagos'] ?? 0 ?></h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3 mb-3">
                <div class="card stats-card">
                  <div class="card-body">
                    <h5 class="card-title text-muted">Monto Total</h5>
                    <h2 class="mb-0">$<?= number_format($estadisticas['monto_total_pagos'] ?? 0, 2) ?></h2>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráficos para Reportes -->
    <div class="row">
      <!-- Reporte 1: Solicitudes por Estado -->
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm report-card">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Solicitudes por Estado</h5>
            <button class="btn btn-sm btn-success generate-report" data-report="solicitudes">
              <i class="fas fa-download"></i> PDF
            </button>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="solicitudesChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Reporte 2: Pagos Mensuales -->
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm report-card">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pagos Mensuales</h5>
            <button class="btn btn-sm btn-success generate-report" data-report="pagos">
              <i class="fas fa-download"></i> PDF
            </button>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="pagosChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Reporte 3: Usuarios por Rol -->
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm report-card">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Usuarios por Rol</h5>
            <button class="btn btn-sm btn-success generate-report" data-report="usuarios">
              <i class="fas fa-download"></i> PDF
            </button>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="usuariosChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery y Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Función para cargar los datos y crear las gráficas
    function cargarDatos() {
      // Solicitar los datos para los reportes
      $.ajax({
        url: 'process.php',
        type: 'POST',
        data: {
          action: 'getReportData'
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            crearGraficoSolicitudes(response.solicitudes);
            crearGraficoPagos(response.pagos);
            crearGraficoUsuarios(response.usuarios);
          } else {
            alert('Error al cargar los datos: ' + response.message);
          }
        },
        error: function() {
          alert('Error de conexión al cargar los datos.');
        }
      });
    }

    // Crear gráfico de solicitudes
    function crearGraficoSolicitudes(datos) {
      const ctx = document.getElementById('solicitudesChart').getContext('2d');

      const labels = datos.map(item => {
        // Capitalizar primera letra
        return item.estado.charAt(0).toUpperCase() + item.estado.slice(1);
      });

      const valores = datos.map(item => item.cantidad);

      // Colores para los diferentes estados
      const colores = {
        'pendiente': '#FFC107',
        'en proceso': '#17A2B8',
        'completado': '#28A745',
        'rechazado': '#DC3545'
      };

      const backgroundColors = datos.map(item => colores[item.estado] || '#6C757D');

      new Chart(ctx, {
        type: 'pie',
        data: {
          labels: labels,
          datasets: [{
            data: valores,
            backgroundColor: backgroundColors,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.raw || 0;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = Math.round((value / total) * 100);
                  return `${label}: ${value} (${percentage}%)`;
                }
              }
            }
          }
        }
      });
    }

    // Crear gráfico de pagos mensuales
    function crearGraficoPagos(datos) {
      const ctx = document.getElementById('pagosChart').getContext('2d');

      const labels = datos.map(item => item.nombre_mes);
      const valores = datos.map(item => parseFloat(item.total_pagos));
      const cantidades = datos.map(item => parseInt(item.cantidad_pagos));

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
              label: 'Monto Total ($)',
              data: valores,
              backgroundColor: 'rgba(54, 162, 235, 0.5)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1,
              yAxisID: 'y'
            },
            {
              label: 'Cantidad de Pagos',
              data: cantidades,
              type: 'line',
              fill: false,
              backgroundColor: 'rgba(255, 99, 132, 0.5)',
              borderColor: 'rgba(255, 99, 132, 1)',
              borderWidth: 2,
              tension: 0.1,
              yAxisID: 'y1'
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              position: 'left',
              title: {
                display: true,
                text: 'Monto ($)'
              }
            },
            y1: {
              beginAtZero: true,
              position: 'right',
              grid: {
                drawOnChartArea: false
              },
              title: {
                display: true,
                text: 'Cantidad'
              }
            }
          },
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    }

    // Crear gráfico de usuarios por rol
    function crearGraficoUsuarios(datos) {
      const ctx = document.getElementById('usuariosChart').getContext('2d');

      const labels = datos.map(item => {
        // Capitalizar primera letra
        return item.rol.charAt(0).toUpperCase() + item.rol.slice(1);
      });

      const valores = datos.map(item => item.cantidad);

      // Colores para los diferentes roles
      const colores = {
        'cliente': '#28A745',
        'desarrollador': '#17A2B8',
        'admin': '#DC3545'
      };

      const backgroundColors = datos.map(item => colores[item.rol] || '#6C757D');

      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: valores,
            backgroundColor: backgroundColors,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.raw || 0;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = Math.round((value / total) * 100);
                  return `${label}: ${value} (${percentage}%)`;
                }
              }
            }
          }
        }
      });
    }


    /*function crearGraficoPagos(datos) {
      const ctx = document.getElementById('pagosChart').getContext('2d');

      // Verifica que el canvas existe
      if (!ctx) {
        console.error('No se encontró pagosChart');
        return;
      }

      // Verifica que hay datos
      if (!datos || datos.length === 0) {
        console.error('Datos vacíos para pagos');
        return;
      }

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: datos.map(item => item.nombre_mes),
          datasets: [{
            label: 'Monto Total ($)',
            data: datos.map(item => item.total_pagos),
            backgroundColor: 'rgba(54, 162, 235, 0.5)'
          }]
        }
      });
    }

    function crearGraficoUsuarios(datos) {
      const ctx = document.getElementById('usuariosChart').getContext('2d');

      if (!ctx) {
        console.error('No se encontró usuariosChart');
        return;
      }

      if (!datos || datos.length === 0) {
        console.error('Datos vacíos para usuarios');
        return;
      }

      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: datos.map(item => item.rol),
          datasets: [{
            data: datos.map(item => item.cantidad),
            backgroundColor: [
              '#FF6384', '#36A2EB', '#FFCE56'
            ]
          }]
        }
      });
    }
     */
    // Función para generar reportes en PDF
    function generarReportePDF(tipoReporte) {
      $.ajax({
        url: 'process.php',
        type: 'POST',
        data: {
          action: 'generateReport',
          reportType: tipoReporte
        },
        xhrFields: {
          responseType: 'blob'
        },
        success: function(blob) {
          const link = document.createElement('a');
          link.href = window.URL.createObjectURL(blob);
          link.download = `reporte_${tipoReporte}_${new Date().toISOString().slice(0, 10)}.pdf`;
          link.click();
        },
        error: function() {
          alert('Error al generar el reporte PDF.');
        }
      });
    }

    // Eventos para los botones de reportes
    $(document).ready(function() {
      // Cargar los datos al iniciar
      cargarDatos();

      // Evento para generar reportes individuales
      $('.generate-report').on('click', function() {
        const reportType = $(this).data('report');
        generarReportePDF(reportType);
      });

      // Evento para generar todos los reportes
      $('#generateAllReports').on('click', function() {
        generarReportePDF('all');
      });
    });
  </script>
</body>

</html>
