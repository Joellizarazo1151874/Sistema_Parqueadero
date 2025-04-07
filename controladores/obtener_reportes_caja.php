<?php
session_start();
include '../modelo/conexion.php';

// Configurar la zona horaria para Colombia
date_default_timezone_set('America/Bogota');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['datos_login'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Obtener el tipo de consulta
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'hoy';
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Para depuración
$debug = [
    'tipo_consulta' => $tipo,
    'fecha_solicitada' => $fecha,
    'fecha_actual' => date('Y-m-d')
];

// Preparar la consulta según el tipo
if ($tipo === 'hoy') {
    // Reportes generados hoy
    $sql = "SELECT 
                r.id_reporte,
                r.fecha_cierre,
                r.total_recaudado,
                u.nombre as operador
            FROM 
                reportes_caja r
            JOIN 
                usuarios u ON r.id_operador = u.id_usuario
            WHERE 
                DATE(r.fecha_cierre) = CURDATE()
            ORDER BY 
                r.fecha_cierre DESC";
    $stmt = $conexion->prepare($sql);
} elseif ($tipo === 'fecha') {
    // Reportes por fecha específica
    $sql = "SELECT 
                r.id_reporte,
                r.fecha_cierre,
                r.total_recaudado,
                u.nombre as operador
            FROM 
                reportes_caja r
            JOIN 
                usuarios u ON r.id_operador = u.id_usuario
            WHERE 
                DATE(r.fecha_cierre) = ?
            ORDER BY 
                r.fecha_cierre DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('s', $fecha);
    
    // Añadir más información de depuración
    $debug['fecha_formateada'] = $fecha;
    $debug['sql_con_valores'] = str_replace('?', "'$fecha'", $sql);
} else {
    // Historial de reportes (últimos 30 días por defecto)
    $sql = "SELECT 
                r.id_reporte,
                r.fecha_cierre,
                r.total_recaudado,
                u.nombre as operador
            FROM 
                reportes_caja r
            JOIN 
                usuarios u ON r.id_operador = u.id_usuario
            ORDER BY 
                r.fecha_cierre DESC
            LIMIT 30";
    $stmt = $conexion->prepare($sql);
}

$stmt->execute();
$resultado = $stmt->get_result();
$reportes = [];

// Añadir información de depuración
$debug['num_resultados'] = $resultado->num_rows;
$debug['consulta_sql'] = $sql;

while ($row = $resultado->fetch_assoc()) {
    // Verificar si existe el archivo HTML
    $fecha_formateada = date('Y-m-d', strtotime($row['fecha_cierre']));
    $hora_formateada = date('Hi', strtotime($row['fecha_cierre']));
    $ruta_html = '../reportes/reporte_caja_' . $fecha_formateada . '_' . $hora_formateada . '.html';
    
    $reportes[] = [
        'id_reporte' => $row['id_reporte'],
        'fecha' => date('d/m/Y', strtotime($row['fecha_cierre'])),
        'hora' => date('H:i', strtotime($row['fecha_cierre'])),
        'total_recaudado' => number_format($row['total_recaudado'], 0, '', ''),
        'operador' => $row['operador'],
        'html_existe' => file_exists($ruta_html),
        'ruta_html' => str_replace('../', '', $ruta_html)
    ];
}

// Devolver los resultados en formato JSON
header('Content-Type: application/json');
// Incluir información de depuración en la respuesta
$response = [
    'debug' => $debug,
    'reportes' => $reportes
];
echo json_encode($response, JSON_PRETTY_PRINT);
?>
