<?php

session_start();


if ($_SESSION['rol'] != "cliente") {
  header('location:login.php');
}



// Función para leer todos los scripts de la carpeta
function obtenerScripts()
{
  $scriptsDir = './scripts';
  $scripts = [];

  if (!is_dir($scriptsDir)) {
    return $scripts;
  }

  $carpetas = scandir($scriptsDir);

  foreach ($carpetas as $carpeta) {
    if ($carpeta === '.' || $carpeta === '..') {
      continue;
    }

    $rutaCarpeta = $scriptsDir . '/' . $carpeta;

    if (is_dir($rutaCarpeta)) {
      $archivoInfo = $rutaCarpeta . '/info.json';
      $archivoScript = $rutaCarpeta . '/script.sh';

      if (file_exists($archivoInfo)) {
        $infoJson = file_get_contents($archivoInfo);
        $info = json_decode($infoJson, true);

        if ($info) {
          $info['carpeta'] = $carpeta;
          $info['tiene_script'] = file_exists($archivoScript);
          $info['tamaño_script'] = $info['tiene_script'] ? filesize($archivoScript) : 0;
          $scripts[] = $info;
        }
      }
    }
  }

  return $scripts;
}

// Función para obtener estadísticas
function obtenerEstadisticas($scripts)
{
  $stats = [
    'total' => count($scripts),
    'categorias' => [],
    'precio_promedio' => 0,
    'total_etiquetas' => 0,
    'precio_min' => PHP_FLOAT_MAX,
    'precio_max' => 0
  ];

  $totalPrecio = 0;
  $etiquetasUnicas = [];

  foreach ($scripts as $script) {
    // Categorías
    $categoria = $script['categoria'] ?? 'Sin categoría';
    $stats['categorias'][$categoria] = ($stats['categorias'][$categoria] ?? 0) + 1;

    // Precios
    $precio = floatval($script['precio'] ?? 0);
    $totalPrecio += $precio;
    $stats['precio_min'] = min($stats['precio_min'], $precio);
    $stats['precio_max'] = max($stats['precio_max'], $precio);

    // Etiquetas
    if (isset($script['etiquetas']) && is_array($script['etiquetas'])) {
      foreach ($script['etiquetas'] as $etiqueta) {
        $etiquetasUnicas[$etiqueta] = true;
      }
    }
  }

  $stats['precio_promedio'] = $stats['total'] > 0 ? $totalPrecio / $stats['total'] : 0;
  $stats['total_etiquetas'] = count($etiquetasUnicas);
  $stats['precio_min'] = $stats['precio_min'] === PHP_FLOAT_MAX ? 0 : $stats['precio_min'];

  return $stats;
}

// Obtener scripts y estadísticas
$scripts = obtenerScripts();
$stats = obtenerEstadisticas($scripts);

