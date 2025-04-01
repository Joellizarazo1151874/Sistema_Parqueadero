<?php
include_once '../modelo/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];

    // Verificar si el correo ya existe para otro cliente
    $check_email = $conexion->prepare("SELECT id_cliente FROM clientes WHERE correo = ? AND id_cliente != ?");
    $check_email->bind_param("si", $correo, $id_cliente);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        // El correo ya existe para otro cliente, redirigir con mensaje de error
        $check_email->close();
        header("Location: ../vistas/Estructuras/clientes.php?error=email_exists");
        exit();
    }
    $check_email->close();

    // Actualizar el cliente
    $stmt = $conexion->prepare("UPDATE clientes SET nombre = ?, telefono = ?, correo = ? WHERE id_cliente = ?");
    $stmt->bind_param("sssi", $nombre, $telefono, $correo, $id_cliente);
    
    if ($stmt->execute()) {
        header("Location: ../vistas/Estructuras/clientes.php?success=updated");
    } else {
        header("Location: ../vistas/Estructuras/clientes.php?error=update_failed");
    }
    
    $stmt->close();
    exit();
}
?> 