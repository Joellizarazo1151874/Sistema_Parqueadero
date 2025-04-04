<?php
// Desactivar la visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Configuración de conexión
$servidor = "localhost";
$nombreBd = "ginuss_smartpark";
$usuario = "root";
$pass = "";

// Crear la conexión
try {
    $conexion = new mysqli($servidor, $usuario, $pass, $nombreBd);
    
    // Verificar si hay error de conexión
    if ($conexion->connect_error) {
        // Registrar error en el log del servidor
        error_log("Error de conexión a la base de datos: " . $conexion->connect_error);
        
        // No lanzar excepciones ni mostrar errores, dejamos que los controladores se encarguen
    } else {
	// Establecer el conjunto de caracteres a UTF-8
        $conexion->set_charset("utf8mb4");
    }
} catch (Exception $e) {
    // Registrar error en el log del servidor
    error_log("Excepción en la conexión a la base de datos: " . $e->getMessage());
    
    // No lanzar excepciones ni mostrar errores, dejamos que los controladores se encarguen
}
?>