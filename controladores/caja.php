<?php
require_once '../modelo/conexion.php';
session_start();

// Manejo de peticiones AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'obtener_tickets':
            $tickets = obtenerTicketsActivos();
            echo json_encode($tickets);
            break;

        case 'obtener_resumen':
            $resumen = obtenerResumenCaja();
            echo json_encode($resumen);
            break;

        case 'obtener_metodos_pago':
            $metodos = obtenerMetodosPago();
            echo json_encode($metodos);
            break;

        case 'actualizar_metodo_pago':
            $id_registro = $_POST['id_registro'] ?? 0;
            $metodo_pago = $_POST['metodo_pago'] ?? '';
            $resultado = actualizarMetodoPago($id_registro, $metodo_pago);
            echo json_encode(['success' => $resultado]);
            break;

        case 'obtener_detalle_ticket':
            $id_registro = $_POST['id_registro'] ?? 0;
            $detalle = obtenerDetalleTicket($id_registro);
            echo json_encode($detalle);
            break;

        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
}

// Funciones para manejar la caja
function obtenerTicketsActivos() {
    global $conexion;
    
    $query = "SELECT r.*, v.placa, v.id_cliente, v.tipo as tipo_vehiculo, r.tipo as tipo_tiempo, 
              TIMESTAMPDIFF(HOUR, r.hora_ingreso, NOW()) as horas_transcurridas,
              TIMESTAMPDIFF(MINUTE, r.hora_ingreso, NOW()) % 60 as minutos_transcurridos
              FROM registros_parqueo r 
              LEFT JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo 
              WHERE r.estado = 'activo' 
              ORDER BY r.hora_ingreso DESC";
    
    $resultado = $conexion->query($query);
    $tickets = [];
    
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            // Agregar el nombre del cliente si existe
            if (!empty($fila['id_cliente'])) {
                $query_cliente = "SELECT nombre FROM clientes WHERE id_cliente = " . $fila['id_cliente'];
                $res_cliente = $conexion->query($query_cliente);
                if ($res_cliente && $res_cliente->num_rows > 0) {
                    $cliente = $res_cliente->fetch_assoc();
                    $fila['nombre_cliente'] = $cliente['nombre'];
                } else {
                    $fila['nombre_cliente'] = 'N/A';
                }
            } else {
                $fila['nombre_cliente'] = 'N/A';
            }
            
            $tickets[] = $fila;
        }
    }
    
    return $tickets;
}

function obtenerResumenCaja() {
    global $conexion;
    
    // Contar tickets activos
    $query_activos = "SELECT COUNT(*) as total_tickets FROM registros_parqueo WHERE estado = 'activo'";
    $resultado_activos = $conexion->query($query_activos);
    $total_tickets = 0;
    
    if ($resultado_activos && $resultado_activos->num_rows > 0) {
        $fila = $resultado_activos->fetch_assoc();
        $total_tickets = $fila['total_tickets'];
    }
    
    // Obtener totales por método de pago para el día actual
    $query_totales = "SELECT 
                SUM(CASE WHEN metodo_pago = 'Efectivo' OR metodo_pago = '1' THEN total_pagado ELSE 0 END) as total_efectivo,
                SUM(CASE WHEN metodo_pago = 'Tarjeta' OR metodo_pago = '2' THEN total_pagado ELSE 0 END) as total_tarjeta,
                SUM(CASE WHEN metodo_pago = 'Transferencia' OR metodo_pago = '3' THEN total_pagado ELSE 0 END) as total_transferencia
             FROM registros_parqueo 
             WHERE DATE(hora_salida) = CURDATE() AND estado = 'cerrado'";
    
    $resultado_totales = $conexion->query($query_totales);
    $totales = [
        'total_tickets' => $total_tickets,
        'total_efectivo' => 0,
        'total_tarjeta' => 0,
        'total_transferencia' => 0
    ];
    
    if ($resultado_totales && $resultado_totales->num_rows > 0) {
        $fila = $resultado_totales->fetch_assoc();
        $totales['total_efectivo'] = intval($fila['total_efectivo']);
        $totales['total_tarjeta'] = intval($fila['total_tarjeta']);
        $totales['total_transferencia'] = intval($fila['total_transferencia']);
    }
    
    return $totales;
}

function obtenerMetodosPago() {
    global $conexion;
    
    $query = "SELECT * FROM metodos_pago WHERE activo = 1";
    $resultado = $conexion->query($query);
    $metodos = [];
    
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $metodos[] = $fila;
        }
    }
    
    return $metodos;
}

function actualizarMetodoPago($id_registro, $metodo_pago) {
    global $conexion;
    
    // Convertir el ID del método de pago a texto
    $nombre_metodo = '';
    switch ($metodo_pago) {
        case '1': $nombre_metodo = 'Efectivo'; break;
        case '2': $nombre_metodo = 'Tarjeta'; break;
        case '3': $nombre_metodo = 'Transferencia'; break;
    }
    
    if (empty($nombre_metodo)) {
        return false;
    }
    
    $query = "UPDATE registros_parqueo 
             SET metodo_pago = ? 
             WHERE id_registro = ?";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('si', $nombre_metodo, $id_registro);
    $resultado = $stmt->execute();
    $stmt->close();
    
    return $resultado;
}

function obtenerDetalleTicket($id_registro) {
    global $conexion;
    
    $query = "SELECT r.*, v.placa, v.id_cliente, v.tipo as tipo_vehiculo, r.tipo as tipo_tiempo,
                    TIMESTAMPDIFF(HOUR, r.hora_ingreso, NOW()) as horas_transcurridas,
                    TIMESTAMPDIFF(MINUTE, r.hora_ingreso, NOW()) % 60 as minutos_transcurridos
             FROM registros_parqueo r 
             LEFT JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo 
             WHERE r.id_registro = ?";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $id_registro);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado && $resultado->num_rows > 0) {
        $detalle = $resultado->fetch_assoc();
        
        // Agregar el nombre del cliente si existe
        if (!empty($detalle['id_cliente'])) {
            $query_cliente = "SELECT nombre FROM clientes WHERE id_cliente = ?";
            $stmt_cliente = $conexion->prepare($query_cliente);
            $stmt_cliente->bind_param('i', $detalle['id_cliente']);
            $stmt_cliente->execute();
            $res_cliente = $stmt_cliente->get_result();
            
            if ($res_cliente && $res_cliente->num_rows > 0) {
                $cliente = $res_cliente->fetch_assoc();
                $detalle['nombre_cliente'] = $cliente['nombre'];
            } else {
                $detalle['nombre_cliente'] = 'N/A';
            }
            
            $stmt_cliente->close();
        } else {
            $detalle['nombre_cliente'] = 'N/A';
        }
        
        $stmt->close();
        return $detalle;
    }
    
    $stmt->close();
    return null;
}
?>