<div id="tab1" class="tab-content active">
    <div class="search-bar mb-3">
        <div class="row">
            <div class="col-md-1">
                <select class="form-select">
                    <option value="todos">Todos</option>
                    <option value="orden">Orden</option>
                </select>
            </div>
            <div class="col-md-2">

                <select class="form-select">
                    <option value="ultimos">Últimos Ingresados</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" id="searchInput" name="busqueda" class="form-control" placeholder="Buscar matrícula" value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>">
            </div>
        </div>
    </div>
    <div class="tickets-container">
        <?php
        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                // Calcular el tiempo transcurrido
                $hora_ingreso = new DateTime($row['hora_ingreso']);
                $fecha_formateada = $hora_ingreso->format('d/n, h:i a'); // Formato: "19/3, 04:53 p. m."
                $timestamp_ingreso = strtotime($row['hora_ingreso']); // Convertir a timestamp UNIX
                $timestamp_actual = time(); // Obtiene el tiempo actual
        ?>
                <div class="ticket">
                    <div class="row" style="background-color: rgb(174, 213, 255); padding: 15px;">
                        <div class="col-3">
                            <div class="time-box">
                                <i class="feather icon-clock"></i>
                                <span>HORA</span>
                            </div>
                        </div>
                        <div class="col-9">
                            <div class="ticket-header">
                                <h3><?php echo strtoupper($row['tipo']); ?></h3>
                                <p class="plate"><?php echo $row['placa']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="ticket-content">
                        <div class="row">
                            <div class="col-6 text-start">
                                <small>ENTRADA</small>
                                <br>
                                <b><?php echo $fecha_formateada; ?></b>
                                <br>
                                <small>IMPORTE ACTUAL</small>
                                <br>
                                <b class="importe-actual"
                                    data-ingreso="<?php echo $timestamp_ingreso; ?>"
                                    data-costo-por-minuto="<?php echo (2000 / 60); ?>">
                                    $<?php echo number_format($costo, 0, ",", "."); ?>
                                </b>
                            </div>
                            <div class="col-6 text-end">
                                <small>TIEMPO</small>
                                <br>
                                <b class="tiempo-transcurrido" data-ingreso="<?php echo $timestamp_ingreso; ?>">Calculando...</b>
                                <br>
                                <small class="debt">DEBE</small>
                                <br>
                                <b class="debt importe-actual"
                                    data-ingreso="<?php echo $timestamp_ingreso; ?>"
                                    data-costo-por-minuto="<?php echo (2000 / 60); ?>">
                                    $<?php echo number_format($costo, 0, ",", "."); ?>
                                </b>
                            </div>
                        </div>
                    </div>
                    <div class="ticket-actions">
                        <div class="icon-btn-group">
                            <button class="icon-btn"><i class="fas fa-edit"></i></button>
                            <button class="icon-btn"><i class="fas fa-print"></i></button>
                            <button class="icon-btn"><i class="fas fa-file-alt"></i></button>
                        </div>
                        <button class="close-btn cerrar-ticket"
                            data-id="<?php echo $row['id_registro']; ?>"
                            data-ingreso="<?php echo $timestamp_ingreso; ?>"
                            data-costo-por-minuto="<?php echo (2000 / 60); ?>"
                            data-placa="<?php echo $row['placa']; ?>"
                            data-tipo="<?php echo strtoupper($row['tipo']); ?>"
                            data-tiempo="<?php echo floor($minutos_transcurridos / 60) . 'h ' . ($minutos_transcurridos % 60) . 'm'; ?>"
                            data-total-pagado="<?php echo $row['total_pagado']; ?>"
                            data-metodo-pago="<?php echo $row['metodo_pago']; ?>">
                            Cerrar
                        </button>
                    </div>
                </div>
                <!-- Modal de Pago -->
                <div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="../../controladores/cierre_ticket.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalPagoLabel">CAJA</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="ticket-info mb-3">
                                        <h6 id="ticketInfo"></h6>
                                    </div>
                                    <div class="amount text-center mb-4">
                                        <h6>Importe:</h6>
                                        <h3><b id="modalCosto">$0</b></h3> <!-- Aquí se mostrará el costo actualizado -->
                                    </div>
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">Seleccione la forma de pago</label>
                                            <select class="form-select" id="metodoPago" name="metodo_pago">
                                                <option value="efectivo">Efectivo</option>
                                                <option value="tarjeta_credito">Tarjeta de Crédito</option>
                                                <option value="tarjeta_debito">Tarjeta de Débito</option>
                                                <option value="mercadopago">MercadoPago</option>
                                            </select>
                                        </div>
                                        <label class="form-label">Descripción (Caja)</label>
                                        <input type="text" class="form-control" id="modalDescripcion" name="descripcion">
                                        <input type="hidden" class="form-control" id="total_pagado" name="total_pagado"> 
                                        <input type="hidden" class="form-control" id="id_ticket" name="id_registro">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Items</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="modalItems" readonly>
                                        </div>
                                    </div>

                                    <div class="card mb-2">
                                        <div class="card-body py-2">
                                            <small class="text-muted d-block mb-2">Impresión de Comprobante</small>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="comprobante" id="sinComprobante" value="sin" checked>
                                                <label class="form-check-label" for="sinComprobante">Sin Comprobante</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="comprobante" id="recibo" value="recibo">
                                                <label class="form-check-label" for="recibo">Recibo</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Cobrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<div class="alert alert-info">No hay vehículos estacionados actualmente.</div>';
        }
        ?>
    </div>

    <!-- Espacio adicional para evitar que el footer oculte contenido -->
    <div class="mb-4"></div>
</div>