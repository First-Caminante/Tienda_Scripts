<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\Functions;

$func = new Functions();

$accion = $_GET['accion'] ?? '';
$archivo = $_GET['archivo'] ?? '';

switch ($accion) {
  case 'crear':
    echo $func->crearBackup();
    break;

  case 'listar':
    header('Content-Type: application/json');
    echo json_encode($func->listarBackups());
    break;

  case 'restaurar':
    if ($archivo) {
      echo $func->restaurarBackup($archivo);
    } else {
      echo "Falta el parámetro 'archivo'.";
    }
    break;

  default:
    echo <<<HTML
            <h3>Acciones disponibles:</h3>
            <ul>
              <li><a href="?accion=crear">Crear Backup</a></li>
              <li><a href="?accion=listar">Listar Backups (JSON)</a></li>
              <li><a href="?accion=restaurar&archivo=backup_YYYY-MM-DD_HH-MM-SS.sql">Restaurar Backup</a></li>
            </ul>
        HTML;
    break;
}
