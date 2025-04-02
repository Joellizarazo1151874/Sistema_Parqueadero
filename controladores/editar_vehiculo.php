<?php
include "../modelo/conexion.php";

// Comprobamos que tengamos los datos necesarios del formulario
if (isset($_POST['id_vehiculo']) && isset($_POST['placa']) && isset($_POST['tipo'])) {
    
    // Obtenemos los datos del formulario
    $id_vehiculo = trim($_POST['id_vehiculo']);
    $id_registro = isset($_POST['id_registro']) ? trim($_POST['id_registro']) : null;
    $placa = strtoupper(trim($_POST['placa']));
    $tipo = strtolower(trim($_POST['tipo']));
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    
    // Validamos que la placa no esté vacía
    if (empty($placa)) {
        $_SESSION['error'] = "La placa del vehículo no puede estar vacía";
        header('Location: ../vistas/Estructuras/gestion.php');
        exit;
    }
    
    // Validamos el tipo de vehículo
    $tipos_validos = ['auto', 'moto', 'camioneta', 'motocarro'];
    if (!in_array($tipo, $tipos_validos)) {
        $_SESSION['error'] = "El tipo de vehículo seleccionado no es válido";
        header('Location: ../vistas/Estructuras/gestion.php');
        exit;
    }
    
    try {
        // Actualizamos los datos del vehículo
        $sql = "UPDATE vehiculos SET placa = ?, tipo = ?, descripcion = ? WHERE id_vehiculo = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssi", $placa, $tipo, $descripcion, $id_vehiculo);
        $resultado = $stmt->execute();
        
        if ($resultado) {
            // Mensaje de éxito
            $_SESSION['success'] = "Los datos del vehículo se han actualizado correctamente";
        } else {
            // Mensaje de error
            $_SESSION['error'] = "No se pudo actualizar el vehículo. Por favor, intenta de nuevo.";
        }
    } catch (Exception $e) {
        // Registramos el error
        error_log("Error al actualizar vehículo: " . $e->getMessage());
        
        // Mensaje de error para el usuario
        $_SESSION['error'] = "Ha ocurrido un error al actualizar los datos del vehículo";
    }
} else {
    // Si faltan datos, mostramos un mensaje de error
    $_SESSION['error'] = "Faltan datos necesarios para actualizar el vehículo";
}

// Redirigimos de vuelta a la página de gestión con un parámetro para indicar actualización
header('Location: ../vistas/Estructuras/clientes.php?actualizado=1&tab=tab2');
exit;
?>
