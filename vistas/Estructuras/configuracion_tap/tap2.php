<!-- Incluir SweetAlert2 y Bootstrap si no están incluidos en el archivo principal -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>

<!-- Estilos personalizados para las notificaciones -->
<style>
    /* Asegurar que las notificaciones estén por encima de todo */
    .swal2-container, 
    .swal2-popup,
    .swal2-toast {
        z-index: 99999 !important;
    }
    
    /* Estilo visual para las notificaciones */
    .swal2-toast {
        padding: 12px 16px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
        border-radius: 8px !important;
        background-color: #ffffff !important;
        color: #333333 !important;
        font-size: 16px !important;
        max-width: 400px !important;
        margin: 12px !important;
        top: 0 !important;
    }
    
    /* Prevenir que el modal bloqueado interfiera con notificaciones */
    body.modal-open {
        overflow: auto !important;
        padding-right: 0 !important;
    }
    
    /* Animación para hacer más visible la notificación */
    @keyframes bounceIn {
        0% { opacity: 0; transform: scale(0.8); }
        50% { opacity: 1; transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .swal2-toast {
        animation: bounceIn 0.3s ease-out;
    }
</style>

<!-- Script para manejar notificaciones -->
<script>
    // Variable para evitar múltiples notificaciones simultáneas
    window.notificationActive = false;
    
    // Cerrar cualquier notificación previa que pueda haber quedado abierta
    window.addEventListener('load', function() {
        console.log('Comprobando SweetAlert2 al cargar la página');
        
        // Asegurarse de que SweetAlert2 está cargado
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 no está cargado correctamente');
            // Intentar cargar SweetAlert2 dinámicamente
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js';
            document.head.appendChild(script);
            script.onload = function() {
                console.log('SweetAlert2 cargado dinámicamente');
            };
        } else {
            // Cerrar cualquier SweetAlert2 abierto
            if (Swal.isVisible()) {
                console.log('Cerrando SweetAlert2 visible al cargar la página');
                Swal.close();
            }
        }
        
        // Limpiar cualquier modal-backdrop huérfano
        document.querySelectorAll('.modal-backdrop').forEach(elem => elem.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        console.log('Limpieza de interfaz completada al cargar la página');
    });

    // Asegurar que la función global esté disponible
    function mostrarNotificacion(titulo, mensaje, tipo) {
        console.log('📢 Mostrando notificación:', titulo, mensaje, tipo);
        window.notificationActive = true;
        
        // Verificar que Swal está definido
        if (typeof Swal === 'undefined') {
            console.error('⚠️ SweetAlert2 no está disponible. Mostrando alerta estándar');
            alert(`${titulo}: ${mensaje}`);
            window.notificationActive = false;
            return;
        }
        
        // Cerrar cualquier Swal previo para evitar superposiciones
        if (Swal.isVisible()) {
            console.log('Cerrando notificación previa');
            Swal.close();
        }
        
        // Crear un elemento div para la notificación que estará fuera del flujo DOM normal
        const notificationContainer = document.createElement('div');
        notificationContainer.className = 'custom-notification-container';
        document.body.appendChild(notificationContainer);
        
        // Aplicar estilos al contenedor
        Object.assign(notificationContainer.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            zIndex: '999999',
            maxWidth: '400px'
        });
        
        // Retraso breve para asegurar que cualquier modal previo se ha cerrado
        setTimeout(() => {
            Swal.fire({
                title: titulo,
                text: mensaje,
                icon: tipo,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                toast: true,
                target: notificationContainer,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                    
                    // Mover la notificación al frente
                    notificationContainer.style.zIndex = '999999';
                    
                    // Añadir transición
                    toast.style.transition = 'all 0.3s ease-in-out';
                },
                willClose: () => {
                    // Remover el contenedor cuando se cierra
                    setTimeout(() => {
                        if (notificationContainer && notificationContainer.parentNode) {
                            notificationContainer.parentNode.removeChild(notificationContainer);
                        }
                        window.notificationActive = false;
                    }, 100);
                }
            });
        }, 300);
    }
