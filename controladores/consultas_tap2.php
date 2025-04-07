<?php
// Configuración de paginación para tickets cerrados
$registros_por_pagina = 8;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// consulta para contar el total de registros cerrados en el mes actual
$sql_count_cerrados_mes = "SELECT COUNT(*) as total FROM registros_parqueo rp 
              JOIN vehiculos v ON rp.id_vehiculo = v.id_vehiculo 
              WHERE rp.estado = 'cerrado' AND MONTH(rp.hora_salida) = MONTH(CURRENT_DATE()) AND YEAR(rp.hora_salida) = YEAR(CURRENT_DATE())";

$stmt_count_cerrados_mes = $conexion->prepare($sql_count_cerrados_mes);
$stmt_count_cerrados_mes->execute();
$total_cerrados_mes = $stmt_count_cerrados_mes->get_result()->fetch_assoc()['total'];

//consulta para contar el total de registros cancelados en el mes actual
$sql_count_cancelados_mes = "SELECT COUNT(*) as total FROM registros_parqueo rp 
              JOIN vehiculos v ON rp.id_vehiculo = v.id_vehiculo 
              WHERE rp.estado = 'cancelado' AND MONTH(rp.hora_salida) = MONTH(CURRENT_DATE()) AND YEAR(rp.hora_salida) = YEAR(CURRENT_DATE())";

$stmt_count_cancelados_mes = $conexion->prepare($sql_count_cancelados_mes);
$stmt_count_cancelados_mes->execute();
$total_cancelados_mes = $stmt_count_cancelados_mes->get_result()->fetch_assoc()['total'];

// consulta para contar el total de registros cerrados con filtro de categoría
$sql_count_categoria = "SELECT COUNT(*) as total FROM registros_parqueo rp 
              JOIN vehiculos v ON rp.id_vehiculo = v.id_vehiculo 
              WHERE (rp.estado = 'cerrado')";

// Filtrar por estado del ticket si se ha seleccionado
$tipo_ticket = isset($_GET['tipo_ticket']) ? $_GET['tipo_ticket'] : 'Cerrados';
if ($tipo_ticket === 'Cancelados') {
    $sql_count_categoria = str_replace("WHERE (rp.estado = 'cerrado')", "WHERE (rp.estado = 'cancelado')", $sql_count_categoria);
}

// Añadir condiciones de filtrado para categoría cerrados
$params_count_categoria = [];
$tipos_params_count_categoria = "";

if ($categoria_cerrados !== 'todos') {
    $sql_count_categoria .= " AND v.tipo = ?";
    $params_count_categoria[] = $categoria_cerrados;
    $tipos_params_count_categoria .= "s";
}

// Filtrar por operador que abrió el ticket si se ha seleccionado
$abierto_por = isset($_GET['abierto_por']) ? $_GET['abierto_por'] : 'todos';
if ($abierto_por !== 'todos') {
    $sql_count_categoria .= " AND rp.abierto_por = ?";
    $params_count_categoria[] = $abierto_por;
    $tipos_params_count_categoria .= "s";
}

// Filtrar por operador que cerró el ticket si se ha seleccionado
$cerrado_por = isset($_GET['cerrado_por']) ? $_GET['cerrado_por'] : 'todos';
if ($cerrado_por !== 'todos') {
    $sql_count_categoria .= " AND rp.cerrado_por = ?";
    $params_count_categoria[] = $cerrado_por;
    $tipos_params_count_categoria .= "s";
}

$stmt_count_categoria = $conexion->prepare($sql_count_categoria);
if (!empty($params_count_categoria)) {
    $stmt_count_categoria->bind_param($tipos_params_count_categoria, ...$params_count_categoria);
}
$stmt_count_categoria->execute();
$total_registros_categoria = $stmt_count_categoria->get_result()->fetch_assoc()['total'];
$total_paginas_categoria = ceil($total_registros_categoria / $registros_por_pagina);

// Usar $total_paginas_categoria para la paginación congruente
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Nueva consulta para filtrar tickets cerrados por categoría


// Filtrar por estado del ticket si se ha seleccionado
$tipo_ticket = isset($_GET['tipo_ticket']) ? $_GET['tipo_ticket'] : 'Cerrados';
if ($tipo_ticket === 'Cancelados') {
    $sql_cerrados_categoria = "SELECT 
    rp.*, 
    rp.descripcion AS descripcion_ticket, 
    rp.tipo AS tipo_registro,
    v.placa, 
    v.tipo, 
    v.descripcion,
    mp.nombre AS nombre_metodo_pago
FROM 
    registros_parqueo rp 
JOIN 
    vehiculos v ON rp.id_vehiculo = v.id_vehiculo 
LEFT JOIN
    metodos_pago mp ON rp.metodo_pago = mp.id_metodo
WHERE 
    (rp.estado = 'cancelado')";
}else{
    $sql_cerrados_categoria = "SELECT 
    rp.*, 
    rp.descripcion AS descripcion_ticket, 
    rp.tipo AS tipo_registro,
    v.placa, 
    v.tipo, 
    v.descripcion,
    mp.nombre AS nombre_metodo_pago
FROM 
    registros_parqueo rp 
JOIN 
    vehiculos v ON rp.id_vehiculo = v.id_vehiculo 
LEFT JOIN
    metodos_pago mp ON rp.metodo_pago = mp.id_metodo
WHERE 
    (rp.estado = 'cerrado')";
}

