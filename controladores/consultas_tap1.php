<?php
// Consulta para obtener los registros activos con la información del vehículo
$query = "SELECT r.*, v.placa, v.tipo 
FROM registros_parqueo r 
INNER JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
WHERE r.estado = 'activo' 
ORDER BY r.hora_ingreso DESC";

$resultado = $conexion->query($query);
?>