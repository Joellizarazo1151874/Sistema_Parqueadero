<!-- Mensajes de notificación -->
<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $_SESSION['success']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo $_SESSION['error']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div id="tab1" class="tab-content active">
    <div class="search-bar mb-3">
        <div class="row">
            <div class="col-md-1">
                <select id="tipoVehiculo" name="tipo_vehiculo" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($tipos_vehiculo as $tipo): ?>
                        <option value="<?php echo htmlspecialchars($tipo); ?>" <?php echo (isset($_GET['tipo_vehiculo']) && $_GET['tipo_vehiculo'] == $tipo) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select id="ordenSelector" name="orden" class="form-select">
                    <option value="desc" <?php echo (!isset($_GET['orden']) || $_GET['orden'] == 'desc') ? 'selected' : ''; ?>>Últimos Ingresados</option>
                    <option value="asc" <?php echo (isset($_GET['orden']) && $_GET['orden'] == 'asc') ? 'selected' : ''; ?>>Primeros Ingresados</option>
                </select>
            </div>
            <div class="col-md-2">
                <form method="GET" class="d-flex">
                    <input type="hidden" name="tipo_vehiculo" id="hiddenTipoVehiculo" value="<?php echo isset($_GET['tipo_vehiculo']) ? htmlspecialchars($_GET['tipo_vehiculo']) : ''; ?>">
                    <input type="hidden" name="orden" id="hiddenOrden" value="<?php echo isset($_GET['orden']) ? htmlspecialchars($_GET['orden']) : 'desc'; ?>">
                    <input type="text" id="searchInput" name="busqueda" class="form-control" placeholder="Buscar matrícula" value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>">
                    <?php if (!empty($_GET['busqueda']) || !empty($_GET['tipo_vehiculo']) || (isset($_GET['orden']) && $_GET['orden'] == 'asc')): ?>
                        <button type="button" id="clearSearchBtn" class="btn btn-secondary ms-2">
                            <i class="fas fa-times"></i>
                        </button>
                    <?php endif; ?>
                </form>
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
                
                // Inicializar la variable tipo_registro para todos los tickets
                $tipo_registro = isset($row['tipo_registro']) ? $row['tipo_registro'] : 'hora';
        ?>
                <div class="ticket">
                    <div class="row" style="background-color: rgb(174, 213, 255); padding: 15px;">
                        <div class="col-3">
                            <div class="time-box">
                                <i class="feather icon-clock"></i>
                                <span><?php echo ucfirst($tipo_registro); ?></span>
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
                                <?php 
                                    // Obtener la tarifa por hora para este tipo de vehículo
                                    $tipo_vehiculo_actual = $row['tipo'];
                                    $tarifa_hora = 0;
                                    $tarifa_dia = 0;
                                    $tolerancia_minutos = 15; // Valor por defecto
                                    
                                    // Obtener tarifa correspondiente
                                    if (isset($tarifas_por_tipo[$tipo_vehiculo_actual])) {
                                        // Obtener la tarifa según el tipo de registro (hora, día, etc.)
                                        if (isset($tarifas_por_tipo[$tipo_vehiculo_actual][$tipo_registro])) {
                                            $tarifa_valor = floatval($tarifas_por_tipo[$tipo_vehiculo_actual][$tipo_registro]);
                                        }
                                        
                                        // También guardar las tarifas de hora y día para referencia
                                        if (isset($tarifas_por_tipo[$tipo_vehiculo_actual]['hora'])) {
                                            $tarifa_hora = floatval($tarifas_por_tipo[$tipo_vehiculo_actual]['hora']);
                                        }
                                        if (isset($tarifas_por_tipo[$tipo_vehiculo_actual]['dia'])) {
                                            $tarifa_dia = floatval($tarifas_por_tipo[$tipo_vehiculo_actual]['dia']);
                                        }
                                    }
                                    
                                    // Obtener tolerancia según el tipo de registro
                                    if (isset($tolerancias_por_tipo[$tipo_registro])) {
                                        $tolerancia_minutos = $tolerancias_por_tipo[$tipo_registro];
                                    }
                                    
                                    // Obtener el tiempo en horas según el tipo de registro
                                    $tiempo_horas = 1; // Valor predeterminado: 1 hora
                                    // Consultar el tiempo configurado para este tipo de registro
                                    $sql_tiempo = "SELECT tiempo FROM tolerancia WHERE tipo = '$tipo_registro'";
                                    $result_tiempo = $conexion->query($sql_tiempo);
                                    if ($result_tiempo && $result_tiempo->num_rows > 0) {
                                        $row_tiempo = $result_tiempo->fetch_assoc();
                                        $tiempo_horas = floatval($row_tiempo['tiempo']);
                                    }
                                    
                                    // Calcular costo por minuto basado en el tipo de registro
                                    $costo_por_minuto = 0;
                                    if ($tipo_registro == 'hora' && $tarifa_hora > 0) {
                                        $costo_por_minuto = $tarifa_hora / 60;
                                    } elseif ($tipo_registro == 'dia' && $tarifa_dia > 0) {
                                        $costo_por_minuto = $tarifa_dia / (24 * 60); // Costo por minuto basado en día
                                    } elseif (isset($tarifa_valor) && $tarifa_valor > 0) {
                                        // Si es otro tipo, usamos el valor específico y el tiempo configurado
                                        if ($tiempo_horas > 0) {
                                            // Convertir tiempo en horas a minutos
                                            $tiempo_minutos = $tiempo_horas * 60;
                                            $costo_por_minuto = $tarifa_valor / $tiempo_minutos;
                                        } else {
                                            // Fallback a los valores por defecto
                                            if ($tipo_registro == 'mes') {
                                                $costo_por_minuto = $tarifa_valor / (30 * 24 * 60);
                                            } elseif ($tipo_registro == 'semana') {
                                                $costo_por_minuto = $tarifa_valor / (7 * 24 * 60);
                                            } else {
                                                // Un caso predeterminado para tipos desconocidos
                                                $costo_por_minuto = $tarifa_valor / 60;
                                            }
                                        }
                                    }
                                ?>
                                <b class="importe-actual"
                                    data-ingreso="<?php echo $timestamp_ingreso; ?>"
                                    data-costo-por-minuto="<?php echo $costo_por_minuto; ?>"
                                    data-tolerancia="<?php echo $tolerancia_minutos; ?>"
                                    data-tarifa-hora="<?php echo $tarifa_hora; ?>"
                                    data-tarifa-dia="<?php echo $tarifa_dia; ?>"
                                    data-tipo-registro="<?php echo $tipo_registro; ?>"
                                    data-tiempo-horas="<?php echo $tiempo_horas; ?>">
                                    Calculando...
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
                                    data-costo-por-minuto="<?php echo $costo_por_minuto; ?>"
                                    data-tolerancia="<?php echo $tolerancia_minutos; ?>"
                                    data-tarifa-hora="<?php echo $tarifa_hora; ?>"
                                    data-tarifa-dia="<?php echo $tarifa_dia; ?>"
                                    data-tipo-registro="<?php echo $tipo_registro; ?>"
                                    data-tiempo-horas="<?php echo $tiempo_horas; ?>">
                                    Calculando...
                                </b>
                            </div>
                        </div>
                    </div>
                    <div class="ticket-actions">
                        <div class="icon-btn-group">
                            <button class="icon-btn editar-ticket" 
                                   data-id="<?php echo $row['id_registro']; ?>"
                                   data-id-vehiculo="<?php echo $row['id_vehiculo']; ?>"
                                   data-placa="<?php echo $row['placa']; ?>"
                                   data-tipo="<?php echo $row['tipo']; ?>"
                                   data-descripcion="<?php echo isset($row['descripcion']) ? htmlspecialchars($row['descripcion']) : ''; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="icon-btn"><i class="fas fa-print"></i></button>
                            <button class="icon-btn cancelar-ticket" 
                                   data-id="<?php echo $row['id_registro']; ?>"
                                   data-placa="<?php echo $row['placa']; ?>">
                                <i class="fas fa-file-alt"></i>
                            </button>
                        </div>
                        <button class="close-btn cerrar-ticket"
                            data-id="<?php echo $row['id_registro']; ?>"
                            data-ingreso="<?php echo $timestamp_ingreso; ?>"
                            data-costo-por-minuto="<?php echo $costo_por_minuto; ?>"
                            data-placa="<?php echo $row['placa']; ?>"
                            data-tipo="<?php echo strtoupper($row['tipo']); ?>"
                            data-tolerancia="<?php echo $tolerancia_minutos; ?>"
                            data-tolerancia-tipo="<?php echo $tipo_registro; ?>"
                            data-tipo-registro="<?php echo $tipo_registro; ?>"
                            data-tarifa-hora="<?php echo $tarifa_hora; ?>"
                            data-tarifa-dia="<?php echo $tarifa_dia; ?>"
                            data-tarifa-valor="<?php echo isset($tarifa_valor) ? $tarifa_valor : 0; ?>"
                            data-tiempo-horas="<?php echo $tiempo_horas; ?>"
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
            if (!empty($_GET['busqueda']) || !empty($_GET['tipo_vehiculo'])) {
                $mensaje = 'No se encontraron vehículos';
                
                if (!empty($_GET['busqueda'])) {
                    $mensaje .= ' con la matrícula "' . htmlspecialchars($_GET['busqueda']) . '"';
                }
                
                if (!empty($_GET['tipo_vehiculo'])) {
                    $tipo_texto = '';
                    switch($_GET['tipo_vehiculo']) {
                        case 'auto': $tipo_texto = 'carro'; break;
                        case 'moto': $tipo_texto = 'moto'; break;
                        case 'camioneta': $tipo_texto = 'camioneta'; break;
                        case 'motocarro': $tipo_texto = 'motocarro'; break;
                    }
                    
                    if (!empty($_GET['busqueda'])) {
                        $mensaje .= ' del tipo ' . $tipo_texto;
                    } else {
                        $mensaje .= ' del tipo ' . $tipo_texto;
                    }
                }
                
                echo '<div class="alert alert-warning">' . $mensaje . '</div>';
            } else {
                echo '<div class="alert alert-info">No hay vehículos estacionados actualmente.</div>';
            }
        }
        ?>
    </div>

    <!-- Espacio adicional para evitar que el footer oculte contenido -->
    <div class="mb-4"></div>
