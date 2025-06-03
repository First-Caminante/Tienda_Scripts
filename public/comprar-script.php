<?php
session_start();
require('../vendor/autoload.php');

use App\Controllers\Functions;

// Verificar si el usuario está logueado
if (!isset($_SESSION['id'])) {
  header('Location: login.php');
  exit;
}

$funciones = new Functions();
$usuario_id = $_SESSION['id'];
$mensaje = '';
$tipo_mensaje = '';
$pago_exitoso = false;

// Obtener ID de la solicitud
$solicitud_id = isset($_GET['solicitud_id']) ? (int)$_GET['solicitud_id'] : 0;

if ($solicitud_id === 0) {
  header('Location: mis-solicitudes.php');
  exit;
}

// Obtener información de la solicitud
$solicitud = $funciones->getSolicitudPorId($solicitud_id);

if (isset($solicitud['error'])) {
  header('Location: mis-solicitudes.php');
  exit;
}

// Verificar que la solicitud pertenezca al usuario y esté completada
if ($solicitud['usuario_id'] != $usuario_id || $solicitud['estado'] !== 'completado') {
  header('Location: mis-solicitudes.php');
  exit;
}

// Generar precio
$precio = $funciones->generarPrecioScript($solicitud_id);

// Procesar pago
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['procesar_pago'])) {
  $datos_pago = [
    'numero_tarjeta' => $_POST['numero_tarjeta'] ?? '',
    'cvv' => $_POST['cvv'] ?? '',
    'mes' => $_POST['mes'] ?? '',
    'año' => $_POST['año'] ?? '',
    'nombre_titular' => $_POST['nombre_titular'] ?? '',
    'email' => $_POST['email'] ?? ''
  ];

  $resultado_pago = $funciones->crearPago($usuario_id, $solicitud_id, $precio, $datos_pago);

  if ($resultado_pago['success']) {
    $mensaje = $resultado_pago['message'];
    $pago_exitoso = $resultado_pago['estado'] === 'pagado';
    $tipo_mensaje = $pago_exitoso ? 'success' : ($resultado_pago['estado'] === 'pendiente' ? 'warning' : 'error');
  } else {
    $mensaje = $resultado_pago['error'];
    $tipo_mensaje = 'error';
  }
}
//dd($_GET, $precio);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comprar Script - Pago Seguro</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #092c1f, #0a3d2b);
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

    .header p {
      color: #a3a3a3;
      font-size: 1.1rem;
    }

    .container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 2rem;
    }

    .payment-layout {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3rem;
      margin-top: 2rem;
    }

    .section {
      background-color: #0f3d2f;
      border-radius: 15px;
      padding: 2.5rem;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(74, 222, 128, 0.1);
    }

    .section h2 {
      color: #4ade80;
      margin-bottom: 2rem;
      font-size: 1.8rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .product-info {
      background: linear-gradient(135deg, #1a5c42, #0f3d2f);
      padding: 2rem;
      border-radius: 12px;
      margin-bottom: 2rem;
      border-left: 5px solid #4ade80;
    }

    .product-title {
      color: #ffffff;
      font-size: 1.4rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .product-description {
      color: #d1d5db;
      margin-bottom: 1.5rem;
      padding: 1rem;
      background-color: rgba(0, 0, 0, 0.2);
      border-radius: 8px;
    }

    .price-display {
      background: linear-gradient(135deg, #4ade80, #22c55e);
      color: #000;
      padding: 1.5rem;
      border-radius: 12px;
      text-align: center;
      margin-bottom: 2rem;
      font-size: 2rem;
      font-weight: bold;
      box-shadow: 0 4px 20px rgba(74, 222, 128, 0.3);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      color: #4ade80;
      font-weight: 600;
    }

    .form-control {
      width: 100%;
      padding: 1rem;
      border: 2px solid #374151;
      border-radius: 8px;
      background-color: #1f2937;
      color: #ffffff;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      outline: none;
      border-color: #4ade80;
      box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.2);
      background-color: #111827;
    }

    .form-row {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 1rem;
    }

    .form-row-three {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 1rem;
    }

    .card-input {
      position: relative;
    }

    .card-input::before {
      content: "💳";
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1.2rem;
      z-index: 1;
    }

    .card-input .form-control {
      padding-left: 3rem;
    }

    .security-badges {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin: 2rem 0;
      flex-wrap: wrap;
    }

    .security-badge {
      background-color: #1f2937;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-size: 0.8rem;
      color: #4ade80;
      border: 1px solid #4ade80;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn {
      padding: 1.2rem 2rem;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 700;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      transition: all 0.3s ease;
      font-size: 1.1rem;
      width: 100%;
    }

    .btn-primary {
      background: linear-gradient(135deg, #4ade80, #22c55e);
      color: #000;
      box-shadow: 0 6px 20px rgba(74, 222, 128, 0.4);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(74, 222, 128, 0.5);
    }

    .btn-secondary {
      background-color: #6b7280;
      color: #fff;
      margin-bottom: 1rem;
    }

    .btn-secondary:hover {
      background-color: #4b5563;
    }

    .mensaje {
      padding: 1.5rem;
      margin-bottom: 2rem;
      border-radius: 10px;
      font-weight: 600;
      text-align: center;
    }

    .mensaje.success {
      background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(22, 163, 74, 0.1));
      border: 2px solid #22c55e;
      color: #4ade80;
    }

    .mensaje.error {
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
      border: 2px solid #ef4444;
      color: #f87171;
    }

    .mensaje.warning {
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.1));
      border: 2px solid #f59e0b;
      color: #fbbf24;
    }

    .success-animation {
      text-align: center;
      padding: 3rem;
    }

    .success-icon {
      font-size: 5rem;
      margin-bottom: 1rem;
      animation: bounce 2s infinite;
    }

    @keyframes bounce {

      0%,
      20%,
      50%,
      80%,
      100% {
        transform: translateY(0);
      }

      40% {
        transform: translateY(-10px);
      }

      60% {
        transform: translateY(-5px);
      }
    }

    .loading-spinner {
      display: none;
      width: 20px;
      height: 20px;
      border: 2px solid #000;
      border-top: 2px solid transparent;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    .form-tips {
      background-color: rgba(74, 222, 128, 0.05);
      padding: 1rem;
      border-radius: 8px;
      border-left: 4px solid #4ade80;
      margin-bottom: 2rem;
      font-size: 0.9rem;
      color: #d1d5db;
    }

    @media (max-width: 768px) {
      .payment-layout {
        grid-template-columns: 1fr;
        gap: 2rem;
      }

      .container {
        padding: 0 1rem;
      }

      .section {
        padding: 1.5rem;
      }

      .form-row,
      .form-row-three {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>
  <div class="header">
    <h1>🛒 Comprar Script</h1>
    <p>Pago seguro y encriptado</p>
  </div>

  <div class="container">
    <?php if (!empty($mensaje)): ?>
      <div class="mensaje <?php echo $tipo_mensaje; ?>">
        <?php if ($pago_exitoso): ?>
          <div class="success-animation">
            <div class="success-icon">🎉</div>
            <h3>¡Pago Exitoso!</h3>
          </div>
        <?php endif; ?>
        <?php echo htmlspecialchars($mensaje); ?>

        <?php if ($pago_exitoso): ?>
          <div style="margin-top: 2rem;">
            <a href="mis-solicitudes.php" class="btn btn-secondary">Volver a Mis Solicitudes</a>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (!$pago_exitoso): ?>
      <div class="payment-layout">
        <!-- Información del Producto -->
        <div class="section">
          <h2>📋 Resumen de Compra</h2>

          <div class="product-info">
            <div class="product-title">
              🎯 <?php echo htmlspecialchars($solicitud['titulo']); ?>
            </div>

            <div class="product-description">
              <strong>Descripción:</strong><br>
              <?php echo nl2br(htmlspecialchars($solicitud['descripcion'])); ?>
            </div>

            <?php if (!empty($solicitud['archivo_script'])): ?>
              <div style="background-color: rgba(74, 222, 128, 0.1); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                <strong>📁 Archivo incluido:</strong><br>
                <?php echo htmlspecialchars($solicitud['archivo_script']); ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($solicitud['desarrollador_nombre'])): ?>
              <div style="margin-top: 1rem; color: #a3a3a3;">
                <strong>👨‍💻 Desarrollado por:</strong> <?php echo htmlspecialchars($solicitud['desarrollador_nombre']); ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="price-display">
            💰 $<?php echo number_format($precio, 2); ?> USD
          </div>

          <div class="security-badges">
            <div class="security-badge">🔒 SSL Seguro</div>
            <div class="security-badge">🛡️ Encriptado</div>
            <div class="security-badge">✅ Garantía</div>
          </div>
        </div>

        <!-- Formulario de Pago -->
        <div class="section">
          <h2>💳 Información de Pago</h2>

          <div class="form-tips">
            <strong>💡 Datos de prueba:</strong><br>
            • Tarjeta: 4111 1111 1111 1111<br>
            • CVV: 123<br>
            • Fecha: Cualquier fecha futura<br>
            <em>Usa datos válidos para simular un pago exitoso</em>
          </div>

          <form method="POST" id="payment-form" action="process.php">
            <div class="form-group">
              <label for="email">📧 Email de Confirmación</label>
              <input type="email"
                name="email"
                id="email"
                class="form-control"
                value="<?php echo htmlspecialchars($_SESSION['email']); ?>"
                required>
            </div>

            <div class="form-group">
              <label for="nombre_titular">👤 Nombre del Titular</label>
              <input type="text"
                name="nombre_titular"
                id="nombre_titular"
                class="form-control"
                placeholder="Nombre completo como aparece en la tarjeta"
                value="<?php echo htmlspecialchars($_SESSION['nombre']); ?>"
                required>
            </div>

            <div class="form-group card-input">
              <label for="numero_tarjeta">💳 Número de Tarjeta</label>
              <input type="text"
                name="numero_tarjeta"
                id="numero_tarjeta"
                class="form-control"
                placeholder="1234 5678 9012 3456"
                maxlength="19"
                required>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="fecha">📅 Fecha de Expiración</label>
                <div class="form-row-three">
                  <select name="mes" id="mes" class="form-control" required>
                    <option value="">Mes</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                      <option value="<?php echo $i; ?>"><?php echo sprintf('%02d', $i); ?></option>
                    <?php endfor; ?>
                  </select>
                  <select name="año" id="año" class="form-control" required>
                    <option value="">Año</option>
                    <?php for ($i = date('Y'); $i <= date('Y') + 10; $i++): ?>
                      <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="cvv">🔐 CVV</label>
                <input type="text"
                  name="cvv"
                  id="cvv"
                  class="form-control"
                  placeholder="123"
                  maxlength="4"
                  required>
              </div>
            </div>

            <input type="hidden" name="precio" value="<?php echo ($precio); ?>">
            <input type="hidden" name="usuario_id" value="<?php echo ($_GET['usuario_id']); ?>">
            <input type="hidden" name="solicitud_id" value="<?php echo ($_GET['solicitud_id']); ?>">

            <button type="submit" name="procesar_pago" class="btn btn-primary" id="btn-pagar">
              <span id="btn-text">🔒 Pagar $<?php echo number_format($precio, 2); ?></span>
              <div class="loading-spinner" id="loading"></div>
            </button>
          </form>

          <div style="text-align: center; margin-top: 2rem;">
            <a href="mis-solicitudes.php" class="btn btn-secondary">← Volver a Mis Solicitudes</a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <script>
    // Formatear número de tarjeta
    document.getElementById('numero_tarjeta').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
      let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
      if (formattedValue.length > 19) {
        formattedValue = formattedValue.substr(0, 19);
      }
      e.target.value = formattedValue;
    });

    // Validar CVV solo números
    document.getElementById('cvv').addEventListener('input', function(e) {
      e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });

    // Animación de procesamiento
    document.getElementById('payment-form').addEventListener('submit', function(e) {
      const btn = document.getElementById('btn-pagar');
      const btnText = document.getElementById('btn-text');
      const loading = document.getElementById('loading');

      btn.disabled = true;
      btnText.style.display = 'none';
      loading.style.display = 'block';

      setTimeout(() => {
        btnText.innerHTML = '⏳ Procesando...';
        btnText.style.display = 'block';
        loading.style.display = 'none';
      }, 1000);
    });

    // Validación en tiempo real
    document.getElementById('payment-form').addEventListener('input', function(e) {
      const form = e.target.form;
      const submitBtn = document.getElementById('btn-pagar');
      const inputs = form.querySelectorAll('input[required], select[required]');
      let allValid = true;

      inputs.forEach(input => {
        if (!input.value.trim()) {
          allValid = false;
        }
      });

      submitBtn.style.opacity = allValid ? '1' : '0.6';
    });
  </script>
</body>

</html>
