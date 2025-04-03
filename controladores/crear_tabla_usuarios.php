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

// Verificar primero si la tabla ya existe
$checkTable = $conexion->query("SHOW TABLES LIKE 'usuarios'");
if ($checkTable->num_rows > 0) {
    ob_end_clean(); // Limpiamos cualquier salida
    echo json_encode(['success' => false, 'error' => 'La tabla usuarios ya existe.']);
    exit;
}

// Consulta SQL para crear la tabla usuarios
$sql = "CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL DEFAULT 'operador',
    fecha_registro DATETIME NOT NULL
)";

// Ejecutar la consulta
if ($conexion->query($sql) === TRUE) {
    // Crear un usuario administrador por defecto
    $nombre = "Administrador";
    $correo = "admin@example.com";
    $contrasena = password_hash("admin123", PASSWORD_DEFAULT);
    $rol = "administrador";
    $fecha_registro = date('Y-m-d H:i:s');
    
    $sql_insert = "INSERT INTO usuarios (nombre, correo, contrasena, rol, fecha_registro) 
                  VALUES ('$nombre', '$correo', '$contrasena', '$rol', '$fecha_registro')";
    
    if ($conexion->query($sql_insert) === TRUE) {
        ob_end_clean(); // Limpiamos cualquier salida
        echo json_encode([
            'success' => true, 
            'message' => 'Tabla usuarios creada con éxito y usuario administrador creado. Correo: admin@example.com, Contraseña: admin123'
        ]);
    } else {
        ob_end_clean(); // Limpiamos cualquier salida
        echo json_encode([
            'success' => true, 
            'message' => 'Tabla usuarios creada con éxito, pero no se pudo crear el usuario administrador: ' . $conexion->error
        ]);
    }
} else {
    ob_end_clean(); // Limpiamos cualquier salida
    echo json_encode(['success' => false, 'error' => 'Error al crear la tabla usuarios: ' . $conexion->error]);
}

$conexion->close();
?> 