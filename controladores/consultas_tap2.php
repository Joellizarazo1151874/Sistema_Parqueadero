<?php
// Definir el número de registros por página
$registros_por_pagina = 7;

// Consulta para obtener el total de registros para la paginación
$query_total = "SELECT COUNT(*) as total FROM registros_parqueo r 
                WHERE r.estado = 'cerrado'";
$resultado_total = $conexion->query($query_total);
$fila_total = $resultado_total->fetch_assoc();
$total_registros = $fila_total['total'];

// Calcular el total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Determinar la página actual (por defecto es 1)
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

// Validar que la página actual sea válida
if ($pagina_actual < 1) {
    $pagina_actual = 1;
} elseif ($total_paginas > 0 && $pagina_actual > $total_paginas) {
    $pagina_actual = $total_paginas;
}

// Calcular el offset para la consulta SQL
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta para obtener los registros cerrados con la información del vehículo (con límite para paginación)
$query_cerrados = "SELECT r.*, v.placa, v.tipo
                 FROM registros_parqueo r 
                 INNER JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
                 WHERE r.estado = 'cerrado' 
                 ORDER BY r.hora_salida DESC
                 LIMIT $offset, $registros_por_pagina";

$resultado_cerrados = $conexion->query($query_cerrados);
?> 