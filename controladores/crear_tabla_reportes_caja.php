<?php
// Determinar la ruta correcta según cómo se ejecute el script
$ruta_base = __DIR__ . '/../';
include $ruta_base . 'modelo/conexion.php';

// Crear tabla de reportes de caja si no existe
$sql_crear_tabla = "CREATE TABLE IF NOT EXISTS reportes_caja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_reporte VARCHAR(20) NOT NULL UNIQUE,
    fecha_cierre DATETIME NOT NULL,
    total_recaudado DECIMAL(10,2) NOT NULL,
    id_operador INT NOT NULL,
    estado ENUM('completado', 'anulado') NOT NULL DEFAULT 'completado',
    detalles JSON,
    ruta_pdf VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_operador) REFERENCES usuarios(id_usuario)
)";

if ($conexion->query($sql_crear_tabla) === TRUE) {
    echo "Tabla 'reportes_caja' creada correctamente o ya existía.";
} else {
    echo "Error al crear la tabla: " . $conexion->error;
}

// Crear directorio para reportes si no existe
$dir = $ruta_base . 'reportes/';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
    echo "<br>Directorio para reportes creado correctamente.";
} else {
    echo "<br>El directorio para reportes ya existe.";
}

$conexion->close();
?>
