<?php
session_start();
include '../modelo/conexion.php';
include '../modelo/conexion_pdo.php';

// Configurar la zona horaria para Colombia
date_default_timezone_set('America/Bogota');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['datos_login'])) {
    header('Location: ../index.php');
    exit;
}

// Verificar si se enviaron los datos necesarios
if (!isset($_POST['fecha_cierre']) || !isset($_POST['hora_cierre'])) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$fecha_cierre = $_POST['fecha_cierre'];
$hora_cierre = $_POST['hora_cierre'];
$operador = $_SESSION['datos_login']['nombre'];
$id_operador = $_SESSION['datos_login']['id_usuario'];

// Verificar si ya existe un reporte para esta fecha y hora exacta
$sql_verificar = "SELECT id_reporte FROM reportes_caja WHERE DATE(fecha_cierre) = ? AND TIME(fecha_cierre) = ? AND estado = 'completado'";
$stmt_verificar = $conexion->prepare($sql_verificar);
$hora_completa = $hora_cierre . ':00';
$stmt_verificar->bind_param('ss', $fecha_cierre, $hora_completa);
$stmt_verificar->execute();
$resultado_verificar = $stmt_verificar->get_result();

if ($resultado_verificar->num_rows > 0) {
    // Ya existe un reporte para esta fecha y hora exacta
    header('Location: ../vistas/Estructuras/caja.php?tab=tab3&error=reporte_existente_misma_hora');
    exit;
}

// Obtener los ingresos del día agrupados por método de pago
$sql_ingresos = "SELECT 
                    rp.metodo_pago, 
                    COALESCE(mp.nombre, rp.metodo_pago) as nombre_metodo,
                    SUM(rp.total_pagado) as total 
                FROM 
                    registros_parqueo rp
                LEFT JOIN
                    metodos_pago mp ON rp.metodo_pago = mp.id_metodo
                WHERE 
                    DATE(rp.hora_salida) = ? 
                    AND rp.estado = 'cerrado' 
                    AND (rp.reportado IS NULL OR rp.reportado = 0)
                GROUP BY 
                    rp.metodo_pago, nombre_metodo";

// Para depuración
$fecha_debug = $fecha_cierre;
$sql_debug = "SELECT id_registro, placa, hora_ingreso, hora_salida, total_pagado, metodo_pago, estado, reportado 
             FROM registros_parqueo rp
             JOIN vehiculos v ON rp.id_vehiculo = v.id_vehiculo
             WHERE DATE(hora_salida) = '$fecha_debug' 
             AND estado = 'cerrado'
             AND (rp.reportado IS NULL OR rp.reportado = 0)";
$resultado_debug = $conexion->query($sql_debug);
$tickets_encontrados = $resultado_debug->num_rows;

// Verificar si hay tickets cerrados en el sistema (independientemente de la fecha)
$sql_total_tickets = "SELECT COUNT(*) as total FROM registros_parqueo WHERE estado = 'cerrado'";
$resultado_total = $conexion->query($sql_total_tickets);
$row_total = $resultado_total->fetch_assoc();
$total_tickets_cerrados = $row_total['total'];

