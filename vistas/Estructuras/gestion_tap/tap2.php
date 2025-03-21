<div id="tab2" class="tab-content d-none">

    <div class="row">
        <div class="col-lg-3 col-md-4">
            <div class="filters card p-3">
                <h6 class="fw-bold">Filtros</h6>
                <div class="filter-options">
                    <div class="mb-2">
                        <label class="form-label">Categoría</label>
                        <select class="form-select">
                            <option value="todos">Todas</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Abierto por</label>
                        <select class="form-select">
                            <option value="todos">Todos los Operadores</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Cerrado por</label>
                        <select class="form-select">
                            <option value="todos">Todos los Operadores</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Tipo de Tickets</label>
                        <select class="form-select">
                            <option value="todos">Todos</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Cancelación</label>
                        <select class="form-select">
                            <option value="todos">Todos</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-8">
            <div class="ticket-summary">
                <p>Aquí encontrarás los tickets que ya han sido resueltos y cerrados.</p>
                <div class="table-responsive">
                    <?php
                    // Inicializar variable para la suma total
                    $importe_total = 0;
                    
                    if ($resultado_cerrados && $resultado_cerrados->num_rows > 0) {
                    ?>
                            <table class="table custom-table mb-1">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Matricula</th>
                                        <th>Categoría</th>
                                        <th>Tipo</th>
                                        <th>Desde</th>
                                        <th>Hasta</th>
                                        <th>Observación</th>
                                        <th>Total</th>
                                        <th>Método Pago</th>
                                        <th>Cerrado por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($row = $resultado_cerrados->fetch_assoc()) { 
                                    // Formatear las fechas
                                    $fecha_ingreso = date('H:i (d/m)', strtotime($row['hora_ingreso']));
                                    $fecha_salida = date('H:i (d/m)', strtotime($row['hora_salida']));
                                    
                                    // Sumar al importe total
                                    $importe_total += $row['total_pagado'];
                                ?>
                                    <tr>
                                        <td><?php echo $row['id_registro']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['hora_ingreso'])); ?></td>
                                        <td><?php echo $row['placa']; ?></td>
                                        <td><?php echo $row['tipo']; ?></td>
                                        <td>x Hora</td>
                                        <td><?php echo $fecha_ingreso; ?></td>
                                        <td><?php echo $fecha_salida; ?></td>
                                        <td><?php echo $row['descripcion']; ?></td>
                                        <td>$<?php echo number_format($row['total_pagado'], 0, ',', '.'); ?></td>
                                        <td><?php echo $row['metodo_pago']; ?></td>
                                        <td><?php echo $row['cerrado_por']; ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                    <?php
                    } else {
                        echo '<div class="alert alert-info">No hay tickets cerrados disponibles.</div>';
                    }
                    ?>
                </div>
                
                <!-- Navegación de paginación -->
                <?php if ($total_paginas > 1): ?>
                <div class="pagination-container py-0">
                    <nav aria-label="Navegación de página">
                        <ul class="pagination justify-content-center mb-2 py-0">
                            <!-- Botón Anterior -->
                            <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="javascript:void(0);" onclick="<?php echo ($pagina_actual <= 1) ? 'return false' : 'cambiarPagina(' . ($pagina_actual - 1) . ')'; ?>" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            
                            <!-- Números de página -->
                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <li class="page-item <?php echo ($pagina_actual == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="javascript:void(0);" onclick="cambiarPagina(<?php echo $i; ?>)"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Botón Siguiente -->
                            <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="javascript:void(0);" onclick="<?php echo ($pagina_actual >= $total_paginas) ? 'return false' : 'cambiarPagina(' . ($pagina_actual + 1) . ')'; ?>" aria-label="Siguiente">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
                
                <!-- Script para la paginación -->
                <script>
                function cambiarPagina(pagina) {
                    // Validar que la página sea válida
                    if (pagina < 1 || pagina > <?php echo $total_paginas; ?>) {
                        return false;
                    }
                    
                    // Crear o actualizar un parámetro en la URL actual
                    const url = new URL(window.location.href);
                    url.searchParams.set('pagina', pagina);
                    
                    // Mantener la pestaña activa
                    const tabActual = document.querySelector('.nav-link.active').getAttribute('data-tab');
                    url.searchParams.set('tab', tabActual);
                    
                    // Navegar a la nueva URL
                    window.location.href = url.toString();
                }
                </script>
            </div>
            <div class="summary-stats row">
                <div class="stat col-6 col-md-3">
                    <strong>Tickets Cerrados</strong>
                    <span><?php echo ($resultado_cerrados) ? $resultado_cerrados->num_rows : 0; ?></span>
                </div>
                <div class="stat col-6 col-md-3">
                    <strong>Cancelados</strong>
                    <span>0</span>
                </div>
                <div class="stat col-6 col-md-3">
                    <strong>Tickets x Hora</strong>
                    <span>0</span>
                </div>
                <div class="stat col-6 col-md-3">
                    <strong>Importe Total</strong>
                    <span>$<?php echo number_format($importe_total, 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>
    </div>
    <!-- Espacio adicional para evitar que el footer oculte contenido -->
    <div class="mb-4"></div>
</div>