document.addEventListener('DOMContentLoaded', function() {
    // Verificar si estamos en la pestaña de cierre de caja
    const tabCierreCaja = document.getElementById('tab3');
    if (!tabCierreCaja) return;

    // Elementos del DOM
    const formCierreCaja = document.getElementById('formCierreCaja');
    const btnBuscarReportes = document.getElementById('btnBuscarReportes');
    const btnLimpiarBusqueda = document.getElementById('btnLimpiarBusqueda');
    const fechaBusqueda = document.getElementById('fecha_busqueda');
    const tablaReportesHoy = document.getElementById('tablaReportesHoy');
    const tablaHistorialReportes = document.getElementById('tablaHistorialReportes');
    
    // Elementos para el filtro del historial de reportes
    const fechaFiltroHistorial = document.getElementById('fecha_filtro_historial');
    const btnFiltrarHistorial = document.getElementById('btnFiltrarHistorial');
    const btnLimpiarFiltroHistorial = document.getElementById('btnLimpiarFiltroHistorial');

    // Establecer la fecha actual como valor predeterminado
    if (fechaBusqueda) {
        const hoy = new Date();
        const formatoFecha = hoy.toISOString().split('T')[0];
        fechaBusqueda.value = formatoFecha;
    }
    
    // Establecer la fecha actual como valor predeterminado para el filtro del historial
    if (fechaFiltroHistorial) {
        const hoy = new Date();
        const formatoFecha = hoy.toISOString().split('T')[0];
        fechaFiltroHistorial.value = formatoFecha;
    }

    // Cargar reportes generados hoy
    cargarReportesHoy();

    // Cargar historial de reportes
    cargarHistorialReportes();

    // Evento para buscar reportes por fecha
    if (btnBuscarReportes) {
        btnBuscarReportes.addEventListener('click', function() {
            buscarReportesPorFecha();
        });
    }

    // Evento para limpiar la búsqueda
    if (btnLimpiarBusqueda) {
        btnLimpiarBusqueda.addEventListener('click', function() {
            limpiarBusqueda();
        });
    }

    // Evento para buscar al presionar Enter en el campo de fecha
    if (fechaBusqueda) {
        fechaBusqueda.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarReportesPorFecha();
            }
        });
    }
    
    // Eventos para el filtro del historial de reportes
    if (btnFiltrarHistorial) {
        btnFiltrarHistorial.addEventListener('click', function() {
            filtrarHistorialReportes();
        });
    }
    
    if (btnLimpiarFiltroHistorial) {
        btnLimpiarFiltroHistorial.addEventListener('click', function() {
            limpiarFiltroHistorial();
        });
    }
    
    if (fechaFiltroHistorial) {
        fechaFiltroHistorial.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filtrarHistorialReportes();
            }
        });
    }
    
    // Función para filtrar el historial de reportes por fecha
    function filtrarHistorialReportes() {
        const fecha = fechaFiltroHistorial.value;
        if (fecha) {
            cargarHistorialReportesPorFecha(fecha);
            // Mostrar el botón de limpiar cuando se realiza una búsqueda
            if (btnLimpiarFiltroHistorial) {
                btnLimpiarFiltroHistorial.style.display = 'inline-block';
            }
        } else {
            mostrarNotificacion('Fecha requerida', 'Por favor, seleccione una fecha para filtrar', 'warning');
        }
    }
    
    // Función para limpiar el filtro del historial y volver a cargar todos los reportes
    function limpiarFiltroHistorial() {
        if (fechaFiltroHistorial) {
            // Establecer la fecha actual
            const hoy = new Date().toISOString().split('T')[0];
            fechaFiltroHistorial.value = hoy;
        }
        
        // Ocultar el botón de limpiar
        if (btnLimpiarFiltroHistorial) {
            btnLimpiarFiltroHistorial.style.display = 'none';
        }
        
        // Cargar el historial de reportes
        cargarHistorialReportes();
        mostrarNotificacion('Filtro limpiado', 'Se ha restablecido la búsqueda del historial', 'info');
    }

    // Función para validar y buscar reportes por fecha
    function buscarReportesPorFecha() {
        const fecha = fechaBusqueda.value;
        if (fecha) {
            cargarReportesPorFecha(fecha);
            // Mostrar el botón de limpiar cuando se realiza una búsqueda
            if (btnLimpiarBusqueda) {
                btnLimpiarBusqueda.style.display = 'block';
            }
        }
    }

    // Función para limpiar la búsqueda y volver a cargar todos los reportes
    function limpiarBusqueda() {
        if (fechaBusqueda) {
            // Establecer la fecha actual
            const hoy = new Date().toISOString().split('T')[0];
            fechaBusqueda.value = hoy;
        }
        
        // Ocultar el botón de limpiar
        if (btnLimpiarBusqueda) {
            btnLimpiarBusqueda.style.display = 'none';
        }
        
        // Cargar el historial de reportes
        cargarHistorialReportes();
        mostrarNotificacion('Filtro limpiado', 'Se ha restablecido la búsqueda', 'info');
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
                
                // Mostrar información de depuración en la consola
                if (data.debug) {
                    console.log('Información de depuración (reportes hoy):', data.debug);
                }
                
                const reportes = data.reportes || data; // Compatibilidad con ambos formatos
                
                if (reportes.length === 0) {
                    tablaReportesHoy.innerHTML = '<tr><td colspan="4" class="text-center">No hay reportes generados hoy</td></tr>';
                    return;
                }

                let html = '';
                reportes.forEach(reporte => {
                    html += `
                        <tr>
                            <td>${reporte.fecha}</td>
                            <td>${reporte.hora}</td>
                            <td>${reporte.operador}</td>
                            <td>
                                ${reporte.html_existe ? 
                                    `<div class="d-flex gap-2">
                                        <a href="../../${reporte.ruta_html}" target="_blank" class="btn btn-sm btn-info" style="width: 38px; height: 31px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-eye"></i> 
                                        </a> 
                                        <a href="../../controladores/descargar_reporte_pdf.php?id=${reporte.id_reporte}" target="_blank" class="btn btn-sm btn-danger" style="width: 38px; height: 31px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-file-pdf"></i>
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
        if (!tablaHistorialReportes) return;
        
        fetch('../../controladores/obtener_reportes_caja.php?tipo=historial')
            .then(response => response.json())
            .then(data => {
                if (!data.reportes || data.reportes.length === 0) {
                    tablaHistorialReportes.innerHTML = '<tr><td colspan="5" class="text-center">No hay reportes históricos disponibles</td></tr>';
                    return;
                }
                
                let html = '';
                data.reportes.forEach(reporte => {
                    html += `
                        <tr>
                            <td>${reporte.fecha}</td>
                            <td>${reporte.hora}</td>
                            <td>$${Number(reporte.total_recaudado).toLocaleString('es-CO')}</td>
                            <td>${reporte.operador}</td>
                            <td>
                                ${reporte.html_existe ? 
                                    `<div class="d-flex gap-2">
                                        <a href="../../${reporte.ruta_html}" target="_blank" class="btn btn-sm btn-info" style="width: 38px; height: 31px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="../../controladores/descargar_reporte_pdf.php?id=${reporte.id_reporte}" target="_blank" class="btn btn-sm btn-danger" style="width: 38px; height: 31px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-file-pdf"></i>
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
                tablaHistorialReportes.innerHTML = '<tr><td colspan="5" class="text-center">Error al cargar reportes históricos</td></tr>';
            });
    }
    
    // Función para cargar el historial de reportes por fecha específica
    function cargarHistorialReportesPorFecha(fecha) {
        if (!tablaHistorialReportes) return;
        
        fetch(`../../controladores/obtener_reportes_caja.php?tipo=fecha&fecha=${fecha}`)
            .then(response => response.json())
            .then(data => {
                if (!data.reportes || data.reportes.length === 0) {
                    tablaHistorialReportes.innerHTML = '<tr><td colspan="5" class="text-center">No hay reportes para la fecha seleccionada</td></tr>';
                    mostrarNotificacion('Sin resultados', `No se encontraron reportes para la fecha ${fecha}`, 'info');
                    return;
                }
                
                let html = '';
                data.reportes.forEach(reporte => {
                    html += `
                        <tr>
                            <td>${reporte.fecha}</td>
                            <td>${reporte.hora}</td>
                            <td>$${Number(reporte.total_recaudado).toLocaleString('es-CO')}</td>
                            <td>${reporte.operador}</td>
                            <td>
                                ${reporte.html_existe ? 
                                    `<div class="d-flex gap-2">
                                        <a href="../../${reporte.ruta_html}" target="_blank" class="btn btn-sm btn-info" style="width: 38px; height: 31px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="../../controladores/descargar_reporte_pdf.php?id=${reporte.id_reporte}" target="_blank" class="btn btn-sm btn-danger" style="width: 38px; height: 31px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>` : 
                                    '<span class="badge bg-danger">Reporte no disponible</span>'}
                            </td>
                        </tr>
                    `;
                });
                
                tablaHistorialReportes.innerHTML = html;
                mostrarNotificacion('Búsqueda completada', `Se encontraron reportes para la fecha ${fecha}`, 'success');
            })
            .catch(error => {
                console.error('Error al cargar reportes por fecha:', error);
                tablaHistorialReportes.innerHTML = '<tr><td colspan="5" class="text-center">Error al cargar reportes</td></tr>';
                mostrarNotificacion('Error', 'Ocurrió un error al buscar los reportes', 'error');
            });
    }

    // Función para cargar reportes por fecha específica
    function cargarReportesPorFecha(fecha) {
        // Mostrar indicador de carga
        tablaHistorialReportes.innerHTML = '<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>';
        
        // Depuración: Mostrar la fecha que se está enviando
        console.log('Enviando búsqueda para la fecha:', fecha);
        
        fetch(`../../controladores/obtener_reportes_caja.php?tipo=fecha&fecha=${fecha}`)
            .then(response => {
                // Depuración: Verificar el estado de la respuesta
                console.log('Estado de la respuesta:', response.status);
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    mostrarNotificacion('Error', data.error, 'error');
                    return;
                }
                
                // Mostrar información de depuración en la consola
                if (data.debug) {
                    console.log('Información de depuración (fecha):', data.debug);
                }
                
                const reportes = data.reportes || data; // Compatibilidad con ambos formatos
                
                // Depuración: Mostrar los reportes recibidos
                console.log('Reportes recibidos:', reportes);
                
                if (reportes.length === 0) {
                    tablaHistorialReportes.innerHTML = '<tr><td colspan="5" class="text-center">No hay reportes para la fecha seleccionada</td></tr>';
                    mostrarNotificacion('Sin resultados', `No se encontraron reportes para la fecha ${fecha}`, 'info');
                    return;
                }

                let html = '';
                reportes.forEach(reporte => {
                    html += `
                        <tr>
                            <td>${reporte.fecha}</td>
                            <td>${reporte.hora}</td>
                            <td>$${Number(reporte.total_recaudado).toLocaleString('es-CO')}</td>
                            <td>${reporte.operador}</td>
                            <td>
                                ${reporte.html_existe ? 
                                    `<div class="d-flex gap-2">
                                        <a href="../../${reporte.ruta_html}" target="_blank" class="btn btn-sm btn-info" style="width: 38px; height: 31px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="../../controladores/descargar_reporte_pdf.php?id=${reporte.id_reporte}" target="_blank" class="btn btn-sm btn-danger" style="width: 38px; height: 31px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>` : 
                                    '<span class="badge bg-danger">Reporte no disponible</span>'}
                            </td>
                        </tr>
                    `;
                });
                
                tablaHistorialReportes.innerHTML = html;
                mostrarNotificacion('Búsqueda completada', `Se encontraron reportes para la fecha ${fecha}`, 'success');
            })
            .catch(error => {
                console.error('Error al cargar reportes por fecha:', error);
                tablaHistorialReportes.innerHTML = '<tr><td colspan="5" class="text-center">Error al cargar reportes</td></tr>';
                mostrarNotificacion('Error', 'Ocurrió un error al buscar los reportes', 'error');
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
