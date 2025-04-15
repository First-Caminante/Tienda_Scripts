<?php
/*
error_reporting(E_ALL);      // Reportar todos los errores
ini_set('display_errors', 1); // Mostrar errores en pantalla
ini_set('display_startup_errors', 1); // Mostrar errores que ocurren al iniciar PHP



#include_once 'Templates/header.php';



<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio de Sesión y Registro Formal</title>
  <link rel="stylesheet" href="bootstrap-5.3.3/dist/css/bootstrap.min.css">
  <script src="bootstrap-5.3.3/dist/js/bootstrap.bundle.min.js"></script>



  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Arial', sans-serif;
    }

    .form-container {
      max-width: 900px;
      margin: 50px auto;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      background-color: white;
    }

    .tab-content {
      padding: 30px;
    }

    .nav-tabs {
      border-bottom: 1px solid #dee2e6;
    }

    .nav-tabs .nav-link {
      color: #212529;
      font-weight: 500;
      padding: 15px 30px;
    }

    .nav-tabs .nav-link.active {
      border-color: #dee2e6 #dee2e6 #fff;
      font-weight: 600;
    }

    .logo {
      text-align: center;
      margin-bottom: 20px;
    }

    .logo span {
      font-size: 30px;
      font-weight: 700;
      letter-spacing: 1px;
    }

    .btn-dark {
      background-color: #212529;
      padding: 10px 30px;
    }

    .form-control:focus {
      border-color: #212529;
      box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.25);
    }

    .form-label {
      font-weight: 500;
    }

    .terms {
      font-size: 14px;
      color: #6c757d;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="form-container">
      <div class="logo pt-4">
        <span>TIENDA DE SCRIPTS</span>
      </div>

      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Iniciar Sesión</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Registrarse</button>
        </li>
      </ul>

      <div class="tab-content" id="myTabContent">
        <!-- Formulario de Inicio de Sesión -->
        <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
          <h3 class="mb-4">Bienvenido de nuevo</h3>
          <form action="process.php" method="POST">

            <div class="mb-4">
              <label for="loginEmail" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="loginEmail" name="email" required>
            </div>
            <div class="mb-4">
              <label for="loginPassword" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="loginPassword" name="password_hash" required>
            </div>
            <div class="mb-4 form-check">
              <input type="checkbox" class="form-check-input" id="rememberMe">
              <label class="form-check-label" for="rememberMe">Recordarme</label>
              <a href="#" class="float-end text-dark">¿Olvidó su contraseña?</a>
            </div>
            <input type="hidden" name="action" value="loginUser">
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-dark">Iniciar Sesión</button>
            </div>
          </form>
        </div>

        <!-- Formulario de Registro -->
        <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
          <h3 class="mb-4">Crear una cuenta nueva</h3>
          <form action="process.php" method="POST" id="miFormulario">
            <div class="row mb-3">
              <div class="col-md-6 mb-3 mb-md-0">
                <label for="firstName" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="firstName" name="nombre" required>
                <span id="errorNombre" style="color: red;"></span>

              </div>
            </div>
            <div class="mb-3">
              <label for="registerEmail" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="registerEmail" name="email" required>
            </div>
            <div class="mb-3">
              <label for="registerPassword" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="registerPassword" name="password_hash" required>
            </div>
            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Confirmar contraseña</label>
              <input type="password" class="form-control" id="confirmPassword" name="Repeat_password" required>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="agreeTerms" required>
              <label class="form-check-label" for="agreeTerms">
                Acepto los <a href="#" class="text-dark">Términos de servicio</a> y la <a href="#" class="text-dark">Política de privacidad</a>
              </label>
            </div>
            <input type="hidden" name="action" value="addUser">
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-dark">Registrarse</button>
            </div>
            <p class="mt-3 terms text-center">
              Al registrarse, acepta nuestros términos de servicio, política de privacidad y política de cookies.
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    const formulario = document.getElementById('miFormulario');
    const nombreInput = document.getElementById('firstName');
    const errorNombre = document.getElementById('errorNombre');

    formulario.addEventListener('submit', function(e) {
      const nombre = nombreInput.value;

      // Expresión regular: solo letras y espacios
      const soloLetras = /^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]+$/;

      if (!soloLetras.test(nombre)) {
        e.preventDefault(); // Evita que se envíe el formulario
        errorNombre.textContent = "El nombre no puede contener números ni caracteres especiales.";
      } else {
        errorNombre.textContent = ""; // Limpia el error si todo está bien
      }
    });
  </script>
</body>

</html>

 */ ?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio de Sesión y Registro Formal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

  <style>
    :root {
      --primary-color: #092c1f;
      --primary-light: #164032;
      --primary-dark: #051a12;
      --accent-color: #2e8b57;
      --text-light: #e0e6e3;
    }

    body {
      background-color: #f0f3f1;
      font-family: 'Arial', sans-serif;
      overflow-x: hidden;
    }

    .form-container {
      max-width: 900px;
      margin: 50px auto;
      box-shadow: 0 10px 30px rgba(9, 44, 31, 0.2);
      background-color: white;
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.5s ease;
    }

    .form-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(9, 44, 31, 0.3);
    }

    .tab-content {
      padding: 40px;
    }

    .nav-tabs {
      border-bottom: none;
      background-color: var(--primary-color);
    }

    .nav-tabs .nav-link {
      color: rgba(255, 255, 255, 0.8);
      font-weight: 500;
      padding: 15px 30px;
      border: none;
      border-radius: 0;
      transition: all 0.3s ease;
      position: relative;
    }

    .nav-tabs .nav-link:hover {
      color: white;
      background-color: rgba(255, 255, 255, 0.1);
    }

    .nav-tabs .nav-link.active {
      color: white;
      background-color: var(--primary-color);
      border: none;
      font-weight: 600;
    }

    .nav-tabs .nav-link.active::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 3px;
      background-color: var(--accent-color);
      animation: slideIn 0.5s ease forwards;
    }

    @keyframes slideIn {
      from {
        width: 0;
      }

      to {
        width: 100%;
      }
    }

    .logo {
      text-align: center;
      margin-bottom: 20px;
      padding-top: 25px;
    }

    .logo span {
      font-size: 32px;
      font-weight: 700;
      letter-spacing: 2px;
      color: var(--primary-color);
      position: relative;
      display: inline-block;
    }

    .logo span::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 50%;
      width: 0;
      height: 2px;
      background-color: var(--accent-color);
      transition: all 0.5s ease;
      transform: translateX(-50%);
    }

    .form-container:hover .logo span::after {
      width: 80%;
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      padding: 12px 30px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .btn-primary::after {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background-color: var(--primary-light);
      transition: all 0.4s ease;
      z-index: -1;
    }

    .btn-primary:hover::after {
      left: 0;
    }

    .btn-primary:hover {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(9, 44, 31, 0.3);
    }

    .form-control {
      padding: 12px;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(9, 44, 31, 0.25);
      transform: translateY(-3px);
    }

    .form-label {
      font-weight: 500;
      color: var(--primary-color);
      margin-bottom: 8px;
      transition: all 0.3s ease;
      opacity: 0.85;
    }

    .form-floating:focus-within label {
      color: var(--primary-color);
      opacity: 1;
    }

    .terms {
      font-size: 14px;
      color: #6c757d;
    }

    .form-check-input:checked {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .form-check-input:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(9, 44, 31, 0.25);
    }

    a {
      color: var(--primary-color);
      transition: all 0.3s ease;
      text-decoration: none;
    }

    a:hover {
      color: var(--accent-color);
    }

    .input-group {
      position: relative;
    }

    .form-floating {
      position: relative;
      margin-bottom: 20px;
    }

    .form-floating label {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      padding: 1rem 0.75rem;
      pointer-events: none;
      border: 1px solid transparent;
      transform-origin: 0 0;
      transition: opacity 0.1s ease-in-out, transform 0.1s ease-in-out;
    }

    .form-floating>.form-control:focus~label,
    .form-floating>.form-control:not(:placeholder-shown)~label {
      opacity: 0.65;
      transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
      background-color: white;
      padding: 0 5px;
      height: auto;
      color: var(--primary-color);
    }

    .form-floating>.form-control {
      padding-top: 1.625rem;
      padding-bottom: 0.625rem;
    }

    .animate-in {
      animation: fadeInUp 0.6s ease-out forwards;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .slide-in {
      animation: slideInRight 0.5s ease-out forwards;
    }

    @keyframes slideInRight {
      from {
        opacity: 0;
        transform: translateX(50px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .pulse-btn {
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(9, 44, 31, 0.4);
      }

      70% {
        box-shadow: 0 0 0 10px rgba(9, 44, 31, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(9, 44, 31, 0);
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="form-container animate__animated animate__fadeIn">
      <div class="logo">
        <span class="animate__animated animate__flipInX">TIENDA DE SCRIPTS</span>
      </div>

      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Iniciar Sesión</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Registrarse</button>
        </li>
      </ul>

      <div class="tab-content" id="myTabContent">
        <!-- Formulario de Inicio de Sesión -->
        <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
          <h3 class="mb-4 animate-in">Bienvenido de nuevo</h3>
          <form action="process.php" method="POST">
            <div class="form-floating mb-4 animate-in" style="animation-delay: 0.1s">
              <input type="email" class="form-control" id="loginEmail" name="email" placeholder="nombre@ejemplo.com" required>
              <label for="loginEmail">Correo electrónico</label>
            </div>

            <div class="form-floating mb-4 animate-in" style="animation-delay: 0.2s">
              <input type="password" class="form-control" id="loginPassword" name="password_hash" placeholder="Contraseña" required>
              <label for="loginPassword">Contraseña</label>
            </div>

            <div class="mb-4 form-check animate-in" style="animation-delay: 0.3s">
              <input type="checkbox" class="form-check-input" id="rememberMe">
              <label class="form-check-label" for="rememberMe">Recordarme</label>
              <a href="#" class="float-end">¿Olvidó su contraseña?</a>
            </div>

            <input type="hidden" name="action" value="loginUser">
            <div class="d-grid gap-2 animate-in" style="animation-delay: 0.4s">
              <button type="submit" class="btn btn-primary pulse-btn">Iniciar Sesión</button>
            </div>
          </form>
        </div>

        <!-- Formulario de Registro -->
        <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
          <h3 class="mb-4 slide-in">Crear una cuenta nueva</h3>
          <form action="process.php" method="POST" id="miFormulario">
            <div class="form-floating mb-3 slide-in" style="animation-delay: 0.1s">
              <input type="text" class="form-control" id="firstName" name="nombre" placeholder="Tu nombre" required>
              <label for="firstName">Nombre</label>
              <span id="errorNombre" style="color: #d9534f; font-size: 0.85rem;"></span>
            </div>

            <div class="form-floating mb-3 slide-in" style="animation-delay: 0.2s">
              <input type="email" class="form-control" id="registerEmail" name="email" placeholder="nombre@ejemplo.com" required>
              <label for="registerEmail">Correo electrónico</label>
            </div>

            <div class="form-floating mb-3 slide-in" style="animation-delay: 0.3s">
              <input type="password" class="form-control" id="registerPassword" name="password_hash" placeholder="Contraseña" required>
              <label for="registerPassword">Contraseña</label>
            </div>

            <div class="form-floating mb-3 slide-in" style="animation-delay: 0.4s">
              <input type="password" class="form-control" id="confirmPassword" name="Repeat_password" placeholder="Confirmar contraseña" required>
              <label for="confirmPassword">Confirmar contraseña</label>
            </div>

            <div class="mb-3 form-check slide-in" style="animation-delay: 0.5s">
              <input type="checkbox" class="form-check-input" id="agreeTerms" required>
              <label class="form-check-label" for="agreeTerms">
                Acepto los <a href="#">Términos de servicio</a> y la <a href="#">Política de privacidad</a>
              </label>
            </div>

            <input type="hidden" name="action" value="addUser">
            <div class="d-grid gap-2 slide-in" style="animation-delay: 0.6s">
              <button type="submit" class="btn btn-primary">Registrarse</button>
            </div>

            <p class="mt-3 terms text-center slide-in" style="animation-delay: 0.7s">
              Al registrarse, acepta nuestros términos de servicio, política de privacidad y política de cookies.
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Script para validación del nombre
      const formulario = document.getElementById('miFormulario');
      const nombreInput = document.getElementById('firstName');
      const errorNombre = document.getElementById('errorNombre');

      formulario.addEventListener('submit', function(e) {
        const nombre = nombreInput.value;
        const soloLetras = /^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]+$/;

        if (!soloLetras.test(nombre)) {
          e.preventDefault();
          errorNombre.textContent = "El nombre no puede contener números ni caracteres especiales.";
          nombreInput.classList.add('is-invalid');
          shake(nombreInput);
        } else {
          errorNombre.textContent = "";
          nombreInput.classList.remove('is-invalid');
        }
      });

      // Animaciones para los tabs
      const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
      tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(event) {
          const targetId = event.target.getAttribute('data-bs-target');
          const target = document.querySelector(targetId);

          // Reiniciar animaciones cuando se cambia de tab
          const animations = target.querySelectorAll('.animate-in, .slide-in');
          animations.forEach(el => {
            el.style.opacity = '0';
            setTimeout(() => {
              el.style.opacity = '1';
              if (el.classList.contains('animate-in')) {
                el.style.animation = 'fadeInUp 0.6s ease-out forwards';
              } else if (el.classList.contains('slide-in')) {
                el.style.animation = 'slideInRight 0.5s ease-out forwards';
              }
            }, 50);
          });
        });
      });

      // Efecto shake para errores
      function shake(element) {
        element.classList.add('animate__animated', 'animate__shakeX');
        element.addEventListener('animationend', () => {
          element.classList.remove('animate__animated', 'animate__shakeX');
        });
      }

      // Animación para campos de formulario cuando reciben foco
      const formControls = document.querySelectorAll('.form-control');
      formControls.forEach(input => {
        input.addEventListener('focus', function() {
          this.parentElement.classList.add('animate__animated', 'animate__pulse');
          this.parentElement.style.animationDuration = '0.5s';
        });

        input.addEventListener('blur', function() {
          this.parentElement.classList.remove('animate__animated', 'animate__pulse');
        });
      });
    });
  </script>
</body>

</html>
