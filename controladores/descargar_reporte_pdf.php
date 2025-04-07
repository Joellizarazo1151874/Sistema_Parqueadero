<?php
session_start();
// Configurar la zona horaria para Colombia
date_default_timezone_set('America/Bogota');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['datos_login'])) {
    header('Location: ../index.php');
    exit;
}

// Verificar si se envió el ID del reporte
if (!isset($_GET['id'])) {
    echo "Error: ID de reporte no especificado";
    exit;
}

$id_reporte = $_GET['id'];

// Incluir la conexión a la base de datos
include '../modelo/conexion.php';

// Obtener la información del reporte
$sql = "SELECT r.*, u.nombre as nombre_operador 
        FROM reportes_caja r 
        JOIN usuarios u ON r.id_operador = u.id_usuario 
        WHERE r.id_reporte = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $id_reporte);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "Error: Reporte no encontrado";
    exit;
}

$reporte = $resultado->fetch_assoc();
$fecha_cierre = date('Y-m-d', strtotime($reporte['fecha_cierre']));
$hora_cierre = date('H:i', strtotime($reporte['fecha_cierre']));
$operador = $reporte['nombre_operador'];
$total_recaudado = $reporte['total_recaudado'];
$detalles = json_decode($reporte['detalles'], true);

// Obtener los tickets del día
$sql_tickets = "SELECT 
                rp.id_registro,
                rp.hora_ingreso,
                rp.hora_salida,
                rp.total_pagado,
                rp.metodo_pago,
                v.placa,
                v.tipo,
                v.descripcion AS descripcion_vehiculo
            FROM 
                registros_parqueo rp
            JOIN 
                vehiculos v ON rp.id_vehiculo = v.id_vehiculo
            WHERE 
                DATE(rp.hora_salida) = ?
                AND rp.estado = 'cerrado'
            ORDER BY 
                rp.hora_salida ASC";
$stmt_tickets = $conexion->prepare($sql_tickets);
$stmt_tickets->bind_param('s', $fecha_cierre);
$stmt_tickets->execute();
$resultado_tickets = $stmt_tickets->get_result();

// Generar el contenido HTML
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
        }
        h1, h2 {
            color: #333;
            text-align: center;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            @page {
                margin: 1cm;
            }
        }
    </style>
</head>
<body>
    <h1>REPORTE DE CIERRE DE CAJA</h1>
    
    <div class="info">
        <p><strong>ID Reporte:</strong> ' . $id_reporte . '</p>
        <p><strong>Fecha de Cierre:</strong> ' . date('d/m/Y', strtotime($fecha_cierre)) . '</p>
        <p><strong>Hora de Cierre:</strong> ' . $hora_cierre . '</p>
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

foreach ($detalles as $metodo => $total) {
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

while ($ticket = $resultado_tickets->fetch_assoc()) {
    $html_content .= '
            <tr>
                <td>' . $ticket['id_registro'] . '</td>
                <td>' . $ticket['placa'] . '</td>
                <td>' . $ticket['tipo'] . '</td>
                <td>' . date('d/m/Y H:i', strtotime($ticket['hora_ingreso'])) . '</td>
                <td>' . date('d/m/Y H:i', strtotime($ticket['hora_salida'])) . '</td>
                <td>' . $ticket['metodo_pago'] . '</td>
                <td class="text-right">$ ' . number_format($ticket['total_pagado'], 0, '', ',') . '</td>
            </tr>';
}

$html_content .= '
        </tbody>
    </table>
    
    <div class="footer">
        <p>Este reporte fue generado automáticamente por el sistema.</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>';

// Guardar el HTML en un archivo temporal
$temp_file = '../reportes/temp_' . $id_reporte . '.html';
file_put_contents($temp_file, $html_content);

// Redirigir al archivo HTML para imprimir
header('Location: ../' . str_replace('../', '', $temp_file));
exit;
?>
