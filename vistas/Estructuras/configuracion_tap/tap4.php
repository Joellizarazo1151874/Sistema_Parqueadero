<div id="tab4" class="tab-content d-none">
    <h3>Usuarios</h3>
    <div class="card p-4 mb-4">
        <div class="ticket-summary">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr class="highlight-column">
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuariosTableBody">
                        <?php
                        // Verificar si la tabla usuarios existe
                        $checkTable = $conexion->query("SHOW TABLES LIKE 'usuarios'");
                        if ($checkTable->num_rows == 0) {
                            echo '<tr><td colspan="6" class="text-center">
                                <div class="alert alert-warning">
                                    <strong>¡Atención!</strong> La tabla usuarios no existe en la base de datos. 
                                    <button class="btn btn-sm btn-primary ms-2" id="btnCrearTablaUsuarios">Crear Tabla</button>
                                </div>
                            </td></tr>';
                        } else {
                            // Consulta para obtener los usuarios
                            $sql = "SELECT * FROM usuarios ORDER BY fecha_registro DESC";
                            $result = $conexion->query($sql);
                            
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr data-id="' . htmlspecialchars($row['id_usuario']) . '">';
                                    echo '<td>' . htmlspecialchars($row['id_usuario']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['correo']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['rol']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['fecha_registro']) . '</td>';
                                    echo '<td>';
                                    echo '<button class="btn btn-sm btn-outline-primary me-2 btn-editar-usuario" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal" data-id="' . htmlspecialchars($row['id_usuario']) . '">';
                                    echo '<i class="fas fa-edit me-1"></i>';
                                    echo '</button>';
                                    echo '<button class="btn btn-sm btn-outline-danger btn-eliminar-usuario" data-id="' . htmlspecialchars($row['id_usuario']) . '">';
                                    echo '<i class="fas fa-trash-alt me-1"></i>';
                                    echo '</button>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                if ($result) {
                                    echo '<tr><td colspan="6" class="text-center">No se encontraron usuarios.</td></tr>';
                                } else {
                                    echo '<tr><td colspan="6" class="text-center">
                                        <div class="alert alert-danger">
                                            <strong>¡Error!</strong> ' . $conexion->error . '
                                        </div>
                                    </td></tr>';
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-end mt-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                <i class="fas fa-plus me-2"></i> Crear Usuario
            </button>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarUsuario">
                        <input type="hidden" id="editIdUsuario" name="id_usuario">
                        <div class="mb-3">
                            <label for="editNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCorreo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="editCorreo" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">Contraseña <small class="text-muted">(Dejar en blanco para mantener la actual)</small></label>
                            <input type="password" class="form-control" id="editPassword" name="contrasena">
                        </div>
                        <div class="mb-3">
                            <label for="editRol" class="form-label">Rol</label>
                            <select class="form-select" id="editRol" name="rol" required>
                                <option value="administrador">Administrador</option>
                                <option value="operador">Operador</option>
                                <option value="consulta">Consulta</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarEditarUsuario">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Usuario -->
    <div class="modal fade" id="eliminarUsuarioModal" tabindex="-1" aria-labelledby="eliminarUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="eliminarUsuarioModalLabel">Eliminar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar al usuario <strong id="nombreUsuarioEliminar"></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Esta acción no se puede deshacer.</p>
                    <input type="hidden" id="idUsuarioEliminar">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminarUsuario">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Usuario -->
    <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1" aria-labelledby="nuevoUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="nuevoUsuarioModalLabel">Crear Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAgregarUsuario">
                        <div class="mb-3">
                            <label for="nuevoNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nuevoNombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuevoCorreo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="nuevoCorreo" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuevoPassword" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="nuevoPassword" name="contrasena" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuevoRol" class="form-label">Rol</label>
                            <select class="form-select" id="nuevoRol" name="rol" required>
                                <option value="administrador">Administrador</option>
                                <option value="operador">Operador</option>
                                <option value="consulta">Consulta</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnGuardarAgregarUsuario">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Botón para crear la tabla de usuarios
    const btnCrearTablaUsuarios = document.getElementById('btnCrearTablaUsuarios');
    if (btnCrearTablaUsuarios) {
        btnCrearTablaUsuarios.addEventListener('click', function() {
            // Mostrar indicador de carga
            const loadingSwal = Swal.fire({
                title: 'Creando tabla...',
                text: 'Por favor espere mientras se crea la tabla de usuarios',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Enviar la solicitud al servidor
            fetch('../../controladores/crear_tabla_usuarios.php')
                .then(response => response.json())
                .then(data => {
                    loadingSwal.close();
                    
                    if (data.success) {
                        window.mostrarNotificacion('¡Éxito!', data.message || 'Tabla de usuarios creada correctamente', 'success');
                        // Recargar la página después de un breve retraso
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        window.mostrarNotificacion('Error', data.error || 'No se pudo crear la tabla de usuarios', 'error');
                    }
                })
                .catch(error => {
                    loadingSwal.close();
                    console.error('Error:', error);
                    window.mostrarNotificacion('Error', 'Ha ocurrido un error al intentar crear la tabla de usuarios', 'error');
                });
        });
    }
    
    // Manejar evento click en botones de editar usuario
    document.querySelectorAll('.btn-editar-usuario').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            
            // Mostrar indicador de carga
            const loadingSwal = Swal.fire({
                title: 'Cargando...',
                text: 'Obteniendo datos del usuario',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Obtener datos del usuario
            fetch(`../../controladores/obtener_usuario.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    loadingSwal.close();
                    if (data.success) {
                        // Llenar formulario con datos del usuario
                        document.getElementById('editIdUsuario').value = data.usuario.id_usuario;
                        document.getElementById('editNombre').value = data.usuario.nombre;
                        document.getElementById('editCorreo').value = data.usuario.correo;
                        document.getElementById('editPassword').value = ''; // No mostramos la contraseña por seguridad
                        document.getElementById('editRol').value = data.usuario.rol;
                    } else {
                        window.mostrarNotificacion('Error', data.error || 'No se pudo obtener la información del usuario', 'error');
                    }
                })
                .catch(error => {
                    loadingSwal.close();
                    console.error('Error:', error);
                    window.mostrarNotificacion('Error', 'Ha ocurrido un error al obtener los datos del usuario', 'error');
                });
        });
    });
    
    // Manejar evento click en botones de eliminar usuario
    document.querySelectorAll('.btn-eliminar-usuario').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const row = this.closest('tr');
            const nombre = row.cells[1].textContent;
            
            // Mostrar el modal de confirmación
            document.getElementById('nombreUsuarioEliminar').textContent = nombre;
            document.getElementById('idUsuarioEliminar').value = id;
            
            // Abrir el modal de confirmación
            const eliminarModal = new bootstrap.Modal(document.getElementById('eliminarUsuarioModal'));
            eliminarModal.show();
        });
    });
    
    // Manejar evento click en el botón de confirmar eliminación
    document.getElementById('btnConfirmarEliminarUsuario').addEventListener('click', function() {
        const id = document.getElementById('idUsuarioEliminar').value;
        
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
        formData.append('id_usuario', id);
        
        // Enviar la solicitud al servidor
        fetch('../../controladores/eliminar_usuario.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Cerrar el SweetAlert de carga
            loadingSwal.close();
            
            // Cerrar el modal con seguridad
            try {
                const modalElement = document.getElementById('eliminarUsuarioModal');
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
                            // Eliminar la fila de la tabla
                            const rows = document.querySelectorAll('#usuariosTableBody tr');
                            for (const row of rows) {
                                if (row.dataset.id === id) {
                                    row.remove();
                                    break;
                                }
                            }
                            
                            // Si no quedan filas, mostrar mensaje de "No se encontraron"
                            const remainingRows = document.querySelectorAll('#usuariosTableBody tr');
                            if (remainingRows.length === 0) {
                                const tbody = document.getElementById('usuariosTableBody');
                                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron usuarios.</td></tr>';
                            }
                            
                            // Mostrar notificación de éxito
                            window.mostrarNotificacion('¡Eliminado!', data.message || 'Usuario eliminado correctamente', 'success');
                        } else {
                            // Mostrar notificación de error
                            window.mostrarNotificacion('Error', data.error || 'No se pudo eliminar el usuario', 'error');
                        }
                    }, 300);
                }
            } catch (e) {
                console.error('Error al cerrar el modal:', e);
                
                // Mostrar notificación incluso si hubo error al cerrar el modal
                if (data.success) {
                    // Eliminar la fila de la tabla
                    const rows = document.querySelectorAll('#usuariosTableBody tr');
                    for (const row of rows) {
                        if (row.dataset.id === id) {
                            row.remove();
                            break;
                        }
                    }
                    
                    // Si no quedan filas, mostrar mensaje de "No se encontraron"
                    const remainingRows = document.querySelectorAll('#usuariosTableBody tr');
                    if (remainingRows.length === 0) {
                        const tbody = document.getElementById('usuariosTableBody');
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron usuarios.</td></tr>';
                    }
                    
                    window.mostrarNotificacion('¡Eliminado!', data.message || 'Usuario eliminado correctamente', 'success');
                } else {
                    window.mostrarNotificacion('Error', data.error || 'No se pudo eliminar el usuario', 'error');
                }
            }
        })
        .catch(error => {
            // Cerrar el SweetAlert de carga en caso de error
            loadingSwal.close();
            
            // Asegurarse de que el modal está cerrado
            try {
                const modalElement = document.getElementById('eliminarUsuarioModal');
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
            window.mostrarNotificacion('Error', 'Ha ocurrido un error al intentar eliminar el usuario', 'error');
        });
    });
    
    // Guardar edición de usuario
    document.getElementById('btnGuardarEditarUsuario').addEventListener('click', function() {
        // Obtener datos del formulario
        const id = document.getElementById('editIdUsuario').value;
        const nombre = document.getElementById('editNombre').value;
        const correo = document.getElementById('editCorreo').value;
        const password = document.getElementById('editPassword').value;
        const rol = document.getElementById('editRol').value;
        
        // Validar campos requeridos
        if (!nombre || !correo || !rol) {
            window.mostrarNotificacion('Error', 'Por favor complete todos los campos requeridos', 'error');
            return;
        }
        
        // Validar formato de correo
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo)) {
            window.mostrarNotificacion('Error', 'Por favor ingrese un correo electrónico válido', 'error');
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
        formData.append('id_usuario', id);
        formData.append('nombre', nombre);
        formData.append('correo', correo);
        if (password) {
            formData.append('contrasena', password);
        }
        formData.append('rol', rol);
        
        // Enviar la solicitud al servidor
        fetch('../../controladores/editar_usuario.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Cerrar el SweetAlert de carga
            loadingSwal.close();
            
            // Cerrar el modal con seguridad
            try {
                const modalElement = document.getElementById('editarUsuarioModal');
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
                            const rows = document.querySelectorAll('#usuariosTableBody tr');
                            for (const row of rows) {
                                if (row.dataset.id === id) {
                                    row.cells[1].textContent = nombre;
                                    row.cells[2].textContent = correo;
                                    row.cells[3].textContent = rol;
                                    break;
                                }
                            }
                            
                            // Mostrar notificación de éxito
                            window.mostrarNotificacion('¡Actualizado!', data.message || 'Usuario actualizado correctamente', 'success');
                        } else {
                            // Mostrar notificación de error
                            window.mostrarNotificacion('Error', data.error || 'No se pudo actualizar el usuario', 'error');
                        }
                    }, 300);
                }
            } catch (e) {
                console.error('Error al cerrar el modal:', e);
                
                // Mostrar notificación incluso si hubo error al cerrar el modal
                if (data.success) {
                    // Actualizar la fila en la tabla
                    const rows = document.querySelectorAll('#usuariosTableBody tr');
                    for (const row of rows) {
                        if (row.dataset.id === id) {
                            row.cells[1].textContent = nombre;
                            row.cells[2].textContent = correo;
                            row.cells[3].textContent = rol;
                            break;
                        }
                    }
                    
                    window.mostrarNotificacion('¡Actualizado!', data.message || 'Usuario actualizado correctamente', 'success');
                } else {
                    window.mostrarNotificacion('Error', data.error || 'No se pudo actualizar el usuario', 'error');
                }
            }
        })
        .catch(error => {
            // Cerrar el SweetAlert de carga en caso de error
            loadingSwal.close();
            
            // Asegurarse de que el modal está cerrado
            try {
                const modalElement = document.getElementById('editarUsuarioModal');
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
            window.mostrarNotificacion('Error', 'Ha ocurrido un error al intentar actualizar el usuario', 'error');
        });
    });
    
    // Guardar nuevo usuario
    document.getElementById('btnGuardarAgregarUsuario').addEventListener('click', function() {
        // Obtener datos del formulario
        const nombre = document.getElementById('nuevoNombre').value.trim();
        const correo = document.getElementById('nuevoCorreo').value.trim();
        const password = document.getElementById('nuevoPassword').value;
        const rol = document.getElementById('nuevoRol').value;
        
        console.log('Enviando datos:', { nombre, correo, password, rol });
        
        // Validar campos requeridos
        if (!nombre || !correo || !password || !rol) {
            window.mostrarNotificacion('Error', 'Por favor complete todos los campos', 'error');
            return;
        }
        
        // Validar formato de correo
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo)) {
            window.mostrarNotificacion('Error', 'Por favor ingrese un correo electrónico válido', 'error');
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
        
        // Preparar los datos para enviar - Usamos un objeto FormData directamente del formulario
        const formData = new FormData(document.getElementById('formAgregarUsuario'));
        
        // Verificar que formData contiene todos los datos requeridos
        console.log('FormData contiene:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Enviar la solicitud al servidor
        fetch('../../controladores/agregar_usuario.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Respuesta recibida:', response);
            return response.text();  // Primero obtenemos el texto completo
        })
        .then(text => {
            console.log('Texto de respuesta:', text);
            try {
                return JSON.parse(text);  // Intentamos parsearlo como JSON
            } catch (e) {
                console.error('Error al parsear JSON:', e);
                throw new Error('Respuesta del servidor no es JSON válido: ' + text);
            }
        })
        .then(data => {
            // Cerrar el SweetAlert de carga
            loadingSwal.close();
            
            // Cerrar el modal con seguridad
            try {
                const modalElement = document.getElementById('nuevoUsuarioModal');
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
                            window.mostrarNotificacion('¡Guardado!', data.message || 'Usuario agregado correctamente', 'success');
                            
                            // Recargar la página para mostrar el nuevo usuario
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            // Mostrar notificación de error
                            window.mostrarNotificacion('Error', data.error || 'No se pudo agregar el usuario', 'error');
                        }
                    }, 300);
                }
            } catch (e) {
                console.error('Error al cerrar el modal:', e);
                
                // Mostrar notificación incluso si hubo error al cerrar el modal
                if (data.success) {
                    window.mostrarNotificacion('¡Guardado!', data.message || 'Usuario agregado correctamente', 'success');
                    // Recargar la página para mostrar el nuevo usuario
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    window.mostrarNotificacion('Error', data.error || 'No se pudo agregar el usuario', 'error');
                }
            }
        })
        .catch(error => {
            // Cerrar el SweetAlert de carga en caso de error
            loadingSwal.close();
            
            // Asegurarse de que el modal está cerrado
            try {
                const modalElement = document.getElementById('nuevoUsuarioModal');
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
            window.mostrarNotificacion('Error', 'Ha ocurrido un error al intentar agregar el usuario', 'error');
        });
    });
});
</script>