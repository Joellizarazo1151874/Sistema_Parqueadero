<?php
// Habilitar los mensajes de error para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión
include_once '../modelo/conexion.php';

// Establecer el encabezado para respuesta JSON
header('Content-Type: application/json');

// Verificar que la conexión esté disponible
if (!isset($conexion) || $conexion->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos: ' . ($conexion->connect_error ?? 'Conexión no disponible')]);
    exit;
}

// Verificar que se recibieron los datos necesarios
if (!isset($_POST['tipo_vehiculo']) || !isset($_POST['tipo_tarifa']) || !isset($_POST['valor'])) {
    echo json_encode(['success' => false, 'error' => 'Faltan parámetros obligatorios: tipo_vehiculo, tipo_tarifa, valor']);
    exit;
}

$tipoVehiculo = $conexion->real_escape_string($_POST['tipo_vehiculo']);
$tipoTarifa = $conexion->real_escape_string($_POST['tipo_tarifa']);
$valor = floatval($_POST['valor']);

// Validar que el valor sea un número positivo
if ($valor < 0) {
    echo json_encode(['success' => false, 'error' => 'El valor de la tarifa debe ser un número positivo']);
    exit;
}

// Verificar si la tarifa ya existe para ese tipo de vehículo
$sqlCheck = "SELECT id_tarifa FROM tarifas WHERE tipo_vehiculo = '$tipoVehiculo'";
$resultCheck = $conexion->query($sqlCheck);

if (!$resultCheck) {
    echo json_encode(['success' => false, 'error' => 'Error al verificar tarifa existente: ' . $conexion->error]);
    exit;
}

if ($resultCheck->num_rows > 0) {
    // La tarifa existe, actualizarla
    $row = $resultCheck->fetch_assoc();
    $idTarifa = $row['id_tarifa'];
    
    // Preparar la consulta de actualización
    $sql = "UPDATE tarifas SET $tipoTarifa = $valor, fecha_actualizacion = NOW() WHERE id_tarifa = $idTarifa";
    $result = $conexion->query($sql);
    
    if (!$result) {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar la tarifa: ' . $conexion->error]);
        exit;
    }
    
    echo json_encode(['success' => true, 'message' => "Tarifa actualizada para $tipoVehiculo ($tipoTarifa)"]);
} else {
    // La tarifa no existe, crearla
    $sql = "INSERT INTO tarifas (tipo_vehiculo, $tipoTarifa, fecha_actualizacion) VALUES ('$tipoVehiculo', $valor, NOW())";
    $result = $conexion->query($sql);
    
    if (!$result) {
        echo json_encode(['success' => false, 'error' => 'Error al crear la tarifa: ' . $conexion->error]);
        exit;
    }
    
    echo json_encode(['success' => true, 'message' => "Tarifa creada para $tipoVehiculo ($tipoTarifa)"]);
}

$conexion->close(); 