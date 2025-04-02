
<div id="tab1" class="tab-content active">
    <div class="row">
        <!-- Columna principal -->
        <div class="col-md-12">
            <div class="ticket-summary card p-3">
                <!-- Filtros -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <form action="" method="GET" class="d-flex gap-2">
                        <input type="text" class="form-control" name="matricula" placeholder="Matrícula" value="<?php echo isset($_GET['matricula']) ? htmlspecialchars($_GET['matricula']) : ''; ?>">
                        <input type="text" class="form-control" name="cliente" placeholder="Cliente" value="<?php echo isset($_GET['cliente']) ? htmlspecialchars($_GET['cliente']) : ''; ?>">
                        <?php if(isset($_GET['matricula']) || isset($_GET['cliente'])): ?>
                            <a href="?" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                        <button class="btn btn-outline-secondary">
                            <i class="fas fa-share-alt"></i>
                        </button>
                    </form>
                </div>

                <!-- Tabla de tickets -->
                <div class="ticket-summary">
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr class="highlight-column">
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Telefono</th>
                                    <th>Correo</th>
                                    <th>Fecha de Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Consulta SQL para obtener los clientes con sus vehículos
                                $sql = "SELECT DISTINCT c.* FROM clientes c 
                                        LEFT JOIN vehiculos v ON c.id_cliente = v.id_cliente 
                                        WHERE 1=1";

                                if (isset($_GET['matricula']) && !empty($_GET['matricula'])) {
                                    $matricula = $conexion->real_escape_string($_GET['matricula']);
                                    $sql .= " AND v.placa LIKE '%$matricula%'";
                                }

                                if (isset($_GET['cliente']) && !empty($_GET['cliente'])) {
                                    $cliente = $conexion->real_escape_string($_GET['cliente']);
                                    $sql .= " AND c.nombre LIKE '%$cliente%'";
                                }

                                $sql .= " ORDER BY c.fecha_registro DESC";

                                $result = $conexion->query($sql);

                                // Verifica si la consulta fue exitosa
                                if ($result) {
                                    // Itera sobre los resultados y genera las filas de la tabla
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>{$row['id_cliente']}</td>";
                                        echo "<td>{$row['nombre']}</td>";
                                        echo "<td>{$row['telefono']}</td>";
                                        echo "<td>{$row['correo']}</td>";
                                        echo "<td>{$row['fecha_registro']}</td>";
                                        echo "<td>";
                                        echo "<button class='btn btn-sm btn-outline-primary me-2 btn-editar-tolerancia' data-bs-toggle='modal' data-bs-target='#editarClienteModal' 
                                              data-id='{$row['id_cliente']}'
                                              data-nombre='{$row['nombre']}'
                                              data-telefono='{$row['telefono']}'
                                              data-correo='{$row['correo']}'>
                                              <i class='fas fa-edit'></i></button> ";
                                        echo "<button class='btn btn-sm btn-outline-danger btn-eliminar-tolerancia' data-bs-toggle='modal' data-bs-target='#eliminarClienteModal'
                                              data-id='{$row['id_cliente']}'
                                              data-nombre='{$row['nombre']}'>
                                              <i class='fas fa-trash-alt'></i></button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "Error en la consulta: " . $conexion->error;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal para crear cliente -->
                <div class="modal fade" id="crearClienteModal" tabindex="-1" aria-labelledby="crearClienteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="crearClienteModalLabel">Crear Cliente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="crearClienteForm" action="../../controladores/agregar_cliente.php" method="POST">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="correo" class="form-label">Correo</label>
                                        <input type="email" class="form-control" id="correo" name="correo" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="matricula" class="form-label">Matrícula(s)</label>
                                        <div id="matriculasContainer">
                                            <div class="vehiculo-grupo mb-3">
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control" name="matriculas[]" placeholder="Matrícula" required>
                                                    <button type="button" class="btn btn-outline-secondary add-matricula">+</button>
                                                </div>
                                                <select name="tipo_vehiculo[]" class="form-select mb-2">
                                                    <option value="">Seleccione tipo de vehículo</option>
                                                    <?php foreach ($tipos_vehiculo as $tipo): ?>
                                                        <option value="<?php echo htmlspecialchars($tipo); ?>">
                                                            <?php echo htmlspecialchars($tipo); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <input type="text" class="form-control" name="descripciones[]" placeholder="Descripción del vehículo">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" form="crearClienteForm">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para editar cliente -->
                <div class="modal fade" id="editarClienteModal" tabindex="-1" aria-labelledby="editarClienteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editarClienteModalLabel">Editar Cliente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editarClienteForm" action="../../controladores/editar_cliente.php" method="POST">
                                    <input type="hidden" id="edit_id_cliente" name="id_cliente">
                                    <div class="mb-3">
                                        <label for="edit_nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="edit_telefono" name="telefono" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_correo" class="form-label">Correo</label>
                                        <input type="email" class="form-control" id="edit_correo" name="correo" required>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" form="editarClienteForm">Guardar cambios</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para eliminar cliente -->
                <div class="modal fade" id="eliminarClienteModal" tabindex="-1" aria-labelledby="eliminarClienteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="eliminarClienteModalLabel">Eliminar Cliente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="eliminarClienteForm" action="../../controladores/eliminar_cliente.php" method="POST">
                                    <input type="hidden" id="delete_id_cliente" name="id_cliente">
                                    <p>¿Está seguro que desea eliminar al cliente <span id="delete_nombre_cliente"></span>?</p>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger" form="eliminarClienteForm">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón para abrir el modal -->
                <div class="text-end mt-1">
                    <button class="btn-edit btn-primary" data-bs-toggle="modal" data-bs-target="#crearClienteModal">Crear Cliente</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si hay mensajes de éxito o error en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');
        const error = urlParams.get('error');

        if (success) {
            let title, text, icon;
            switch(success) {
                case '1':
                    title = '¡Éxito!';
                    text = 'Cliente creado correctamente';
                    icon = 'success';
                    break;
                case 'updated':
                    title = '¡Éxito!';
                    text = 'Cliente actualizado correctamente';
                    icon = 'success';
                    break;
                case 'deleted':
                    title = '¡Éxito!';
                    text = 'Cliente eliminado correctamente';
                    icon = 'success';
                    break;
            }
            if (title) {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true
                });
            }
        }

        if (error) {
            let title, text, icon;
            switch(error) {
                case 'email_exists':
                    title = 'Error';
                    text = 'El correo electrónico ya está registrado';
                    icon = 'error';
                    break;
                case 'create_failed':
                    title = 'Error';
                    text = 'No se pudo crear el cliente';
                    icon = 'error';
                    break;
                case 'update_failed':
                    title = 'Error';
                    text = 'No se pudo actualizar el cliente';
                    icon = 'error';
                    break;
                case 'delete_failed':
                    title = 'Error';
                    text = 'No se pudo eliminar el cliente';
                    icon = 'error';
                    break;
            }
            if (title) {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true
                });
            }
        }

        document.querySelector('.add-matricula').addEventListener('click', function() {
            const container = document.getElementById('matriculasContainer');
            const newVehiculoGrupo = document.createElement('div');
            newVehiculoGrupo.classList.add('vehiculo-grupo', 'mb-3');
            
            // Crear HTML para el nuevo grupo de vehículo (matrícula + tipo)
            let tipoOptions = '';
            <?php foreach ($tipos_vehiculo as $tipo): ?>
                tipoOptions += `<option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo htmlspecialchars($tipo); ?></option>`;
            <?php endforeach; ?>

            newVehiculoGrupo.innerHTML = `
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="matriculas[]" placeholder="Matrícula" required>
                    <button type="button" class="btn btn-outline-secondary remove-matricula">-</button>
                </div>
                <select name="tipo_vehiculo[]" class="form-select mb-2">
                    <option value="">Seleccione tipo de vehículo</option>
                    ${tipoOptions}
                </select>
                <input type="text" class="form-control" name="descripciones[]" placeholder="Descripción del vehículo">
            `;
            container.appendChild(newVehiculoGrupo);

            newVehiculoGrupo.querySelector('.remove-matricula').addEventListener('click', function() {
                container.removeChild(newVehiculoGrupo);
            });
        });

        // Manejar el modal de edición
        const editarModal = document.getElementById('editarClienteModal');
        if (editarModal) {
            editarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');
                const telefono = button.getAttribute('data-telefono');
                const correo = button.getAttribute('data-correo');

                document.getElementById('edit_id_cliente').value = id;
                document.getElementById('edit_nombre').value = nombre;
                document.getElementById('edit_telefono').value = telefono;
                document.getElementById('edit_correo').value = correo;
            });
        }

        // Manejar el modal de eliminación
        const eliminarModal = document.getElementById('eliminarClienteModal');
        if (eliminarModal) {
            eliminarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');

                document.getElementById('delete_id_cliente').value = id;
                document.getElementById('delete_nombre_cliente').textContent = nombre;
            });
        }
    });
</script>
</body>
</html>