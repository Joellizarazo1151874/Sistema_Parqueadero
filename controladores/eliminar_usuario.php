<?php
// Habilitar los mensajes de error para depuración
error_reporting(E_ALL);
ini_set('display_errors', 0); // Desactivamos para evitar que los errores PHP interfieran con el JSON

// Establecer encabezado de respuesta JSON
header('Content-Type: application/json');

// Iniciar buffer de salida para capturar cualquier error o salida inesperada
ob_start();

// Incluir el archivo de conexión
include_once '../modelo/conexion.php';

// Verificar que la conexión esté disponible
if (!isset($conexion) || $conexion->connect_error) {
    ob_end_clean(); // Limpiamos cualquier salida
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos: ' . ($conexion->connect_error ?? 'Conexión no disponible')]);
    exit;
}

// Verificar que se recibió el ID de usuario
if (isset($_POST['id_usuario'])) {
    $id_usuario = $conexion->real_escape_string($_POST['id_usuario']);
    
    // Evitar eliminar un usuario administrador único
    $checkAdmin = $conexion->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'administrador'");
    $adminCount = $checkAdmin->fetch_assoc()['total'];
    
    $checkUserRole = $conexion->query("SELECT rol FROM usuarios WHERE id_usuario = '$id_usuario'");
    
    if ($checkUserRole->num_rows > 0) {
        $userRole = $checkUserRole->fetch_assoc()['rol'];
        
        // Si es el último administrador, no permitir la eliminación
        if ($userRole == 'administrador' && $adminCount <= 1) {
            ob_end_clean(); // Limpiamos cualquier salida
            echo json_encode(['success' => false, 'error' => 'No se puede eliminar el último usuario administrador.']);
            exit;
        }
        
        // Eliminar el usuario
        $sql = "DELETE FROM usuarios WHERE id_usuario = '$id_usuario'";
        
        if ($conexion->query($sql) === TRUE) {
            if ($conexion->affected_rows > 0) {
                ob_end_clean(); // Limpiamos cualquier salida
                echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
            } else {
                ob_end_clean(); // Limpiamos cualquier salida
                echo json_encode(['success' => false, 'error' => 'No se encontró el usuario a eliminar.']);
            }
        } else {
            ob_end_clean(); // Limpiamos cualquier salida
            echo json_encode(['success' => false, 'error' => 'Error al eliminar el usuario: ' . $conexion->error]);
        }
    } else {
        ob_end_clean(); // Limpiamos cualquier salida
        echo json_encode(['success' => false, 'error' => 'No se encontró el usuario a eliminar.']);
    }
} else {
    ob_end_clean(); // Limpiamos cualquier salida
    echo json_encode(['success' => false, 'error' => 'Falta el ID del usuario a eliminar.']);
}

$conexion->close();
?> 