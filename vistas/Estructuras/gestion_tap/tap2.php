<div id="tab2" class="tab-content d-none">

    <div class="row">
        <div class="col-lg-3 col-md-4">
            <div class="filters card p-3">
                <h6 class="fw-bold">Filtros</h6>
                <form id="filtroCategoriaForm" method="GET">
                    <input type="hidden" name="tab" value="tab2">
                    <div class="filter-options">
                        <div class="mb-2">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" name="categoria_cerrados" id="categoriaCerrados">
                                <option value="todos">Todas</option>
                                <?php foreach ($tipos_vehiculo as $tipo): ?>
                                    <option value="<?php echo htmlspecialchars($tipo); ?>" <?php echo (isset($_GET['categoria_cerrados']) && $_GET['categoria_cerrados'] == $tipo) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tipo); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Abierto por</label>
                            <select class="form-select" name="abierto_por" id="abiertoPor" onchange="this.form.submit()">
                                <option value="todos">Todos los Operadores</option>
                                <?php foreach ($operadores as $operador): ?>
                                    <option value="<?php echo htmlspecialchars($operador); ?>" <?php echo (isset($_GET['abierto_por']) && $_GET['abierto_por'] == $operador) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($operador); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Cerrado por</label>
                            <select class="form-select" name="cerrado_por" id="cerradoPor" onchange="this.form.submit()">
                                <option value="todos">Todos los Operadores</option>
                                <?php foreach ($operadores as $operador): ?>
                                    <option value="<?php echo htmlspecialchars($operador); ?>" <?php echo (isset($_GET['cerrado_por']) && $_GET['cerrado_por'] == $operador) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($operador); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Tipo de Tickets</label>
                            <select class="form-select" name="tipo_ticket" id="tipoTicket" onchange="this.form.submit()">
                                <option value="Cerrados" <?php echo (isset($_GET['tipo_ticket']) && $_GET['tipo_ticket'] == 'Cerrados') ? 'selected' : ''; ?>>Cerrados</option>
                                <option value="Cancelados" <?php echo (isset($_GET['tipo_ticket']) && $_GET['tipo_ticket'] == 'Cancelados') ? 'selected' : ''; ?>>Cancelados</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-9 col-md-8">
            <div class="ticket-summary">
                <div class="table-responsive">
                    <?php
                    // Inicializar variable para la suma total
                    $importe_total = 0;
                    
                    // Usar los resultados de la consulta filtrada por categoría
                    if ($resultado_cerrados_categoria && $resultado_cerrados_categoria->num_rows > 0) {
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
                                        <th>Total</th>
                                        <th>Método Pago</th>
                                        <th>Descripción</th>
                                        <th>Abierto por</th>
                                        <th>Cerrado por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($row = $resultado_cerrados_categoria->fetch_assoc()) { 
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
                                        <td>x <?php echo ucfirst($row['tipo_registro'] ?? 'Hora'); ?></td>
                                        <td><?php echo $fecha_ingreso; ?></td>
                                        <td><?php echo $fecha_salida; ?></td>
                                        <td>$<?php echo number_format($row['total_pagado'], 0, ',', '.'); ?></td>
                                        <td><?php 
                                            if ($row['estado'] === 'cancelado') {
                                                echo 'Cancelado';
                                            } else {
                                                echo htmlspecialchars($row['nombre_metodo_pago'] ?? 'No especificado');
                                            }
                                        ?></td>
                                        <td>
                                            <div class="descripcion-scrolleable" title="<?php echo htmlspecialchars($row['descripcion_ticket']); ?>">
                                                <?php echo $row['descripcion_ticket']; ?>
                                            </div>
                                        </td>
                                        <td><?php echo !empty($row['abierto_por']) ? $row['abierto_por'] : 'Sistema'; ?></td>
                                        <td><?php echo !empty($row['cerrado_por']) ? $row['cerrado_por'] : $_SESSION['datos_login']['nombre']; ?></td>
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
                <?php if ($total_paginas_categoria > 1): ?>
                <div class="pagination-container py-0 mb-0">
                    <nav aria-label="Navegación de página">
                        <ul class="pagination justify-content-center mb-0 py-0">
                            <!-- Botón Anterior -->
                            <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="javascript:void(0);" onclick="<?php echo ($pagina_actual <= 1) ? 'return false' : 'cambiarPagina(' . ($pagina_actual - 1) . ')'; ?>" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            
                            <!-- Números de página -->
                            <?php for ($i = 1; $i <= $total_paginas_categoria; $i++): ?>
                                <li class="page-item <?php echo ($pagina_actual == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="javascript:void(0);" onclick="cambiarPagina(<?php echo $i; ?>)"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Botón Siguiente -->
                            <li class="page-item <?php echo ($pagina_actual >= $total_paginas_categoria) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="javascript:void(0);" onclick="<?php echo ($pagina_actual >= $total_paginas_categoria) ? 'return false' : 'cambiarPagina(' . ($pagina_actual + 1) . ')'; ?>" aria-label="Siguiente">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
                
                <!-- Script para la paginación y tooltips -->
                <script>
                function cambiarPagina(pagina) {
                    // Validar que la página sea válida
                    if (pagina < 1 || pagina > <?php echo $total_paginas_categoria; ?>) {
                        return false;
                    }
                    
                    // Crear o actualizar un parámetro en la URL actual
                    const url = new URL(window.location.href);
                    url.searchParams.set('pagina', pagina);
                    
                    // Mantener el operador que abrió el ticket seleccionado
                    const abiertoPorSeleccionado = document.getElementById('abiertoPor').value;
                    url.searchParams.set('abierto_por', abiertoPorSeleccionado);
                    
                    // Mantener el operador que cerró el ticket seleccionado
                    const cerradoPorSeleccionado = document.getElementById('cerradoPor').value;
                    url.searchParams.set('cerrado_por', cerradoPorSeleccionado);
                    
                    // Mantener la categoría seleccionada
                    const categoriaSeleccionada = document.getElementById('categoriaCerrados').value;
                    url.searchParams.set('categoria_cerrados', categoriaSeleccionada);
                    
                    // Mantener la pestaña activa
                    const tabActual = document.querySelector('.nav-link.active').getAttribute('data-tab');
                    url.searchParams.set('tab', tabActual);
                    
                    // Navegar a la nueva URL
                    window.location.href = url.toString();
                }

                // Inicializar tooltips
                document.addEventListener('DOMContentLoaded', function() {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl, {
                            placement: 'top',
                            trigger: 'hover',
                            container: 'body'
                        });
                    });
                });

                // Añadir evento para el nuevo selector de categorías de tickets cerrados
                document.getElementById('categoriaCerrados').addEventListener('change', function() {
                    document.getElementById('filtroCategoriaForm').submit();
                });
                </script>
            </div>
            <div class="summary-stats row mt-2">
                <div class="stat col-6 col-md-3">
                <strong>Cerrados x Mes</strong>
                <span><?php echo $total_cerrados_mes; ?></span>
                </div>
                <div class="stat col-6 col-md-3">
                    <strong>Cancelados x Mes</strong>
                    <span><?php echo $total_cancelados_mes; ?></span>
                </div>
                <div class="stat col-6 col-md-3">
                    <strong>Importe Total Día</strong>
                        <?php foreach ($importe_total_dia as $fecha => $total): ?>
                            <?php 
                            $fecha_actual = date('Y-m-d');
                            
                            if($fecha == $fecha_actual) {
                                echo "$" . number_format($total, 0, ',', '.');
                            }
                            ?>
                        <?php endforeach; ?>
                </div>
                <div class="stat col-6 col-md-3">
                    <strong>Importe Total x Mes</strong>
                    <span>$<?php echo number_format($total_importe_mes, 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>
    </div>
    <!-- Espacio adicional para evitar que el footer oculte contenido -->
    <div class="mb-4"></div>
</div>