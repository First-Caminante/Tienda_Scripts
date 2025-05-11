<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Desarrollador - Tienda de Scripts</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

  <style>
    :root {
      --primary-color: #092c1f;
      --primary-light: #164032;
      --primary-dark: #051a12;
      --accent-color: #2e8b57;
      --text-light: #e0e6e3;
      --bg-light: #f0f3f1;
      --sidebar-width: 280px;
    }

    body {
      background-color: var(--bg-light);
      font-family: 'Arial', sans-serif;
      overflow-x: hidden;
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: var(--sidebar-width);
      background: var(--primary-color);
      color: var(--text-light);
      z-index: 1000;
      transition: all 0.4s ease;
      box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
    }

    .sidebar-header {
      padding: 20px;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header h3 {
      margin: 0;
      font-weight: 700;
      letter-spacing: 1px;
      font-size: 22px;
    }

    .sidebar-menu {
      padding: 20px 0;
      list-style: none;
      margin: 0;
    }

    .sidebar-menu li {
      margin-bottom: 5px;
    }

    .sidebar-menu a {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
    }

    .sidebar-menu a:hover, 
    .sidebar-menu a.active {
      color: white;
      background: rgba(255, 255, 255, 0.1);
      border-left: 3px solid var(--accent-color);
    }

    .sidebar-menu a i {
      margin-right: 15px;
      width: 24px;
      text-align: center;
    }

    .sidebar-menu .menu-label {
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: rgba(255, 255, 255, 0.5);
      padding: 10px 20px;
      margin-top: 15px;
    }

    .user-profile {
      padding: 15px 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      position: absolute;
      bottom: 0;
      width: 100%;
      display: flex;
      align-items: center;
    }

    .user-profile img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
      border: 2px solid var(--accent-color);
    }

    .user-info {
      flex: 1;
    }

    .user-info h6 {
      margin: 0;
      font-size: 14px;
      font-weight: 600;
    }

    .user-info p {
      margin: 0;
      font-size: 12px;
      opacity: 0.7;
    }

    .logout-btn {
      color: rgba(255, 255, 255, 0.7);
      background: none;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
      padding: 0;
      font-size: 18px;
    }

    .logout-btn:hover {
      color: white;
    }

    /* Main Content */
    .main-content {
      margin-left: var(--sidebar-width);
      padding: 20px 30px;
      transition: all 0.4s ease;
    }

    .content-header {
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .content-header h1 {
      font-size: 24px;
      font-weight: 600;
      color: var(--primary-color);
    }

    .breadcrumb {
      margin-bottom: 0;
    }

    .breadcrumb .breadcrumb-item a {
      color: var(--primary-color);
      text-decoration: none;
    }

    .breadcrumb-item + .breadcrumb-item::before {
      color: var(--primary-color);
    }

    .breadcrumb-item.active {
      color: var(--accent-color);
    }

    /* Cards */
    .card {
      border: none;
      border-radius: 12px;
      margin-bottom: 25px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.4s ease;
      overflow: hidden;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .card-header {
      background-color: white;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      padding: 15px 20px;
      font-weight: 600;
      color: var(--primary-color);
    }

    .card-body {
      padding: 20px;
    }

    /* Stats Cards */
    .stats-card {
      padding: 20px;
      display: flex;
      align-items: center;
    }

    .stats-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      background: var(--primary-color);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      margin-right: 20px;
    }

    .stats-info h3 {
      margin: 0;
      font-size: 22px;
      font-weight: 700;
      color: var(--primary-dark);
    }

    .stats-info p {
      margin: 0;
      font-size: 14px;
      color: #6c757d;
    }

    /* Table */
    .table {
      margin-bottom: 0;
    }

    .table th {
      border-top: 0;
      font-weight: 600;
      color: var(--primary-color);
      background-color: rgba(9, 44, 31, 0.05);
    }

    .table td {
      vertical-align: middle;
    }

    .badge-status {
      padding: 6px 12px;
      border-radius: 50px;
      font-weight: 500;
      font-size: 12px;
    }

    .badge-active {
      background-color: #d4edda;
      color: #155724;
    }

    .badge-pending {
      background-color: #fff3cd;
      color: #856404;
    }

    .badge-rejected {
      background-color: #f8d7da;
      color: #721c24;
    }

    .action-btns {
      display: flex;
      gap: 8px;
    }

    .action-btns .btn {
      padding: 4px 8px;
      font-size: 13px;
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-primary:hover {
      background-color: var(--primary-light);
      border-color: var(--primary-light);
    }

    .btn-outline-primary {
      color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-outline-primary:hover {
      background-color: var(--primary-color);
      color: white;
    }

    /* Script Editor */
    .editor-container {
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .editor-header {
      background-color: var(--primary-color);
      color: white;
      padding: 10px 15px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .editor-header h5 {
      margin: 0;
      font-size: 16px;
    }

    .editor-toolbar {
      display: flex;
      gap: 10px;
    }

    .editor-toolbar button {
      background: none;
      border: none;
      color: rgba(255, 255, 255, 0.8);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .editor-toolbar button:hover {
      color: white;
    }

    .editor-body {
      background-color: #1e1e1e;
      color: #d4d4d4;
      padding: 15px;
      height: 400px;
      overflow-y: auto;
    }

    .editor-body pre {
      margin: 0;
      white-space: pre-wrap;
      font-family: 'Consolas', 'Monaco', monospace;
      font-size: 14px;
      line-height: 1.6;
    }

    .editor-body code {
      color: inherit;
    }

    .editor-footer {
      background-color: #f8f9fa;
      padding: 10px 15px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .editor-status {
      font-size: 13px;
      color: #6c757d;
    }

    .editor-actions {
      display: flex;
      gap: 10px;
    }

    /* Form Controls */
    .form-control:focus,
    .form-select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.25rem rgba(9, 44, 31, 0.25);
    }

    .form-label {
      font-weight: 500;
      color: var(--primary-dark);
    }

    /* Animations */
    .fade-in {
      animation: fadeIn 0.6s ease forwards;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .slide-up {
      animation: slideUp 0.5s ease forwards;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 992px) {
      .sidebar {
        transform: translateX(-100%);
      }
      
      .sidebar.active {
        transform: translateX(0);
      }
      
      .main-content {
        margin-left: 0;
      }
      
      .sidebar-toggle {
        display: block;
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1001;
        width: 40px;
        height: 40px;
        background: var(--primary-color);
        color: white;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      }
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-header">
      <h3 class="animate__animated animate__fadeIn">TIENDA DE SCRIPTS</h3>
    </div>
    
    <ul class="sidebar-menu">
      <li><a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
      <li><a href="#"><i class="fas fa-code"></i> Mis Scripts</a></li>
      <li><a href="#"><i class="fas fa-plus-circle"></i> Crear Script</a></li>
      <li><a href="#"><i class="fas fa-chart-line"></i> Estadísticas</a></li>
      <li><a href="#"><i class="fas fa-dollar-sign"></i> Ventas</a></li>
      
      <div class="menu-label">Configuración</div>
      <li><a href="#"><i class="fas fa-user-cog"></i> Perfil</a></li>
      <li><a href="#"><i class="fas fa-bell"></i> Notificaciones</a></li>
      <li><a href="#"><i class="fas fa-question-circle"></i> Ayuda</a></li>
    </ul>
    
    <div class="user-profile">
      <img src="/api/placeholder/40/40" alt="Perfil">
      <div class="user-info">
        <h6>Juan Pérez</h6>
        <p>Desarrollador</p>
      </div>
      <button class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
      </button>
    </div>
  </div>

  <!-- Mobile Toggle Button -->
  <button class="sidebar-toggle d-lg-none">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Content Header -->
    <div class="content-header">
      <div class="row align-items-center">
        <div class="col">
          <h1 class="animate__animated animate__fadeIn">Dashboard de Desarrollador</h1>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </nav>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary animate__animated animate__fadeIn">
            <i class="fas fa-plus"></i> Nuevo Script
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
      <div class="col-md-3 fade-in" style="animation-delay: 0.1s">
        <div class="card stats-card">
          <div class="stats-icon">
            <i class="fas fa-code"></i>
          </div>
          <div class="stats-info">
            <h3>12</h3>
            <p>Scripts Publicados</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 fade-in" style="animation-delay: 0.2s">
        <div class="card stats-card">
          <div class="stats-icon">
            <i class="fas fa-download"></i>
          </div>
          <div class="stats-info">
            <h3>3,254</h3>
            <p>Descargas Totales</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 fade-in" style="animation-delay: 0.3s">
        <div class="card stats-card">
          <div class="stats-icon">
            <i class="fas fa-star"></i>
          </div>
          <div class="stats-info">
            <h3>4.8</h3>
            <p>Calificación Promedio</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 fade-in" style="animation-delay: 0.4s">
        <div class="card stats-card">
          <div class="stats-icon">
            <i class="fas fa-dollar-sign"></i>
          </div>
          <div class="stats-info">
            <h3>$1,245</h3>
            <p>Ganancias del Mes</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Scripts -->
    <div class="row mt-4">
      <div class="col-md-8 slide-up" style="animation-delay: 0.3s">
        <div class="card">
          <div class="card-header">
            <i class="fas fa-code me-2"></i> Scripts Recientes
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Automatización de Backups</td>
                    <td>Utilidades</td>
                    <td>15/04/2025</td>
                    <td><span class="badge-status badge-active">Activo</span></td>
                    <td>
                      <div class="action-btns">
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Análisis de Datos CSV</td>
                    <td>Análisis</td>
                    <td>10/04/2025</td>
                    <td><span class="badge-status badge-pending">Pendiente</span></td>
                    <td>
                      <div class="action-btns">
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Convertidor de Formatos</td>
                    <td>Multimedia</td>
                    <td>05/04/2025</td>
                    <td><span class="badge-status badge-active">Activo</span></td>
                    <td>
                      <div class="action-btns">
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>API REST Automatizada</td>
                    <td>Desarrollo Web</td>
                    <td>01/04/2025</td>
                    <td><span class="badge-status badge-rejected">Rechazado</span></td>
                    <td>
                      <div class="action-btns">
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer text-center">
            <a href="#" class="btn btn-sm btn-outline-primary">Ver todos los scripts</a>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 slide-up" style="animation-delay: 0.4s">
        <div class="card">
          <div class="card-header">
            <i class="fas fa-bell me-2"></i> Notificaciones
          </div>
          <div class="card-body p-0">
            <div class="list-group list-group-flush">
              <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1">Revisión completada</h6>
                  <small>Ahora</small>
                </div>
                <p class="mb-1">Tu script "Análisis de Datos CSV" ha sido revisado.</p>
              </a>
              <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1">Nueva venta</h6>
                  <small>2 horas</small>
                </div>
                <p class="mb-1">Has recibido $25 por la venta de "Automatización de Backups".</p>
              </a>
              <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1">Nueva reseña</h6>
                  <small>1 día</small>
                </div>
                <p class="mb-1">Has recibido una calificación de 5 estrellas.</p>
              </a>
              <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1">Actualización de la plataforma</h6>
                  <small>3 días</small>
                </div>
                <p class="mb-1">Nueva función: ahora puedes programar publicaciones.</p>
              </a>
            </div>
          </div>
          <div class="card-footer text-center">
            <a href="#" class="btn btn-sm btn-outline-primary">Ver todas las notificaciones</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Script Editor -->
    <div class="row mt-4">
      <div class="col-12 slide-up" style="animation-delay: 0.5s">
        <div class="card">
          <div class="card-header">
            <i class="fas fa-code me-2"></i> Editor de Scripts - Proyecto en Curso
          </div>
          <div class="card-body p-3">
            <div class="editor-container">
              <div class="editor-header">
                <h5>Automatización de Backups v2.0</h5>
                <div class="editor-toolbar">
                  <button title="Ejecutar"><i class="fas fa-play"></i></button>
                  <button title="Guardar"><i class="fas fa-save"></i></button>
                  <button title="Descargar"><i class="fas fa-download"></i></button>
                  <button title="Expandir"><i class="fas fa-expand"></i></button>
                </div>
              </div>
              <div class="editor-body">
                <pre><code>#!/bin/bash
# Script de Automatización de Backups v2.0
# Desarrollado por: Juan Chambi 

# Configuración
SOURCE_DIR="/var/www/html"
BACKUP_DIR="/mnt/backups"
MAX_BACKUPS=7
DATE=$(date +"%Y-%m-%d_%H-%M-%S")
FILENAME="backup-$DATE.tar.gz"

# Colores para mensajes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Iniciando proceso de backup...${NC}"

# Verificar directorios
if [ ! -d "$SOURCE_DIR" ]; then
    echo -e "${RED}Error: El directorio fuente no existe.${NC}"
    exit 1
fi

if [ ! -d "$BACKUP_DIR" ]; then
    echo -e "${YELLOW}Creando directorio de backups...${NC}"
    mkdir -p "$BACKUP_DIR"
fi

# Crear backup comprimido
echo -e "Comprimiendo archivos de $SOURCE_DIR..."
tar -czf "$BACKUP_DIR/$FILENAME" "$SOURCE_DIR" 2>/dev/null

# Verificar si el backup fue exitoso
if [ $? -eq 0 ]; then
    echo -e "${GREEN}Backup completado con éxito: $BACKUP_DIR/$FILENAME${NC}"
    
    # Limpiar backups antiguos
    echo -e "Revisando backups antiguos..."
    BACKUP_COUNT=$(ls -1 "$BACKUP_DIR"/backup-*.tar.gz 2>/dev/null | wc -l)
    
    if [ $BACKUP_COUNT -gt $MAX_BACKUPS ]; then
        echo -e "Eliminando backups antiguos (manteniendo los últimos $MAX_BACKUPS)..."
        ls -1t "$BACKUP_DIR"/backup-*.tar.gz | tail -n +$(($MAX_BACKUPS + 1)) | xargs rm -f
    fi
    
    echo -e "${GREEN}¡Proceso finalizado correctamente!${NC}"
else
    echo -e "${RED}Error al crear el backup.${NC}"
    exit 1
fi</code></pre>
              </div>
              <div class="editor-footer">
                <div class="editor-status">Última modificación: 15/04/2025 10:30</div>
                <div class="editor-actions">
                  <button class="btn btn-sm btn-outline-secondary">Formato</button>
                  <button class="btn btn-sm btn-outline-primary">Guardar</button>
                  <button class="btn btn-sm btn-primary">Publicar</button>
                </div>
              </div>
            </div>
            
            <div class="row mt-4">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="scriptTitle" class="form-label">Título del Script</label>
                  <input type="text" class="form-control" id="scriptTitle" value="Automatización de Backups v2.0">
                </div>
                <div class="mb-3">
                  <label for="scriptCategory" class="form-label">Categoría</label>
                  <select class="form-select" id="scriptCategory">
                    <option selected>Utilidades</option>
                    <option>Análisis</option>
                    <option>Desarrollo Web</option>
                    <option>Seguridad</option>
                    <option>Multimedia</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="scriptDesc" class="form-label">Descripción</label>
                  <textarea class="form-control" id="scriptDesc" rows="4">Script avanzado para automatizar backups con rotación, compresión y notificaciones. Compatible con Linux y macOS.</textarea>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="scriptPrice" class="form-label">Precio ($)</label>
                  <input type="number" class="form-control" id="scriptPrice" value="29.99">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="scriptTags" class="form-label">Etiquetas (separadas por comas)</label>
                  <input type="text" class="form-control" id="scriptTags" value="backup, automatización, seguridad, shell">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Toggle sidebar on mobile
      const sidebarToggle = document.querySelector('.sidebar-toggle');
      const sidebar = document.querySelector('.sidebar');
      
      if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
          sidebar.classList.toggle('active');
        });
      }
      
      // Animate elements when they come into view
      const animateElements = function() {
        const fadeElements = document.querySelectorAll('.fade-in:not(.animated)');
        const slideElements = document.querySelectorAll('.slide-up:not(.animated)');
        
        fadeElements.forEach(el => {
          const rect = el.getBoundingClientRect();
          if (rect.top <= window.innerHeight - 100) {
            el.classList.add('animated');
          }
        });
        
        slideElements.forEach(el => {
          const rect = el.getBoundingClientRect();
          if (rect.top <= window.innerHeight - 100) {
            el.classList.add('animated');
          }
        });
      };
      
      // Run once on load and then on scroll
      animateElements();
      window.addEventListener('scroll', animateElements);
      
      // Simulate loading for demo purposes
      setTimeout(() => {
        document.querySelectorAll('.animate__animated').forEach(el => {
          el.classList.add('animate__fadeIn');
        });
      }, 300);
      
      // Handle logout button click
      const logoutBtn = document.querySelector('.logout-btn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
          // Here you would typically make an API call to logout
          alert('Sesión cerrada con éxito');
          // Then redirect to login page
          window.location.href = 'index.php';
        });
      }
      
      // Handle editor actions
      const editorActions = document.querySelectorAll('.editor-toolbar button, .editor-actions button');
      editorActions.forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          const action = this.querySelector('i').className.split(' ')[1];
          
          switch(action) {
            case 'fa-play':
              alert('Ejecutando script... (simulación)');
              break;
            case 'fa-save':
              alert('Script guardado correctamente');
              break;
            case 'fa-download':
              alert('Descargando script...');
              break;
            case 'fa-expand':
              alert('Pantalla completa activada');
              break;
            default:
              if (this.textContent.includes('Publicar')) {
                alert('Script publicado con éxito');
              } else if (this.textContent.includes('Guardar')) {
                alert('Cambios guardados');
              }
          }
        });
      });
      
      // Handle table actions
      const tableActions = document.querySelectorAll('.action-btns button');
      tableActions.forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          const action = this.querySelector('i').className.split(' ')[1];
          const scriptName = this.closest('tr').querySelector('td:first-child').textContent;
          
          switch(action) {
            case 'fa-edit':
              alert(`Editando: ${scriptName}`);
              break;
            case 'fa-eye':
              alert(`Viendo detalles de: ${scriptName}`);
              break;
            case 'fa-trash':
              if (confirm(`¿Estás seguro de eliminar "${scriptName}"?`)) {
                this.closest('tr').style.opacity = '0';
                setTimeout(() => {
                  this.closest('tr').remove();
                }, 300);
              }
              break;
          }
        });
      });
    });
  </script>
</body>
</html>
