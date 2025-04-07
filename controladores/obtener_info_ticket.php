<?php
// Incluir la conexión a la base de datos
include_once '../config/conexion.php';

// Verificar si se recibió un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de ticket no proporcionado'
    ]);
    exit;
}

// Obtener el ID del ticket
$id_registro = intval($_GET['id']);

// Consulta para obtener la información del ticket
$sql = "SELECT r.*, v.placa, v.tipo as tipo_vehiculo, t.valor_hora, t.valor_fraccion
        FROM registros_parqueo r 
        LEFT JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
        LEFT JOIN tarifas t ON v.tipo = t.tipo_vehiculo
        WHERE r.id_registro = ? AND r.estado = 'abierto'";

// Preparar la consulta
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $id_registro);
$stmt->execute();
$resultado = $stmt->get_result();

// Verificar si se encontró el ticket
if ($resultado && $resultado->num_rows > 0) {
    $ticket = $resultado->fetch_assoc();
    
    // Calcular el tiempo transcurrido
    $hora_ingreso = new DateTime($ticket['hora_ingreso']);
    $hora_actual = new DateTime();
    $diferencia = $hora_ingreso->diff($hora_actual);
    
    // Calcular el total a pagar
    $horas_totales = $diferencia->days * 24 + $diferencia->h;
    $minutos_totales = $diferencia->i;
    
    // Si hay minutos adicionales, se cobra una fracción o una hora completa según la configuración
    if ($minutos_totales > 0) {
        $horas_totales += 1; // Se cobra la hora completa por fracción
    }
    
    // Calcular el total estimado
    $total_estimado = $horas_totales * $ticket['valor_hora'];

    // Formatear valores para mostrar sin decimales y con separadores de miles
    $ticket['valor_hora_formateado'] = number_format($ticket['valor_hora'], 0, '', ',');
    $ticket['total_estimado_formateado'] = number_format($total_estimado, 0, '', ',');

    // Agregar el total estimado al ticket
    $ticket['total_estimado'] = $total_estimado;

    // Agregar información de tiempo para mostrar en la interfaz
    $ticket['tiempo_horas'] = $horas_totales;
    $ticket['tiempo_minutos'] = $minutos_totales;
    $ticket['tiempo_dias'] = $diferencia->days;
    
    // Devolver la información en formato JSON
    echo json_encode([
        'success' => true,
        'ticket' => $ticket
    ]);
} else {
    // No se encontró el ticket o no está abierto
    echo json_encode([
        'success' => false,
        'message' => 'Ticket no encontrado o ya está cerrado'
    ]);
}

// Cerrar la conexión
$stmt->close();
$conexion->close();
?>