</script>

<!-- Botón de emergencia para mostrar una notificación de prueba -->
<div id="botonEmergencia" style="position: fixed; bottom: 10px; right: 10px; z-index: 9999; opacity: 0.7;">
    <button class="btn btn-sm btn-danger" onclick="limpiarInterfazEmergencia()" title="Si la interfaz se bloquea, haz clic aquí">
        <i class="fas fa-broom"></i>
    </button>
    <button class="btn btn-sm btn-info ms-2" onclick="probarNotificacion()" title="Probar notificación">
        <i class="fas fa-bell"></i>
    </button>
</div>

<script>
    // Función para probar una notificación
    function probarNotificacion() {
        mostrarNotificacion('Prueba de notificación', 'Esta es una notificación de prueba. Si puedes ver esto, las notificaciones funcionan correctamente.', 'success');
    }
    
    // Función global accesible desde cualquier parte
    function limpiarInterfazEmergencia() {
        console.log('Limpieza de emergencia iniciada');
        
        // 1. Cerrar cualquier SweetAlert
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
        
        // 2. Limpiar modales de Bootstrap
        document.body.classList.remove('modal-open');
        document.querySelectorAll('.modal-backdrop').forEach(elem => elem.remove());
        
        // 3. Ocultar todos los modales que puedan estar abiertos
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        });
        
        // 4. Eliminar estilos inline que bloqueen el scroll
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        // 5. Mostrar confirmación
        mostrarNotificacion('Interfaz limpiada', 'La interfaz ha sido limpiada exitosamente', 'success');
    }
</script>

