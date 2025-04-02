<?php
// Habilitar reportes de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir archivo de conexión
include '../modelo/conexion.php';

// Responder siempre como JSON
header('Content-Type: application/json');

// Verificar si la conexión a la base de datos fue exitosa
if (!$conexion) {
    echo json_encode([
        'success' => false,
        'error' => 'Error de conexión a la base de datos'
    ]);
    exit;
}

// Verificar si se recibió el tipo de tolerancia
if (!isset($_POST['tipo']) || empty($_POST['tipo'])) {
    echo json_encode([
        'success' => false,
        'error' => 'No se especificó un tipo de tolerancia para eliminar'
    ]);
    exit;
}

// Obtener y sanitizar el tipo de tolerancia
$tipo = $conexion->real_escape_string($_POST['tipo']);

// Verificar si el tipo de tolerancia existe
$sql_verificar = "SELECT * FROM tolerancia WHERE tipo = '$tipo'";
$result_verificar = $conexion->query($sql_verificar);

if (!$result_verificar || $result_verificar->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'error' => 'El tipo de tolerancia especificado no existe'
    ]);
    exit;
}

// Eliminar el tipo de tolerancia
$sql_eliminar = "DELETE FROM tolerancia WHERE tipo = '$tipo'";
$result_eliminar = $conexion->query($sql_eliminar);

if ($result_eliminar) {
    echo json_encode([
        'success' => true,
        'message' => "Tipo de tolerancia '$tipo' eliminado correctamente"
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Error al eliminar el tipo de tolerancia: ' . $conexion->error
    ]);
}

// Cerrar la conexión
$conexion->close();
?> 