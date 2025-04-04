<?php
// Desactivar la visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Configuración de conexión
$servidor = "localhost";
$nombreBd = "ginuss_smartpark";
$usuario = "root";
$pass = "";

// Crear la conexión PDO
try {
    $conn = new PDO("mysql:host=$servidor;dbname=$nombreBd;charset=utf8mb4", $usuario, $pass);
    
    // Configurar el modo de error de PDO para que lance excepciones
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar el modo de obtención por defecto (FETCH_ASSOC)
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Opciones adicionales para mejorar rendimiento
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch (PDOException $e) {
    // Registrar error en el log del servidor
    error_log("Error de conexión PDO a la base de datos: " . $e->getMessage());
    
    // No mostrar el error al usuario por seguridad
    $conn = null;
}
?> 