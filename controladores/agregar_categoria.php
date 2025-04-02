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
if (isset($_POST['nombre_categoria']) || isset($_GET['nombre_categoria'])) {
    // Obtener el nombre de la categoría del POST o GET
    $nombreCategoria = isset($_POST['nombre_categoria']) ? $_POST['nombre_categoria'] : $_GET['nombre_categoria'];
    $nombreCategoria = $conexion->real_escape_string($nombreCategoria);
    
    // Verificar que el parámetro no esté vacío
    if (empty($nombreCategoria)) {
        echo json_encode(['success' => false, 'error' => 'El nombre de la categoría no puede estar vacío.']);
        exit;
    }
    
    // Verificar que la tabla existe
    $checkTable = $conexion->query("SHOW TABLES LIKE 'tarifas'");
    if ($checkTable->num_rows == 0) {
        echo json_encode(['success' => false, 'error' => 'La tabla tarifas no existe en la base de datos.']);
        exit;
    }
    
    // Verificar si la categoría ya existe
    $checkCategoria = $conexion->query("SELECT tipo_vehiculo FROM tarifas WHERE tipo_vehiculo = '$nombreCategoria'");
    if ($checkCategoria->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'La categoría ya existe en la base de datos.']);
        exit;
    }

    // Consulta para agregar la nueva categoría
    $sql = "INSERT INTO tarifas (tipo_vehiculo) VALUES ('$nombreCategoria')";
    
    // Ejecutar la consulta
    if ($conexion->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Categoría agregada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta: ' . $conexion->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Nombre de categoría no especificado.']);
}

$conexion->close(); 