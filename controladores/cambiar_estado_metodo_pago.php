<?php
// Incluir archivo de conexión
include '../modelo/conexion.php';

// Verificar si se recibieron los datos necesarios
if (isset($_POST['id']) && isset($_POST['activo'])) {
    // Sanitizar entradas
    $id = intval($_POST['id']);
    $activo = intval($_POST['activo']);
    
    // Actualizar estado del método de pago
    $sql_actualizar = "UPDATE metodos_pago SET activo = $activo WHERE id_metodo = $id";
    
    if ($conexion->query($sql_actualizar) === TRUE) {
        $estado = $activo ? 'activado' : 'desactivado';
        $respuesta = array(
            'exito' => true,
            'mensaje' => 'Método de pago ' . $estado . ' correctamente'
        );
    } else {
        $respuesta = array(
            'exito' => false,
            'mensaje' => 'Error al cambiar el estado del método de pago: ' . $conexion->error
        );
    }
} else {
    $respuesta = array(
        'exito' => false,
        'mensaje' => 'Faltan datos requeridos para cambiar el estado del método de pago'
    );
}

// Devolver respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($respuesta);
?>
