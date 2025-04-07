document.addEventListener('DOMContentLoaded', function() {
    // Verificar si estamos en la pestaña de cierre de caja
    const tabCierreCaja = document.getElementById('tab3');
    if (!tabCierreCaja) return;

    // Elementos del DOM
    const formCierreCaja = document.getElementById('formCierreCaja');
    const btnBuscarReportes = document.getElementById('btnBuscarReportes');
    const fechaBusqueda = document.getElementById('fecha_busqueda');
    const tablaReportesHoy = document.getElementById('tablaReportesHoy');
    const tablaHistorialReportes = document.getElementById('tablaHistorialReportes');

    // Cargar reportes generados hoy
    cargarReportesHoy();

    // Cargar historial de reportes
    cargarHistorialReportes();

    // Evento para buscar reportes por fecha
    if (btnBuscarReportes) {
        btnBuscarReportes.addEventListener('click', function() {
            const fecha = fechaBusqueda.value;
            if (fecha) {
                cargarReportesPorFecha(fecha);
            } else {
                alert('Por favor seleccione una fecha para buscar.');
            }
        });
    }

    // Validación del formulario
    if (formCierreCaja) {
        formCierreCaja.addEventListener('submit', function(event) {
            event.preventDefault(); // Siempre prevenir el envío por defecto
            
            if (!formCierreCaja.checkValidity()) {
                event.stopPropagation();
                formCierreCaja.classList.add('was-validated');
            } else {
                // Confirmar antes de generar el reporte con un modal bonito
                Swal.fire({
                    title: '¿Generar reporte de cierre?',
                    text: '¿Está seguro de generar el reporte de cierre de caja? Una vez generado, no podrá ser modificado.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, generar reporte',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si el usuario confirma, enviar el formulario
                        formCierreCaja.submit();
                    }
                });
            }
            
            formCierreCaja.classList.add('was-validated');
        });
    }

    // Función para cargar los reportes generados hoy
    function cargarReportesHoy() {
        fetch('../../controladores/obtener_reportes_caja.php?tipo=hoy')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                
                if (data.length === 0) {
                    tablaReportesHoy.innerHTML = '<tr><td colspan="4" class="text-center">No hay reportes generados hoy</td></tr>';
                    return;
                }

                let html = '';
                data.forEach(reporte => {
                    html += `
                        <tr>
                            <td>${reporte.fecha}</td>
                            <td>${reporte.hora}</td>
                            <td>${reporte.operador}</td>
                            <td>
                                ${reporte.html_existe ? 
                                    `<div class="btn-group">
                                        <a href="../../${reporte.ruta_html}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-file"></i> Ver Reporte
                                        </a>
                                        <a href="../../controladores/descargar_reporte_pdf.php?id=${reporte.id_reporte}" target="_blank" class="btn btn-sm btn-success">
                                            <i class="fas fa-file-pdf"></i> Descargar PDF
                                        </a>
                                    </div>` : 
                                    '<span class="badge bg-danger">Reporte no disponible</span>'}
                            </td>
                        </tr>
                    `;
                });
                
                tablaReportesHoy.innerHTML = html;
            })
            .catch(error => {
                console.error('Error al cargar reportes:', error);
                tablaReportesHoy.innerHTML = '<tr><td colspan="4" class="text-center">Error al cargar reportes</td></tr>';
            });
    }

    // Función para cargar el historial de reportes
    function cargarHistorialReportes() {
        fetch('../../controladores/obtener_reportes_caja.php?tipo=historial')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                
                if (data.length === 0) {
                    tablaHistorialReportes.innerHTML = '<tr><td colspan="5" class="text-center">No hay reportes disponibles</td></tr>';
                    return;
                }

                let html = '';
                data.forEach(reporte => {
                    html += `
                        <tr>
                            <td>${reporte.fecha}</td>
                            <td>${reporte.hora}</td>
                            <td>$${Number(reporte.total_recaudado).toLocaleString('es-CO')}</td>
                            <td>${reporte.operador}</td>
                            <td>
                                ${reporte.html_existe ? 
                                    `<div class="btn-group">
                                        <a href="../../${reporte.ruta_html}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-file"></i> Ver Reporte
                                        </a>
                                        <a href="../../controladores/descargar_reporte_pdf.php?id=${reporte.id_reporte}" target="_blank" class="btn btn-sm btn-success">
                                            <i class="fas fa-file-pdf"></i> Descargar PDF
                                        </a>
                                    </div>` : 
                                    '<span class="badge bg-danger">Reporte no disponible</span>'}
                            </td>
                        </tr>
                    `;
                });
                
                tablaHistorialReportes.innerHTML = html;
            })
            .catch(error => {
                console.error('Error al cargar historial de reportes:', error);
                tablaHistorialReportes.innerHTML = '<tr><td colspan="5" class="text-center">Error al cargar reportes</td></tr>';
            });
    }

    // Función para cargar reportes por fecha específica
    function cargarReportesPorFecha(fecha) {
        fetch(`../../controladores/obtener_reportes_caja.php?tipo=fecha&fecha=${fecha}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                
                if (data.length === 0) {
                    tablaHistorialReportes.innerHTML = '<tr><td colspan="5" class="text-center">No hay reportes para la fecha seleccionada</td></tr>';
                    return;
                }

                let html = '';
                data.forEach(reporte => {
                    html += `
                        <tr>
                            <td>${reporte.fecha}</td>
                            <td>${reporte.hora}</td>
                            <td>$${Number(reporte.total_recaudado).toLocaleString('es-CO')}</td>
                            <td>${reporte.operador}</td>
                            <td>
                                ${reporte.html_existe ? 
                                    `<div class="btn-group">
                                        <a href="../../${reporte.ruta_html}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-file"></i> Ver Reporte
                                        </a>
                                        <a href="../../controladores/descargar_reporte_pdf.php?id=${reporte.id_reporte}" target="_blank" class="btn btn-sm btn-success">
                                            <i class="fas fa-file-pdf"></i> Descargar PDF
                                        </a>
                                    </div>` : 
                                    '<span class="badge bg-danger">Reporte no disponible</span>'}
                            </td>
                        </tr>
                    `;
                });
                
                tablaHistorialReportes.innerHTML = html;
            })
            .catch(error => {
                console.error('Error al cargar reportes por fecha:', error);
                tablaHistorialReportes.innerHTML = '<tr><td colspan="5" class="text-center">Error al cargar reportes</td></tr>';
            });
    }

    // Función para mostrar notificaciones tipo toast
    function mostrarNotificacion(titulo, mensaje, tipo) {
        // Tipos: success, info, warning, error
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: tipo,
            title: titulo,
            text: mensaje,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'colored-toast',
                title: 'toast-title',
                content: 'toast-content'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    }

    // Mostrar alertas según parámetros URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success') && urlParams.get('success') === 'reporte_generado') {
        mostrarNotificacion('¡Éxito!', 'Reporte de cierre de caja generado correctamente.', 'success');
        // Recargar los reportes
        cargarReportesHoy();
        cargarHistorialReportes();
    } else if (urlParams.has('error')) {
        const error = urlParams.get('error');
        if (error === 'reporte_existente') {
            mostrarNotificacion('Reporte Existente', 'Ya existe un reporte de cierre para la fecha seleccionada.', 'warning');
        } else if (error === 'reporte_existente_misma_hora') {
            mostrarNotificacion('Reporte Existente', 'Ya existe un reporte de cierre para la fecha y hora seleccionada. Por favor, seleccione una hora diferente.', 'warning');
        }
    }
});
