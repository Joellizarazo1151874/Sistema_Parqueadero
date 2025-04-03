<div id="tab3" class="tab-content d-none">
    <h3>Precios</h3>
    <p>Establece los precios para cada categoría de vehículo en cada tipo de tarifa</p>

    <!-- Alerta para notificaciones -->
    <div id="alertTarifas" class="alert alert-success d-none" role="alert">
        Los cambios se han guardado correctamente.
    </div>

    <div class="card p-4 mb-4">
        <div class="ticket-summary">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Tarifas</h5>
                <button id="btnGuardarTodos" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Guardar cambios
                </button>
            </div>
            <div class="table-responsive">
                <table id="tablaTarifas" class="table table-hover">
                    <thead class="table-light">
                        <tr id="tarifasHeader">
                            <th class="highlight-column">Vehículo / Tarifa</th>
                            <!-- Los tipos de tarifas se cargarán dinámicamente -->
                        </tr>
                    </thead>
                    <tbody id="tarifasBody">
                        <!-- Las filas se cargarán dinámicamente -->
                        <tr>
                            <td colspan="100%" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando tarifas...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script específico para tap3 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const tablaTarifas = document.getElementById('tablaTarifas');
    const tarifasHeader = document.getElementById('tarifasHeader');
    const tarifasBody = document.getElementById('tarifasBody');
    const btnGuardarTodos = document.getElementById('btnGuardarTodos');
    const alertTarifas = document.getElementById('alertTarifas');
    
    // Función para mostrar alertas
    function mostrarAlerta(mensaje, tipo) {
        alertTarifas.textContent = mensaje;
        alertTarifas.className = `alert alert-${tipo}`;
        alertTarifas.classList.remove('d-none');
        
        // Ocultar después de 3 segundos
        setTimeout(() => {
            alertTarifas.classList.add('d-none');
        }, 3000);
    }
    
    // Función para cargar todas las tarifas
    function cargarTarifas() {
        // Mostrar indicador de carga
        tarifasBody.innerHTML = `
            <tr>
                <td colspan="100%" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Verificando estructura de base de datos...</p>
                </td>
            </tr>
        `;
        
        // Primero verificar la estructura de la tabla
        fetch('../../controladores/verificar_estructura_tarifas.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Estructura de tabla verificada:', data.message);
                    // Ahora cargar las tarifas
                    obtenerTarifas();
                } else {
                    tarifasBody.innerHTML = `
                        <tr>
                            <td colspan="100%" class="text-center text-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                ${data.error || 'Error al verificar la estructura de la tabla.'}
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tarifasBody.innerHTML = `
                    <tr>
                        <td colspan="100%" class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error de conexión. Por favor intente nuevamente.
                        </td>
                    </tr>
                `;
            });
    }
    
    // Función para obtener las tarifas después de verificar la estructura
    function obtenerTarifas() {
        tarifasBody.innerHTML = `
            <tr>
                <td colspan="100%" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando tarifas...</p>
                </td>
            </tr>
        `;
        
        fetch('../../controladores/obtener_tarifas.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Construir encabezados de columna
                    let headerHTML = '<th class="highlight-column">Vehículo / Tarifa</th>';
                    for (const tipoTarifa of data.tipos_tarifa) {
                        headerHTML += `<th class="highlight-column">${tipoTarifa.toUpperCase()}</th>`;
                    }
                    tarifasHeader.innerHTML = headerHTML;
                    
                    // Construir filas de la tabla
                    let bodyHTML = '';
                    for (const tipoVehiculo of data.tipos_vehiculo) {
                        bodyHTML += `<tr data-tipo-vehiculo="${tipoVehiculo}">`;
                        bodyHTML += `<td class="highlight-column">${tipoVehiculo}</td>`;
                        
                        // Para cada tipo de tarifa, agregar un input para el valor
                        for (const tipoTarifa of data.tipos_tarifa) {
                            const valor = data.tarifas[tipoVehiculo][tipoTarifa.toLowerCase()] || 0;
                            bodyHTML += `
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                            class="form-control input-tarifa" 
                                            data-tipo-tarifa="${tipoTarifa}" 
                                            value="${valor}"
                                            min="0" 
                                            step="100">
                                    </div>
                                </td>
                            `;
                        }
                        
                        bodyHTML += '</tr>';
                    }
                    
                    // Si no hay tipos de vehículo, mostrar mensaje
                    if (data.tipos_vehiculo.length === 0) {
                        bodyHTML = `
                            <tr>
                                <td colspan="100%" class="text-center">
                                    No hay categorías de vehículos configuradas. 
                                    Primero debe agregar categorías en la sección de Categorías.
                                </td>
                            </tr>
                        `;
                    }
                    // Si no hay tipos de tarifa, mostrar mensaje
                    else if (data.tipos_tarifa.length === 0) {
                        bodyHTML = `
                            <tr>
                                <td colspan="100%" class="text-center">
                                    No hay tipos de tarifa configurados.
                                    Primero debe agregar tolerancias en la sección de Tolerancias.
                                </td>
                            </tr>
                        `;
                    }
                    
                    tarifasBody.innerHTML = bodyHTML;
                    
                    // Agregar eventos para guardar al salir del input
                    document.querySelectorAll('.input-tarifa').forEach(input => {
                        input.addEventListener('change', function() {
                            const tipoVehiculo = this.closest('tr').dataset.tipoVehiculo;
                            const tipoTarifa = this.dataset.tipoTarifa;
                            const valor = this.value;
                            
                            actualizarTarifa(tipoVehiculo, tipoTarifa, valor, false);
                        });
                    });
                    
                } else {
                    // Mostrar error
                    tarifasBody.innerHTML = `
                        <tr>
                            <td colspan="100%" class="text-center text-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                ${data.error || 'Error al cargar las tarifas'}
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tarifasBody.innerHTML = `
                    <tr>
                        <td colspan="100%" class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error de conexión. Por favor intente nuevamente.
                        </td>
                    </tr>
                `;
            });
    }
    
    // Función para actualizar una tarifa específica
    function actualizarTarifa(tipoVehiculo, tipoTarifa, valor, mostrarNotificacion = true) {
        // Validar valor
        if (valor < 0) {
            mostrarAlerta('El valor de la tarifa debe ser un número positivo', 'danger');
            return;
        }
        
        // Preparar formulario para enviar
        const formData = new FormData();
        formData.append('tipo_vehiculo', tipoVehiculo);
        formData.append('tipo_tarifa', tipoTarifa);
        formData.append('valor', valor);
        
        // Enviar solicitud al servidor
        fetch('../../controladores/actualizar_tarifa.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && mostrarNotificacion) {
                mostrarAlerta(data.message || 'Tarifa actualizada correctamente', 'success');
            } else if (!data.success) {
                mostrarAlerta(data.error || 'Error al actualizar la tarifa', 'danger');
                // Recargar para deshacer cambios
                cargarTarifas();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error de conexión. Por favor intente nuevamente.', 'danger');
            // Recargar para deshacer cambios
            cargarTarifas();
        });
    }
    
    // Evento para guardar todos los cambios
    btnGuardarTodos.addEventListener('click', function() {
        // Mostrar indicador de carga
        btnGuardarTodos.disabled = true;
        btnGuardarTodos.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
        
        // Obtener todas las filas
        const filas = tarifasBody.querySelectorAll('tr[data-tipo-vehiculo]');
        let actualizacionesCompletadas = 0;
        let totalActualizaciones = 0;
        
        // Para cada fila, obtener sus inputs y actualizar
        filas.forEach(fila => {
            const tipoVehiculo = fila.dataset.tipoVehiculo;
            const inputs = fila.querySelectorAll('.input-tarifa');
            
            // Actualizar cada input
            inputs.forEach(input => {
                const tipoTarifa = input.dataset.tipoTarifa;
                const valor = input.value;
                totalActualizaciones++;
                
                // Crear formulario y enviar
                const formData = new FormData();
                formData.append('tipo_vehiculo', tipoVehiculo);
                formData.append('tipo_tarifa', tipoTarifa);
                formData.append('valor', valor);
                
                fetch('../../controladores/actualizar_tarifa.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    actualizacionesCompletadas++;
                    
                    // Cuando todas las actualizaciones estén completas
                    if (actualizacionesCompletadas === totalActualizaciones) {
                        btnGuardarTodos.disabled = false;
                        btnGuardarTodos.innerHTML = '<i class="fas fa-save me-2"></i> Guardar cambios';
                        mostrarAlerta('Todas las tarifas han sido actualizadas correctamente', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    actualizacionesCompletadas++;
                    
                    // Cuando todas las actualizaciones estén completas
                    if (actualizacionesCompletadas === totalActualizaciones) {
                        btnGuardarTodos.disabled = false;
                        btnGuardarTodos.innerHTML = '<i class="fas fa-save me-2"></i> Guardar cambios';
                        mostrarAlerta('Hubo errores al guardar algunas tarifas', 'warning');
                        cargarTarifas(); // Recargar para mostrar los valores correctos
                    }
                });
            });
        });
        
        // Si no hay actualizaciones que hacer
        if (totalActualizaciones === 0) {
            btnGuardarTodos.disabled = false;
            btnGuardarTodos.innerHTML = '<i class="fas fa-save me-2"></i> Guardar cambios';
            mostrarAlerta('No hay tarifas para actualizar', 'info');
        }
    });
    
    // Cargar tarifas al iniciar
    cargarTarifas();
    
    // Agregar soporte para notificaciones SweetAlert2 si está disponible
    if (typeof window.mostrarNotificacion === 'function') {
        // Reemplazar la función de mostrar alerta por SweetAlert2
        mostrarAlerta = function(mensaje, tipo) {
            window.mostrarNotificacion(
                tipo === 'success' ? '¡Éxito!' : tipo === 'danger' ? 'Error' : 'Atención',
                mensaje,
                tipo === 'danger' ? 'error' : tipo
            );
        };
    }
});
</script>