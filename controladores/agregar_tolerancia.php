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

// Verificar que se recibieron los parámetros necesarios
if (isset($_POST['tipo']) && isset($_POST['tolerancia'])) {
    $tipo = $conexion->real_escape_string($_POST['tipo']);
    $tolerancia = $conexion->real_escape_string($_POST['tolerancia']);
    $tiempo = isset($_POST['tiempo']) ? $conexion->real_escape_string($_POST['tiempo']) : 0;
    
    // Validar que la tolerancia sea un número entero positivo
    if (!is_numeric($tolerancia) || intval($tolerancia) < 0) {
        echo json_encode(['success' => false, 'error' => 'La tolerancia debe ser un número entero positivo.']);
        exit;
    }
    
    // Validar que el tiempo sea un número positivo
    if (!is_numeric($tiempo) || floatval($tiempo) < 0) {
        echo json_encode(['success' => false, 'error' => 'El tiempo debe ser un número positivo.']);
        exit;
    }
    
    // Verificar si el tipo ya existe
    $checkTipo = $conexion->query("SELECT tipo FROM tolerancia WHERE tipo = '$tipo'");
    if ($checkTipo->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Este tipo de tolerancia ya existe. Utilice la función de editar.']);
        exit;
    }
    
    // Consulta para insertar la nueva tolerancia
    $sql = "INSERT INTO tolerancia (tipo, tolerancia, tiempo) VALUES ('$tipo', '$tolerancia', '$tiempo')";
    
    if ($conexion->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Tolerancia agregada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al agregar la tolerancia: ' . $conexion->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Faltan parámetros necesarios para agregar la tolerancia.']);
}

$conexion->close(); 