<?php
// Habilitar los mensajes de error para depuración
error_reporting(E_ALL);
ini_set('display_errors', 0); // Desactivamos para evitar que los errores PHP interfieran con el JSON

// Establecer encabezado de respuesta JSON
header('Content-Type: application/json');

// Iniciar buffer de salida para capturar cualquier error o salida inesperada
ob_start();

// Registrar errores en el log del servidor
ini_set('log_errors', 1);
error_log("Ejecutando obtener_usuario.php");

// Incluir el archivo de conexión
include_once '../modelo/conexion.php';

// Verificar que la conexión esté disponible
if (!isset($conexion)) {
    error_log("Error: Variable de conexión no definida");
    ob_end_clean(); // Limpiamos cualquier salida
    echo json_encode(['success' => false, 'error' => 'Error: Variable de conexión no definida']);
    exit;
} elseif ($conexion->connect_error) {
    error_log("Error de conexión: " . $conexion->connect_error);
    ob_end_clean(); // Limpiamos cualquier salida
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos: ' . $conexion->connect_error]);
    exit;
}

// Comprobar si la tabla usuarios existe
$checkTable = $conexion->query("SHOW TABLES LIKE 'usuarios'");
if ($checkTable->num_rows == 0) {
    error_log("Error: La tabla 'usuarios' no existe en la base de datos");
    ob_end_clean(); // Limpiamos cualquier salida
    echo json_encode(['success' => false, 'error' => 'La tabla usuarios no existe en la base de datos. Por favor, cree la tabla primero.']);
    exit;
}

try {
    // Si se solicita un usuario específico por ID
    if (isset($_GET['id'])) {
        $id_usuario = $conexion->real_escape_string($_GET['id']);
        $sql = "SELECT id_usuario, nombre, correo, rol, fecha_registro FROM usuarios WHERE id_usuario = '$id_usuario'";
        error_log("Ejecutando consulta: " . $sql);
        
        $result = $conexion->query($sql);
        
        if (!$result) {
            error_log("Error en la consulta: " . $conexion->error);
            ob_end_clean(); // Limpiamos cualquier salida
            echo json_encode(['success' => false, 'error' => 'Error en la consulta: ' . $conexion->error]);
            exit;
        }
        
        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            ob_end_clean(); // Limpiamos cualquier salida
            echo json_encode(['success' => true, 'usuario' => $usuario]);
        } else {
            ob_end_clean(); // Limpiamos cualquier salida
            echo json_encode(['success' => false, 'error' => 'No se encontró el usuario solicitado.']);
        }
    }
    // Si se solicitan todos los usuarios
    else {
        $sql = "SELECT id_usuario, nombre, correo, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC";
        error_log("Ejecutando consulta: " . $sql);
        
        $result = $conexion->query($sql);
        
        if (!$result) {
            error_log("Error en la consulta: " . $conexion->error);
            ob_end_clean(); // Limpiamos cualquier salida
            echo json_encode(['success' => false, 'error' => 'Error en la consulta: ' . $conexion->error]);
            exit;
        }
        
        if ($result->num_rows > 0) {
            $usuarios = [];
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
            ob_end_clean(); // Limpiamos cualquier salida
            echo json_encode(['success' => true, 'usuarios' => $usuarios]);
        } else {
            ob_end_clean(); // Limpiamos cualquier salida
            echo json_encode(['success' => false, 'error' => 'No se encontraron usuarios.']);
        }
    }
} catch (Exception $e) {
    error_log("Excepción capturada: " . $e->getMessage());
    ob_end_clean(); // Limpiamos cualquier salida
    echo json_encode(['success' => false, 'error' => 'Error inesperado: ' . $e->getMessage()]);
}

$conexion->close();
?> 