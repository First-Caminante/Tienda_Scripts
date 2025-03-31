<?php

error_reporting(E_ALL);      // Reportar todos los errores
ini_set('display_errors', 1); // Mostrar errores en pantalla
ini_set('display_startup_errors', 1); // Mostrar errores que ocurren al iniciar PHP



#include_once 'Templates/header.php';

?>

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
          <form action="process.php" method="POST">
            <div class="row mb-3">
              <div class="col-md-6 mb-3 mb-md-0">
                <label for="firstName" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="firstName" name="nombre" required>
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

</body>

</html>
