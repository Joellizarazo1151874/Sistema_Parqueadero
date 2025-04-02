<div id="tab2" class="tab-content d-none">
    <div class="row">
        <!-- Columna principal -->
        <div class="col-md-12">
            <div class="ticket-summary card p-3">
                <!-- Filtros -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <form action="" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="tab2">
                        <input type="text" class="form-control" name="matricula" placeholder="Matrícula" value="<?php echo isset($_GET['matricula']) ? htmlspecialchars($_GET['matricula']) : ''; ?>">
                        <input type="text" class="form-control" name="cliente" placeholder="Cliente" value="<?php echo isset($_GET['cliente']) ? htmlspecialchars($_GET['cliente']) : ''; ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if(isset($_GET['matricula']) || isset($_GET['cliente']) || isset($_GET['filtro_fecha'])): ?>
                            <a href="?tab=tab2" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Tabla de vehículos -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr class="table-light">
                                <th>ID</th>
                                <th>Matrícula</th>
                                <th>Categoría</th>
                                <th>Descripción</th>
                                <th>Cliente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Consulta SQL para obtener los vehículos con sus clientes
                            $sql = "SELECT v.*, c.nombre as nombre_cliente 
                                   FROM vehiculos v 
                                   LEFT JOIN clientes c ON v.id_cliente = c.id_cliente 
                                   WHERE 1=1";

                            if (isset($_GET['matricula']) && !empty($_GET['matricula'])) {
                                $matricula = $conexion->real_escape_string($_GET['matricula']);
                                $sql .= " AND v.placa LIKE '%$matricula%'";
                            }

                            if (isset($_GET['cliente']) && !empty($_GET['cliente'])) {
                                $cliente = $conexion->real_escape_string($_GET['cliente']);
                                $sql .= " AND c.nombre LIKE '%$cliente%'";
                            }

                            if (isset($_GET['filtro_fecha']) && $_GET['filtro_fecha'] == 'historico') {
                                $sql .= " AND v.id_cliente IS NULL";
                            }

                            $sql .= " ORDER BY v.id_vehiculo DESC";

                            $result = $conexion->query($sql);

                            if ($result) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>{$row['id_vehiculo']}</td>";
                                    echo "<td>{$row['placa']}</td>";
                                    echo "<td>" . strtoupper($row['tipo']) . "</td>";
                                    echo "<td>{$row['descripcion']}</td>";
                                    echo "<td>" . ($row['nombre_cliente'] ?? 'Sin asignar') . "</td>";
                                    echo "<td>";
                                    echo "<button class='btn btn-sm btn-outline-primary me-2 btn-editar-tolerancia' data-bs-toggle='modal' data-bs-target='#editarVehiculoModal' 
                                          data-id='{$row['id_vehiculo']}'
                                          data-placa='{$row['placa']}'
                                          data-tipo='{$row['tipo']}'
                                          data-descripcion='{$row['descripcion']}'>
                                          <i class='fas fa-edit'></i></button>";
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

                <!-- Modal para editar vehículo -->
                <div class="modal fade" id="editarVehiculoModal" tabindex="-1" aria-labelledby="editarVehiculoModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editarVehiculoModalLabel">Editar Vehículo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editarVehiculoForm" action="../../controladores/editar_vehiculo.php" method="POST">
                                    <input type="hidden" id="edit_id_vehiculo" name="id_vehiculo">
                                    <input type="hidden" name="tab" value="2">
                                    <div class="mb-3">
                                        <label for="edit_placa" class="form-label">Matrícula</label>
                                        <input type="text" class="form-control" id="edit_placa" name="placa" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_tipo" class="form-label">Categoría</label>
                                        <select class="form-select" id="edit_tipo" name="tipo" required>
                                            <option value="auto">Carro</option>
                                            <option value="moto">Moto</option>
                                            <option value="camioneta">Camioneta</option>
                                            <option value="motocarro">Motocarro</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="2"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" form="editarVehiculoForm">Guardar cambios</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si hay mensajes de éxito en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('actualizado');

        if (success && success === '1') {
            Swal.fire({
                title: '¡Éxito!',
                text: 'El vehículo ha sido editado correctamente',
                icon: 'success',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true
            });
        }

        // Manejar el modal de edición de vehículo
        const editarVehiculoModal = document.getElementById('editarVehiculoModal');
        if (editarVehiculoModal) {
            editarVehiculoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const placa = button.getAttribute('data-placa');
                const tipo = button.getAttribute('data-tipo');
                const descripcion = button.getAttribute('data-descripcion');

                document.getElementById('edit_id_vehiculo').value = id;
                document.getElementById('edit_placa').value = placa;
                document.getElementById('edit_tipo').value = tipo;
                document.getElementById('edit_descripcion').value = descripcion;
            });
        }
    });
</script>