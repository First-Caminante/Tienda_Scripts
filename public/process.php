<?php

require('../vendor/autoload.php');

use App\Controllers\Functions;

$funciones = new Functions();

/*
 *
 *funciones reportes
 *
 * */





// Añade estas funciones ANTES del switch principal

/**
 * Genera reporte de solicitudes en PDF
 */
function generateSolicitudesReport($pdf, $funciones)
{
  $pdf->AddPage();
  $pdf->SetFont('helvetica', 'B', 16);
  $pdf->Cell(0, 10, 'Reporte de Solicitudes por Estado', 0, 1, 'C');

  $solicitudes = $funciones->getSolicitudesPorEstado();

  // Cabecera de tabla
  $pdf->SetFont('helvetica', 'B', 12);
  $pdf->Cell(100, 10, 'Estado', 1, 0, 'C');
  $pdf->Cell(80, 10, 'Cantidad', 1, 1, 'C');

  // Datos
  $pdf->SetFont('helvetica', '', 12);
  foreach ($solicitudes as $solicitud) {
    $pdf->Cell(100, 10, ucfirst($solicitud['estado']), 1, 0, 'L');
    $pdf->Cell(80, 10, $solicitud['cantidad'], 1, 1, 'C');
  }
}

/**
 * Genera reporte de pagos en PDF
 */
function generatePagosReport($pdf, $funciones)
{
  $pdf->AddPage();
  $pdf->SetFont('helvetica', 'B', 16);
  $pdf->Cell(0, 10, 'Reporte de Pagos Mensuales', 0, 1, 'C');

  $pagos = $funciones->getPagosMensuales();

  // Cabecera de tabla
  $pdf->SetFont('helvetica', 'B', 12);
  $pdf->Cell(70, 10, 'Mes', 1, 0, 'C');
  $pdf->Cell(60, 10, 'Monto Total', 1, 0, 'C');
  $pdf->Cell(60, 10, 'Cantidad', 1, 1, 'C');

  // Datos
  $pdf->SetFont('helvetica', '', 12);
  foreach ($pagos as $pago) {
    $pdf->Cell(70, 10, $pago['nombre_mes'], 1, 0, 'L');
    $pdf->Cell(60, 10, '$' . number_format($pago['total_pagos'], 2), 1, 0, 'R');
    $pdf->Cell(60, 10, $pago['cantidad_pagos'], 1, 1, 'C');
  }
}

/**
 * Genera reporte de usuarios en PDF
 */
function generateUsuariosReport($pdf, $funciones)
{
  $pdf->AddPage();
  $pdf->SetFont('helvetica', 'B', 16);
  $pdf->Cell(0, 10, 'Reporte de Usuarios por Rol', 0, 1, 'C');

  $usuarios = $funciones->getUsuariosPorRol();

  // Cabecera de tabla
  $pdf->SetFont('helvetica', 'B', 12);
  $pdf->Cell(100, 10, 'Rol', 1, 0, 'C');
  $pdf->Cell(80, 10, 'Cantidad', 1, 1, 'C');

  // Datos
  $pdf->SetFont('helvetica', '', 12);
  foreach ($usuarios as $usuario) {
    $pdf->Cell(100, 10, ucfirst($usuario['rol']), 1, 0, 'L');
    $pdf->Cell(80, 10, $usuario['cantidad'], 1, 1, 'C');
  }
}



##################################################




