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

// Verificar que se recibieron los parámetros necesarios
if (isset($_POST['id_usuario']) && isset($_POST['nombre']) && isset($_POST['correo']) && isset($_POST['rol'])) {
    $id_usuario = $conexion->real_escape_string($_POST['id_usuario']);
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $correo = $conexion->real_escape_string($_POST['correo']);
    $rol = $conexion->real_escape_string($_POST['rol']);
    
    // Validar campos
    if (empty($id_usuario) || empty($nombre) || empty($correo) || empty($rol)) {
        ob_end_clean(); // Limpiamos cualquier salida
        echo json_encode(['success' => false, 'error' => 'Los campos nombre, correo y rol son obligatorios.']);
        exit;
    }
    
    // Validar formato de correo electrónico
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        ob_end_clean(); // Limpiamos cualquier salida
        echo json_encode(['success' => false, 'error' => 'El formato del correo electrónico no es válido.']);
        exit;
    }
    
    // Verificar si el correo ya existe para otro usuario
    $checkCorreo = $conexion->query("SELECT id_usuario FROM usuarios WHERE correo = '$correo' AND id_usuario != '$id_usuario'");
    if ($checkCorreo->num_rows > 0) {
        ob_end_clean(); // Limpiamos cualquier salida
        echo json_encode(['success' => false, 'error' => 'Este correo electrónico ya está registrado por otro usuario. Por favor use otro.']);
        exit;
    }
    
    // Iniciar la consulta SQL
    $sql = "UPDATE usuarios SET nombre = '$nombre', correo = '$correo', rol = '$rol'";
    
    // Si se proporcionó una nueva contraseña, actualizarla
    if (isset($_POST['contrasena']) && !empty($_POST['contrasena'])) {
        $contrasena = $_POST['contrasena'];
        $contrasena_cifrada = password_hash($contrasena, PASSWORD_DEFAULT);
        $sql .= ", contrasena = '$contrasena_cifrada'";
    }
    
    // Completar la consulta
    $sql .= " WHERE id_usuario = '$id_usuario'";
    
    if ($conexion->query($sql) === TRUE) {
        // Verificar si se actualizó algún registro
        if ($conexion->affected_rows > 0) {
            ob_end_clean(); // Limpiamos cualquier salida
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente.']);
        } else {
            ob_end_clean(); // Limpiamos cualquier salida
            echo json_encode(['success' => false, 'error' => 'No se encontró el usuario o no se realizaron cambios.']);
        }
    } else {
        ob_end_clean(); // Limpiamos cualquier salida
        echo json_encode(['success' => false, 'error' => 'Error al actualizar el usuario: ' . $conexion->error]);
    }
} else {
    ob_end_clean(); // Limpiamos cualquier salida
    echo json_encode(['success' => false, 'error' => 'Faltan parámetros necesarios para actualizar el usuario.']);
}

$conexion->close();
?> 