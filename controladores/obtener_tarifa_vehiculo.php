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

// Verificar que se recibió el tipo de vehículo
if (!isset($_GET['tipo_vehiculo'])) {
    echo json_encode(['success' => false, 'error' => 'Tipo de vehículo no especificado']);
    exit;
}

$tipoVehiculo = $conexion->real_escape_string($_GET['tipo_vehiculo']);

// Obtener la tarifa para el tipo de vehículo desde la tabla tarifas
$sql = "SELECT * FROM tarifas WHERE tipo_vehiculo = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta: ' . $conexion->error]);
    exit;
}

$stmt->bind_param("s", $tipoVehiculo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $tarifa = $resultado->fetch_assoc();
    
    // Obtener los tipos de tarifa de la tabla tolerancia
    $sqlTipos = "SELECT tipo FROM tolerancia";
    $resultTipos = $conexion->query($sqlTipos);
    
    if (!$resultTipos) {
        echo json_encode(['success' => false, 'error' => 'Error al consultar tipos de tarifa: ' . $conexion->error]);
        exit;
    }
    
    $tiposTarifa = [];
    while ($rowTipo = $resultTipos->fetch_assoc()) {
        $tipoTarifa = strtolower($rowTipo['tipo']);
        $tiposTarifa[$tipoTarifa] = isset($tarifa[$tipoTarifa]) ? floatval($tarifa[$tipoTarifa]) : 0;
    }
    
    // Responder con las tarifas
    echo json_encode([
        'success' => true,
        'tipo_vehiculo' => $tipoVehiculo,
        'tarifas' => $tiposTarifa
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'No se encontró tarifa para el tipo de vehículo especificado']);
}

$stmt->close();
$conexion->close(); 