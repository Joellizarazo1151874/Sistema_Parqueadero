<?php
// Incluir la conexión a la base de datos
require_once '../modelo/conexion.php';

// Configurar cabeceras para JSON
header('Content-Type: application/json');

// Verificar si se recibió un ID de reporte
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_reporte = $conexion->real_escape_string($_GET['id']);
    
    try {
        // Consulta SQL para obtener los detalles del reporte
        $sql = "SELECT r.*, u.nombre as nombre_operador 
                FROM reportes_caja r
                LEFT JOIN usuarios u ON r.id_operador = u.id_usuario
                WHERE r.id_reporte = '$id_reporte'";
        
        // Ejecutar la consulta
        $resultado = $conexion->query($sql);
        
        if ($resultado === false) {
            throw new Exception("Error en la consulta: " . $conexion->error);
        }
        
        // Verificar si se encontró el reporte
        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            
            // Formatear la fecha y hora
            $fecha_hora = new DateTime($fila['fecha_cierre']);
            
            // Decodificar los detalles JSON si existen
            $detalles = [];
            if (!empty($fila['detalles'])) {
                $detalles = json_decode($fila['detalles'], true);
            }
            
            // Crear un array con los datos del reporte
            $reporte = array(
                'id_reporte' => $fila['id_reporte'],
                'fecha_hora' => $fila['fecha_cierre'],
                'fecha_formateada' => $fecha_hora->format('d/m/Y'),
                'hora_formateada' => $fecha_hora->format('h:i A'),
                'total_recaudado' => $fila['total_recaudado'],
                'operador' => $fila['nombre_operador'] ?? 'Administrador',
                'estado' => $fila['estado'],
                'detalles' => $detalles,
                'ruta_pdf' => $fila['ruta_pdf'] ?? ''
            );
            
            // Devolver los detalles del reporte en formato JSON
            echo json_encode(array(
                'success' => true,
                'reporte' => $reporte
            ));
        } else {
            // No se encontró el reporte
            echo json_encode(array(
                'success' => false,
                'message' => 'No se encontró el reporte especificado'
            ));
        }
    } catch (Exception $e) {
        // Error en la consulta
        echo json_encode(array(
            'success' => false,
            'message' => $e->getMessage()
        ));
    }
} else {
    // No se proporcionó un ID de reporte
    echo json_encode(array(
        'success' => false,
        'message' => 'No se proporcionó un ID de reporte válido'
    ));
}
