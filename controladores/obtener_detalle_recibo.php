<?php
// Configurar la zona horaria para Colombia
date_default_timezone_set('America/Bogota');
header('Content-Type: application/json');

// Incluir conexión a la base de datos
include '../modelo/conexion.php';

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de recibo no proporcionado'
    ]);
    exit;
}

$id_recibo = intval($_GET['id']);

// Consultar los detalles del recibo
$sql = "SELECT r.*, v.placa, v.tipo as tipo_vehiculo, mp.nombre as nombre_metodo_pago
        FROM registros_parqueo r 
        LEFT JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
        LEFT JOIN metodos_pago mp ON r.metodo_pago = mp.id_metodo
        WHERE r.id_registro = ? AND r.estado = 'cerrado'";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_recibo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows > 0) {
    $recibo = $resultado->fetch_assoc();
    
    // Calcular tiempo de estancia
    $hora_ingreso = new DateTime($recibo['hora_ingreso']);
    $hora_salida = new DateTime($recibo['hora_salida']);
    $diferencia = $hora_ingreso->diff($hora_salida);
    
    // Formatear tiempo de estancia
    $tiempo_estancia = '';
    if ($diferencia->days > 0) {
        $tiempo_estancia .= $diferencia->days . 'd ';
    }
    $tiempo_estancia .= $diferencia->h . 'h ' . $diferencia->i . 'm';
    
    // Formatear fechas para mostrar
    $hora_ingreso_formateada = $hora_ingreso->format('d/m/Y H:i');
    $hora_salida_formateada = $hora_salida->format('d/m/Y H:i');
    
    // Agregar los campos formateados al array de recibo
    $recibo['tiempo_estancia'] = $tiempo_estancia;
    $recibo['hora_ingreso_formateada'] = $hora_ingreso_formateada;
    $recibo['hora_salida_formateada'] = $hora_salida_formateada;
    $recibo['metodo_pago'] = $recibo['nombre_metodo_pago'] ?? $recibo['metodo_pago'];
    
    // Obtener los costos adicionales asociados al registro
    $sql_costos = "SELECT * FROM costos_adicionales WHERE id_registro = ?";
    $stmt_costos = $conexion->prepare($sql_costos);
    $stmt_costos->bind_param("i", $id_recibo);
    $stmt_costos->execute();
    $resultado_costos = $stmt_costos->get_result();
    
    $costos_adicionales = [];
    $total_adicionales = 0;
    
    if ($resultado_costos && $resultado_costos->num_rows > 0) {
        while ($costo = $resultado_costos->fetch_assoc()) {
            $costos_adicionales[] = [
                'concepto' => $costo['concepto'],
                'valor' => $costo['valor'],
                'valor_formateado' => '$' . number_format($costo['valor'], 0, '', ',')
            ];
            $total_adicionales += floatval($costo['valor']);
        }
    }
    
    // Calcular costo de estacionamiento (total - adicionales)
    $costo_estacionamiento = floatval($recibo['total_pagado']) - $total_adicionales;
    
    // Agregar los costos adicionales y el desglose al array de recibo
    $recibo['costos_adicionales'] = $costos_adicionales;
    $recibo['total_adicionales'] = $total_adicionales;
    $recibo['total_adicionales_formateado'] = '$' . number_format($total_adicionales, 0, '', ',');
    $recibo['costo_estacionamiento'] = $costo_estacionamiento;
    $recibo['costo_estacionamiento_formateado'] = '$' . number_format($costo_estacionamiento, 0, '', ',');
    
    echo json_encode([
        'success' => true,
        'recibo' => $recibo
    ]);
    
    $stmt_costos->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Recibo no encontrado o no está cerrado'
    ]);
}

$stmt->close();
$conexion->close();
?>