if ($_SERVER['REQUEST_METHOD'] === "POST") {
  $action = $_POST['action'] ?? '';
  switch ($action) {
    case 'addUser':
      $data = [
        ':nombre' => $_POST['nombre'],
        ':email' => $_POST['email'],
        ':password_hash' => password_hash($_POST['password_hash'], PASSWORD_DEFAULT)
      ];
      #dd($data);
      $funciones->addUser($data);
      header('Location:/EISPDM_PROJECTS/Tienda_Scripts/public/login.php');

      break;
    case 'loginUser':
      $data = [
        'email' => $_POST['email'],
        'password_hash' => $_POST['password_hash']
      ];

      $user = $funciones->loginUsuario($_POST['email'], $_POST['password_hash']);



      if ($user['success']) {
        //header('location:/EISPDM_PROJECTS/Tienda_Scripts/public/user.php');
        $rol = $user['rol'];

        session_start();

        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['id'] = $user['id'];


        if ($rol === 'admin') {

          header('Location:/EISPDM_PROJECTS/Tienda_Scripts/public/admin.php');
        } elseif ($rol === 'cliente') {

          header('Location:/EISPDM_PROJECTS/Tienda_Scripts/public/user.php');
          exit;
        } elseif ($rol === 'desarrollador') {
          header('Location: dev.php');
        } else {
          echo "Rol desconocido.";
        }
      } else {
        echo "error al iniciar session";
      }

      break;
    case 'editUser':
      // Verificar que el ID exista
      if (isset($_POST['id'])) {
        $userId = $_POST['id'];
        $data = [];

        // Recoger los datos del formulario
        if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
          $data['nombre'] = $_POST['nombre'];
        }

        if (isset($_POST['rol']) && !empty($_POST['rol'])) {
          $data['rol'] = $_POST['rol'];
        }

        // Solo actualizar la contraseña si se proporcionó una nueva
        if (isset($_POST['password_hash']) && !empty($_POST['password_hash'])) {
          $data['password_hash'] = password_hash($_POST['password_hash'], PASSWORD_DEFAULT);
          // Aquí podrías añadir un hash a la contraseña si lo necesitas
          // $data['password_hash'] = password_hash($_POST['password_hash'], PASSWORD_DEFAULT);
        }

        //dd($data, $userId);

        // Llamar a la función editUser
        $result = $funciones->editUser($userId, $data);

        header('Location:/EISPDM_PROJECTS/Tienda_Scripts/public/admin.php');

        // Redirigir con mensaje según el resultado
        /*  if ($result['success']) {*/
        /*    header('Location: index.php?tab=usuarios&msg=Usuario actualizado correctamente');*/
        /*  } else {*/
        /*    header('Location: index.php?tab=usuarios&error=' . urlencode($result['message']));*/
        /*  }*/
        /*} else {*/
        /*  header('Location: index.php?tab=usuarios&error=ID de usuario no especificado');*/
      }
      break;
    case 'deleteUser':

      $id = $_POST['id'];

      //dd($id);

      $funciones->deleteUser($id);

      header('Location:/EISPDM_PROJECTS/Tienda_Scripts/public/admin.php');
      exit;
      break;

    #nuevos reportes
    #
    case 'getReportData':
      // Verificar si el usuario está autenticado y es admin (opcional)

      // Obtener los datos para los reportes
      $solicitudes = $funciones->getSolicitudesPorEstado();
      $pagos = $funciones->getPagosMensuales();
      $usuarios = $funciones->getUsuariosPorRol();

      // Verificar si hay errores
      if (isset($solicitudes['success']) && $solicitudes['success'] === false) {
        echo json_encode(['success' => false, 'message' => $solicitudes['message']]);
        exit;
      }

      // Devolver los datos en formato JSON
      echo json_encode([
        'success' => true,
        'solicitudes' => $solicitudes,
        'pagos' => $pagos,
        'usuarios' => $usuarios
      ]);
      exit;
      break;

    /*case 'generateReport':
      // Verificar si el usuario está autenticado y es admin

      // Incluir la librería TCPDF
      require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

      // Tipo de reporte a generar
      $reportType = $_POST['reportType'] ?? 'all';

      // Crear un nuevo objeto PDF
      $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

      // Configurar el PDF
      $pdf->SetCreator('Tienda Scripts');
      $pdf->SetAuthor('Administrador');
      $pdf->SetTitle('Reporte - Tienda Scripts');
      $pdf->SetSubject('Reporte Estadístico');

      // Eliminar cabecera y pie de página predeterminados
      $pdf->setPrintHeader(false);
      $pdf->setPrintFooter(false);

      // Establecer márgenes
      $pdf->SetMargins(15, 15, 15);

      // Dependiendo del tipo de reporte solicitado
      switch ($reportType) {
        case 'solicitudes':
          generateSolicitudesReport($pdf, $funciones);
          break;

        case 'pagos':
          generatePagosReport($pdf, $funciones);
          break;

        case 'usuarios':
          generateUsuariosReport($pdf, $funciones);
          break;

        case 'all':
        default:
          // Añadir una página
          $pdf->AddPage();

          // Título del reporte
          $pdf->SetFont('helvetica', 'B', 20);
          $pdf->Cell(0, 15, 'Reporte Completo - Tienda Scripts', 0, 1, 'C');
          $pdf->SetFont('helvetica', '', 12);
          $pdf->Cell(0, 10, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
          $pdf->Ln(10);

          // Añadir las estadísticas generales
          addEstadisticasGenerales($pdf, $funciones);

          // Añadir los reportes específicos
          generateSolicitudesReport($pdf, $funciones);
          generatePagosReport($pdf, $funciones);
          generateUsuariosReport($pdf, $funciones);
          break;*/

    case 'generateReport':
      try {
        require_once('../vendor/autoload.php');
        $reportType = $_POST['reportType'] ?? 'all';

        // Configuración básica de TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Tienda Scripts');
        $pdf->SetAuthor('Sistema de Reportes');
        $pdf->SetTitle('Reporte ' . ucfirst($reportType));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Contenido simple para prueba
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Reporte de ' . ucfirst($reportType), 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Generado el: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

        // Forzar descarga
        $pdf->Output('reporte_' . $reportType . '.pdf', 'D');
        exit;
      } catch (Exception $e) {
        error_log('Error generando PDF: ' . $e->getMessage());
        echo json_encode([
          'success' => false,
          'message' => 'Error al generar PDF: ' . $e->getMessage()
        ]);
      }
      break;
    ////////////desde aqui al final solicitudes
    //
    case 'crearSolicitud':
      session_start();

      if (!isset($_SESSION['id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
        exit;
      }

      $data = [
        'usuario_id' => $_SESSION['id'],
        'titulo' => $_POST['titulo'] ?? '',
        'descripcion' => $_POST['descripcion'] ?? '',
        'estado' => 'pendiente'
      ];

      // Validación básica
      if (empty($data['titulo']) || empty($data['descripcion'])) {
        echo json_encode(['success' => false, 'message' => 'Título y descripción son requeridos']);
        exit;
      }

      $result = $funciones->crearSolicitud($data);

      if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
        header('Content-Type: application/json');
        echo json_encode($result);
      } else {
        if ($result['success']) {
          header('Location: solicitudes.php?success=1');
        } else {
          header('Location: solicitudes.php?error=' . urlencode($result['message']));
        }
      }
      exit;
      break;

    case 'getSolicitudesUsuario':
      session_start();

      if (!isset($_SESSION['id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
        exit;
      }

      $solicitudes = $funciones->getSolicitudesByUsuario($_SESSION['id']);

      header('Content-Type: application/json');
      echo json_encode([
        'success' => true,
        'solicitudes' => $solicitudes
      ]);
      exit;
      break;

    case 'eliminarSolicitud':
      session_start();

      if (!isset($_SESSION['id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
        exit;
      }

      $solicitud_id = $_POST['solicitud_id'] ?? 0;

      if (!$solicitud_id) {
        echo json_encode(['success' => false, 'message' => 'ID de solicitud requerido']);
        exit;
      }

      // Solo permitir eliminar solicitudes del usuario actual
      $result = $funciones->eliminarSolicitud($solicitud_id, $_SESSION['id']);

      if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
        header('Content-Type: application/json');
        echo json_encode($result);
      } else {
        if ($result['success']) {
          header('Location: solicitudes.php?deleted=1');
        } else {
          header('Location: solicitudes.php?error=' . urlencode($result['message']));
        }
      }
      exit;
      break;

    case 'actualizarEstadoSolicitud':
      session_start();

      // Solo admin puede cambiar estados
      if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
        exit;
      }

      $solicitud_id = $_POST['solicitud_id'] ?? 0;
      $nuevo_estado = $_POST['estado'] ?? '';

      $estados_validos = ['pendiente', 'en proceso', 'completado', 'rechazado'];

      if (!$solicitud_id || !in_array($nuevo_estado, $estados_validos)) {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        exit;
      }

      $result = $funciones->actualizarEstadoSolicitud($solicitud_id, $nuevo_estado);

      header('Content-Type: application/json');
      echo json_encode($result);
      exit;
      break;
  };
} else {
  echo "error no method post aaaaaaaaaa";
}

// Función para añadir estadísticas generales
function addEstadisticasGenerales($pdf, $funciones)
{
  $estadisticas = $funciones->getEstadisticasGenerales();

  $pdf->SetFont('helvetica', 'B', 16);
  $pdf->Cell(0, 10, 'Estadísticas Generales', 0, 1, 'L');
  $pdf->Ln(5);

  $pdf->SetFont('helvetica', '', 12);
  $pdf->Cell(90, 10, 'Total de Usuarios:', 0, 0, 'L');
  $pdf->Cell(0, 10, $estadisticas['total_usuarios'] ?? '0', 0, 1, 'L');

  $pdf->Cell(90, 10, 'Total de Solicitudes:', 0, 0, 'L');
  $pdf->Cell(0, 10, $estadisticas['total_solicitudes'] ?? '0', 0, 1, 'L');

  $pdf->Cell(90, 10, 'Total de Pagos Realizados:', 0, 0, 'L');
  $pdf->Cell(0, 10, $estadisticas['total_pagos'] ?? '0', 0, 1, 'L');

  $pdf->Cell(90, 10, 'Monto Total de Pagos:', 0, 0, 'L');
  //$pdf->Cell(0, 10, ' . number_format($estadisticas['monto_total_pagos'] ?? 0, 2), 0, 1, 'L');
  $pdf->Cell(0, 10, '$' . number_format($estadisticas['monto_total_pagos'] ?? 0, 2), 0, 1, 'L');
  $pdf->Ln(5);
}
