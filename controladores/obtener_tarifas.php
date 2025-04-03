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

// Obtener todos los tipos de vehículos
$sqlTiposVehiculo = "SELECT DISTINCT tipo_vehiculo FROM tarifas ORDER BY tipo_vehiculo";
$resultTiposVehiculo = $conexion->query($sqlTiposVehiculo);

if (!$resultTiposVehiculo) {
    echo json_encode(['success' => false, 'error' => 'Error al consultar tipos de vehículos: ' . $conexion->error]);
    exit;
}

$tiposVehiculo = [];
while ($row = $resultTiposVehiculo->fetch_assoc()) {
    $tiposVehiculo[] = $row['tipo_vehiculo'];
}

// Obtener todos los tipos de tolerancia
$sqlTiposTarifa = "SELECT tipo FROM tolerancia ORDER BY tipo";
$resultTiposTarifa = $conexion->query($sqlTiposTarifa);

if (!$resultTiposTarifa) {
    echo json_encode(['success' => false, 'error' => 'Error al consultar tipos de tarifa: ' . $conexion->error]);
    exit;
}

$tiposTarifa = [];
while ($row = $resultTiposTarifa->fetch_assoc()) {
    $tiposTarifa[] = $row['tipo'];
}

// Obtener todas las tarifas existentes
$tarifas = [];

// Para cada tipo de vehículo, obtener sus tarifas
foreach ($tiposVehiculo as $tipoVehiculo) {
    $sql = "SELECT * FROM tarifas WHERE tipo_vehiculo = '$tipoVehiculo'";
    $result = $conexion->query($sql);
    
    if (!$result) {
        echo json_encode(['success' => false, 'error' => 'Error al consultar tarifas: ' . $conexion->error]);
        exit;
    }
    
    if ($result->num_rows > 0) {
        $tarifasVehiculo = $result->fetch_assoc();
        $tarifas[$tipoVehiculo] = $tarifasVehiculo;
    } else {
        // Si no hay tarifas para este vehículo, inicializar con valores por defecto
        $tarifas[$tipoVehiculo] = [
            'tipo_vehiculo' => $tipoVehiculo,
            'id_tarifa' => null
        ];
        
        // Agregar todos los tipos de tarifa con valor 0
        foreach ($tiposTarifa as $tipoTarifa) {
            $tarifas[$tipoVehiculo][strtolower($tipoTarifa)] = 0;
        }
    }
}

// Devolver resultado
echo json_encode([
    'success' => true,
    'tipos_vehiculo' => $tiposVehiculo,
    'tipos_tarifa' => $tiposTarifa,
    'tarifas' => $tarifas
]);

$conexion->close(); 