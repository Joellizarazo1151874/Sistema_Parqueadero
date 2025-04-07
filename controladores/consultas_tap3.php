<?php
// Configuración de paginación
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Obtener la fecha seleccionada del calendario
$fecha_seleccionada = isset($_GET['fecha']) ? $_GET['fecha'] : null;

// Obtener la matrícula ingresada
$matricula = isset($_GET['matricula']) ? '%' . $_GET['matricula'] . '%' : '';

// Obtener el Ticket ID ingresado
$ticket_id = isset($_GET['Ticketid']) ? $_GET['Ticketid'] : '';

// Obtener el Detalle ingresado
$detalle = isset($_GET['Detalle']) ? '%' . $_GET['Detalle'] . '%' : '';

// Filtro para mostrar solo tickets no reportados
$solo_no_reportados = isset($_GET['no_reportados']) && $_GET['no_reportados'] == '1';

// Consulta para contar el total de registros
$sql_count = "SELECT COUNT(*) as total FROM registros_parqueo rp JOIN vehiculos v ON rp.id_vehiculo = v.id_vehiculo WHERE rp.estado IN ('activo', 'cerrado')";
if ($fecha_seleccionada) {
    $sql_count .= " AND DATE(rp.hora_ingreso) = ?";
}
if ($matricula) {
    $sql_count .= " AND v.placa LIKE ?";
}
if ($ticket_id) {
    $sql_count .= " AND rp.id_registro = ?";
}
if ($detalle) {
    $sql_count .= " AND v.descripcion LIKE ?";
}
if ($solo_no_reportados) {
    $sql_count .= " AND (rp.reportado IS NULL OR rp.reportado = 0)";
}
$stmt_count = $conexion->prepare($sql_count);
if ($fecha_seleccionada && $matricula && $ticket_id && $detalle && $solo_no_reportados) {
    $stmt_count->bind_param('ssss', $fecha_seleccionada, $matricula, $ticket_id, $detalle);
} elseif ($fecha_seleccionada && $matricula && $ticket_id && $solo_no_reportados) {
    $stmt_count->bind_param('sss', $fecha_seleccionada, $matricula, $ticket_id);
} elseif ($fecha_seleccionada && $matricula && $detalle && $solo_no_reportados) {
    $stmt_count->bind_param('sss', $fecha_seleccionada, $matricula, $detalle);
} elseif ($fecha_seleccionada && $ticket_id && $detalle && $solo_no_reportados) {
    $stmt_count->bind_param('sss', $fecha_seleccionada, $ticket_id, $detalle);
} elseif ($matricula && $ticket_id && $detalle && $solo_no_reportados) {
    $stmt_count->bind_param('sss', $matricula, $ticket_id, $detalle);
} elseif ($fecha_seleccionada && $matricula && $solo_no_reportados) {
    $stmt_count->bind_param('ss', $fecha_seleccionada, $matricula);
} elseif ($fecha_seleccionada && $ticket_id && $solo_no_reportados) {
    $stmt_count->bind_param('ss', $fecha_seleccionada, $ticket_id);
} elseif ($fecha_seleccionada && $detalle && $solo_no_reportados) {
    $stmt_count->bind_param('ss', $fecha_seleccionada, $detalle);
} elseif ($matricula && $ticket_id && $solo_no_reportados) {
    $stmt_count->bind_param('ss', $matricula, $ticket_id);
} elseif ($matricula && $detalle && $solo_no_reportados) {
    $stmt_count->bind_param('ss', $matricula, $detalle);
} elseif ($ticket_id && $detalle && $solo_no_reportados) {
    $stmt_count->bind_param('ss', $ticket_id, $detalle);
} elseif ($fecha_seleccionada && $solo_no_reportados) {
    $stmt_count->bind_param('s', $fecha_seleccionada);
} elseif ($matricula && $solo_no_reportados) {
    $stmt_count->bind_param('s', $matricula);
} elseif ($ticket_id && $solo_no_reportados) {
    $stmt_count->bind_param('s', $ticket_id);
} elseif ($detalle && $solo_no_reportados) {
    $stmt_count->bind_param('s', $detalle);
} elseif ($solo_no_reportados) {
    $stmt_count->bind_param('', '');
} elseif ($fecha_seleccionada && $matricula && $ticket_id && $detalle) {
    $stmt_count->bind_param('ssss', $fecha_seleccionada, $matricula, $ticket_id, $detalle);
} elseif ($fecha_seleccionada && $matricula && $ticket_id) {
    $stmt_count->bind_param('sss', $fecha_seleccionada, $matricula, $ticket_id);
} elseif ($fecha_seleccionada && $matricula && $detalle) {
    $stmt_count->bind_param('sss', $fecha_seleccionada, $matricula, $detalle);
} elseif ($fecha_seleccionada && $ticket_id && $detalle) {
    $stmt_count->bind_param('sss', $fecha_seleccionada, $ticket_id, $detalle);
} elseif ($matricula && $ticket_id && $detalle) {
    $stmt_count->bind_param('sss', $matricula, $ticket_id, $detalle);
} elseif ($fecha_seleccionada && $matricula) {
    $stmt_count->bind_param('ss', $fecha_seleccionada, $matricula);
} elseif ($fecha_seleccionada && $ticket_id) {
    $stmt_count->bind_param('ss', $fecha_seleccionada, $ticket_id);
} elseif ($fecha_seleccionada && $detalle) {
    $stmt_count->bind_param('ss', $fecha_seleccionada, $detalle);
} elseif ($matricula && $ticket_id) {
    $stmt_count->bind_param('ss', $matricula, $ticket_id);
} elseif ($matricula && $detalle) {
    $stmt_count->bind_param('ss', $matricula, $detalle);
} elseif ($ticket_id && $detalle) {
    $stmt_count->bind_param('ss', $ticket_id, $detalle);
} elseif ($fecha_seleccionada) {
    $stmt_count->bind_param('s', $fecha_seleccionada);
} elseif ($matricula) {
    $stmt_count->bind_param('s', $matricula);
} elseif ($ticket_id) {
    $stmt_count->bind_param('s', $ticket_id);
} elseif ($detalle) {
    $stmt_count->bind_param('s', $detalle);
}
$stmt_count->execute();
$total_registros = $stmt_count->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta para obtener registros con estados 'activo' y 'cerrado', ordenados por `hora_ingreso`, incluyendo la descripción del vehículo
$sql_tickets_activos_cerrados = "SELECT 
    rp.id_registro, 
    rp.id_vehiculo, 
    rp.hora_ingreso, 
    rp.hora_salida, 
    rp.estado, 
    rp.total_pagado, 
    rp.metodo_pago, 
    v.tipo, 
    v.descripcion AS descripcion_vehiculo, 
    v.placa,
    rp.cerrado_por, 
    rp.abierto_por,
    mp.nombre AS nombre_metodo_pago,
    rp.reportado,
    rp.id_reporte
