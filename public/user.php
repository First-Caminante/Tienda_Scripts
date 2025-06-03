<?php

require("../vendor/autoload.php");

use Model\Usuario;


session_start();

//dd($_SESSION);

if ($_SESSION['rol'] != "cliente") {
  header('location:login.php');
}



$usuario = new Usuario();


$name = $_SESSION['nombre'];
$rol = $_SESSION['rol'];
$id = $_SESSION['id'];
$email = $_SESSION['email'];




$usuario->setNombre($name);
$usuario->setRol($rol);
$usuario->setId($id);
$usuario->setEmail($email);


?>



<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ScriptMaster - Panel de Usuario</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #092c1f;
      --primary-light: #124a35;
      --primary-dark: #051a12;
      --accent-color: #4caf50;
      --accent-light: #80e27e;
      --accent-dark: #087f23;
      --light-color: #f5f5f5;
      --gray-color: #e0e0e0;
      --text-light: #f8f9fa;
      --text-dark: #343a40;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
      color: var(--text-dark);
    }

    .navbar {
      background-color: var(--primary-color);
      padding: 12px 0;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand img {
      height: 40px;
      transition: transform 0.3s ease;
    }

    .navbar-brand:hover img {
      transform: scale(1.05);
    }

    .nav-link {
      color: var(--text-light) !important;
      font-weight: 500;
      position: relative;
      padding: 8px 16px !important;
      transition: all 0.3s ease;
    }

    .nav-link:before {
      content: "";
      position: absolute;
      width: 0;
      height: 2px;
      bottom: 0;
      left: 50%;
      background-color: var(--accent-color);
      transition: width 0.3s ease, left 0.3s ease;
    }

    .nav-link:hover:before {
      width: 100%;
      left: 0;
    }

    .nav-link:hover {
      color: var(--accent-light) !important;
    }

    .dropdown-menu {
      background-color: var(--primary-light);
      border: none;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
      padding: 0.5rem 0;
      border-radius: 8px;
      margin-top: 12px;
    }

    .dropdown-item {
      color: var(--text-light);
      padding: 8px 20px;
      transition: all 0.2s ease;
    }

    .dropdown-item:hover {
      background-color: var(--primary-dark);
      color: var(--accent-light);
      transform: translateX(5px);
    }

    .profile-dropdown .dropdown-toggle::after {
      display: none;
    }

    .profile-dropdown .dropdown-toggle {
      display: flex;
      align-items: center;
    }

    .profile-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--accent-color);
      transition: all 0.3s ease;
    }

    .profile-dropdown:hover .profile-avatar {
      border-color: var(--accent-light);
      transform: scale(1.05);
    }

    .sidebar {
      background-color: var(--primary-color);
      color: var(--text-light);
      height: calc(100vh - 70px);
      position: fixed;
      top: 70px;
      left: 0;
      width: 250px;
      padding: 20px 0;
      box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .sidebar.collapsed {
      width: 70px;
    }

    .sidebar-header {
      padding: 0 20px 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }

    .sidebar-toggle {
      background: none;
      border: none;
      color: var(--text-light);
      font-size: 20px;
      padding: 0;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .sidebar-toggle:hover {
      color: var(--accent-color);
      transform: rotate(180deg);
    }

    .sidebar-link {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      color: var(--text-light);
      text-decoration: none;
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
    }

    .sidebar-link i {
      font-size: 18px;
      width: 30px;
      text-align: center;
      transition: all 0.3s ease;
    }

    .sidebar-link span {
      margin-left: 15px;
      white-space: nowrap;
      opacity: 1;
      transition: all 0.3s ease;
    }

    .sidebar.collapsed .sidebar-link span {
      opacity: 0;
      width: 0;
      margin-left: 0;
    }

    .sidebar-link:hover,
    .sidebar-link.active {
      background-color: var(--primary-light);
      color: var(--accent-light);
      border-left-color: var(--accent-color);
    }

    .sidebar-link:hover i,
    .sidebar-link.active i {
      transform: translateX(5px);
    }

    .main-content {
      margin-left: 250px;
      padding: 30px;
      transition: all 0.3s ease;
    }

    .main-content.expanded {
      margin-left: 70px;
    }

    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      overflow: hidden;
      height: 100%;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .card-header {
      background-color: var(--primary-color);
      color: var(--text-light);
      border-bottom: none;
      padding: 1rem 1.5rem;
      font-weight: 600;
    }

    .card-icon {
      font-size: 24px;
      margin-right: 10px;
      color: var(--accent-color);
    }

    .stat-card {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
      color: white;
      border-radius: 10px;
      padding: 25px;
      transition: all 0.3s ease;
      height: 100%;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .stat-card .stat-icon {
      font-size: 40px;
      opacity: 0.8;
      margin-bottom: 15px;
      color: var(--accent-light);
    }

    .stat-card h2 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 5px;
    }

    .stat-card p {
      font-size: 16px;
      margin-bottom: 0;
      opacity: 0.9;
    }

    .progress {
      height: 8px;
      border-radius: 4px;
      margin-top: 15px;
      background-color: rgba(255, 255, 255, 0.2);
    }

    .progress-bar {
      background-color: var(--accent-color);
    }

    .script-item {
      background-color: white;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      border-left: 4px solid transparent;
    }

    .script-item:hover {
      border-left-color: var(--accent-color);
      transform: translateX(5px);
    }

    .script-icon {
      width: 50px;
      height: 50px;
      border-radius: 8px;
      background-color: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 22px;
    }

    .script-title {
      font-weight: 600;
      margin-bottom: 5px;
    }

    .script-meta {
      font-size: 12px;
      color: #6c757d;
    }

    .btn-action {
      padding: 5px 12px;
      border-radius: 50px;
      font-size: 12px;
      margin-right: 5px;
      transition: all 0.3s ease;
    }

    .btn-action:hover {
      transform: translateY(-2px);
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-primary:hover {
      background-color: var(--primary-light);
      border-color: var(--primary-light);
    }

    .btn-success {
      background-color: var(--accent-color);
      border-color: var(--accent-color);
    }

    .btn-success:hover {
      background-color: var(--accent-dark);
      border-color: var(--accent-dark);
    }

    .btn-outline-primary {
      color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-outline-primary:hover {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .notification-badge {
      position: absolute;
      top: 0;
      right: 5px;
      background-color: var(--accent-color);
      color: white;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
      }

      70% {
        transform: scale(1.1);
        box-shadow: 0 0 0 10px rgba(76, 175, 80, 0);
      }

      100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
      }
    }

    .chart-container {
      height: 300px;
      position: relative;
    }

    .activity-feed {
      max-height: 350px;
      overflow-y: auto;
    }

    .activity-item {
      padding: 12px 0;
      border-bottom: 1px solid #eee;
      display: flex;
      align-items: center;
    }

    .activity-icon {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background-color: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      margin-right: 15px;
      flex-shrink: 0;
    }

    .activity-content {
      flex-grow: 1;
    }

    .activity-time {
      font-size: 12px;
      color: #6c757d;
    }

    .search-bar {
      position: relative;
      margin-right: 20px;
    }

    .search-bar input {
      border-radius: 50px;
      padding-left: 40px;
      background-color: rgba(255, 255, 255, 0.1);
      border: none;
      color: white;
      transition: all 0.3s ease;
    }

    .search-bar input::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }

    .search-bar input:focus {
      background-color: rgba(255, 255, 255, 0.2);
      box-shadow: none;
    }

    .search-bar i {
      position: absolute;
      left: 15px;
      top: 10px;
      color: rgba(255, 255, 255, 0.6);
    }

    .breadcrumb {
      background-color: transparent;
      padding: 0;
      margin-bottom: 20px;
    }

    .breadcrumb-item a {
      color: var(--primary-color);
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .breadcrumb-item a:hover {
      color: var(--accent-color);
    }

    .breadcrumb-item.active {
      color: #6c757d;
    }

    .page-header {
      margin-bottom: 30px;
      border-bottom: 1px solid #eee;
      padding-bottom: 15px;
    }

    .quick-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 20px;
    }

    .quick-action-btn {
      flex: 1;
      text-align: center;
      padding: 15px;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      text-decoration: none;
      color: var(--text-dark);
      min-width: 120px;
    }

    .quick-action-btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
      color: var(--primary-color);
    }

    .quick-action-btn i {
      font-size: 24px;
      margin-bottom: 10px;
      color: var(--primary-color);
      transition: all 0.3s ease;
    }

    .quick-action-btn:hover i {
      color: var(--accent-color);
      transform: scale(1.2);
    }

    /* Animaciones para AOS */
    [data-aos] {
      pointer-events: auto !important;
    }

    /* Media queries */
    @media (max-width: 992px) {
      .sidebar {
        width: 70px;
      }

      .sidebar .sidebar-link span {
        opacity: 0;
        width: 0;
        margin-left: 0;
      }

      .main-content {
        margin-left: 70px;
      }

      .sidebar.expanded {
        width: 250px;
      }

      .sidebar.expanded .sidebar-link span {
        opacity: 1;
        width: auto;
        margin-left: 15px;
      }
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 20px 15px;
      }

      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .mobile-toggle {
        display: block !important;
      }
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand text-white" href="#">
        <img src="/api/placeholder/160/40" alt="ScriptMaster Logo" class="img-fluid">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <div class="search-bar d-none d-lg-block">
          <input type="text" class="form-control" placeholder="Buscar scripts...">
          <i class="fas fa-search"></i>
        </div>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="#" id="mobileToggleSidebar" style="display: none;">
              <i class="fas fa-bars"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link position-relative" href="#">
              <i class="fas fa-bell"></i>
              <span class="notification-badge">3</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link position-relative" href="#">
              <i class="fas fa-envelope"></i>
              <span class="notification-badge">5</span>
            </a>
          </li>
          <li class="nav-item dropdown profile-dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="/api/placeholder/100/100" alt="Usuario" class="profile-avatar">
              <span class="ms-2 d-none d-lg-inline"><?= $name ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Mi Perfil</a></li>
              <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Configuración</a></li>
              <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i> Mis Descargas</a></li>

              <li><a class="dropdown-item" href="manual/"><i class="fas fa-question-circle me-2"></i> AYUDA</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <button id="sidebarToggle" class="sidebar-toggle">
        <i class="fas fa-angle-left"></i>
      </button>
    </div>
    <div class="sidebar-menu">
      <a href="#" class="sidebar-link active">
        <i class="fas fa-tachometer-alt"></i>
        <span>Panel Principal</span>
      </a>
      <a href="#" class="sidebar-link">
        <i class="fas fa-code"></i>
        <span>Mis Scripts</span>
      </a>
      <a href="catalogo.php" class="sidebar-link">
        <i class="fas fa-store"></i>
        <span>Tienda</span>
      </a>
      <a href="#" class="sidebar-link">
        <i class="fas fa-download"></i>
        <span>Descargas</span>
      </a>
      <a href="#" class="sidebar-link">
        <i class="fas fa-heart"></i>
        <span>Favoritos</span>
      </a>
      <a href="solicitudes.php" class="sidebar-link">
        <i class="fas fa-comments"></i>
        <span>Solicitudes</span>
      </a>
      <a href="solicitudes_usuario.php" class="sidebar-link">
        <i class="fas fa-credit-card"></i>
        <span>Respuestas</span>
      </a>
      <a href="#" class="sidebar-link">
        <i class="fas fa-user-circle"></i>
        <span>Perfil</span>
      </a>
      <a href="catalogo.php" class="sidebar-link">
        <i class="fas fa-cog"></i>
        <span>Ver Catalogo</span>
      </a>
      <a href="logout.php" class="sidebar-link">
        <i class="fas fa-sign-out-alt"></i>
        <span>Cerrar Sesión</span>
      </a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content" id="mainContent">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Panel Principal</li>
      </ol>
    </nav>

    <div class="page-header d-flex justify-content-between align-items-center">
      <h1 class="h3">Panel Principal</h1>
      <button class="btn btn-success animate__animated animate__pulse animate__infinite">
        <i class="fas fa-plus me-2"></i>Explorar Nuevos Scripts
      </button>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions" data-aos="fade-up">
      <a href="catalogo.php" class="quick-action-btn">
        <i class="fas fa-download"></i>
        <div>Descargar</div>
      </a>
      <a href="#" class="quick-action-btn">
        <i class="fas fa-code"></i>
        <div>Nuevo Script</div>
      </a>
      <a href="solicitudes.php" class="quick-action-btn">
        <i class="fas fa-headset"></i>
        <div>Soporte</div>
      </a>
      <a href="#" class="quick-action-btn">
        <i class="fas fa-gift"></i>
        <div>Promociones</div>
      </a>
      <a href="#" class="quick-action-btn">
        <i class="fas fa-book"></i>
        <div>Documentación</div>
      </a>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
      <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card">
          <i class="fas fa-code stat-icon"></i>
          <h2>24</h2>
          <p>Scripts Adquiridos</p>
          <div class="progress">
            <div class="progress-bar" style="width: 75%"></div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="stat-card">
          <i class="fas fa-download stat-icon"></i>
          <h2>163</h2>
          <p>Descargas Totales</p>
          <div class="progress">
            <div class="progress-bar" style="width: 60%"></div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
        <div class="stat-card">
          <i class="fas fa-star stat-icon"></i>
          <h2>12</h2>
          <p>Scripts Favoritos</p>
          <div class="progress">
            <div class="progress-bar" style="width: 45%"></div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
        <div class="stat-card">
          <i class="fas fa-ticket-alt stat-icon"></i>
          <h2>3</h2>
          <p>Tickets de Soporte</p>
          <div class="progress">
            <div class="progress-bar" style="width: 30%"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts and Activity Row -->
    <div class="row mb-4">
      <div class="col-lg-8 mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-chart-line card-icon"></i>Actividad de Scripts</span>
            <div class="dropdown">
              <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Este Mes
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="#">Semana Pasada</a></li>
                <li><a class="dropdown-item" href="#">Mes Pasado</a></li>
                <li><a class="dropdown-item" href="#">Último Trimestre</a></li>
              </ul>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <img src="/api/placeholder/800/350" alt="Gráfico de actividad" class="img-fluid">
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-history card-icon"></i>Actividad Reciente</span>
            <button class="btn btn-sm btn-outline-light">Ver Todo</button>
          </div>
          <div class="card-body p-0">
            <div class="activity-feed">
              <div class="activity-item">
                <div class="activity-icon bg-success">
                  <i class="fas fa-download"></i>
                </div>
                <div class="activity-content">
                  <div>Descargado <strong>E-commerce Suite</strong></div>
                  <div class="activity-time">Hace 2 horas</div>
                </div>
              </div>
              <div class="activity-item">
                <div class="activity-icon bg-primary">
                  <i class="fas fa-star"></i>
                </div>
                <div class="activity-content">
                  <div>Añadido a favoritos <strong>Dashboard Pro</strong></div>
                  <div class="activity-time">Hace 4 horas</div>
                </div>
              </div>
              <div class="activity-item">
                <div class="activity-icon bg-warning">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="activity-content">
                  <div>Comprado <strong>Chat App Master</strong></div>
                  <div class="activity-time">Ayer</div>
                </div>
              </div>
              <div class="activity-item">
                <div class="activity-icon bg-info">
                  <i class="fas fa-comment"></i>
                </div>
                <div class="activity-content">
                  <div>Comentario en <strong>CRM Business</strong></div>
                  <div class="activity-time">Hace 2 días</div>
                </div>
              </div>
              <div class="activity-item">
                <div class="activity-icon bg-danger">
                  <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="activity-content">
                  <div>Ticket de soporte creado <strong>#34562</strong></div>
                  <div class="activity-time">Hace 3 días</div>
                </div>
              </div>
              <div class="activity-item">
                <div class="activity-icon bg-success">
                  <i class="fas fa-download"></i>
                  <div class="activity-content">
                    <div>Descargado <strong>LMS Education</strong></div>
                    <div class="activity-time">Hace 4 días</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Scripts Section -->
      <div class="row mb-4">
        <div class="col-12" data-aos="fade-up">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span><i class="fas fa-code card-icon"></i>Mis Scripts Recientes</span>
              <button class="btn btn-sm btn-outline-light">Ver Todos</button>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-lg-6 mb-3" data-aos="fade-up" data-aos-delay="100">
                  <div class="script-item d-flex align-items-center">
                    <div class="script-icon me-3">
                      <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="flex-grow-1">
                      <h5 class="script-title">E-commerce Suite</h5>
                      <div class="script-meta">Descargado: 12 Abr, 2025 · Tamaño: 4.2 MB</div>
                    </div>
                    <div class="ms-3">
                      <button class="btn btn-sm btn-primary btn-action">
                        <i class="fas fa-download me-1"></i> Descargar
                      </button>
                      <button class="btn btn-sm btn-outline-primary btn-action">
                        <i class="fas fa-info-circle"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 mb-3" data-aos="fade-up" data-aos-delay="200">
                  <div class="script-item d-flex align-items-center">
                    <div class="script-icon me-3">
                      <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="flex-grow-1">
                      <h5 class="script-title">Admin Dashboard Pro</h5>
                      <div class="script-meta">Descargado: 10 Abr, 2025 · Tamaño: 2.8 MB</div>
                    </div>
                    <div class="ms-3">
                      <button class="btn btn-sm btn-primary btn-action">
                        <i class="fas fa-download me-1"></i> Descargar
                      </button>
                      <button class="btn btn-sm btn-outline-primary btn-action">
                        <i class="fas fa-info-circle"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 mb-3" data-aos="fade-up" data-aos-delay="300">
                  <div class="script-item d-flex align-items-center">
                    <div class="script-icon me-3">
                      <i class="fas fa-comments"></i>
                    </div>
                    <div class="flex-grow-1">
                      <h5 class="script-title">Chat App Master</h5>
                      <div class="script-meta">Descargado: 8 Abr, 2025 · Tamaño: 1.5 MB</div>
                    </div>
                    <div class="ms-3">
                      <button class="btn btn-sm btn-primary btn-action">
                        <i class="fas fa-download me-1"></i> Descargar
                      </button>
                      <button class="btn btn-sm btn-outline-primary btn-action">
                        <i class="fas fa-info-circle"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 mb-3" data-aos="fade-up" data-aos-delay="400">
                  <div class="script-item d-flex align-items-center">
                    <div class="script-icon me-3">
                      <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="flex-grow-1">
                      <h5 class="script-title">Reservation System</h5>
                      <div class="script-meta">Descargado: 5 Abr, 2025 · Tamaño: 3.1 MB</div>
                    </div>
                    <div class="ms-3">
                      <button class="btn btn-sm btn-primary btn-action">
                        <i class="fas fa-download me-1"></i> Descargar
                      </button>
                      <button class="btn btn-sm btn-outline-primary btn-action">
                        <i class="fas fa-info-circle"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recommended and Support Section -->
      <div class="row">
        <div class="col-lg-8 mb-4" data-aos="fade-up" data-aos-delay="100">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span><i class="fas fa-star card-icon"></i>Scripts Recomendados Para Ti</span>
              <button class="btn btn-sm btn-outline-light">Ver Más</button>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-4">
                  <div class="card product-card h-100">
                    <div class="position-relative">
                      <img src="/api/placeholder/400/200" class="card-img-top" alt="CRM Script">
                      <span class="badge bg-success position-absolute top-0 end-0 m-2">Nuevo</span>
                    </div>
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Advanced Analytics Dashboard</h5>
                        <span class="badge bg-primary rounded-pill">$89</span>
                      </div>
                      <p class="card-text">Dashboard con analíticas avanzadas, gráficos interactivos y reportes personalizados.</p>
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="text-warning">
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star-half-alt"></i>
                          <small class="ms-1 text-muted">(126)</small>
                        </div>
                        <button class="btn btn-sm btn-success">
                          <i class="fas fa-cart-plus me-1"></i> Añadir
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="card product-card h-100">
                    <img src="/api/placeholder/400/200" class="card-img-top" alt="LMS Script">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Social Network Kit</h5>
                        <span class="badge bg-primary rounded-pill">129bs</span>
                      </div>
                      <p class="card-text">Sistema completo para crear redes sociales con perfiles, amigos, mensajes y notificaciones.</p>
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="text-warning">
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <small class="ms-1 text-muted">(89)</small>
                        </div>
                        <button class="btn btn-sm btn-success">
                          <i class="fas fa-cart-plus me-1"></i> Añadir
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
          <div class="card h-100">
            <div class="card-header">
              <i class="fas fa-headset card-icon"></i>Soporte Técnico
            </div>
            <div class="card-body">
              <div class="text-center mb-4">
                <i class="fas fa-headset fa-4x text-primary mb-3"></i>
                <h5>¿Necesitas ayuda con algún script?</h5>
                <p class="text-muted">Nuestro equipo de soporte está disponible 24/7 para resolver tus dudas.</p>
              </div>
              <div class="d-grid gap-2">
                <button class="btn btn-primary animate__animated animate__pulse animate__infinite">
                  <i class="fas fa-ticket-alt me-2"></i>Crear Ticket
                </button>
                <button class="btn btn-outline-primary">
                  <i class="fas fa-comment me-2"></i>Chat en Vivo
                </button>
              </div>
              <hr>
              <h6 class="mb-3">Artículos de ayuda populares:</h6>
              <ul class="list-unstyled">
                <li class="mb-2">
                  <a href="#" class="text-decoration-none">
                    <i class="fas fa-file-alt me-2 text-primary"></i>Guía de instalación
                  </a>
                </li>
                <li class="mb-2">
                  <a href="#" class="text-decoration-none">
                    <i class="fas fa-file-alt me-2 text-primary"></i>Solución de problemas comunes
                  </a>
                </li>
                <li class="mb-2">
                  <a href="#" class="text-decoration-none">
                    <i class="fas fa-file-alt me-2 text-primary"></i>Personalización de scripts
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Updates & News Section -->
      <div class="row mb-4">
        <div class="col-12" data-aos="fade-up">
          <div class="card">
            <div class="card-header">
              <i class="fas fa-newspaper card-icon"></i>Novedades y Actualizaciones
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                  <div class="border-start border-success border-4 ps-3">
                    <h5>Nuevas funcionalidades</h5>
                    <p class="text-muted mb-2">10 Abril, 2025</p>
                    <p>Hemos añadido nuevas opciones de personalización a nuestros scripts de E-commerce y Analytics Dashboard.</p>
                    <a href="#" class="btn btn-sm btn-outline-success">Leer más</a>
                  </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                  <div class="border-start border-primary border-4 ps-3">
                    <h5>Actualizaciones de seguridad</h5>
                    <p class="text-muted mb-2">8 Abril, 2025</p>
                    <p>Importante actualización de seguridad para todos los scripts de CRM y Admin Dashboard.</p>
                    <a href="#" class="btn btn-sm btn-outline-primary">Leer más</a>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="border-start border-warning border-4 ps-3">
                    <h5>Próximos lanzamientos</h5>
                    <p class="text-muted mb-2">5 Abril, 2025</p>
                    <p>Próxima semana lanzaremos un nuevo script de Análisis de Datos con Inteligencia Artificial.</p>
                    <a href="#" class="btn btn-sm btn-outline-warning">Leer más</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer class="mt-5 pt-4 border-top">
        <div class="row">
          <div class="col-md-6">
            <p>© 2025 ScriptMaster. Todos los derechos reservados.</p>
          </div>
          <div class="col-md-6 text-md-end">
            <a href="#" class="text-decoration-none me-3">Términos</a>
            <a href="#" class="text-decoration-none me-3">Privacidad</a>
            <a href="#" class="text-decoration-none me-3">Ayuda</a>
          </div>
        </div>
      </footer>
    </div>

    <!-- Modal Auth -->
    <div class="modal fade" id="modal-auth" tabindex="-1" aria-labelledby="modalAuthLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAuthLabel">Acceso a ScriptMaster</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <ul class="nav nav-tabs" id="authTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Iniciar Sesión</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Registrarse</button>
              </li>
            </ul>
            <div class="tab-content pt-3" id="authTabContent">
              <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                <form>
                  <div class="mb-3">
                    <label for="loginEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="loginEmail" placeholder="tu@email.com">
                  </div>
                  <div class="mb-3">
                    <label for="loginPassword" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="loginPassword">
                  </div>
                  <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Recordarme</label>
                  </div>
                  <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                  </div>
                </form>
                <div class="text-center mt-3">
                  <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                </div>
              </div>
              <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                <form>
                  <div class="mb-3">
                    <label for="registerName" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="registerName">
                  </div>
                  <div class="mb-3">
                    <label for="registerEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="registerEmail" placeholder="tu@email.com">
                  </div>
                  <div class="mb-3">
                    <label for="registerPassword" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="registerPassword">
                  </div>
                  <div class="mb-3">
                    <label for="registerConfirmPassword" class="form-label">Confirmar Contraseña</label>
                    <input type="password" class="form-control" id="registerConfirmPassword">
                  </div>
                  <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="agreeTerms">
                    <label class="form-check-label" for="agreeTerms">Acepto los términos y condiciones</label>
                  </div>
                  <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Inicializar AOS (Animate On Scroll)
        AOS.init({
          duration: 800,
          easing: 'ease-in-out',
          once: true
        });

        // Toggle Sidebar
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileToggle = document.getElementById('mobileToggleSidebar');

        // Función para alternar el sidebar
        function toggleSidebar() {
          sidebar.classList.toggle('collapsed');
          mainContent.classList.toggle('expanded');

          // Cambiar ícono del botón
          const iconElement = sidebarToggle.querySelector('i');
          if (sidebar.classList.contains('collapsed')) {
            iconElement.classList.remove('fa-angle-left');
            iconElement.classList.add('fa-angle-right');
          } else {
            iconElement.classList.remove('fa-angle-right');
            iconElement.classList.add('fa-angle-left');
          }
        }

        // Toggle en click
        if (sidebarToggle) {
          sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
          });
        }

        // Toggle en móvil
        if (mobileToggle) {
          mobileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('show');
          });
        }

        // Ajustar sidebar en tamaño móvil
        function checkScreenSize() {
          if (window.innerWidth < 768) {
            document.getElementById('mobileToggleSidebar').style.display = 'block';
          } else {
            document.getElementById('mobileToggleSidebar').style.display = 'none';
          }
        }

        // Verificar el tamaño inicial de la pantalla
        checkScreenSize();

        // Escuchar cambios de tamaño de pantalla
        window.addEventListener('resize', checkScreenSize);

        // Añadir animaciones a elementos específicos para destacarlos
        function addPulseEffect() {
          const statCards = document.querySelectorAll('.stat-card');

          statCards.forEach(card => {
            card.addEventListener('mouseover', function() {
              this.classList.add('animate__animated', 'animate__pulse');
            });

            card.addEventListener('mouseout', function() {
              this.classList.remove('animate__animated', 'animate__pulse');
            });
          });
        }

        addPulseEffect();

        // Animación para scripts recientes
        function animateScriptItems() {
          const scriptItems = document.querySelectorAll('.script-item');

          scriptItems.forEach(item => {
            item.addEventListener('mouseover', function() {
              this.style.transform = 'translateX(5px)';
              this.style.borderLeftColor = 'var(--accent-color)';
              this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
            });

            item.addEventListener('mouseout', function() {
              this.style.transform = '';
              this.style.borderLeftColor = 'transparent';
              this.style.boxShadow = '0 3px 10px rgba(0, 0, 0, 0.05)';
            });
          });
        }

        animateScriptItems();
      });
    </script>
</body>

</html>
