<?php
require_once '../app/Controllers/Functions.php';
require_once '../vendor/autoload.php';



use App\Controllers\Functions;

$functions = new Functions();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['crear'])) {
    $mensaje = $functions->crearBackup();
  } elseif (isset($_POST['restaurar']) && isset($_POST['archivo'])) {
    $archivo = $_POST['archivo'];
    $mensaje = $functions->restaurarBackup($archivo);
  }
}

$archivos = glob(__DIR__ . '/backup/*.sql');
usort($archivos, fn($a, $b) => filemtime($b) - filemtime($a)); // ordena por fecha
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Backup y Restauración de BD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <div class="container py-5">
    <h1 class="mb-4">🔐 Panel de Backup y Restauración</h1>

    <?php if ($mensaje): ?>
      <div class="alert alert-info"><?= nl2br(htmlspecialchars($mensaje)) ?></div>
    <?php endif; ?>

    <div class="row">
      <!-- Backup -->
      <div class="col-md-6 mb-4">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">📦 Crear Backup</div>
          <div class="card-body">
            <form method="POST">
              <button name="crear" class="btn btn-success w-100">Crear Backup Ahora</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Restaurar -->
      <div class="col-md-6 mb-4">
        <div class="card shadow">
          <div class="card-header bg-warning">♻️ Restaurar desde Backup</div>
          <div class="card-body">
            <form method="POST">
              <div class="mb-3">
                <label for="archivo" class="form-label">Elige un archivo .sql:</label>
                <select name="archivo" id="archivo" class="form-select" required>
                  <?php foreach ($archivos as $ruta): ?>
                    <?php $nombre = basename($ruta); ?>
                    <option value="<?= $nombre ?>"><?= $nombre ?> — <?= date("Y-m-d H:i", filemtime($ruta)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button name="restaurar" class="btn btn-danger w-100">Restaurar Backup</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Lista de backups -->
    <div class="card shadow mt-4">
      <div class="card-header bg-dark text-white">📚 Historial de Backups</div>
      <ul class="list-group list-group-flush">
        <?php if (empty($archivos)): ?>
          <li class="list-group-item text-muted">No hay respaldos aún.</li>
        <?php else: ?>
          <?php foreach ($archivos as $ruta): ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><?= basename($ruta) ?></span>
              <span class="text-muted"><?= date("Y-m-d H:i:s", filemtime($ruta)) ?></span>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>

</body>

</html>
