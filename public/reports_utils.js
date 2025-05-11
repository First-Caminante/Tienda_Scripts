/**
 * Utilidades para los reportes y generación de PDF
 */

// Mostrar indicador de carga
function showLoadingIndicator() {
  const loader = document.createElement('div');
  loader.id = 'pdf-loading-indicator';
  loader.style.position = 'fixed';
  loader.style.top = '0';
  loader.style.left = '0';
  loader.style.width = '100%';
  loader.style.height = '100%';
  loader.style.backgroundColor = 'rgba(0,0,0,0.5)';
  loader.style.display = 'flex';
  loader.style.justifyContent = 'center';
  loader.style.alignItems = 'center';
  loader.style.zIndex = '9999';
  
  const spinner = document.createElement('div');
  spinner.style.border = '5px solid #f3f3f3';
  spinner.style.borderTop = '5px solid #3498db';
  spinner.style.borderRadius = '50%';
  spinner.style.width = '50px';
  spinner.style.height = '50px';
  spinner.style.animation = 'spin 1s linear infinite';
  
  const style = document.createElement('style');
  style.textContent = `
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  `;
  
  loader.appendChild(spinner);
  document.body.appendChild(style);
  document.body.appendChild(loader);
}

// Ocultar indicador de carga
function hideLoadingIndicator() {
  const loader = document.getElementById('pdf-loading-indicator');
  if (loader) {
    loader.remove();
  }
}

/**
 * Convierte una gráfica de Chart.js en una imagen base64
 * @param {HTMLCanvasElement} chartCanvas - El canvas de la gráfica
 * @return {Promise<string>} - Promesa que resuelve a una imagen base64
 */
function chartToImage(chartCanvas) {
  return new Promise((resolve) => {
    const image = chartCanvas.toDataURL('image/png', 1.0);
    resolve(image);
  });
}

/**
 * Genera un PDF con jsPDF incluyendo las gráficas
 * @param {string} reportType - Tipo de reporte a generar (solicitudes, pagos, usuarios, all)
 */
