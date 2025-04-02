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
if (isset($_GET['tipo_vehiculo']) && isset($_GET['nuevo_nombre'])) {
    $tipoVehiculo = $conexion->real_escape_string($_GET['tipo_vehiculo']);
    $nuevoNombre = $conexion->real_escape_string($_GET['nuevo_nombre']);
    
    // Verificar que los parámetros no estén vacíos
    if (empty($tipoVehiculo) || empty($nuevoNombre)) {
        echo json_encode(['success' => false, 'error' => 'El tipo de vehículo y el nuevo nombre no pueden estar vacíos.']);
        exit;
    }
    
    // Verificar que la tabla existe
    $checkTable = $conexion->query("SHOW TABLES LIKE 'tarifas'");
    if ($checkTable->num_rows == 0) {
        echo json_encode(['success' => false, 'error' => 'La tabla tarifas no existe en la base de datos.']);
        exit;
    }

    // Consulta para modificar la categoría
    $sql = "UPDATE tarifas SET tipo_vehiculo = '$nuevoNombre' WHERE tipo_vehiculo = '$tipoVehiculo'";
    
    // Ejecutar la consulta
    if ($conexion->query($sql) === TRUE) {
        if ($conexion->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Categoría actualizada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se encontró la categoría para actualizar.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta: ' . $conexion->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos insuficientes para modificar la categoría.']);
}

$conexion->close(); 
