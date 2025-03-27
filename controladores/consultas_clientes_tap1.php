<?php
// Realiza la consulta a la base de datos
$query = "SELECT id_cliente, nombre, telefono, correo, fecha_registro FROM clientes";
$result = $conexion->query($query);

// Consulta para obtener los tipos de vehículos desde la tabla tarifas
$sql_tipos_vehiculo = "SELECT DISTINCT tipo_vehiculo FROM tarifas";
$resultado_tipos_vehiculo = $conexion->query($sql_tipos_vehiculo);
$tipos_vehiculo = [];
if ($resultado_tipos_vehiculo->num_rows > 0) {
    while ($row = $resultado_tipos_vehiculo->fetch_assoc()) {
        $tipos_vehiculo[] = $row['tipo_vehiculo'];
    }
}

?>