// Añadir condiciones de filtrado para categoría cerrados
$params_cerrados_categoria = [];
$tipos_params_cerrados_categoria = "";

if ($categoria_cerrados !== 'todos') {
    $sql_cerrados_categoria .= " AND v.tipo = ?";
    $params_cerrados_categoria[] = $categoria_cerrados;
    $tipos_params_cerrados_categoria .= "s";
}

// Filtrar por operador que abrió el ticket si se ha seleccionado
$abierto_por = isset($_GET['abierto_por']) ? $_GET['abierto_por'] : 'todos';
if ($abierto_por !== 'todos') {
    $sql_cerrados_categoria .= " AND rp.abierto_por = ?";
    $params_cerrados_categoria[] = $abierto_por;
    $tipos_params_cerrados_categoria .= "s";
}

// Filtrar por operador que cerró el ticket si se ha seleccionado
$cerrado_por = isset($_GET['cerrado_por']) ? $_GET['cerrado_por'] : 'todos';
if ($cerrado_por !== 'todos') {
    $sql_cerrados_categoria .= " AND rp.cerrado_por = ?";
    $params_cerrados_categoria[] = $cerrado_por;
    $tipos_params_cerrados_categoria .= "s";
}

$sql_cerrados_categoria .= " ORDER BY rp.hora_salida {$orden} LIMIT ? OFFSET ?";

$stmt_cerrados_categoria = $conexion->prepare($sql_cerrados_categoria);
$params_cerrados_categoria[] = $registros_por_pagina;
$params_cerrados_categoria[] = $offset;
$tipos_params_cerrados_categoria .= "ii";
$stmt_cerrados_categoria->bind_param($tipos_params_cerrados_categoria, ...$params_cerrados_categoria);

$stmt_cerrados_categoria->execute();
$resultado_cerrados_categoria = $stmt_cerrados_categoria->get_result();

// Consulta para obtener los nombres de los operadores
$sql_operadores = "SELECT nombre FROM usuarios";
$resultado_operadores = $conexion->query($sql_operadores);
$operadores = [];
if ($resultado_operadores->num_rows > 0) {
    while ($row = $resultado_operadores->fetch_assoc()) {
        $operadores[] = $row['nombre'];
    }
}

// Consulta para obtener los tipos de vehículos desde la tabla tarifas
$sql_tipos_vehiculo = "SELECT DISTINCT tipo_vehiculo FROM tarifas";
$resultado_tipos_vehiculo = $conexion->query($sql_tipos_vehiculo);
$tipos_vehiculo = [];
if ($resultado_tipos_vehiculo->num_rows > 0) {
    while ($row = $resultado_tipos_vehiculo->fetch_assoc()) {
        $tipos_vehiculo[] = $row['tipo_vehiculo'];
    }
}

// Consulta para calcular el importe total por día
$sql_importe_total_dia = "SELECT DATE(rp.hora_salida) as fecha, SUM(rp.total_pagado) as total_dia FROM registros_parqueo rp 
              WHERE (rp.estado = 'cerrado' OR rp.estado = 'cancelado') 
              GROUP BY DATE(rp.hora_salida)";

$stmt_importe_total_dia = $conexion->prepare($sql_importe_total_dia);
$stmt_importe_total_dia->execute();
$resultado_importe_total_dia = $stmt_importe_total_dia->get_result();
$importe_total_dia = [];
while ($row = $resultado_importe_total_dia->fetch_assoc()) {
    $importe_total_dia[$row['fecha']] = $row['total_dia'];
}

// Consulta para calcular el importe total en el mes actual
$sql_importe_total_mes = "SELECT SUM(rp.total_pagado) as total_mes FROM registros_parqueo rp 
              WHERE (rp.estado = 'cerrado' OR rp.estado = 'cancelado') 
              AND MONTH(rp.hora_salida) = MONTH(CURRENT_DATE()) AND YEAR(rp.hora_salida) = YEAR(CURRENT_DATE())";

$stmt_importe_total_mes = $conexion->prepare($sql_importe_total_mes);
$stmt_importe_total_mes->execute();
$total_importe_mes = $stmt_importe_total_mes->get_result()->fetch_assoc()['total_mes'];

?> 