async function generarPDFConGraficas(reportType) {
  try {
    // Mostrar indicador de carga
    showLoadingIndicator();
    
    // Inicializar jsPDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // Función para añadir encabezado
    function addHeader(title) {
      doc.setFontSize(22);
      doc.setFont('helvetica', 'bold');
      doc.text(title, 105, 20, null, null, 'center');
      doc.setFontSize(12);
      doc.setFont('helvetica', 'normal');
      doc.text(`Fecha: ${new Date().toLocaleDateString()}`, 105, 30, null, null, 'center');
      doc.line(20, 35, 190, 35);
    }
    
    // Función para añadir estadísticas generales
    function addEstadisticas(stats) {
      doc.setFontSize(16);
      doc.setFont('helvetica', 'bold');
      doc.text('Estadísticas Generales', 20, 45);
      
      doc.setFontSize(12);
      doc.setFont('helvetica', 'normal');
      doc.text(`Total Usuarios: ${stats.total_usuarios || 0}`, 20, 55);
      doc.text(`Total Solicitudes: ${stats.total_solicitudes || 0}`, 20, 62);
      doc.text(`Total Pagos: ${stats.total_pagos || 0}`, 20, 69);
      doc.text(`Monto Total: $${(stats.monto_total_pagos || 0).toFixed(2)}`, 20, 76);
    }
    
    // Obtener los datos del servidor
    const response = await fetch('process.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `action=getReportData`
    });
    
    const data = await response.json();
    
    if (!data.success) {
      hideLoadingIndicator();
      alert('Error al cargar los datos para el reporte');
      return;
    }
    
    // Determinar qué reportes incluir
    let incluirEstadisticas = false;
    let incluirSolicitudes = false;
    let incluirPagos = false;
    let incluirUsuarios = false;
    
    switch (reportType) {
      case 'all':
        incluirEstadisticas = true;
        incluirSolicitudes = true;
        incluirPagos = true;
        incluirUsuarios = true;
        break;
      case 'solicitudes':
        incluirSolicitudes = true;
        break;
      case 'pagos':
        incluirPagos = true;
        break;
      case 'usuarios':
        incluirUsuarios = true;
        break;
    }
    
    // Primera página con estadísticas generales (si aplica)
    if (incluirEstadisticas) {
      addHeader('Reporte General - Tienda Scripts');
      
      // Obtener estadísticas generales
      const statsResponse = await fetch('process.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=getEstadisticasGenerales`
      });
      
      const stats = await statsResponse.json();
      addEstadisticas(stats);
    }
    
    // Reporte de solicitudes
    if (incluirSolicitudes) {
      if (incluirEstadisticas) doc.addPage();
      addHeader('Reporte de Solicitudes por Estado');
      
      // Convertir el gráfico de solicitudes a imagen
      const solicitudesCanvas = document.getElementById('solicitudesChart');
      const solicitudesImg = await chartToImage(solicitudesCanvas);
      
      // Añadir la imagen al PDF
      doc.text('Distribución de Solicitudes por Estado', 105, 45, null, null, 'center');
      doc.addImage(solicitudesImg, 'PNG', 25, 50, 160, 80);
      
      // Añadir tabla de datos
      doc.text('Detalle de Solicitudes', 20, 140);
      
      // Configurar la tabla
      const solicitudesHeaders = [['Estado', 'Cantidad', 'Porcentaje']];
      const solicitudesBody = [];
      
      // Calcular el total
      const totalSolicitudes = data.solicitudes.reduce((sum, item) => sum + parseInt(item.cantidad), 0);
      
      // Preparar los datos
      data.solicitudes.forEach(item => {
        const estado = item.estado.charAt(0).toUpperCase() + item.estado.slice(1);
        const cantidad = parseInt(item.cantidad);
        const porcentaje = totalSolicitudes > 0 ? ((cantidad / totalSolicitudes) * 100).toFixed(2) + '%' : '0%';
        
        solicitudesBody.push([estado, cantidad.toString(), porcentaje]);
      });
      
      // Añadir fila de total
      solicitudesBody.push(['Total', totalSolicitudes.toString(), '100%']);
      
      // Generar la tabla
      doc.autoTable({
        head: solicitudesHeaders,
        body: solicitudesBody,
        startY: 145,
        theme: 'grid',
        headStyles: { fillColor: [41, 128, 185], textColor: 255 },
        footStyles: { fillColor: [220, 220, 220], textColor: 0, fontStyle: 'bold' }
      });
      
      // Añadir descripción
      const descripccionY = doc.previousAutoTable.finalY + 10;
      doc.setFontSize(11);
      doc.text('Este reporte muestra la distribución de solicitudes por estado en el sistema. Los estados', 20, descripccionY);
      doc.text('posibles son: pendiente, en proceso, completado y rechazado. Estas métricas son útiles', 20, descripccionY + 6);
      doc.text('para evaluar la eficiencia en la atención de solicitudes y la carga de trabajo actual.', 20, descripccionY + 12);
    }
    
    // Reporte de pagos mensuales
    if (incluirPagos) {
      doc.addPage();
      addHeader('Reporte de Pagos Mensuales');
      
      // Convertir el gráfico de pagos a imagen
      const pagosCanvas = document.getElementById('pagosChart');
      const pagosImg = await chartToImage(pagosCanvas);
      
      // Añadir la imagen al PDF
      doc.text('Evolución de Pagos Mensuales', 105, 45, null, null, 'center');
      doc.addImage(pagosImg, 'PNG', 25, 50, 160, 80);
      
      // Añadir tabla de datos
      doc.text('Detalle de Pagos Mensuales', 20, 140);
      
      // Configurar la tabla
      const pagosHeaders = [['Mes', 'Monto Total ($)', 'Cantidad de Pagos']];
      const pagosBody = [];
      
      // Calcular totales
      let totalMonto = 0;
      let totalCantidad = 0;
      
      // Preparar los datos
      data.pagos.forEach(item => {
        const mes = item.nombre_mes;
        const monto = parseFloat(item.total_pagos);
        const cantidad = parseInt(item.cantidad_pagos);
        
        totalMonto += monto;
        totalCantidad += cantidad;
        
        pagosBody.push([mes, monto.toFixed(2), cantidad.toString()]);
      });
      
      // Añadir fila de total
      pagosBody.push(['Total', totalMonto.toFixed(2), totalCantidad.toString()]);
      
      // Generar la tabla
      doc.autoTable({
        head: pagosHeaders,
        body: pagosBody,
        startY: 145,
        theme: 'grid',
        headStyles: { fillColor: [41, 128, 185], textColor: 255 },
        footStyles: { fillColor: [220, 220, 220], textColor: 0, fontStyle: 'bold' }
      });
      
      // Añadir descripción
      const descripccionY = doc.previousAutoTable.finalY + 10;
      doc.setFontSize(11);
      doc.text('Este reporte muestra los pagos mensuales realizados durante el año en curso. Se incluye', 20, descripccionY);
      doc.text('tanto el monto total recaudado como la cantidad de pagos procesados por mes. Esta información', 20, descripccionY + 6);
      doc.text('es útil para analizar tendencias de ingresos y planificar estrategias financieras.', 20, descripccionY + 12);
    }
    
    // Reporte de usuarios por rol
    if (incluirUsuarios) {
      doc.addPage();
      addHeader('Reporte de Usuarios por Rol');
      
      // Convertir el gráfico de usuarios a imagen
      const usuariosCanvas = document.getElementById('usuariosChart');
      const usuariosImg = await chartToImage(usuariosCanvas);
      
      // Añadir la imagen al PDF
      doc.text('Distribución de Usuarios por Rol', 105, 45, null, null, 'center');
      doc.addImage(usuariosImg, 'PNG', 25, 50, 160, 80);
      
      // Añadir tabla de datos
      doc.text('Detalle de Usuarios por Rol', 20, 140);
      
      // Configurar la tabla
      const usuariosHeaders = [['Rol', 'Cantidad', 'Porcentaje']];
      const usuariosBody = [];
      
      // Calcular el total
      const totalUsuarios = data.usuarios.reduce((sum, item) => sum + parseInt(item.cantidad), 0);
      
      // Preparar los datos
      data.usuarios.forEach(item => {
        const rol = item.rol.charAt(0).toUpperCase() + item.rol.slice(1);
        const cantidad = parseInt(item.cantidad);
        const porcentaje = totalUsuarios > 0 ? ((cantidad / totalUsuarios) * 100).toFixed(2) + '%' : '0%';
        
        usuariosBody.push([rol, cantidad.toString(), porcentaje]);
      });
      
      // Añadir fila de total
      usuariosBody.push(['Total', totalUsuarios.toString(), '100%']);
      
      // Generar la tabla
      doc.autoTable({
        head: usuariosHeaders,
        body: usuariosBody,
        startY: 145,
        theme: 'grid',
        headStyles: { fillColor: [41, 128, 185], textColor: 255 },
        footStyles: { fillColor: [220, 220, 220], textColor: 0, fontStyle: 'bold' }
      });
      
      // Añadir descripción
      const descripccionY = doc.previousAutoTable.finalY + 10;
      doc.setFontSize(11);
      doc.text('Este reporte muestra la distribución de usuarios según su rol en el sistema. Los roles', 20, descripccionY);
      doc.text('principales son: administrador, desarrollador y cliente. Esta información es útil para', 20, descripccionY + 6);
      doc.text('entender la composición de usuarios y asignar recursos adecuadamente.', 20, descripccionY + 12);
    }
    
    // Guardar el PDF
    doc.save(`reporte_${reportType}_${new Date().toISOString().slice(0, 10)}.pdf');
    
    // Ocultar indicador de carga
    hideLoadingIndicator();
    
  } catch (error) {
    console.error('Error al generar el PDF:', error);
    hideLoadingIndicator();
    alert('Ocurrió un error al generar el reporte: ' + error.message);
  }
 }

// Exportar funciones para uso global
window.reportUtils = {
  chartToImage,
  generarPDFConGraficas,
  showLoadingIndicator,
  hideLoadingIndicator
};
