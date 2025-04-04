<?php
header('Content-Type: application/json');
include "../modelo/conexion.php";

if ($conexion->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]));
}

try {
    // Consultar los tipos de vehículos desde la tabla tarifas
    $query = "SELECT tipo_vehiculo FROM tarifas";
    $result = $conexion->query($query);
    
    if ($result === false) {
        throw new Exception("Error en la consulta: " . $conexion->error);
    }
    
    $tipos_vehiculos = [];
    while ($row = $result->fetch_assoc()) {
        $tipos_vehiculos[] = $row['tipo_vehiculo'];
    }
    
    echo json_encode($tipos_vehiculos);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
