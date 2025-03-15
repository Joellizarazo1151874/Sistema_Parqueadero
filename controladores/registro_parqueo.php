<?php
include "../modelo/conexion.php";

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_vehiculo = $_POST['tipo_vehiculo'];
    $matricula = $_POST['matricula'];
    $descripcion = $_POST['descripcion'];

    try {
        // Verificar si el vehículo ya existe
        $stmt = $conexion->prepare("SELECT * FROM vehiculos WHERE placa = ?");
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // El vehículo ya existe, obtener su ID
            $row = $result->fetch_assoc();
            $id_vehiculo = $row['id'];
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

        // Registrar el ingreso del vehículo con la hora actual
        $hora_actual = date('Y-m-d H:i:s');
        $stmt = $conexion->prepare("INSERT INTO registro_parqueo (id_vehiculo, hora_ingreso, hora_salida, estado, total_pagado, metodo_pago) VALUES (?, ?, NULL, 'activo', 0, NULL)");
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }
        $stmt->bind_param("is", $id_vehiculo, $hora_actual);
        $stmt->execute();

        header("Location: ../vistas/Estructuras/gestion.php");
        exit();

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