// Si no hay tickets cerrados para esta fecha, mostrar un mensaje
if ($tickets_encontrados == 0) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>No se encontraron tickets</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                line-height: 1.6;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            h2 {
                color: #d9534f;
            }
            .btn {
                display: inline-block;
                padding: 8px 15px;
                background-color: #337ab7;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                margin-top: 20px;
            }
            .debug-info {
                background-color: #f8f9fa;
                border: 1px solid #ddd;
                padding: 15px;
                margin-top: 20px;
                font-family: monospace;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>No se encontraron tickets cerrados para la fecha: $fecha_cierre</h2>
            <p>Verifica que hayas cerrado tickets en esta fecha y que la <strong>fecha de salida</strong> corresponda a la fecha seleccionada.</p>
            
            <div class='debug-info'>
                <h3>Información de depuración:</h3>
                <p>Fecha seleccionada: $fecha_cierre</p>
                <p>Hora seleccionada: $hora_cierre</p>
                <p>Consulta ejecutada: $sql_debug</p>
                <p>Tickets encontrados: $tickets_encontrados</p>
                <p>Tickets cerrados en el sistema: $total_tickets_cerrados</p>
                
                <h4>Tickets cerrados en el sistema:</h4>";
                
    // Mostrar algunos tickets cerrados para referencia
    $sql_algunos_tickets = "SELECT id_registro, placa, DATE(hora_ingreso) as fecha_ingreso, 
                           DATE(hora_salida) as fecha_salida, total_pagado, estado 
                           FROM registros_parqueo rp
                           JOIN vehiculos v ON rp.id_vehiculo = v.id_vehiculo
                           WHERE estado = 'cerrado'
                           ORDER BY hora_salida DESC LIMIT 5";
    $resultado_algunos = $conexion->query($sql_algunos_tickets);
    
    if ($resultado_algunos->num_rows > 0) {
        echo "<table border='1' style='width: 100%; border-collapse: collapse;'>
              <tr>
                <th>ID</th>
                <th>Placa</th>
                <th>Fecha Ingreso</th>
                <th>Fecha Salida</th>
                <th>Total</th>
              </tr>";
        
        while ($row = $resultado_algunos->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id_registro']}</td>
                <td>{$row['placa']}</td>
                <td>{$row['fecha_ingreso']}</td>
                <td>{$row['fecha_salida']}</td>
                <td>\${$row['total_pagado']}</td>
              </tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No hay tickets cerrados en el sistema.</p>";
    }
    
    echo "
            </div>
            
            <a href='../vistas/Estructuras/caja.php?tab=tab3' class='btn'>Volver a Cierre de Caja</a>
        </div>
    </body>
    </html>";
    exit;
}

$stmt_ingresos = $conexion->prepare($sql_ingresos);
$stmt_ingresos->bind_param('s', $fecha_cierre);
$stmt_ingresos->execute();
$resultado_ingresos = $stmt_ingresos->get_result();

// Calcular el total recaudado
$total_recaudado = 0;
$ingresos_por_metodo = array();

while ($fila = $resultado_ingresos->fetch_assoc()) {
    $metodo = $fila['nombre_metodo']; // Usar el nombre del método en lugar del ID
    $total = $fila['total'];
    $ingresos_por_metodo[$metodo] = $total;
    $total_recaudado += $total;
}

// Obtener el detalle de todos los tickets cerrados en el día
$sql_tickets = "SELECT 
                rp.id_registro, 
                rp.hora_ingreso, 
                rp.hora_salida, 
                rp.total_pagado, 
                rp.metodo_pago,
                COALESCE(mp.nombre, 'Efectivo') as nombre_metodo,
                v.placa,
                v.tipo,
                v.descripcion AS descripcion_vehiculo
              FROM 
                registros_parqueo rp
              JOIN 
                vehiculos v ON rp.id_vehiculo = v.id_vehiculo
              LEFT JOIN
                metodos_pago mp ON rp.metodo_pago = mp.id_metodo
              WHERE 
                DATE(rp.hora_salida) = ? 
                AND rp.estado = 'cerrado' 
                AND (rp.reportado IS NULL OR rp.reportado = 0)
              ORDER BY 
                rp.hora_salida ASC";

$stmt_tickets = $conexion->prepare($sql_tickets);
$stmt_tickets->bind_param('s', $fecha_cierre);
$stmt_tickets->execute();
$resultado_tickets = $stmt_tickets->get_result();

// Generar un ID único para el reporte
$id_reporte = uniqid('REP-');

// Guardar el reporte en la base de datos
$fecha_hora_cierre = $fecha_cierre . ' ' . $hora_cierre . ':00';
$sql_guardar = "INSERT INTO reportes_caja (
                    id_reporte,
                    fecha_cierre,
                    total_recaudado,
                    id_operador,
                    estado,
                    detalles
                ) VALUES (?, ?, ?, ?, 'completado', ?)";

$detalles_json = json_encode($ingresos_por_metodo);
$stmt_guardar = $conexion->prepare($sql_guardar);
$stmt_guardar->bind_param('ssdis', $id_reporte, $fecha_hora_cierre, $total_recaudado, $id_operador, $detalles_json);

