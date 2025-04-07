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
$sql = "SELECT r.*, v.placa, v.tipo as tipo_vehiculo 
        FROM registros_parqueo r 
        LEFT JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
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
    
    echo json_encode([
        'success' => true,
        'recibo' => $recibo
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Recibo no encontrado o no está cerrado'
    ]);
}

$stmt->close();
$conexion->close();
?>
