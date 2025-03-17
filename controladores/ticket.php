<?php
include '../modelo/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_registro']) && isset($_POST['metodo_pago'])) {
    error_log("Datos recibidos: id_registro=" . $_POST['id_registro'] . ", metodo_pago=" . $_POST['metodo_pago']); // Depuración
    $id_registro = intval($_POST['id_registro']); // Asegurar que es un número entero
    $metodo_pago = $_POST['metodo_pago'];

    $query = "UPDATE registros_parqueo SET estado = 'cerrado', hora_salida = NOW(), metodo_pago = ? WHERE id_registro = ?";
    $stmt = $conexion->prepare($query);
    if (!$stmt) {
        error_log("Error en la preparación de la consulta: " . $conexion->error); // Depuración
        echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta"]);
        exit;
    }

    $stmt->bind_param("si", $metodo_pago, $id_registro);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        error_log("Error al ejecutar la consulta: " . $stmt->error); // Depuración
        echo json_encode(["success" => false, "message" => "No se pudo cerrar el ticket"]);
    }

    $stmt->close();
    $conexion->close();
} else {
    error_log("Solicitud no válida: Método=" . $_SERVER["REQUEST_METHOD"] . ", id_registro=" . ($_POST['id_registro'] ?? 'no definido') . ", metodo_pago=" . ($_POST['metodo_pago'] ?? 'no definido')); // Depuración
    echo json_encode(["success" => false, "message" => "Solicitud no válida"]);
}
?>