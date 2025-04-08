<?php
include '../modelo/conexion.php'; // Conectar a la base de datos

// Verificar si las funciones necesarias están disponibles
if (!function_exists('time') || !function_exists('date_default_timezone_set') || 
    !function_exists('json_encode') || !function_exists('date') || 
    !function_exists('implode') || !function_exists('header')) {
    die("Error: Funciones esenciales de PHP no están disponibles. Verifique su instalación de PHP.");
}

date_default_timezone_set('America/Bogota'); // Cambia 'America/Bogota' por tu zona horaria
// Obtener la hora actual del servidor
$hora_servidor = time(); // Timestamp en segundos

// Devolver la hora en formato JSON
echo json_encode(["hora_servidor" => $hora_servidor]);

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recuperar los datos del formulario
    $id_registro = $_POST['id_registro'];
    $metodo_pago = $_POST['metodo_pago'];
    $descripcion = $_POST['descripcion'];
    $total_pagado = $_POST['total_pagado'];

    // Si total_pagado está vacío, asignar 0
    if (empty($total_pagado)) {
        $total_pagado = 0;
    }
    
    // Iniciar la sesión para obtener datos del usuario actual
    session_start();
    $cerrado_por = $_SESSION['datos_login']['nombre'];

    // Array para almacenar los campos faltantes o vacíos
    $campos_faltantes = [];

    // Verificar si cada campo está vacío o no se recibió
    if (empty($id_registro)) {
        $campos_faltantes[] = 'id_registro';
    }
    if (empty($metodo_pago)) {
        $campos_faltantes[] = 'metodo_pago';
    }
    if (empty($descripcion)) {
        $campos_faltantes[] = 'descripcion';
    }

    // Si hay campos faltantes, mostrar un mensaje detallado
    if (!empty($campos_faltantes)) {
        die("Los siguientes campos son obligatorios y no se recibieron o están vacíos: " . implode(', ', $campos_faltantes));
    }

    // Obtener la hora actual del servidor
    $hora_salida = date('Y-m-d H:i:s');

    // Los costos adicionales ya están incluidos en el total_pagado enviado desde el frontend
    // Por lo tanto, ya no es necesario volver a calcularlos y sumarlos
    
    // Sin embargo, seguimos obteniendo la información para incluirla en la descripción
    $query_costos = "SELECT SUM(valor) as total_costos FROM costos_adicionales WHERE id_registro = ?";
    $stmt_costos = $conexion->prepare($query_costos);
    $stmt_costos->bind_param("i", $id_registro);
    $stmt_costos->execute();
    $result_costos = $stmt_costos->get_result();
    $row_costos = $result_costos->fetch_assoc();
    $total_costos_adicionales = ($row_costos['total_costos']) ? floatval($row_costos['total_costos']) : 0;
    
    // Ya NO sumamos los costos adicionales al total_pagado porque ya están incluidos
    // $total_pagado = floatval($total_pagado) + $total_costos_adicionales;
    
    // Incluir información de los costos adicionales en la descripción
    if ($total_costos_adicionales > 0) {
        // Obtener el detalle de los costos adicionales
        $query_detalle = "SELECT concepto, valor FROM costos_adicionales WHERE id_registro = ?";
        $stmt_detalle = $conexion->prepare($query_detalle);
        $stmt_detalle->bind_param("i", $id_registro);
        $stmt_detalle->execute();
        $result_detalle = $stmt_detalle->get_result();
        
        $detalles_costos = [];
        while ($row_detalle = $result_detalle->fetch_assoc()) {
            $detalles_costos[] = $row_detalle['concepto'] . ': $' . number_format($row_detalle['valor'], 0, ',', '.');
        }
        
        $descripcion_adicional = " + Costos adicionales: " . implode(", ", $detalles_costos);
        $descripcion .= $descripcion_adicional;
        
        $stmt_detalle->close();
    }
    
    // Actualizar el registro en la base de datos
    $query = "UPDATE registros_parqueo 
              SET hora_salida = ?, estado = 'cerrado', total_pagado = ?, metodo_pago = ?, descripcion = ?, cerrado_por = ?
              WHERE id_registro = ?";
    $stmt = $conexion->prepare($query);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    // Vincular los parámetros
    $stmt->bind_param("sssssi", $hora_salida, $total_pagado, $metodo_pago, $descripcion, $cerrado_por, $id_registro);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        header("location: ../vistas/estructuras/gestion.php");
    } else {
        echo "Error al cerrar el ticket: " . $stmt->error;
    }

    // Cerrar la conexión
    $stmt->close();
    if (isset($stmt_costos)) $stmt_costos->close();
    $conexion->close();
} else {
    echo "Acceso no permitido.";
}