if ($stmt_guardar->execute()) {
    // Crear directorio para reportes si no existe
    $dir = '../reportes/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    // Marcar los tickets incluidos en este reporte como reportados
    $sql_marcar_reportados = "UPDATE registros_parqueo 
                             SET reportado = 1, id_reporte = ? 
                             WHERE DATE(hora_salida) = ? 
                             AND estado = 'cerrado' 
                             AND (reportado IS NULL OR reportado = 0)";
    $stmt_marcar = $conexion->prepare($sql_marcar_reportados);
    $stmt_marcar->bind_param('ss', $id_reporte, $fecha_cierre);
    $stmt_marcar->execute();

    // En lugar de generar un PDF, crearemos un archivo HTML con los datos
    $html_content = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Cierre de Caja</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                line-height: 1.6;
                color: #333;
            }
            h1, h2 {
                color: #333;
                text-align: center;
                margin-bottom: 20px;
            }
            .info {
                margin-bottom: 30px;
                border: 1px solid #ddd;
                padding: 15px;
                border-radius: 5px;
                background-color: #f9f9f9;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
                box-shadow: 0 2px 3px rgba(0,0,0,0.1);
            }
            th, td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            tr:hover {
                background-color: #f1f1f1;
            }
            .total {
                font-weight: bold;
                background-color: #e9ecef;
            }
            .text-right {
                text-align: right;
            }
            .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 0.9em;
                color: #777;
                border-top: 1px solid #ddd;
                padding-top: 15px;
            }
            @media print {
                body {
                    margin: 0;
                    padding: 15px;
                }
                .no-print {
                    display: none;
                }
            }
        </style>
    </head>
    <body>
        <h1>REPORTE DE CIERRE DE CAJA</h1>
        
        <div class="info">
            <p><strong>ID Reporte:</strong> ' . $id_reporte . '</p>
            <p><strong>Fecha de Cierre:</strong> ' . date('d/m/Y', strtotime($fecha_hora_cierre)) . '</p>
            <p><strong>Hora de Cierre:</strong> ' . date('H:i', strtotime($fecha_hora_cierre)) . '</p>
            <p><strong>Operador:</strong> ' . $operador . '</p>
        </div>
        
        <h2>Resumen de Ingresos</h2>
        <table>
            <thead>
                <tr>
                    <th>Método de Pago</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($ingresos_por_metodo as $metodo => $total) {
        $html_content .= '
                <tr>
                    <td>' . $metodo . '</td>
                    <td class="text-right">$ ' . number_format($total, 0, '', ',') . '</td>
                </tr>';
    }

    $html_content .= '
                <tr class="total">
                    <td>TOTAL RECAUDADO</td>
                    <td class="text-right">$ ' . number_format($total_recaudado, 0, '', ',') . '</td>
                </tr>
            </tbody>
        </table>
        
        <h2>Detalle de Tickets</h2>
        <table>
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Placa</th>
                    <th>Tipo</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>Método Pago</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>';

    // Reiniciar el puntero del resultado para volver a recorrer los tickets
    mysqli_data_seek($resultado_tickets, 0);
    
    // Variable para verificar si hay tickets
    $hay_tickets = false;
    
    while ($ticket = $resultado_tickets->fetch_assoc()) {
        $hay_tickets = true;
        
        // Asegurarse de que el método de pago tenga un valor válido
        $metodo_pago = !empty($ticket['nombre_metodo']) ? $ticket['nombre_metodo'] : 'Efectivo';
        
        // Formatear las fechas correctamente
        $fecha_ingreso = date('d/m/Y', strtotime($ticket['hora_ingreso']));
        $hora_ingreso = date('H:i', strtotime($ticket['hora_ingreso']));
        $fecha_salida = date('d/m/Y', strtotime($ticket['hora_salida']));
        $hora_salida = date('H:i', strtotime($ticket['hora_salida']));
        
        $html_content .= '
                <tr>
                    <td>' . $ticket['id_registro'] . '</td>
                    <td>' . htmlspecialchars($ticket['placa']) . '</td>
                    <td>' . htmlspecialchars($ticket['tipo']) . '</td>
                    <td>' . $fecha_ingreso . ' ' . $hora_ingreso . '</td>
                    <td>' . $fecha_salida . ' ' . $hora_salida . '</td>
                    <td>' . htmlspecialchars($metodo_pago) . '</td>
                    <td class="text-right">$ ' . number_format($ticket['total_pagado'], 0, '', ',') . '</td>
                </tr>';
    }
    
    // Si no hay tickets, mostrar un mensaje
    if (!$hay_tickets) {
        $html_content .= '
                <tr>
                    <td colspan="7" style="text-align: center;">No se encontraron tickets para este reporte</td>
                </tr>';
    }

    $html_content .= '
            </tbody>
        </table>
        
        <div class="footer">
            <p>Este reporte fue generado automáticamente por el sistema.</p>
        </div>
    </body>
    </html>';

    // Guardar el HTML
    $html_filename = $dir . 'reporte_caja_' . $fecha_cierre . '_' . str_replace(':', '', $hora_cierre) . '.html';
    file_put_contents($html_filename, $html_content);

    // Actualizar la ruta del archivo en la base de datos
    $ruta_relativa = 'reportes/reporte_caja_' . $fecha_cierre . '_' . str_replace(':', '', $hora_cierre) . '.html';
    $sql_actualizar = "UPDATE reportes_caja SET ruta_pdf = ? WHERE id_reporte = ?";
    $stmt_actualizar = $conexion->prepare($sql_actualizar);
    $stmt_actualizar->bind_param('ss', $ruta_relativa, $id_reporte);
    $stmt_actualizar->execute();

    // Redireccionar con mensaje de éxito
    header('Location: ../vistas/Estructuras/caja.php?tab=tab3&success=reporte_generado&id=' . $id_reporte);
} else {
    // Error al guardar el reporte
    echo "Error al guardar el reporte: " . $stmt_guardar->error;
}
exit;
?>
