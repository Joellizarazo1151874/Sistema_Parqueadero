<style>
    .add-matricula,
    .remove-matricula {
        background-color: #007bff;
        color: white;
    }

    .add-matricula:hover,
    .remove-matricula:hover {
        background-color: #0056b3;
    }
</style>

<div id="tab1" class="tab-content active">
    <div class="row">
        <!-- Columna principal -->
        <div class="col-md-12">
            <div class="ticket-summary card p-3">
                <!-- Filtros -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <input type="text" class="form-control" placeholder="Matrícula">
                    <input type="text" class="form-control" placeholder="Cliente">

                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-share-alt"></i>
                    </button>
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
                                        echo "<button class='btn-edit'><i class='fas fa-edit'></i> Editar</button> ";
                                        echo "<button class='btn-eliminar'><i class='fas fa-trash-alt'></i> Eliminar</button>";
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

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
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
                    });
                </script>

                <!-- Botón para abrir el modal -->
                <div class="text-end mt-1">
                    <button class="btn-edit btn-primary" data-bs-toggle="modal" data-bs-target="#crearClienteModal">Crear Cliente</button>
                </div>
            </div>
        </div>
    </div>
</div>