FROM 
    registros_parqueo rp 
JOIN 
    vehiculos v ON rp.id_vehiculo = v.id_vehiculo 
LEFT JOIN
    metodos_pago mp ON rp.metodo_pago = mp.id_metodo
WHERE 
    rp.estado IN ('activo', 'cerrado')";
if ($fecha_seleccionada) {
    $sql_tickets_activos_cerrados .= " AND DATE(rp.hora_ingreso) = ?";
}
if ($matricula) {
    $sql_tickets_activos_cerrados .= " AND v.placa LIKE ?";
}
if ($ticket_id) {
    $sql_tickets_activos_cerrados .= " AND rp.id_registro = ?";
}
if ($detalle) {
    $sql_tickets_activos_cerrados .= " AND v.descripcion LIKE ?";
}
if ($solo_no_reportados) {
    $sql_tickets_activos_cerrados .= " AND (rp.reportado IS NULL OR rp.reportado = 0)";
}
$sql_tickets_activos_cerrados .= " ORDER BY rp.hora_ingreso DESC LIMIT ? OFFSET ?";

$stmt_tickets_activos_cerrados = $conexion->prepare($sql_tickets_activos_cerrados);
if ($fecha_seleccionada && $matricula && $ticket_id && $detalle && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('ssssii', $fecha_seleccionada, $matricula, $ticket_id, $detalle, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $matricula && $ticket_id && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('sssii', $fecha_seleccionada, $matricula, $ticket_id, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $matricula && $detalle && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('sssii', $fecha_seleccionada, $matricula, $detalle, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $ticket_id && $detalle && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('sssii', $fecha_seleccionada, $ticket_id, $detalle, $registros_por_pagina, $offset);
} elseif ($matricula && $ticket_id && $detalle && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('sssii', $matricula, $ticket_id, $detalle, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $matricula && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('ssii', $fecha_seleccionada, $matricula, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $ticket_id && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('ssii', $fecha_seleccionada, $ticket_id, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $detalle && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('ssii', $fecha_seleccionada, $detalle, $registros_por_pagina, $offset);
} elseif ($matricula && $ticket_id && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('ssii', $matricula, $ticket_id, $registros_por_pagina, $offset);
} elseif ($matricula && $detalle && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('ssii', $matricula, $detalle, $registros_por_pagina, $offset);
} elseif ($ticket_id && $detalle && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('ssii', $ticket_id, $detalle, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('sii', $fecha_seleccionada, $registros_por_pagina, $offset);
} elseif ($matricula && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('sii', $matricula, $registros_por_pagina, $offset);
} elseif ($ticket_id && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('sii', $ticket_id, $registros_por_pagina, $offset);
} elseif ($detalle && $solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('sii', $detalle, $registros_por_pagina, $offset);
} elseif ($solo_no_reportados) {
    $stmt_tickets_activos_cerrados->bind_param('ii', $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $matricula && $ticket_id && $detalle) {
    $stmt_tickets_activos_cerrados->bind_param('ssssii', $fecha_seleccionada, $matricula, $ticket_id, $detalle, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $matricula && $ticket_id) {
    $stmt_tickets_activos_cerrados->bind_param('sssii', $fecha_seleccionada, $matricula, $ticket_id, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $matricula && $detalle) {
    $stmt_tickets_activos_cerrados->bind_param('sssii', $fecha_seleccionada, $matricula, $detalle, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $ticket_id && $detalle) {
    $stmt_tickets_activos_cerrados->bind_param('sssii', $fecha_seleccionada, $ticket_id, $detalle, $registros_por_pagina, $offset);
} elseif ($matricula && $ticket_id && $detalle) {
    $stmt_tickets_activos_cerrados->bind_param('sssii', $matricula, $ticket_id, $detalle, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $matricula) {
    $stmt_tickets_activos_cerrados->bind_param('ssii', $fecha_seleccionada, $matricula, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $ticket_id) {
    $stmt_tickets_activos_cerrados->bind_param('ssii', $fecha_seleccionada, $ticket_id, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada && $detalle) {
    $stmt_tickets_activos_cerrados->bind_param('ssii', $fecha_seleccionada, $detalle, $registros_por_pagina, $offset);
} elseif ($matricula && $ticket_id) {
    $stmt_tickets_activos_cerrados->bind_param('ssii', $matricula, $ticket_id, $registros_por_pagina, $offset);
} elseif ($matricula && $detalle) {
    $stmt_tickets_activos_cerrados->bind_param('ssii', $matricula, $detalle, $registros_por_pagina, $offset);
} elseif ($ticket_id && $detalle) {
    $stmt_tickets_activos_cerrados->bind_param('ssii', $ticket_id, $detalle, $registros_por_pagina, $offset);
} elseif ($fecha_seleccionada) {
    $stmt_tickets_activos_cerrados->bind_param('sii', $fecha_seleccionada, $registros_por_pagina, $offset);
} elseif ($matricula) {
    $stmt_tickets_activos_cerrados->bind_param('sii', $matricula, $registros_por_pagina, $offset);
} elseif ($ticket_id) {
    $stmt_tickets_activos_cerrados->bind_param('sii', $ticket_id, $registros_por_pagina, $offset);
} elseif ($detalle) {
    $stmt_tickets_activos_cerrados->bind_param('sii', $detalle, $registros_por_pagina, $offset);
} else {
    $stmt_tickets_activos_cerrados->bind_param('ii', $registros_por_pagina, $offset);
}
$stmt_tickets_activos_cerrados->execute();
$resultado_tickets_activos_cerrados = $stmt_tickets_activos_cerrados->get_result();
?>
