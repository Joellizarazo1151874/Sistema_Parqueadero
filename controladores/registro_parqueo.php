<?php
include "../modelo/conexion.php";

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_vehiculo = $_POST['tipo_vehiculo'];
    $matricula = $_POST['matricula'];
    $descripcion = $_POST['descripcion'];
    
    // Iniciar sesión para obtener datos del usuario actual
    session_start();
    $abierto_por = isset($_SESSION['datos_login']['nombre']) ? $_SESSION['datos_login']['nombre'] : 'Sistema';

    try {
        // Verificar si el vehículo ya existe
        $stmt = $conexion->prepare("SELECT id_vehiculo FROM vehiculos WHERE placa = ?");
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // El vehículo ya existe, obtener su ID
            $row = $result->fetch_assoc();
            $id_vehiculo = $row['id_vehiculo'];
        } else {
            // El vehículo no existe, registrarlo
            $stmt = $conexion->prepare("INSERT INTO vehiculos (id_cliente, placa, tipo, descripcion) VALUES (NULL, ?, ?, ?)");
            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $conexion->error);
            }
            $stmt->bind_param("sss", $matricula, $tipo_vehiculo, $descripcion);
            $stmt->execute();
            $id_vehiculo = $conexion->insert_id;
        }

        // Registrar el ingreso del vehículo al parqueadero
        $stmt = $conexion->prepare("INSERT INTO registros_parqueo (id_vehiculo, hora_ingreso, hora_salida, estado, total_pagado, metodo_pago, abierto_por) VALUES (?, NOW(), NULL, 'activo', 0, NULL, ?)");
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }
        $stmt->bind_param("is", $id_vehiculo, $abierto_por);
        $stmt->execute();

        header("Location: ../vistas/Estructuras/gestion.php?success=1");
        exit();

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
