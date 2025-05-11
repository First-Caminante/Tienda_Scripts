<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ScriptMaster - Tu tienda de scripts profesionales</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #092c1f;
      --secondary-color: #2dce89;
      --dark-color: #32325d;
      --light-color: #f8f9fe;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--light-color);
    }

    .navbar {
      box-shadow: 0 4px 6px rgba(50, 50, 93, 0.1);
      background-color: white;
      padding: 15px 0;
    }

    .navbar-brand img {
      height: 40px;
    }

    .nav-link {
      color: var(--dark-color) !important;
      font-weight: 500;
      margin: 0 10px;
      transition: all 0.3s;
    }

    .nav-link:hover {
      color: var(--primary-color) !important;
    }

    .btn-auth {
      background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
      border: none;
      padding: 12px 30px;
      border-radius: 50px;
      font-weight: 600;
      color: white;
      transition: all 0.3s;
    }

    .btn-auth:hover {
      transform: translateY(-2px);
      box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
    }

    .hero {
      padding: 100px 0;
      background: linear-gradient(135deg, #092c1f 0%, #092c1f 100%);
      color: white;
    }

    .hero h1 {
      font-weight: 700;
      font-size: 3rem;
      margin-bottom: 20px;
    }

    .hero p {
      font-size: 1.2rem;
      margin-bottom: 30px;
      opacity: 0.9;
    }

    .btn-primary {
      background-color: var(--secondary-color);
      border: none;
      padding: 12px 30px;
      border-radius: 50px;
      font-weight: 600;
      transition: all 0.3s;
    }

    .btn-primary:hover {
      background-color: #25a870;
      transform: translateY(-2px);
      box-shadow: 0 7px 14px rgba(45, 206, 137, 0.2);
    }

    .features {
      padding: 80px 0;
      background-color: white;
    }

    .feature-box {
      padding: 30px;
      margin-bottom: 30px;
      border-radius: 10px;
      background-color: white;
      box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
      transition: all 0.3s;
    }

    .feature-box:hover {
      transform: translateY(-5px);
    }

    .feature-box i {
      font-size: 40px;
      color: var(--primary-color);
      margin-bottom: 20px;
    }

    .product-card {
      border: none;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1);
      transition: all 0.3s;
      margin-bottom: 30px;
    }

    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(50, 50, 93, 0.15);
    }

    .product-card img {
      height: 200px;
      object-fit: cover;
    }

    .badge-custom {
      background-color: var(--secondary-color);
      color: white;
      font-weight: 500;
      border-radius: 50px;
      padding: 5px 15px;
    }

    footer {
      background-color: var(--dark-color);
      color: white;
      padding: 50px 0 20px;
    }

    .social-links a {
      display: inline-block;
      width: 40px;
      height: 40px;
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      text-align: center;
      line-height: 40px;
      color: white;
      margin-right: 10px;
      transition: all 0.3s;
    }

    .social-links a:hover {
      background-color: var(--primary-color);
      transform: translateY(-3px);
    }

    .footer-links li {
      margin-bottom: 15px;
    }

    .footer-links a {
      color: rgba(255, 255, 255, 0.7);
      text-decoration: none;
      transition: all 0.3s;
    }

    .footer-links a:hover {
      color: white;
      padding-left: 5px;
    }

    #modal-auth .modal-content {
      border-radius: 15px;
      overflow: hidden;
    }

    #modal-auth .modal-header {
      background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
      color: white;
      border-bottom: none;
    }

    #modal-auth .nav-tabs {
      border-bottom: none;
      margin-bottom: 20px;
    }

    #modal-auth .nav-link {
      border: none;
      border-radius: 0;
      padding: 10px 20px;
      font-weight: 600;
      color: #8898aa;
    }

    #modal-auth .nav-link.active {
      border-bottom: 3px solid var(--primary-color);
      color: var(--primary-color);
      background-color: transparent;
    }

    .testimonial {
      padding: 30px;
      border-radius: 10px;
      background: white;
      box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1);
      margin: 15px 0;
    }

    .testimonial-img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
    }

    .stats-container {
      padding: 60px 0;
      background: linear-gradient(135deg, #092c1f 0%, #092c1f 100%);
      color: white;
    }

    .stat-item h2 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .stat-item p {
      font-size: 1.1rem;
      opacity: 0.9;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img src="images/logo.jpg" alt="ScriptMaster Logo" class="img-fluid">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="#">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#features">Características</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#products">Scripts</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#testimonials">Testimonios</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#pricing">Precios</a>
          </li>
        </ul>
        <button class="btn btn-auth" data-bs-toggle="modal" data-bs-target="#modal-auth">
          <a href="login.php" class="btn btn-auth">
            <i class="fas fa-user me-2"></i>Iniciar sesión / Registrarse
          </a>
        </button>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero" id="home">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h1>Los mejores scripts para impulsar tu proyecto</h1>
          <p>Descubre nuestra colección de scripts premium diseñados para desarrolladores. Ahorra tiempo y mejora la calidad de tus proyectos con soluciones probadas.</p>
          <a href="#products" class="btn btn-primary me-3">Ver Catálogo</a>
          <a href="#contact" class="btn btn-outline-light">Contactar</a>
        </div>
        <div class="col-lg-6">
          <img src="/api/placeholder/600/400" alt="Scripts Showcase" class="img-fluid rounded shadow">
        </div>
      </div>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="stats-container">
    <div class="container">
      <div class="row text-center">
        <div class="col-md-3 col-6 mb-4 mb-md-0">
          <div class="stat-item">
            <h2>3,500+</h2>
            <p>Scripts</p>
          </div>
        </div>
        <div class="col-md-3 col-6 mb-4 mb-md-0">
          <div class="stat-item">
            <h2>15,000+</h2>
            <p>Clientes satisfechos</p>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-item">
            <h2>97%</h2>
            <p>Valoración positiva</p>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-item">
            <h2>24/7</h2>
            <p>Soporte técnico</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features" id="features">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">¿Por qué elegir ScriptMaster?</h2>
        <p class="text-muted">Descubre las ventajas que nos hacen destacar en el mercado</p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="feature-box text-center">
            <i class="fas fa-code"></i>
            <h4>Código Limpio</h4>
            <p>Nuestros scripts están escritos con los más altos estándares de codificación, garantizando un rendimiento óptimo.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-box text-center">
            <i class="fas fa-sync"></i>
            <h4>Actualizaciones Gratuitas</h4>
            <p>Recibe actualizaciones y mejoras sin costo adicional para todos tus scripts adquiridos.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-box text-center">
            <i class="fas fa-headset"></i>
            <h4>Soporte Premium</h4>
            <p>Nuestro equipo de soporte está disponible 24/7 para resolver cualquier duda o problema.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-box text-center">
            <i class="fas fa-shield-alt"></i>
            <h4>Código Seguro</h4>
            <p>Todos los scripts son revisados para garantizar la máxima seguridad contra vulnerabilidades.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-box text-center">
            <i class="fas fa-file-alt"></i>
            <h4>Documentación Completa</h4>
            <p>Instrucciones detalladas para implementar y personalizar cada script según tus necesidades.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-box text-center">
            <i class="fas fa-mobile-alt"></i>
            <h4>Responsive Design</h4>
            <p>Scripts adaptados a todo tipo de dispositivos para una experiencia de usuario perfecta.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Products Section -->
  <section class="py-5 bg-light" id="products">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">Nuestros Scripts Destacados</h2>
        <p class="text-muted">Explora nuestra selección de los scripts más populares</p>
      </div>
      <div class="row">
        <div class="col-lg-4 col-md-6">
          <div class="product-card card">
            <img src="/api/placeholder/400/300" class="card-img-top" alt="E-commerce Script">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">E-commerce Suite</h5>
                <span class="badge-custom">Bs129</span>
              </div>
              <p class="card-text">Sistema completo de tienda online con carrito de compras, pagos y panel de administración.</p>
              <div class="d-flex justify-content-between">
                <button class="btn btn-sm btn-outline-primary">Vista previa</button>
                <button class="btn btn-sm btn-primary">Añadir al carrito</button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="product-card card">
            <img src="/api/placeholder/400/300" class="card-img-top" alt="Dashboard Script">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">Admin Dashboard Pro</h5>
                <span class="badge-custom">Bs79</span>
              </div>
              <p class="card-text">Panel de administración con estadísticas, gráficos y gestión de usuarios en tiempo real.</p>
              <div class="d-flex justify-content-between">
                <button class="btn btn-sm btn-outline-primary">Vista previa</button>
                <button class="btn btn-sm btn-primary">Añadir al carrito</button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="product-card card">
            <img src="/api/placeholder/400/300" class="card-img-top" alt="CRM Script">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">CRM Business</h5>
                <span class="badge-custom">Bs199</span>
              </div>
              <p class="card-text">Sistema de gestión de clientes con seguimiento de ventas, tickets y comunicaciones.</p>
              <div class="d-flex justify-content-between">
                <button class="btn btn-sm btn-outline-primary">Vista previa</button>
                <button class="btn btn-sm btn-primary">Añadir al carrito</button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="product-card card">
            <img src="/api/placeholder/400/300" class="card-img-top" alt="Chat Script">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">Chat App Master</h5>
                <span class="badge-custom">Bs69</span>
              </div>
              <p class="card-text">Sistema de chat en tiempo real con mensajes privados, grupos y notificaciones.</p>
              <div class="d-flex justify-content-between">
                <button class="btn btn-sm btn-outline-primary">Vista previa</button>
                <button class="btn btn-sm btn-primary">Añadir al carrito</button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="product-card card">
            <img src="/api/placeholder/400/300" class="card-img-top" alt="Booking Script">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">Reservation System</h5>
                <span class="badge-custom">Bs89</span>
              </div>
              <p class="card-text">Sistema de reservas y citas con calendario, recordatorios y pagos anticipados.</p>
              <div class="d-flex justify-content-between">
                <button class="btn btn-sm btn-outline-primary">Vista previa</button>
                <button class="btn btn-sm btn-primary">Añadir al carrito</button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="product-card card">
            <img src="/api/placeholder/400/300" class="card-img-top" alt="LMS Script">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">LMS Education</h5>
                <span class="badge-custom">Bs149</span>
              </div>
              <p class="card-text">Plataforma de aprendizaje con cursos, lecciones, exámenes y certificaciones.</p>
              <div class="d-flex justify-content-between">
                <button class="btn btn-sm btn-outline-primary">Vista previa</button>
                <button class="btn btn-sm btn-primary">Añadir al carrito</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center mt-4">
        <a href="#" class="btn btn-outline-primary">Ver todos los scripts</a>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="py-5" id="testimonials">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">Lo que dicen nuestros clientes</h2>
        <p class="text-muted">Miles de desarrolladores confían en nosotros</p>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="testimonial">
            <div class="d-flex align-items-center mb-3">
              <img src="/api/placeholder/60/60" class="testimonial-img" alt="Customer">
              <div>
                <h5 class="mb-0">Carlos Rodríguez</h5>
                <small class="text-muted">CTO, TechMex</small>
              </div>
            </div>
            <p>"Los scripts de ScriptMaster han ahorrado a nuestro equipo cientos de horas de desarrollo. La calidad y soporte son excepcionales."</p>
            <div class="text-warning">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="testimonial">
            <div class="d-flex align-items-center mb-3">
              <img src="/api/placeholder/60/60" class="testimonial-img" alt="Customer">
              <div>
                <h5 class="mb-0">Laura Sánchez</h5>
                <small class="text-muted">Freelancer</small>
              </div>
            </div>
            <p>"Como desarrolladora independiente, los scripts de ScriptMaster me permiten entregar proyectos más rápido y con mejor calidad. Vale cada centavo."</p>
            <div class="text-warning">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star-half-alt"></i>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="testimonial">
            <div class="d-flex align-items-center mb-3">
              <img src="/api/placeholder/60/60" class="testimonial-img" alt="Customer">
              <div>
                <h5 class="mb-0">Miguel Ángel Torres</h5>
                <small class="text-muted">Director de Desarrollo, StartupHub</small>
              </div>
            </div>
            <p>"Hemos implementado varios scripts de ScriptMaster en nuestros proyectos. La documentación es clara y el código es limpio y fácil de personalizar."</p>
            <div class="text-warning">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="testimonial">
            <div class="d-flex align-items-center mb-3">
              <img src="/api/placeholder/60/60" class="testimonial-img" alt="Customer">
              <div>
                <h5 class="mb-0">Ana María López</h5>
                <small class="text-muted">CEO, DigitalFusion</small>
              </div>
            </div>
            <p>"El soporte técnico de ScriptMaster es impresionante. Siempre responden rápido y resuelven cualquier problema de forma efectiva."</p>
            <div class="text-warning">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section class="py-5 bg-light" id="pricing">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">Planes de suscripción</h2>
        <p class="text-muted">Ahorra con acceso ilimitado a nuestra biblioteca de scripts</p>
      </div>
      <div class="row">
        <div class="col-lg-4">
          <div class="card border-0 shadow mb-4">
            <div class="card-header text-center bg-white pt-4 border-0">
              <h4>Básico</h4>
              <h1 class="display-4 fw-bold">Bs29<small class="fs-5 text-muted">/mes</small></h1>
            </div>
            <div class="card-body">
              <ul class="list-unstyled">
                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Acceso a 50 scripts</li>
                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Actualizaciones incluidas</li>
                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Soporte por email</li>
                <li class="mb-3 text-muted"><i class="fas fa-times text-danger me-2"></i> Personalización</li>
                <li class="mb-3 text-muted"><i class="fas fa-times text-danger me-2"></i> Uso comercial</li>
              </ul>
              <div class="text-center mt-4">
                <button class="btn btn-outline-primary btn-lg w-100">Comenzar</button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card border-0 shadow mb-4 border-primary">
            <div class="card-header text-center bg-primary text-white pt-4 border-0">
              <span class="badge bg-warning position-absolute top-0 start-50 translate-middle px-3 py-2">Popular</span>
              <h4>Profesional</h4>
              <h1 class="display-4 fw-bold">Bs89<small class="fs-5 text-light">/mes</small></h1>
            </div>
            <div class="card-body">
              <ul class="list-unstyled">
                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Acceso a 200 scripts</li>
                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Actualizaciones incluidas</li>
                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Soporte prioritario</li>
                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Personalización básica</li>
                <li class="mb-3 text-muted"><i class="fas fa-times text-danger me-2"></i> Uso comercial</li>
              </ul>
              <div class="text-center mt-4">
                <button class="btn btn-primary btn-lg w-100">Comenzar</button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card border-0 shadow mb-4">
            <div class="card-header text-center bg-white pt-4 border-0">
              <h4>Empresarial</h4>
              <h1 class="display-4 fw-bold">Bs199<small class="fs-5 text-muted">/mes</small></h1>
            </div>
            <div class="card-body">
              <ul class="list-unstyled">
                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Acceso ilimitado</li>
                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Actualizaciones incluidas</li>
                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Soporte 24/7</li>
                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Personalización avanzada</li>
                <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Uso comercial</li>
              </ul>
              <div class="text-center mt-4">
                <button class="btn btn-outline-primary btn-lg w-
