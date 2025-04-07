<?php
// Incluir archivo de conexión
include '../modelo/conexion.php';

// Verificar si se recibió el nombre del método de pago
if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
    // Sanitizar entrada
    $nombre = $conexion->real_escape_string(trim($_POST['nombre']));
    
    // Verificar si ya existe un método de pago con el mismo nombre
    $sql_verificar = "SELECT COUNT(*) as total FROM metodos_pago WHERE nombre = '$nombre'";
    $resultado = $conexion->query($sql_verificar);
    $fila = $resultado->fetch_assoc();
    
    if ($fila['total'] > 0) {
        // Ya existe un método de pago con ese nombre
        $respuesta = array(
            'exito' => false,
            'mensaje' => 'Ya existe un método de pago con ese nombre'
        );
    } else {
        // Insertar nuevo método de pago
        $sql_insertar = "INSERT INTO metodos_pago (nombre) VALUES ('$nombre')";
        
        if ($conexion->query($sql_insertar) === TRUE) {
            $respuesta = array(
                'exito' => true,
                'mensaje' => 'Método de pago guardado correctamente',
                'id' => $conexion->insert_id
            );
        } else {
            $respuesta = array(
                'exito' => false,
                'mensaje' => 'Error al guardar el método de pago: ' . $conexion->error
            );
        }
    }
} else {
    $respuesta = array(
        'exito' => false,
        'mensaje' => 'El nombre del método de pago es obligatorio'
    );
}

// Devolver respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($respuesta);
?>
