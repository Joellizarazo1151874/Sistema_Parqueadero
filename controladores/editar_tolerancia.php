<?php
// Habilitar los mensajes de error para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión
include_once '../modelo/conexion.php';

// Verificar que la conexión esté disponible
if (!isset($conexion) || $conexion->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos: ' . ($conexion->connect_error ?? 'Conexión no disponible')]);
    exit;
}

// Si se trata de una petición para actualizar la tolerancia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verificar que se recibieron los parámetros necesarios
    if (isset($_POST['tipo']) && isset($_POST['tolerancia'])) {
        $tipo = $conexion->real_escape_string($_POST['tipo']);
        $tolerancia = $conexion->real_escape_string($_POST['tolerancia']);
        $tiempo = isset($_POST['tiempo']) ? $conexion->real_escape_string($_POST['tiempo']) : 0;
        
        // Validar que la tolerancia sea un número entero positivo
        if (!is_numeric($tolerancia) || intval($tolerancia) < 0) {
            echo json_encode(['success' => false, 'error' => 'La tolerancia debe ser un número entero positivo.']);
            exit;
        }
        
        // Validar que el tiempo sea un número positivo
        if (!is_numeric($tiempo) || floatval($tiempo) < 0) {
            echo json_encode(['success' => false, 'error' => 'El tiempo debe ser un número positivo.']);
            exit;
        }
        
        // Consulta para actualizar la tolerancia
        $sql = "UPDATE tolerancia SET tolerancia = '$tolerancia', tiempo = '$tiempo' WHERE tipo = '$tipo'";
        
        if ($conexion->query($sql) === TRUE) {
            // Verificar si se actualizó algún registro
            if ($conexion->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Tolerancia actualizada correctamente.']);
            } else {
                // Si no se actualizó ningún registro, podría ser porque no existía ese tipo
                // Intentar insertar un nuevo registro
                $sqlInsert = "INSERT INTO tolerancia (tipo, tolerancia, tiempo) VALUES ('$tipo', '$tolerancia', '$tiempo')";
                if ($conexion->query($sqlInsert) === TRUE) {
                    echo json_encode(['success' => true, 'message' => 'Tolerancia agregada correctamente.']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al agregar la tolerancia: ' . $conexion->error]);
                }
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar la tolerancia: ' . $conexion->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Faltan parámetros necesarios para actualizar la tolerancia.']);
    }
}
// Si es una petición para obtener las tolerancias
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Si se solicita una tolerancia específica
    if (isset($_GET['tipo'])) {
        $tipo = $conexion->real_escape_string($_GET['tipo']);
        $sql = "SELECT * FROM tolerancia WHERE tipo = '$tipo'";
        $result = $conexion->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $row]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se encontró la tolerancia para el tipo especificado.']);
        }
    }
    // Si se solicitan todas las tolerancias
    else {
        $sql = "SELECT * FROM tolerancia";
        $result = $conexion->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $tolerancias = [];
            while ($row = $result->fetch_assoc()) {
                $tolerancias[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $tolerancias]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se encontraron tolerancias.']);
        }
    }
}
else {
    echo json_encode(['success' => false, 'error' => 'Método de solicitud no válido.']);
}

$conexion->close(); 