</div>

<!-- Modal de Edición -->
<div class="modal fade" id="modalEdicion" tabindex="-1" aria-labelledby="modalEdicionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEdicionVehiculo" action="../../controladores/editar_vehiculo.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEdicionLabel">Editar vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editar_id_vehiculo" name="id_vehiculo">
                    <input type="hidden" id="editar_id_registro" name="id_registro">
                    
                    <div class="mb-3">
                        <label for="editar_placa" class="form-label">Matrícula</label>
                        <input type="text" class="form-control" id="editar_placa" name="placa" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editar_tipo" class="form-label">Tipo de vehículo</label>
                        <select class="form-select" id="editar_tipo" name="tipo" required>
                            <option value="auto">Carro</option>
                            <option value="moto">Moto</option>
                            <option value="camioneta">Camioneta</option>
                            <option value="motocarro">Motocarro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editar_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="editar_descripcion" name="descripcion" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Cancelación -->
<div class="modal fade" id="modalCancelacion" tabindex="-1" aria-labelledby="modalCancelacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formCancelacionTicket" action="../../controladores/cancelar_ticket.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCancelacionLabel">Cancelar Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cancelar_id_registro" name="id_registro">
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Está a punto de cancelar el ticket <strong id="cancelar_placa"></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="motivo_cancelacion" class="form-label">Motivo de cancelación</label>
                        <textarea class="form-control" id="motivo_cancelacion" name="motivo_cancelacion" rows="3" placeholder="Indique el motivo de la cancelación del ticket"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar cancelación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si hay mensajes de éxito en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');

        if (success && success === '1') {
            Swal.fire({
                title: '¡Éxito!',
                text: 'El ingreso del vehículo ha sido registrado correctamente',
                icon: 'success',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true
            });
        }

        console.log('DOM cargado completamente');
        
        // Detectar si hay tickets en la página
        const tickets = document.querySelectorAll('.ticket');
        console.log('Tickets encontrados:', tickets.length);
        
        // Mostrar información de los tickets para depuración
        tickets.forEach((ticket, index) => {
            const ticketHeader = ticket.querySelector('.ticket-header h3');
            const ticketPlate = ticket.querySelector('.plate');
            const closeButton = ticket.querySelector('.cerrar-ticket');
            
            if (closeButton) {
                const tipoRegistro = closeButton.getAttribute('data-tolerancia-tipo');
                const tipo = closeButton.getAttribute('data-tipo');
                
                console.log(`Ticket #${index + 1}:`, {
                    vehiculo: ticketHeader ? ticketHeader.textContent : 'N/A',
                    placa: ticketPlate ? ticketPlate.textContent : 'N/A',
                    tipo: tipo || 'N/A',
                    tipoRegistro: tipoRegistro || 'N/A',
                    tarifa: {
                        hora: closeButton.getAttribute('data-tarifa-hora'),
                        dia: closeButton.getAttribute('data-tarifa-dia')
                    }
                });
            }
        });
    });
</script>