// Filtros
$categoriaFiltro = $_GET['categoria'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';

// Aplicar filtros
$scriptsFiltrados = $scripts;

if ($categoriaFiltro) {
  $scriptsFiltrados = array_filter($scriptsFiltrados, function ($script) use ($categoriaFiltro) {
    return ($script['categoria'] ?? '') === $categoriaFiltro;
  });
}

if ($busqueda) {
  $scriptsFiltrados = array_filter($scriptsFiltrados, function ($script) use ($busqueda) {
    $texto = strtolower($script['titulo'] . ' ' . $script['descripcion'] . ' ' . implode(' ', $script['etiquetas'] ?? []));
    return strpos($texto, strtolower($busqueda)) !== false;
  });
}

// Ordenar por fecha de modificación (más recientes primero)
usort($scriptsFiltrados, function ($a, $b) {
  return strtotime($b['ultima_modificacion'] ?? '2000-01-01') - strtotime($a['ultima_modificacion'] ?? '2000-01-01');
});
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catálogo de Scripts - TiendaScripts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #092c1f;
      --primary-light: #0d3d2a;
      --primary-dark: #051a12;
      --accent-color: #4a7c59;
    }

    .bg-primary-custom {
      background-color: var(--primary-color) !important;
    }

    .btn-primary-custom {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      color: white;
    }

    .btn-primary-custom:hover {
      background-color: var(--primary-light);
      border-color: var(--primary-light);
    }

    .btn-outline-primary-custom {
      color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-outline-primary-custom:hover {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      color: white;
    }

    .text-primary-custom {
      color: var(--primary-color) !important;
    }

    .border-primary-custom {
      border-color: var(--primary-color) !important;
    }

    .navbar-brand {
      font-weight: bold;
      font-size: 1.5rem;
    }

    .script-card {
      transition: all 0.3s ease;
      border: 1px solid #e0e0e0;
      height: 100%;
    }

    .script-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(9, 44, 31, 0.15);
      border-color: var(--primary-color);
    }

    .price-badge {
      background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
      color: white;
      font-weight: bold;
      font-size: 1.1rem;
    }

    .category-badge {
      background-color: var(--primary-color);
      color: white;
    }

    .tag-badge {
      background-color: #f8f9fa;
      color: var(--primary-color);
      border: 1px solid var(--primary-color);
    }

    .stats-card {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
      color: white;
      border: none;
    }

    .search-box {
      border: 2px solid var(--primary-color);
      border-radius: 25px;
    }

    .search-box:focus {
      border-color: var(--primary-light);
      box-shadow: 0 0 0 0.2rem rgba(9, 44, 31, 0.25);
    }

    .filter-section {
      background-color: #f8f9fa;
      border-radius: 10px;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .no-scripts {
      text-align: center;
      padding: 3rem;
      color: #6c757d;
    }

    .script-description {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .modification-date {
      font-size: 0.85rem;
      color: #6c757d;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-primary-custom">
    <div class="container">
      <a class="navbar-brand text-white" href="#">
        <i class="bi bi-code-square"></i> TiendaScripts - Catálogo
      </a>
      <div class="navbar-nav ms-auto">
        <span class="navbar-text text-white">
          <i class="bi bi-collection"></i> <?php echo count($scripts); ?> Scripts Disponibles
        </span>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <!-- Header -->
    <div class="row mb-4">
      <div class="col-12">
        <h1 class="text-primary-custom">
          <i class="bi bi-grid-3x3-gap"></i> Catálogo de Scripts
        </h1>
        <p class="lead">Explora nuestra colección completa de scripts automatizados</p>
      </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
      <div class="col-md-3 mb-3">
        <div class="card stats-card">
          <div class="card-body text-center">
            <i class="bi bi-collection-fill fs-2 mb-2"></i>
            <h3 class="mb-1"><?php echo $stats['total']; ?></h3>
            <p class="mb-0">Scripts Totales</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card stats-card">
          <div class="card-body text-center">
            <i class="bi bi-tags-fill fs-2 mb-2"></i>
            <h3 class="mb-1"><?php echo count($stats['categorias']); ?></h3>
            <p class="mb-0">Categorías</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card stats-card">
          <div class="card-body text-center">
            <i class="bi bi-currency-dollar fs-2 mb-2"></i>
            <h3 class="mb-1">$<?php echo number_format($stats['precio_promedio'], 2); ?></h3>
            <p class="mb-0">Precio Promedio</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card stats-card">
          <div class="card-body text-center">
            <i class="bi bi-bookmark-fill fs-2 mb-2"></i>
            <h3 class="mb-1"><?php echo $stats['total_etiquetas']; ?></h3>
            <p class="mb-0">Etiquetas Únicas</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="filter-section">
      <form method="GET" class="row g-3">
        <div class="col-md-6">
          <label for="busqueda" class="form-label">
            <i class="bi bi-search"></i> Buscar Scripts
          </label>
          <input type="text" class="form-control search-box" id="busqueda" name="busqueda"
            placeholder="Buscar por título, descripción o etiquetas..."
            value="<?php echo htmlspecialchars($busqueda); ?>">
        </div>
        <div class="col-md-4">
          <label for="categoria" class="form-label">
            <i class="bi bi-funnel"></i> Filtrar por Categoría
          </label>
          <select class="form-select" id="categoria" name="categoria">
            <option value="">Todas las categorías</option>
            <?php foreach ($stats['categorias'] as $cat => $count): ?>
              <option value="<?php echo htmlspecialchars($cat); ?>"
                <?php echo $categoriaFiltro === $cat ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($cat); ?> (<?php echo $count; ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-primary-custom w-100">
            <i class="bi bi-filter"></i> Filtrar
          </button>
        </div>
      </form>

      <?php if ($busqueda || $categoriaFiltro): ?>
        <div class="mt-3">
          <span class="badge bg-info me-2">
            Mostrando <?php echo count($scriptsFiltrados); ?> de <?php echo $stats['total']; ?> scripts
          </span>
          <a href="?" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-x-circle"></i> Limpiar filtros
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Scripts Grid -->
    <?php if (empty($scriptsFiltrados)): ?>
      <div class="no-scripts">
        <i class="bi bi-inbox fs-1 text-muted"></i>
        <h3 class="mt-3">No se encontraron scripts</h3>
        <p>Intenta ajustar los filtros de búsqueda o categoría.</p>
        <a href="?" class="btn btn-primary-custom">Ver todos los scripts</a>
      </div>
    <?php else: ?>
      <div class="row">
        <?php foreach ($scriptsFiltrados as $script): ?>
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="card script-card">
              <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="badge category-badge">
                  <?php echo htmlspecialchars($script['categoria'] ?? 'Sin categoría'); ?>
                </span>
                <span class="badge price-badge">
                  $<?php echo number_format($script['precio'] ?? 0, 2); ?>
                </span>
              </div>

              <div class="card-body">
                <h5 class="card-title text-primary-custom">
                  <i class="bi bi-file-code"></i>
                  <?php echo htmlspecialchars($script['titulo'] ?? 'Sin título'); ?>
                </h5>

                <p class="card-text script-description">
                  <?php echo htmlspecialchars($script['descripcion'] ?? 'Sin descripción disponible.'); ?>
                </p>

                <!-- Etiquetas -->
                <?php if (!empty($script['etiquetas'])): ?>
                  <div class="mb-3">
                    <?php foreach ($script['etiquetas'] as $etiqueta): ?>
                      <span class="badge tag-badge me-1 mb-1">
                        #<?php echo htmlspecialchars($etiqueta); ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <!-- Información adicional -->
                <div class="row text-muted small mb-3">
                  <div class="col-6">
                    <i class="bi bi-folder2"></i>
                    <strong>Carpeta:</strong><br>
                    <code><?php echo htmlspecialchars($script['carpeta']); ?></code>
                  </div>
                  <div class="col-6">
                    <i class="bi bi-file-earmark-code"></i>
                    <strong>Script:</strong><br>
                    <?php if ($script['tiene_script']): ?>
                      <span class="text-success">
                        ✓ Disponible (<?php echo number_format($script['tamaño_script'] / 1024, 1); ?>KB)
                      </span>
                    <?php else: ?>
                      <span class="text-danger">✗ No encontrado</span>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="modification-date mb-3">
                  <i class="bi bi-clock"></i>
                  <strong>Última modificación:</strong>
                  <?php
                  $fecha = $script['ultima_modificacion'] ?? '';
                  if ($fecha) {
                    echo date('d/m/Y H:i', strtotime($fecha));
                  } else {
                    echo 'No disponible';
                  }
                  ?>
                </div>

                <!-- Botones de acción -->
                <div class="d-grid gap-2">
                  <button class="btn btn-primary-custom"
                    onclick="verDetalles('<?php echo htmlspecialchars($script['carpeta']); ?>')">
                    <i class="bi bi-eye"></i> Ver Detalles
                  </button>
                  <?php if ($script['tiene_script']): ?>
                    <button class="btn btn-outline-primary-custom"
                      onclick="descargarScript('<?php echo htmlspecialchars($script['carpeta']); ?>')">
                      <i class="bi bi-download"></i> Descargar Script
                    </button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Resumen de precios -->
    <?php if (!empty($scriptsFiltrados)): ?>
      <div class="card mt-4 border-primary-custom">
        <div class="card-header bg-primary-custom text-white">
          <h5 class="mb-0"><i class="bi bi-graph-up"></i> Resumen de Precios</h5>
        </div>
        <div class="card-body">
          <div class="row text-center">
            <div class="col-md-3">
              <h4 class="text-primary-custom">$<?php echo number_format($stats['precio_min'], 2); ?></h4>
              <small class="text-muted">Precio Mínimo</small>
            </div>
            <div class="col-md-3">
              <h4 class="text-primary-custom">$<?php echo number_format($stats['precio_promedio'], 2); ?></h4>
              <small class="text-muted">Precio Promedio</small>
            </div>
            <div class="col-md-3">
              <h4 class="text-primary-custom">$<?php echo number_format($stats['precio_max'], 2); ?></h4>
              <small class="text-muted">Precio Máximo</small>
            </div>
            <div class="col-md-3">
              <h4 class="text-primary-custom">$<?php echo number_format(array_sum(array_column($scriptsFiltrados, 'precio')), 2); ?></h4>
              <small class="text-muted">Valor Total</small>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- Modal para detalles -->
  <div class="modal fade" id="detallesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary-custom text-white">
          <h5 class="modal-title">
            <i class="bi bi-info-circle"></i> Detalles del Script
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="modalContent">
          <!-- Contenido dinámico -->
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Datos de scripts para JavaScript
    const scripts = <?php echo json_encode($scriptsFiltrados); ?>;

    function verDetalles(carpeta) {
      const script = scripts.find(s => s.carpeta === carpeta);
      if (!script) return;

      const modalContent = document.getElementById('modalContent');
      modalContent.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="text-primary-custom">${script.titulo}</h4>
                        <p class="lead">${script.descripcion}</p>
                        
                        <h6 class="text-primary-custom">Información Técnica:</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Categoría:</strong>
                                <span class="badge category-badge">${script.categoria || 'Sin categoría'}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Carpeta:</strong>
                                <code>${script.carpeta}</code>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Script disponible:</strong>
                                <span class="${script.tiene_script ? 'text-success' : 'text-danger'}">
                                    ${script.tiene_script ? '✓ Sí' : '✗ No'}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Tamaño:</strong>
                                <span>${(script.tamaño_script / 1024).toFixed(1)} KB</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Última modificación:</strong>
                                <span>${new Date(script.ultima_modificacion).toLocaleString('es-ES')}</span>
                            </li>
                        </ul>
                        
                        ${script.etiquetas && script.etiquetas.length > 0 ? `
                            <h6 class="text-primary-custom">Etiquetas:</h6>
                            <div class="mb-3">
                                ${script.etiquetas.map(tag => `
                                    <span class="badge tag-badge me-1">#${tag}</span>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-currency-dollar fs-1 text-primary-custom"></i>
                                <h2 class="text-primary-custom">$${parseFloat(script.precio).toFixed(2)}</h2>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary-custom">
                                        <i class="bi bi-cart-plus"></i> Añadir al Carrito
                                    </button>
                                    <button class="btn btn-outline-primary-custom">
                                        <i class="bi bi-heart"></i> Favoritos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

      new bootstrap.Modal(document.getElementById('detallesModal')).show();
    }

    function descargarScript(carpeta) {
      // Simular descarga - aquí podrías implementar la descarga real
      alert(`Descargando script de la carpeta: ${carpeta}\n\nEn una implementación real, esto iniciaría la descarga del archivo script.sh`);
    }

    // Efecto de hover en las tarjetas
    document.addEventListener('DOMContentLoaded', function() {
      const cards = document.querySelectorAll('.script-card');
      cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.borderColor = 'var(--primary-color)';
        });
        card.addEventListener('mouseleave', function() {
          this.style.borderColor = '#e0e0e0';
        });
      });
    });
  </script>
</body>

</html>
