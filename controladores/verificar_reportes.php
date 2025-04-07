<?php
// Configurar la zona horaria para Colombia
date_default_timezone_set('America/Bogota');

// Incluir conexión a la base de datos
include '../modelo/conexion.php';

// Verificar la estructura de la tabla reportes_caja
echo "<h2>Verificación de la tabla reportes_caja</h2>";

// 1. Verificar si la tabla existe
$sql_check_table = "SHOW TABLES LIKE 'reportes_caja'";
$result_check_table = $conexion->query($sql_check_table);

if ($result_check_table->num_rows == 0) {
    echo "<p style='color:red'>Error: La tabla 'reportes_caja' no existe en la base de datos.</p>";
    exit;
}

echo "<p style='color:green'>La tabla 'reportes_caja' existe en la base de datos.</p>";

// 2. Verificar la estructura de la tabla
$sql_describe = "DESCRIBE reportes_caja";
$result_describe = $conexion->query($sql_describe);

echo "<h3>Estructura de la tabla:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Llave</th><th>Default</th><th>Extra</th></tr>";

while ($row = $result_describe->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}

echo "</table>";

// 3. Verificar si hay datos en la tabla
$sql_count = "SELECT COUNT(*) as total FROM reportes_caja";
$result_count = $conexion->query($sql_count);
$row_count = $result_count->fetch_assoc();

echo "<h3>Cantidad de reportes:</h3>";
echo "<p>Total de reportes en la tabla: " . $row_count['total'] . "</p>";

// 4. Verificar reportes de hoy
$sql_today = "SELECT COUNT(*) as total FROM reportes_caja WHERE DATE(fecha_cierre) = CURDATE()";
$result_today = $conexion->query($sql_today);
$row_today = $result_today->fetch_assoc();

echo "<p>Reportes generados hoy (" . date('Y-m-d') . "): " . $row_today['total'] . "</p>";

// 5. Mostrar algunos reportes de ejemplo
$sql_sample = "SELECT * FROM reportes_caja ORDER BY fecha_cierre DESC LIMIT 5";
$result_sample = $conexion->query($sql_sample);

echo "<h3>Últimos 5 reportes:</h3>";

if ($result_sample->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Fecha Cierre</th><th>Total Recaudado</th><th>ID Operador</th></tr>";
    
    while ($row = $result_sample->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_reporte'] . "</td>";
        echo "<td>" . $row['fecha_cierre'] . " (" . date('Y-m-d', strtotime($row['fecha_cierre'])) . ")</td>";
        echo "<td>" . $row['total_recaudado'] . "</td>";
        echo "<td>" . $row['id_operador'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No hay reportes disponibles.</p>";
}

// 6. Probar la consulta de reportes por fecha específica
$fecha_prueba = date('Y-m-d');
$sql_test = "SELECT COUNT(*) as total FROM reportes_caja WHERE DATE(fecha_cierre) = '$fecha_prueba'";
$result_test = $conexion->query($sql_test);
$row_test = $result_test->fetch_assoc();

echo "<h3>Prueba de consulta por fecha:</h3>";
echo "<p>Consulta: $sql_test</p>";
echo "<p>Reportes para la fecha $fecha_prueba: " . $row_test['total'] . "</p>";

// 7. Verificar si hay reportes para otras fechas
$sql_other_dates = "SELECT DATE(fecha_cierre) as fecha, COUNT(*) as total 
                    FROM reportes_caja 
                    GROUP BY DATE(fecha_cierre) 
                    ORDER BY fecha DESC";
$result_other_dates = $conexion->query($sql_other_dates);

echo "<h3>Reportes por fecha:</h3>";

if ($result_other_dates->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Fecha</th><th>Cantidad de Reportes</th></tr>";
    
    while ($row = $result_other_dates->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['fecha'] . "</td>";
        echo "<td>" . $row['total'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No hay reportes disponibles.</p>";
}

$conexion->close();
?>
