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

// Verificar que se recibió el tipo
if (!isset($_GET['tipo'])) {
    // Si no se especifica un tipo, devolver todas las tolerancias
    $sql = "SELECT tipo, tolerancia FROM tolerancia";
    $result = $conexion->query($sql);
    
    if (!$result) {
        echo json_encode(['success' => false, 'error' => 'Error al consultar tolerancias: ' . $conexion->error]);
        exit;
    }
    
    $tolerancias = [];
    while ($row = $result->fetch_assoc()) {
        $tolerancias[$row['tipo']] = intval($row['tolerancia']);
    }
    
    echo json_encode(['success' => true, 'tolerancias' => $tolerancias]);
    exit;
}

$tipo = $conexion->real_escape_string($_GET['tipo']);

// Obtener la tolerancia para el tipo especificado
$sql = "SELECT tolerancia FROM tolerancia WHERE tipo = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta: ' . $conexion->error]);
    exit;
}

$stmt->bind_param("s", $tipo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    echo json_encode([
        'success' => true,
        'tipo' => $tipo,
        'tolerancia' => intval($row['tolerancia'])
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'No se encontró tolerancia para el tipo especificado']);
}

$stmt->close();
$conexion->close(); 