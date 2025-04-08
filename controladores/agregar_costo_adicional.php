<?php
session_start();
include '../modelo/conexion.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['datos_login'])) {
    header('Location: ../index.php');
    exit;
}

// Verificar si se recibieron los datos necesarios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar los datos recibidos
    if (!isset($_POST['id_registro']) || !isset($_POST['concepto']) || !isset($_POST['valor'])) {
        $_SESSION['error'] = "Faltan datos requeridos";
        header('Location: ../vistas/estructuras/gestion.php');
        exit;
    }
    
    // Obtener y sanitizar los datos
    $id_registro = intval($_POST['id_registro']);
    $concepto = htmlspecialchars(trim($_POST['concepto']));
    $valor = floatval($_POST['valor']);
    
    // Validar que el valor sea positivo
    if ($valor <= 0) {
        $_SESSION['error'] = "El valor debe ser mayor que cero";
        header('Location: ../vistas/estructuras/gestion.php');
        exit;
    }
    
    // Verificar que el registro exista y esté activo
    $query_check = "SELECT id_registro FROM registros_parqueo WHERE id_registro = ? AND estado = 'activo'";
    $stmt_check = $conexion->prepare($query_check);
    $stmt_check->bind_param("i", $id_registro);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows === 0) {
        $_SESSION['error'] = "El ticket no existe o ya está cerrado";
        header('Location: ../vistas/estructuras/gestion.php');
        exit;
    }
    
    // Insertar el costo adicional
    $query_insert = "INSERT INTO costos_adicionales (id_registro, concepto, valor) VALUES (?, ?, ?)";
    $stmt_insert = $conexion->prepare($query_insert);
    $stmt_insert->bind_param("isd", $id_registro, $concepto, $valor);
    
    if ($stmt_insert->execute()) {
        $_SESSION['success'] = "Costo adicional agregado correctamente";
    } else {
        $_SESSION['error'] = "Error al guardar el costo adicional: " . $conexion->error;
    }
    
    $stmt_insert->close();
    $stmt_check->close();
    
    // Redireccionar de vuelta a la página de gestión
    header('Location: ../vistas/estructuras/gestion.php');
} else {
    // Si no es una solicitud POST, redireccionar
    header('Location: ../vistas/estructuras/gestion.php');
}

$conexion->close();
?> 