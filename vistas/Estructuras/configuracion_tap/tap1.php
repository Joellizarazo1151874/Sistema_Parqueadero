
<div id="tab1" class="tab-content active">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <?php
                    if ($result && $result->num_rows > 0) {
                        echo '<h3 class="text-center mb-4">Categorías de Vehículos</h3>';
                        echo '<div class="list-group mb-4" id="categoriasList">';
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="seleccionarCategoria(this)" data-categoria="' . htmlspecialchars($row['tipo_vehiculo']) . '">';
                            echo '<span>' . htmlspecialchars($row['tipo_vehiculo']) . '</span>';
                            echo '<div class="badge bg-light text-dark rounded-pill">Seleccionar</div>';
                            echo '</div>';
                        }
                        echo '</div>';
                    } else {
                        echo '<div class="alert alert-info" role="alert">
                                <h4 class="alert-heading">No hay categorías</h4>
                                <p>No se encontraron categorías de vehículos en la base de datos.</p>
                                <hr>
                                <p class="mb-0">Puedes agregar una nueva categoría utilizando el botón "Nueva Categoría".</p>
                              </div>';
                    }
                    ?>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <button class="btn btn-outline-danger" id="btnEliminar" disabled>
                            Eliminar
                        </button>
                        <button class="btn btn-outline-primary" id="btnModificar" disabled>
                            Modificar
                        </button>
                        <button class="btn btn-primary" id="btnNueva" data-bs-toggle="modal" data-bs-target="#nuevaCategoriaModal">
                            Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Eliminar Categoría -->
    <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="eliminarModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Eliminar Categoría
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-trash-alt fa-4x text-danger mb-3"></i>
                        <h4>¿Está seguro de eliminar esta categoría?</h4>
                        <p class="text-muted">Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="alert alert-warning">
                        Categoría a eliminar: <strong id="categoriaAEliminar"></strong>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmarEliminar">
                        <i class="fas fa-trash-alt me-2"></i>Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Modificar Categoría -->
    <div class="modal fade" id="modificarModal" tabindex="-1" aria-labelledby="modificarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modificarModalLabel">
                        <i class="fas fa-edit me-2"></i>Modificar Categoría
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label for="categoriaActual" class="form-label">Categoría actual</label>
                        <input type="text" class="form-control bg-light" id="categoriaActual" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nuevaCategoria" class="form-label">Nueva categoría</label>
                        <input type="text" class="form-control" id="nuevaCategoria" placeholder="Ingrese el nuevo nombre">
                        <div class="form-text">El nombre debe ser único y descriptivo.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmarModificar">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Nueva Categoría -->
    <div class="modal fade" id="nuevaCategoriaModal" tabindex="-1" aria-labelledby="nuevaCategoriaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="nuevaCategoriaModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Categoría
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nombreNuevaCategoria" class="form-label">Nombre de la categoría</label>
                        <input type="text" class="form-control" id="nombreNuevaCategoria" placeholder="Ingrese el nombre de la categoría">
                        <div class="form-text">El nombre debe ser único y descriptivo.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="crearCategoria">
                        <i class="fas fa-plus me-2"></i>Crear Categoría
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables globales
        let selectedCategoria = null;
        const btnEliminar = document.getElementById('btnEliminar');
        const btnModificar = document.getElementById('btnModificar');
        const eliminarModal = new bootstrap.Modal(document.getElementById('eliminarModal'));
        const modificarModal = new bootstrap.Modal(document.getElementById('modificarModal'));

        // Función para mostrar notificaciones
        const mostrarNotificacion = (titulo, mensaje, tipo) => {
            Swal.fire({
                title: titulo,
                text: mensaje,
                icon: tipo,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true
            });
        };

        // Verificar si hay mensajes en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');
        const error = urlParams.get('error');

        if (success) {
            switch(success) {
                case 'created':
                    mostrarNotificacion('¡Éxito!', 'Categoría creada correctamente', 'success');
                    break;
                case 'updated':
                    mostrarNotificacion('¡Éxito!', 'Categoría actualizada correctamente', 'success');
                    break;
                case 'deleted':
                    mostrarNotificacion('¡Éxito!', 'Categoría eliminada correctamente', 'success');
                    break;
            }
        }

        if (error) {
            switch(error) {
                case 'create_failed':
                    mostrarNotificacion('Error', 'No se pudo crear la categoría', 'error');
                    break;
                case 'update_failed':
                    mostrarNotificacion('Error', 'No se pudo actualizar la categoría', 'error');
                    break;
                case 'delete_failed':
                    mostrarNotificacion('Error', 'No se pudo eliminar la categoría', 'error');
                    break;
            }
        }

        // Función para seleccionar categoría
        window.seleccionarCategoria = function(elemento) {
            // Quitar selección previa
            const items = document.querySelectorAll('#categoriasList .list-group-item');
            items.forEach(item => {
                item.classList.remove('active');
                item.querySelector('.badge').textContent = 'Seleccionar';
                item.querySelector('.badge').classList.remove('bg-primary');
                item.querySelector('.badge').classList.add('bg-light', 'text-dark');
            });
            
            // Aplicar selección al elemento actual
            elemento.classList.add('active');
            elemento.querySelector('.badge').textContent = 'Seleccionado';
            elemento.querySelector('.badge').classList.remove('bg-light', 'text-dark');
            elemento.querySelector('.badge').classList.add('bg-primary');
            
            // Guardar categoría seleccionada
            selectedCategoria = elemento.getAttribute('data-categoria');
            
            // Habilitar botones
            btnEliminar.disabled = false;
            btnModificar.disabled = false;
        };

        // Manejador de evento para botón Eliminar
        btnEliminar.addEventListener('click', function() {
            if (selectedCategoria) {
                document.getElementById('categoriaAEliminar').textContent = selectedCategoria;
                eliminarModal.show();
            }
        });

        // Manejador de evento para confirmar eliminación
        document.getElementById('confirmarEliminar').addEventListener('click', function() {
            if (selectedCategoria) {
                // Mostrar indicador de carga
                Swal.fire({
                    title: 'Eliminando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Construir URL con parámetros
                const url = `../../controladores/eliminar_categoria.php?tipo_vehiculo=${encodeURIComponent(selectedCategoria)}`;

                // Realizar petición al servidor
                fetch(url)
                    .then(response => response.text())
                    .then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (error) {
                            throw new Error('La respuesta no es un JSON válido: ' + text);
                        }
                    })
                    .then(data => {
                        eliminarModal.hide();
                        
                        if (data.success) {
                            // Eliminar elemento de la lista
                            const items = document.querySelectorAll('#categoriasList .list-group-item');
                            items.forEach(item => {
                                if (item.getAttribute('data-categoria') === selectedCategoria) {
                                    item.remove();
                                }
                            });
                            
                            // Desactivar botones
                            btnEliminar.disabled = true;
                            btnModificar.disabled = true;
                            selectedCategoria = null;
                            
                            // Mostrar notificación de éxito
                            mostrarNotificacion('¡Eliminado!', data.message || 'La categoría ha sido eliminada correctamente', 'success');
                        } else {
                            // Mostrar notificación de error
                            mostrarNotificacion('Error', data.error || 'No se pudo eliminar la categoría', 'error');
                        }
                    })
                    .catch(error => {
                        eliminarModal.hide();
                        console.error('Error en la petición:', error);
                        
                        // Mostrar notificación de error
                        mostrarNotificacion('Error', 'Ha ocurrido un error al intentar eliminar la categoría', 'error');
                    });
            }
        });

        // Manejador de evento para botón Modificar
        btnModificar.addEventListener('click', function() {
            if (selectedCategoria) {
                document.getElementById('categoriaActual').value = selectedCategoria;
                document.getElementById('nuevaCategoria').value = selectedCategoria;
                modificarModal.show();
            }
        });

        // Manejador de evento para confirmar modificación
        document.getElementById('confirmarModificar').addEventListener('click', function() {
            const nuevoNombre = document.getElementById('nuevaCategoria').value.trim();
            
            if (!nuevoNombre) {
                mostrarNotificacion('Advertencia', 'El nombre de la categoría no puede estar vacío', 'warning');
                return;
            }
            
            if (nuevoNombre === selectedCategoria) {
                modificarModal.hide();
                return;
            }
            
            // Mostrar indicador de carga
            Swal.fire({
                title: 'Actualizando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Construir URL con parámetros
            const url = `../../controladores/modificar_categoria.php?tipo_vehiculo=${encodeURIComponent(selectedCategoria)}&nuevo_nombre=${encodeURIComponent(nuevoNombre)}`;

            // Realizar petición al servidor
            fetch(url)
                .then(response => response.text())
                .then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (error) {
                        throw new Error('La respuesta no es un JSON válido: ' + text);
                    }
                })
                .then(data => {
                    modificarModal.hide();
                    
                    if (data.success) {
                        // Actualizar elemento en la lista
                        const items = document.querySelectorAll('#categoriasList .list-group-item');
                        items.forEach(item => {
                            if (item.getAttribute('data-categoria') === selectedCategoria) {
                                item.setAttribute('data-categoria', nuevoNombre);
                                item.querySelector('span').textContent = nuevoNombre;
                            }
                        });
                        
                        // Actualizar categoría seleccionada
                        selectedCategoria = nuevoNombre;
                        
                        // Mostrar notificación de éxito
                        mostrarNotificacion('¡Actualizado!', data.message || 'La categoría ha sido actualizada correctamente', 'success');
                    } else {
                        // Mostrar notificación de error
                        mostrarNotificacion('Error', data.error || 'No se pudo actualizar la categoría', 'error');
                    }
                })
                .catch(error => {
                    modificarModal.hide();
                    console.error('Error en la petición:', error);
                    
                    // Mostrar notificación de error
                    mostrarNotificacion('Error', 'Ha ocurrido un error al intentar actualizar la categoría', 'error');
                });
        });

        // Manejador de evento para crear nueva categoría
        document.getElementById('crearCategoria').addEventListener('click', function() {
            const nombreCategoria = document.getElementById('nombreNuevaCategoria').value.trim();
            
            if (!nombreCategoria) {
                mostrarNotificacion('Advertencia', 'El nombre de la categoría no puede estar vacío', 'warning');
                return;
            }
            
            // Mostrar indicador de carga
            Swal.fire({
                title: 'Creando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Construir URL con parámetros
            const url = `../../controladores/agregar_categoria.php`;
            
            // Crear objeto FormData para enviar datos por POST
            const formData = new FormData();
            formData.append('nombre_categoria', nombreCategoria);
            
            // Realizar petición al servidor
            fetch(url, {
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
                const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaCategoriaModal'));
                modal.hide();
                
                if (data.success) {
                    // Mostrar notificación de éxito
                    mostrarNotificacion('¡Creado!', data.message || 'La categoría ha sido creada correctamente', 'success');
                    
                    // Recargar la página después de un breve retraso
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    // Mostrar notificación de error
                    mostrarNotificacion('Error', data.error || 'No se pudo crear la categoría', 'error');
                }
            })
            .catch(error => {
                console.error('Error en la petición:', error);
                
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaCategoriaModal'));
                modal.hide();
                
                // Mostrar notificación de error
                mostrarNotificacion('Error', 'Ha ocurrido un error al intentar crear la categoría: ' + error.message, 'error');
            });
        });
        
        // También podemos agregar la categoría al presionar Enter en el campo de texto
        document.getElementById('nombreNuevaCategoria').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('crearCategoria').click();
            }
        });
    });
</script>

