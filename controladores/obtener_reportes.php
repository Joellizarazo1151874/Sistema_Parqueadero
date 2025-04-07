<?php
// Incluir la conexión a la base de datos
require_once '../modelo/conexion.php';

// Configurar cabeceras para JSON
header('Content-Type: application/json');

try {
    // Verificar si la tabla existe
    $check_table = $conexion->query("SHOW TABLES LIKE 'reportes_caja'");
    if ($check_table->num_rows == 0) {
        // La tabla no existe, enviar respuesta con mensaje claro
        echo json_encode([
            'success' => false,
            'message' => 'La tabla de reportes no existe en la base de datos'
        ]);
        exit;
    }
    
    // Verificar si se recibió una fecha
    if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
        $fecha = $conexion->real_escape_string($_GET['fecha']);
        
        // Consulta SQL para obtener los reportes de la fecha especificada
        $sql = "SELECT r.*, u.nombre as nombre_operador 
                FROM reportes_caja r
                LEFT JOIN usuarios u ON r.id_operador = u.id_usuario
                WHERE DATE(r.fecha_cierre) = DATE('$fecha')
                ORDER BY r.fecha_cierre DESC";
    } else {
        // Si no se especificó fecha, obtener los reportes más recientes (limitados a 10)
        $sql = "SELECT r.*, u.nombre as nombre_operador 
                FROM reportes_caja r
                LEFT JOIN usuarios u ON r.id_operador = u.id_usuario
                ORDER BY r.fecha_cierre DESC
                LIMIT 10";
    }

    // Ejecutar la consulta
    $resultado = $conexion->query($sql);
    
    if ($resultado === false) {
        throw new Exception("Error en la consulta: " . $conexion->error);
    }
    
    // Verificar si hubo resultados
    if ($resultado->num_rows > 0) {
        $reportes = array();
        
        // Recorrer los resultados
        while ($fila = $resultado->fetch_assoc()) {
            // Formatear la fecha y hora
            $fecha_hora = new DateTime($fila['fecha_cierre']);
            
            // Crear un array con los datos del reporte
            $reporte = array(
                'id_reporte' => $fila['id_reporte'],
                'fecha_hora' => $fila['fecha_cierre'],
                'fecha_formateada' => $fecha_hora->format('d/m/Y'),
                'hora_formateada' => $fecha_hora->format('h:i A'),
                'total_recaudado' => $fila['total_recaudado'],
                'operador' => $fila['nombre_operador'] ?? 'Administrador',
                'detalles' => $fila['detalles'] ?? '',
                'ruta_pdf' => $fila['ruta_pdf'] ?? ''
            );
            
            // Agregar el reporte al array de reportes
            $reportes[] = $reporte;
        }
        
        // Devolver los reportes en formato JSON
        echo json_encode(array(
            'success' => true,
            'reportes' => $reportes
        ));
    } else {
        // No se encontraron reportes
        echo json_encode(array(
            'success' => true,
            'reportes' => array()
        ));
    }
} catch (Exception $e) {
    // Error en la consulta
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}
