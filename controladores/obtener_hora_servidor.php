<?php
// obtener_hora_servidor.php
include '../modelo/conexion.php';

// Obtener la hora actual del servidor
$hora_servidor = time(); // Timestamp en segundos

// Devolver la hora en formato JSON
echo json_encode(["hora_servidor" => $hora_servidor]);
?>