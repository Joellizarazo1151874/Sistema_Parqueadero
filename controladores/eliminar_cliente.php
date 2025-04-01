<?php
include_once '../modelo/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = $_POST['id_cliente'];

    // Actualizar los vehículos asociados al cliente, estableciendo id_cliente como NULL
    $stmt_vehiculos = $conexion->prepare("UPDATE vehiculos SET id_cliente = NULL WHERE id_cliente = ?");
    $stmt_vehiculos->bind_param("i", $id_cliente);
    $stmt_vehiculos->execute();
    $stmt_vehiculos->close();

    // Luego eliminar el cliente
    $stmt = $conexion->prepare("DELETE FROM clientes WHERE id_cliente = ?");
    $stmt->bind_param("i", $id_cliente);
    
    if ($stmt->execute()) {
        header("Location: ../vistas/Estructuras/clientes.php?success=deleted");
    } else {
        header("Location: ../vistas/Estructuras/clientes.php?error=delete_failed");
    }
    
    $stmt->close();
    exit();
}
?> 