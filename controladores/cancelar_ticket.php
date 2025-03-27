<?php
session_start();
include "../modelo/conexion.php";

// Verificar que se recibió el ID del ticket
if (isset($_POST['id_registro'])) {
    $id_registro = trim($_POST['id_registro']);
    $motivo_cancelacion = isset($_POST['motivo_cancelacion']) ? trim($_POST['motivo_cancelacion']) : '';
        // Iniciar la sesión para obtener datos del usuario actual
        session_start();
        $usuario = $_SESSION['datos_login']['nombre'];
    // Obtener la hora actual para registrar como hora de salida
    $hora_salida = date('Y-m-d H:i:s');
    
    try {
        // Actualizar el estado del ticket a "cancelado" y registrar la hora de salida, motivo y usuario
        $sql = "UPDATE registros_parqueo SET 
                    estado = 'cancelado', 
                    hora_salida = ?,
                    metodo_pago = 'cancelado',  
                    descripcion = ?, 
                    cerrado_por = ? 
                WHERE id_registro = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssi", $hora_salida, $motivo_cancelacion, $usuario, $id_registro);
        $resultado = $stmt->execute();
        
        if ($resultado) {
            // Mensaje de éxito
            $_SESSION['success'] = "El ticket ha sido cancelado correctamente";
        } else {
            // Mensaje de error
            $_SESSION['error'] = "No se pudo cancelar el ticket. Por favor, intente de nuevo.";
        }
    } catch (Exception $e) {
        // Registrar el error
        error_log("Error al cancelar ticket: " . $e->getMessage());
        
        // Mensaje de error para el usuario
        $_SESSION['error'] = "Ha ocurrido un error al cancelar el ticket";
    }
} else {
    // Si falta el ID, mostrar un mensaje de error
    $_SESSION['error'] = "No se ha proporcionado el ID del ticket a cancelar";
}

// Redirigir de vuelta a la página de gestión
header('Location: ../vistas/Estructuras/gestion.php?actualizado=1');
exit;
?> 