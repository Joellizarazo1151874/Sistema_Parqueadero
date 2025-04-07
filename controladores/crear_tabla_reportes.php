<?php
// Incluir la conexión a la base de datos
require_once '../modelo/conexion.php';

// Verificar si la tabla ya existe
$check_table = $conexion->query("SHOW TABLES LIKE 'reportes_caja'");
if ($check_table->num_rows > 0) {
    echo "La tabla reportes_caja ya existe en la base de datos.";
    exit;
}

// Crear la tabla reportes_caja
$sql_crear_tabla = "CREATE TABLE reportes_caja (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    fecha_hora DATETIME NOT NULL,
    total_recaudado DECIMAL(10,2) NOT NULL,
    tickets_cerrados INT NOT NULL DEFAULT 0,
    id_operador INT,
    observaciones TEXT,
    FOREIGN KEY (id_operador) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conexion->query($sql_crear_tabla) === TRUE) {
    echo "Tabla reportes_caja creada correctamente.";
    
    // Insertar algunos datos de ejemplo
    $fecha_actual = date('Y-m-d H:i:s');
    $fecha_ayer = date('Y-m-d H:i:s', strtotime('-1 day'));
    $fecha_anteayer = date('Y-m-d H:i:s', strtotime('-2 day'));
    
    $sql_insertar = "INSERT INTO reportes_caja (fecha_hora, total_recaudado, tickets_cerrados, id_operador, observaciones) VALUES 
    ('$fecha_actual', 250000, 15, 1, 'Cierre de caja del día'),
    ('$fecha_ayer', 180000, 12, 1, 'Cierre de caja normal'),
    ('$fecha_anteayer', 320000, 20, 1, 'Día con alta afluencia')";
    
    if ($conexion->query($sql_insertar) === TRUE) {
        echo "<br>Datos de ejemplo insertados correctamente.";
    } else {
        echo "<br>Error al insertar datos de ejemplo: " . $conexion->error;
    }
} else {
    echo "Error al crear la tabla reportes_caja: " . $conexion->error;
}

$conexion->close();
?>
