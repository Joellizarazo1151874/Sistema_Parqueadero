<?php
// Incluir archivo de conexión
include '../modelo/conexion.php';

/**
 * Obtiene todos los métodos de pago activos
 * @return array Array con los métodos de pago
 */
function obtenerMetodosPago() {
    global $conexion;
    
    $metodos = array();
    
    $sql = "SELECT id_metodo, nombre FROM metodos_pago WHERE activo = 1 ORDER BY nombre";
    $resultado = $conexion->query($sql);
    
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $metodos[] = $fila;
        }
    }
    
    return $metodos;
}

// Si se llama directamente al script, devolver los métodos en formato JSON
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Content-Type: application/json');
    echo json_encode(obtenerMetodosPago());
}
?>
