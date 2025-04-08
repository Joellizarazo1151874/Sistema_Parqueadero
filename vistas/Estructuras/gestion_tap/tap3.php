<div id="tab3" class="tab-content d-none">
    <div class="row">
        <!-- Columna del calendario -->
        <div class="col-md-3">
            <div class="card p-3">
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Columna principal -->
        <div class="col-md-9">
            <div class="ticket-summary card p-3">
                <!-- Filtros -->
                <style>
                    .form-inline {
                        display: flex;
                        flex-wrap: nowrap;
                        gap: 10px;
                        align-items: center;
                    }
                </style>

                <form method="GET" action="">
                    <input type="hidden" name="tab" value="tab3">
                    <input type="hidden" name="fecha" value="<?php echo htmlspecialchars(isset($_GET['fecha']) ? $_GET['fecha'] : ''); ?>">
                    <div class="form-inline mb-3">
                        <input type="text" class="form-control" name="matricula" placeholder="Matrícula" value="<?php echo htmlspecialchars(isset($_GET['matricula']) ? $_GET['matricula'] : ''); ?>">
                        <input type="text" class="form-control" name="Ticketid" placeholder="Ticket ID">
                        <input type="text" class="form-control" name="Detalle" placeholder="Detalle">
                        <div class="form-check ms-2">
                            <input class="form-check-input" type="checkbox" name="no_reportados" value="1" id="checkNoReportados" <?php echo isset($_GET['no_reportados']) && $_GET['no_reportados'] == '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="checkNoReportados">
                            </label>
                        </div>
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Tabla de tickets -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr class="table-light">
                                <th>E/S</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Detalle</th>
                                <th>Categoría</th>
                                <th>Placa</th>
                                <th>Ticket ID</th>
                                <th>Método Pago</th>
                                <th>Total Pagado</th>
                                <th>Operador</th>
                                <th>Reportado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $resultado_tickets_activos_cerrados->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['estado'] === 'activo' ? '<i class="fas fa-arrow-right text-success"></i>' : '<i class="fas fa-arrow-left text-danger"></i>'; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['hora_ingreso'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($row['hora_ingreso'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['descripcion_vehiculo']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                                    <td><?php echo htmlspecialchars($row['placa']); ?></td>
                                    <td><?php echo htmlspecialchars($row['id_registro']); ?></td>
                                    <td><?php 
                                        if ($row['estado'] === 'cancelado') {
                                            echo 'Cancelado';
                                        } else {
                                            echo htmlspecialchars($row['nombre_metodo_pago'] ?? 'No especificado');
                                        }
                                    ?></td>
                                    <td>$<?php echo number_format((float)$row['total_pagado'], 0, '', ','); ?></td>
                                    <td><?php echo htmlspecialchars($row['abierto_por']); ?></td>
                                    <td>
                                        <?php 
                                            if ($row['reportado'] == 1) {
                                                echo '<span class="badge bg-success">Sí</span>';
                                                if (!empty($row['id_reporte'])) {
                                                    echo ' <small>(' . htmlspecialchars($row['id_reporte']) . ')</small>';
                                                }
                                            } else {
                                                echo '<span class="badge bg-warning">No</span>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Navegación de paginación -->
                <?php if ($total_paginas > 1): ?>
                <div class="pagination-container py-0 mb-0">
                    <nav aria-label="Navegación de página">
                        <ul class="pagination justify-content-center mb-0 py-0">
                            <!-- Botón Anterior -->
                            <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?tab=tab3&pagina=<?php echo $pagina_actual - 1; ?>" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            
                            <!-- Números de página -->
                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <li class="page-item <?php echo ($pagina_actual == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?tab=tab3&pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Botón Siguiente -->
                            <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?tab=tab3&pagina=<?php echo $pagina_actual + 1; ?>" aria-label="Siguiente">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Espacio adicional para evitar que el footer oculte contenido -->
    <div class="mb-4"></div>
</div>

<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/es.js'></script>

<script>
$(document).ready(function() {
    $('#calendar').fullCalendar({
        locale: 'es',
        header: {
            left: 'prev,next',
            center: 'title',
            right: 'today'
        },
        selectable: true,
        selectHelper: true,
        dayRender: function(date, cell) {
            if (date.format('YYYY-MM-DD') === '<?php echo isset($_GET['fecha']) ? $_GET['fecha'] : ''; ?>') {
                cell.css('background-color', '#d9edf7');
            }
        },
        dayClick: function(date) {
            // Redirigir a la misma página con el parámetro de fecha
            window.location.href = '?tab=tab3&fecha=' + date.format('YYYY-MM-DD');
        }
    });
});
</script>