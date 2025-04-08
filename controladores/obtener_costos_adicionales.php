<?php
session_start();
include '../modelo/conexion.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['datos_login'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Verificar si se recibió el ID del registro
if (isset($_GET['id_registro'])) {
    $id_registro = intval($_GET['id_registro']);
    
    // Consultar los costos adicionales del ticket
    $query = "SELECT id_costo, concepto, valor, fecha_registro 
              FROM costos_adicionales 
              WHERE id_registro = ? 
              ORDER BY fecha_registro DESC";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_registro);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $costos = [];
    $total = 0;
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $costos[] = [
                'id_costo' => $row['id_costo'],
                'concepto' => $row['concepto'],
                'valor' => $row['valor'],
                'fecha_registro' => $row['fecha_registro']
            ];
            $total += floatval($row['valor']);
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'costos' => $costos,
        'total' => $total
    ]);
    
    $stmt->close();
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Se requiere el ID del registro']);
}

$conexion->close();
?> 