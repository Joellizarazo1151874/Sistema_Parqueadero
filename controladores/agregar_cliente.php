<?php
include_once '../modelo/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $matriculas = $_POST['matriculas'];
    $tipos_vehiculo = $_POST['tipo_vehiculo'];
    $descripciones = $_POST['descripciones'];

    // Verificar si el correo ya existe
    $check_email = $conexion->prepare("SELECT id_cliente FROM clientes WHERE correo = ?");
    $check_email->bind_param("s", $correo);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        // El correo ya existe, redirigir con mensaje de error
        $check_email->close();
        header("Location: ../vistas/Estructuras/clientes.php?error=email_exists");
        exit();
    }
    $check_email->close();

    // Insertar el cliente en la tabla cliente
    $stmt = $conexion->prepare("INSERT INTO clientes (nombre, telefono, correo, fecha_registro) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $nombre, $telefono, $correo);
    $stmt->execute();
    $id_cliente = $stmt->insert_id; // Obtener el ID del cliente recién insertado
    $stmt->close();

    // Procesar cada matrícula con su tipo de vehículo y descripción correspondiente
    for ($i = 0; $i < count($matriculas); $i++) {
        $matricula = $matriculas[$i];
        $tipo_vehiculo = isset($tipos_vehiculo[$i]) ? $tipos_vehiculo[$i] : 'Desconocido';
        $descripcion = isset($descripciones[$i]) ? $descripciones[$i] : '';
        
        // Verificar si la matrícula ya existe en la tabla vehiculos
        $stmt = $conexion->prepare("SELECT id_vehiculo FROM vehiculos WHERE placa = ?");
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Si existe, actualizar el id_cliente
            $stmt->bind_result($id_vehiculo);
            $stmt->fetch();
            $stmt_update = $conexion->prepare("UPDATE vehiculos SET id_cliente = ?, tipo = ?, descripcion = ? WHERE id_vehiculo = ?");
            $stmt_update->bind_param("issi", $id_cliente, $tipo_vehiculo, $descripcion, $id_vehiculo);
            $stmt_update->execute();
            $stmt_update->close();
        } else {
            // Si no existe, insertar un nuevo vehículo
            $stmt_insert = $conexion->prepare("INSERT INTO vehiculos (id_cliente, placa, descripcion, tipo) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("isss", $id_cliente, $matricula, $descripcion, $tipo_vehiculo);
            $stmt_insert->execute();
            $stmt_insert->close();
        }
        $stmt->close();
    }

    // Redirigir
    header("Location: ../vistas/Estructuras/clientes.php?success=1");
    exit();
}

?>
