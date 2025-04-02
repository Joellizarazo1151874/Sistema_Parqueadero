<?php
// Habilitar los mensajes de error para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión
include_once '../modelo/conexion.php';

// Verificar que la conexión esté disponible
if (!isset($conexion) || $conexion->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos: ' . ($conexion->connect_error ?? 'Conexión no disponible')]);
    exit;
}

// Procesar la solicitud
if (isset($_GET['tipo_vehiculo'])) {
    $tipoVehiculo = $conexion->real_escape_string($_GET['tipo_vehiculo']);
    
    // Verificar que el parámetro no esté vacío
    if (empty($tipoVehiculo)) {
        echo json_encode(['success' => false, 'error' => 'El tipo de vehículo no puede estar vacío.']);
        exit;
    }
    
    // Verificar que la tabla existe
    $checkTable = $conexion->query("SHOW TABLES LIKE 'tarifas'");
    if ($checkTable->num_rows == 0) {
        echo json_encode(['success' => false, 'error' => 'La tabla tarifas no existe en la base de datos.']);
        exit;
    }

    // Consulta para eliminar la categoría
    $sql = "DELETE FROM tarifas WHERE tipo_vehiculo = '$tipoVehiculo'";
    
    // Ejecutar la consulta
    if ($conexion->query($sql) === TRUE) {
        if ($conexion->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Categoría eliminada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se encontró la categoría para eliminar.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta: ' . $conexion->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Tipo de vehículo no especificado.']);
}

$conexion->close(); 

