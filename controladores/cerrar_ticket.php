<?php
// Iniciar sesión para obtener el usuario actual
session_start();

// Incluir la conexión a la base de datos
include_once '../config/conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php?error=no_session');
    exit;
}

// Verificar si se recibieron los datos necesarios
if (!isset($_POST['id_registro']) || empty($_POST['id_registro']) || !isset($_POST['metodo_pago']) || empty($_POST['metodo_pago']) || !isset($_POST['descripcion']) || empty($_POST['descripcion'])) {
    header('Location: ../vistas/Estructuras/caja.php?tab=tab1&error=datos_incompletos');
    exit;
}

// Obtener los datos del formulario
$id_registro = intval($_POST['id_registro']);
$id_metodo_pago = intval($_POST['metodo_pago']);
$descripcion = $_POST['descripcion'];
$usuario_actual = $_SESSION['usuario'];

// Obtener el nombre del método de pago
$sql_metodo = "SELECT nombre FROM metodos_pago WHERE id_metodo = ?";
$stmt_metodo = $conexion->prepare($sql_metodo);
$stmt_metodo->bind_param('i', $id_metodo_pago);
$stmt_metodo->execute();
$resultado_metodo = $stmt_metodo->get_result();

if (!$resultado_metodo || $resultado_metodo->num_rows === 0) {
    header('Location: ../vistas/Estructuras/caja.php?tab=tab1&error=metodo_no_encontrado');
    exit;
}

$metodo = $resultado_metodo->fetch_assoc();
$nombre_metodo_pago = $metodo['nombre'];

// Iniciar transacción
$conexion->begin_transaction();

try {
    // 1. Obtener la información del ticket
    $sql_ticket = "SELECT r.*, v.placa, v.tipo as tipo_vehiculo, t.valor_hora, t.valor_fraccion
                  FROM registros_parqueo r 
                  LEFT JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
                  LEFT JOIN tarifas t ON v.tipo = t.tipo_vehiculo
                  WHERE r.id_registro = ? AND r.estado = 'abierto'";
    
    $stmt_ticket = $conexion->prepare($sql_ticket);
    $stmt_ticket->bind_param('i', $id_registro);
    $stmt_ticket->execute();
    $resultado_ticket = $stmt_ticket->get_result();
    
    if (!$resultado_ticket || $resultado_ticket->num_rows === 0) {
        throw new Exception('Ticket no encontrado o ya está cerrado');
    }
    
    $ticket = $resultado_ticket->fetch_assoc();
    
    // 2. Calcular el tiempo transcurrido y el total a pagar
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
    
    // Calcular el total a pagar
    $total_pagado = $horas_totales * $ticket['valor_hora'];
    
    // 3. Actualizar el registro de parqueo como cerrado
    $sql_actualizar = "UPDATE registros_parqueo 
                      SET estado = 'cerrado', 
                          hora_salida = NOW(), 
                          total_pagado = ?, 
                          metodo_pago = ?, 
                          descripcion = ?,
                          cerrado_por = ? 
                      WHERE id_registro = ?";
    
    $stmt_actualizar = $conexion->prepare($sql_actualizar);
    $stmt_actualizar->bind_param('dsssi', $total_pagado, $nombre_metodo_pago, $descripcion, $usuario_actual, $id_registro);
    $resultado_actualizar = $stmt_actualizar->execute();
    
    if (!$resultado_actualizar) {
        throw new Exception('Error al actualizar el ticket');
    }
    
    // Confirmar la transacción
    $conexion->commit();
    
    // Redirigir con mensaje de éxito
    header('Location: ../vistas/Estructuras/caja.php?tab=tab1&success=ticket_cerrado');
    exit;
    
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conexion->rollback();
    
    // Redirigir con mensaje de error
    header('Location: ../vistas/Estructuras/caja.php?tab=tab1&error=error_cerrar_ticket&message=' . urlencode($e->getMessage()));
    exit;
}
?>