<div id="tab2" class="tab-content d-none">
    <div class="card p-4 mb-4">
        <div class="ticket-summary">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Tarifas en bloques de tiempo</h4>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#agregarTarifaModal">
                    <i class="fas fa-plus me-2"></i> Agregar Nueva tarifa
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Tolerancia (min)</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="toleranciasTableBody">
                        <?php
                        // Consulta para obtener las tolerancias
                        $sql = "SELECT * FROM tolerancia ORDER BY tipo";
                        $result = $conexion->query($sql);
                        
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr data-tipo="' . htmlspecialchars($row['tipo']) . '" data-tolerancia="' . htmlspecialchars($row['tolerancia']) . '">';
                                echo '<td>' . htmlspecialchars($row['tipo']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['tolerancia']) . '</td>';
                                echo '<td>';
                                echo '<button class="btn btn-sm btn-outline-primary me-2 btn-editar-tolerancia" data-bs-toggle="modal" data-bs-target="#editarTarifaModal">';
                                echo '<i class="fas fa-edit me-1"></i> Editar';
                                echo '</button>';
                                echo '<button class="btn btn-sm btn-outline-danger btn-eliminar-tolerancia">';
                                echo '<i class="fas fa-trash-alt me-1"></i> Eliminar';
                                echo '</button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="3" class="text-center">No se encontraron configuraciones de tolerancia.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Modal Editar Tarifa -->
            <div class="modal fade" id="editarTarifaModal" tabindex="-1" aria-labelledby="editarTarifaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="editarTarifaModalLabel">Editar Tolerancia</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formEditarTolerancia">
                                <div class="mb-3">
                                    <label for="tipoToleranciaEdit" class="form-label">Tipo</label>
                                    <input type="text" class="form-control" id="tipoToleranciaEdit" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="toleranciaEdit" class="form-label">Tolerancia en minutos</label>
                                    <input type="number" class="form-control" id="toleranciaEdit" min="0" required>
                                    <div class="form-text">Ingrese un valor en minutos para el período de tolerancia.</div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btnGuardarEditarTolerancia">Guardar cambios</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Agregar Tarifa -->
            <div class="modal fade" id="agregarTarifaModal" tabindex="-1" aria-labelledby="agregarTarifaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="agregarTarifaModalLabel">Agregar Nueva Tolerancia</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formAgregarTolerancia">
                                <div class="mb-3">
                                    <label for="tipoToleranciaAdd" class="form-label">Tipo</label>
                                    <input type="text" class="form-control" id="tipoToleranciaAdd" required>
                                    <div class="form-text">Ejemplos: Hora, Día, Semana, Mes, etc.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="toleranciaAdd" class="form-label">Tolerancia en minutos</label>
                                    <input type="number" class="form-control" id="toleranciaAdd" min="0" value="0" required>
                                    <div class="form-text">Ingrese un valor en minutos para el período de tolerancia.</div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-success" id="btnGuardarAgregarTolerancia">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Confirmar Eliminación -->
            <div class="modal fade" id="eliminarTarifaModal" tabindex="-1" aria-labelledby="eliminarTarifaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="eliminarTarifaModalLabel">Confirmar Eliminación</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>¿Está seguro que desea eliminar el tipo de tolerancia <strong id="tipoToleranciaEliminar"></strong>?</p>
                            <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Esta acción no se puede deshacer.</p>
                            <input type="hidden" id="tipoToleranciaEliminarHidden">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger" id="btnConfirmarEliminarTolerancia">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para limpiar cualquier modal o SweetAlert que pueda haber quedado abierto
        const limpiarInterfaz = () => {
            // 1. Cerrar cualquier SweetAlert que pueda estar abierto
            if (typeof Swal !== 'undefined' && Swal.isVisible()) {
                Swal.close();
            }
            
            // 2. Limpiar cualquier modal de Bootstrap
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(elem => elem.remove());
            document.querySelectorAll('.modal.show').forEach(modal => {
                try {
                    const instance = bootstrap.Modal.getInstance(modal);
                    if (instance) instance.hide();
                } catch (e) {
                    console.error('Error al cerrar modal:', e);
                    // Forzar la limpieza
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    modal.setAttribute('aria-hidden', 'true');
                }
            });
        };
        
        // Ejecutar una limpieza inicial para asegurarnos de que la interfaz está limpia
        limpiarInterfaz();
        
        // Manejar evento click en botones de editar
        document.querySelectorAll('.btn-editar-tolerancia').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const tipo = row.dataset.tipo;
                const tolerancia = row.dataset.tolerancia;
                
                document.getElementById('tipoToleranciaEdit').value = tipo;
                document.getElementById('toleranciaEdit').value = tolerancia;
            });
        });
        
        // Manejar evento click en botones de eliminar
        document.querySelectorAll('.btn-eliminar-tolerancia').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const tipo = row.dataset.tipo;
                
                // Mostrar el modal de confirmación
                document.getElementById('tipoToleranciaEliminar').textContent = tipo;
                document.getElementById('tipoToleranciaEliminarHidden').value = tipo;
                
                // Abrir el modal de confirmación
                const eliminarModal = new bootstrap.Modal(document.getElementById('eliminarTarifaModal'));
                eliminarModal.show();
            });
        });
        
        // Manejar evento click en el botón de confirmar eliminación
        document.getElementById('btnConfirmarEliminarTolerancia').addEventListener('click', function() {
            const tipo = document.getElementById('tipoToleranciaEliminarHidden').value;
            
            // Mostrar indicador de carga
            const loadingSwal = Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Preparar los datos para enviar
            const formData = new FormData();
            formData.append('tipo', tipo);
            
            // Enviar la solicitud al servidor
            fetch('../../controladores/eliminar_tolerancia.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    return JSON.parse(text);
                } catch (error) {
                    throw new Error('La respuesta no es un JSON válido: ' + text);
                }
            })
            .then(data => {
                // Cerrar el SweetAlert de carga
                loadingSwal.close();
                
                // Cerrar el modal con seguridad
                try {
                    const modalElement = document.getElementById('eliminarTarifaModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                        
                        // Asegurarse de que el modal se oculta completamente
                        setTimeout(() => {
                            document.body.classList.remove('modal-open');
                            const backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) backdrop.remove();
                            
                            // Mostrar notificación después de que todo esté limpio
                            if (data.success) {
                                // Mostrar notificación de éxito
                                window.mostrarNotificacion('¡Eliminado!', data.message || 'Tipo de tolerancia eliminado correctamente', 'success');
                                
                                // Eliminar la fila de la tabla
                                const rows = document.querySelectorAll('#toleranciasTableBody tr');
                                for (const row of rows) {
                                    if (row.dataset.tipo === tipo) {
                                        row.remove();
                                        break;
                                    }
                                }
                                
                                // Si no quedan filas, mostrar mensaje de "No se encontraron"
                                const remainingRows = document.querySelectorAll('#toleranciasTableBody tr');
                                if (remainingRows.length === 0) {
                                    const tbody = document.getElementById('toleranciasTableBody');
                                    tbody.innerHTML = '<tr><td colspan="3" class="text-center">No se encontraron configuraciones de tolerancia.</td></tr>';
                                }
                            } else {
                                // Mostrar notificación de error
                                window.mostrarNotificacion('Error', data.error || 'No se pudo eliminar el tipo de tolerancia', 'error');
                            }
                        }, 300);
                    } else {
                        // No se pudo obtener la instancia del modal, mostrar notificación directamente
                        if (data.success) {
                            window.mostrarNotificacion('¡Eliminado!', data.message || 'Tipo de tolerancia eliminado correctamente', 'success');
                            
                            // Actualizar UI
                            const rows = document.querySelectorAll('#toleranciasTableBody tr');
                            for (const row of rows) {
                                if (row.dataset.tipo === tipo) {
                                    row.remove();
                                    break;
                                }
                            }
                            
                            // Si no quedan filas, mostrar mensaje de "No se encontraron"
                            const remainingRows = document.querySelectorAll('#toleranciasTableBody tr');
                            if (remainingRows.length === 0) {
                                const tbody = document.getElementById('toleranciasTableBody');
                                tbody.innerHTML = '<tr><td colspan="3" class="text-center">No se encontraron configuraciones de tolerancia.</td></tr>';
                            }
                        } else {
                            window.mostrarNotificacion('Error', data.error || 'No se pudo eliminar el tipo de tolerancia', 'error');
                        }
                    }
                } catch (e) {
                    console.error('Error al cerrar el modal:', e);
                    // Si hay error cerrando el modal, asegurarse de limpiar manualmente
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                    
                    // Mostrar notificación incluso si hubo error al cerrar el modal
                    if (data.success) {
                        window.mostrarNotificacion('¡Eliminado!', data.message || 'Tipo de tolerancia eliminado correctamente', 'success');
                        
                        // Actualizar UI
                        const rows = document.querySelectorAll('#toleranciasTableBody tr');
                        for (const row of rows) {
                            if (row.dataset.tipo === tipo) {
                                row.remove();
                                break;
                            }
                        }
                        
                        // Si no quedan filas, mostrar mensaje de "No se encontraron"
                        const remainingRows = document.querySelectorAll('#toleranciasTableBody tr');
                        if (remainingRows.length === 0) {
                            const tbody = document.getElementById('toleranciasTableBody');
                            tbody.innerHTML = '<tr><td colspan="3" class="text-center">No se encontraron configuraciones de tolerancia.</td></tr>';
                        }
                    } else {
                        window.mostrarNotificacion('Error', data.error || 'No se pudo eliminar el tipo de tolerancia', 'error');
                    }
                }
            })
            .catch(error => {
                // Cerrar el SweetAlert de carga en caso de error
                loadingSwal.close();
                
                // Asegurarse de que el modal está cerrado
                try {
                    const modalElement = document.getElementById('eliminarTarifaModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                } catch (e) {
                    console.error('Error al cerrar el modal:', e);
                }
                
                // Limpiar manualmente
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
                
                console.error('Error:', error);
                window.mostrarNotificacion('Error', 'Ha ocurrido un error al intentar eliminar el tipo de tolerancia', 'error');
            });
        });
        
        // Guardar cambios de edición de tolerancia
        document.getElementById('btnGuardarEditarTolerancia').addEventListener('click', function() {
            const tipo = document.getElementById('tipoToleranciaEdit').value;
            const tolerancia = document.getElementById('toleranciaEdit').value;
            
            // Validar que la tolerancia sea un número positivo
            if (!tolerancia || isNaN(tolerancia) || parseInt(tolerancia) < 0) {
                window.mostrarNotificacion('Error', 'La tolerancia debe ser un número entero positivo', 'error');
                return;
            }
            
            // Mostrar indicador de carga
            const loadingSwal = Swal.fire({
                title: 'Actualizando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Preparar los datos para enviar
            const formData = new FormData();
            formData.append('tipo', tipo);
            formData.append('tolerancia', tolerancia);
            
            // Enviar la solicitud al servidor
            fetch('../../controladores/editar_tolerancia.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    return JSON.parse(text);
                } catch (error) {
                    throw new Error('La respuesta no es un JSON válido: ' + text);
                }
            })
            .then(data => {
                // Cerrar el SweetAlert de carga
                loadingSwal.close();
                
                // Cerrar el modal con seguridad
                try {
                    const modalElement = document.getElementById('editarTarifaModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                        
                        // Asegurarse de que el modal se oculta completamente
                        setTimeout(() => {
                            document.body.classList.remove('modal-open');
                            const backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) backdrop.remove();
                            
                            // Mostrar notificación después de que todo esté limpio
                            if (data.success) {
                                // Actualizar la fila en la tabla
                                const rows = document.querySelectorAll('#toleranciasTableBody tr');
                                for (const row of rows) {
                                    if (row.dataset.tipo === tipo) {
                                        row.dataset.tolerancia = tolerancia;
                                        row.cells[1].textContent = tolerancia;
                                        break;
                                    }
                                }
                                
                                // Mostrar notificación de éxito
                                window.mostrarNotificacion('¡Actualizado!', data.message || 'Tolerancia actualizada correctamente', 'success');
                            } else {
                                // Mostrar notificación de error
                                window.mostrarNotificacion('Error', data.error || 'No se pudo actualizar la tolerancia', 'error');
                            }
                        }, 300);
                    } else {
                        // No se pudo obtener la instancia del modal, mostrar notificación directamente
                        if (data.success) {
                            // Actualizar la fila en la tabla
                            const rows = document.querySelectorAll('#toleranciasTableBody tr');
                            for (const row of rows) {
                                if (row.dataset.tipo === tipo) {
                                    row.dataset.tolerancia = tolerancia;
                                    row.cells[1].textContent = tolerancia;
                                    break;
                                }
                            }
                            
                            window.mostrarNotificacion('¡Actualizado!', data.message || 'Tolerancia actualizada correctamente', 'success');
                        } else {
                            window.mostrarNotificacion('Error', data.error || 'No se pudo actualizar la tolerancia', 'error');
                        }
                    }
                } catch (e) {
                    console.error('Error al cerrar el modal:', e);
                    // Si hay error cerrando el modal, asegurarse de limpiar manualmente
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                    
                    // Mostrar notificación incluso si hubo error al cerrar el modal
                    if (data.success) {
                        // Actualizar la fila en la tabla
                        const rows = document.querySelectorAll('#toleranciasTableBody tr');
                        for (const row of rows) {
                            if (row.dataset.tipo === tipo) {
                                row.dataset.tolerancia = tolerancia;
                                row.cells[1].textContent = tolerancia;
                                break;
                            }
                        }
                        
                        window.mostrarNotificacion('¡Actualizado!', data.message || 'Tolerancia actualizada correctamente', 'success');
                    } else {
                        window.mostrarNotificacion('Error', data.error || 'No se pudo actualizar la tolerancia', 'error');
                    }
                }
            })
            .catch(error => {
                // Cerrar el SweetAlert de carga en caso de error
                loadingSwal.close();
                
                // Asegurarse de que el modal está cerrado
                try {
                    const modalElement = document.getElementById('editarTarifaModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                } catch (e) {
                    console.error('Error al cerrar el modal:', e);
                }
                
                // Limpiar manualmente
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
                
                console.error('Error:', error);
                window.mostrarNotificacion('Error', 'Ha ocurrido un error al intentar actualizar la tolerancia', 'error');
            });
        });
        
        // Guardar nueva tolerancia
        document.getElementById('btnGuardarAgregarTolerancia').addEventListener('click', function() {
            const tipo = document.getElementById('tipoToleranciaAdd').value.trim();
            const tolerancia = document.getElementById('toleranciaAdd').value;
            
            // Validar los campos
            if (!tipo) {
                window.mostrarNotificacion('Error', 'El tipo de tolerancia no puede estar vacío', 'error');
                return;
            }
            
            if (!tolerancia || isNaN(tolerancia) || parseInt(tolerancia) < 0) {
                window.mostrarNotificacion('Error', 'La tolerancia debe ser un número entero positivo', 'error');
                return;
            }
            
            // Mostrar indicador de carga
            const loadingSwal = Swal.fire({
                title: 'Guardando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Preparar los datos para enviar
            const formData = new FormData();
            formData.append('tipo', tipo);
            formData.append('tolerancia', tolerancia);
            
            // Enviar la solicitud al servidor
            fetch('../../controladores/agregar_tolerancia.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    return JSON.parse(text);
                } catch (error) {
                    throw new Error('La respuesta no es un JSON válido: ' + text);
                }
            })
            .then(data => {
                // Cerrar el SweetAlert de carga
                loadingSwal.close();
                
                // Cerrar el modal con seguridad
                try {
                    const modalElement = document.getElementById('agregarTarifaModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                        
                        // Asegurarse de que el modal se oculta completamente
                        setTimeout(() => {
                            document.body.classList.remove('modal-open');
                            const backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) backdrop.remove();
                            
                            // Mostrar notificación después de que todo esté limpio
                            if (data.success) {
                                // Mostrar notificación de éxito
                                window.mostrarNotificacion('¡Guardado!', data.message || 'Tolerancia agregada correctamente', 'success');
                                
                                // Recargar la página después de un breve retraso
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                // Mostrar notificación de error
                                window.mostrarNotificacion('Error', data.error || 'No se pudo agregar la tolerancia', 'error');
                            }
                        }, 300);
                    } else {
                        // No se pudo obtener la instancia del modal, mostrar notificación directamente
                        if (data.success) {
                            window.mostrarNotificacion('¡Guardado!', data.message || 'Tolerancia agregada correctamente', 'success');
                            // Recargar la página después de un breve retraso
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            window.mostrarNotificacion('Error', data.error || 'No se pudo agregar la tolerancia', 'error');
                        }
                    }
                } catch (e) {
                    console.error('Error al cerrar el modal:', e);
                    // Si hay error cerrando el modal, asegurarse de limpiar manualmente
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                    
                    // Mostrar notificación incluso si hubo error al cerrar el modal
                    if (data.success) {
                        window.mostrarNotificacion('¡Guardado!', data.message || 'Tolerancia agregada correctamente', 'success');
                        // Recargar la página después de un breve retraso
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        window.mostrarNotificacion('Error', data.error || 'No se pudo agregar la tolerancia', 'error');
                    }
                }
            })
            .catch(error => {
                // Cerrar el SweetAlert de carga en caso de error
                loadingSwal.close();
                
                // Asegurarse de que el modal está cerrado
                try {
                    const modalElement = document.getElementById('agregarTarifaModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                } catch (e) {
                    console.error('Error al cerrar el modal:', e);
                }
                
                // Limpiar manualmente
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
                
                console.error('Error:', error);
                window.mostrarNotificacion('Error', 'Ha ocurrido un error al intentar agregar la tolerancia', 'error');
            });
        });
    });
</script>