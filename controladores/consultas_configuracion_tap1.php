<?php
    // Consulta para obtener los tipos de vehículos desde la tabla tarifa
    $sql = "SELECT DISTINCT tipo_vehiculo FROM tarifas";
     $result = $conexion->query($sql);
?>