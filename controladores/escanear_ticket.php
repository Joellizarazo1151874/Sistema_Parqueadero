<?php
// Configurar la zona horaria para Colombia
date_default_timezone_set('America/Bogota');

// Incluir conexión a la base de datos
include '../modelo/conexion.php';

// Verificar si se recibió un ID de ticket
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID de ticket válido");
}

$id_ticket = intval($_GET['id']);

// Obtener información del ticket
$sql = "SELECT r.*, v.placa, v.tipo as tipo_vehiculo 
        FROM registros_parqueo r 
        LEFT JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
        WHERE r.id_registro = ? AND r.estado = 'activo'";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_ticket);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows > 0) {
    $ticket = $resultado->fetch_assoc();
    
    // Redireccionar al formulario de cierre con el ticket ya cargado
    header("Location: ../vistas/Estructuras/gestion.php?tab=tab1&escaneo=1&id=" . $id_ticket);
    exit;
} else {
    // El ticket no existe o ya no está activo
    echo "<div style='text-align:center; font-family:Arial; margin-top:50px;'>";
    echo "<h1>Ticket no válido</h1>";
    echo "<p>El ticket escaneado no existe o ya ha sido cerrado.</p>";
    echo "<a href='../vistas/Estructuras/gestion.php?tab=tab1' style='display:inline-block; margin-top:20px; padding:10px 20px; background-color:#007bff; color:white; text-decoration:none; border-radius:5px;'>Volver al Sistema</a>";
    echo "</div>";
}

$stmt->close();
$conexion->close();
?> 