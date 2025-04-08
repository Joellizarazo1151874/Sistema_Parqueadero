<?php
session_start();
include '../modelo/conexion.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['datos_login'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Verificar si se recibió el ID del costo adicional
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_costo'])) {
    $id_costo = intval($_POST['id_costo']);
    
    // Eliminar el costo adicional
    $query = "DELETE FROM costos_adicionales WHERE id_costo = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_costo);
    
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Error al eliminar el costo adicional: ' . $conexion->error]);
    }
    
    $stmt->close();
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Se requiere el ID del costo adicional']);
}

$conexion->close();
?> 