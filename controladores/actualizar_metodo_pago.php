<?php
// Incluir archivo de conexión
include '../modelo/conexion.php';

// Verificar si se recibieron los datos necesarios
if (isset($_POST['id']) && isset($_POST['nombre']) && !empty($_POST['nombre'])) {
    // Sanitizar entradas
    $id = intval($_POST['id']);
    $nombre = $conexion->real_escape_string(trim($_POST['nombre']));
    $activo = isset($_POST['activo']) ? intval($_POST['activo']) : 1;
    
    // Verificar si ya existe otro método de pago con el mismo nombre (excluyendo el actual)
    $sql_verificar = "SELECT COUNT(*) as total FROM metodos_pago WHERE nombre = '$nombre' AND id_metodo != $id";
    $resultado = $conexion->query($sql_verificar);
    $fila = $resultado->fetch_assoc();
    
    if ($fila['total'] > 0) {
        // Ya existe otro método de pago con ese nombre
        $respuesta = array(
            'exito' => false,
            'mensaje' => 'Ya existe otro método de pago con ese nombre'
        );
    } else {
        // Actualizar método de pago
        $sql_actualizar = "UPDATE metodos_pago SET nombre = '$nombre', activo = $activo WHERE id_metodo = $id";
        
        if ($conexion->query($sql_actualizar) === TRUE) {
            $respuesta = array(
                'exito' => true,
                'mensaje' => 'Método de pago actualizado correctamente'
            );
        } else {
            $respuesta = array(
                'exito' => false,
                'mensaje' => 'Error al actualizar el método de pago: ' . $conexion->error
            );
        }
    }
} else {
    $respuesta = array(
        'exito' => false,
        'mensaje' => 'Faltan datos requeridos para actualizar el método de pago'
    );
}

// Devolver respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($respuesta);
?>
