<?php
// Incluir la conexión a la base de datos
include 'modelo/conexion.php';

// Configurar la zona horaria para Colombia
date_default_timezone_set('America/Bogota');

// Verificar si las columnas ya existen
$result = $conexion->query("SHOW COLUMNS FROM registros_parqueo LIKE 'reportado'");
$reportado_exists = $result->num_rows > 0;

$result = $conexion->query("SHOW COLUMNS FROM registros_parqueo LIKE 'id_reporte'");
$id_reporte_exists = $result->num_rows > 0;

// Agregar las columnas si no existen
$messages = [];

if (!$reportado_exists) {
    $sql = "ALTER TABLE registros_parqueo ADD COLUMN reportado TINYINT(1) DEFAULT 0";
    if ($conexion->query($sql) === TRUE) {
        $messages[] = "Columna 'reportado' agregada correctamente";
    } else {
        $messages[] = "Error al agregar columna 'reportado': " . $conexion->error;
    }
}

if (!$id_reporte_exists) {
    $sql = "ALTER TABLE registros_parqueo ADD COLUMN id_reporte VARCHAR(50) DEFAULT NULL";
    if ($conexion->query($sql) === TRUE) {
        $messages[] = "Columna 'id_reporte' agregada correctamente";
    } else {
        $messages[] = "Error al agregar columna 'id_reporte': " . $conexion->error;
    }
}

// Mostrar resultado
echo "<!DOCTYPE html>
<html>
<head>
    <title>Actualización de Base de Datos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        h2 {
            color: #4CAF50;
        }
        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #337ab7;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h2>Actualización de la Estructura de la Base de Datos</h2>";

if (empty($messages)) {
    echo "<div class='message success'>Las columnas ya existen en la tabla.</div>";
} else {
    foreach ($messages as $message) {
        if (strpos($message, 'Error') !== false) {
            echo "<div class='message error'>$message</div>";
        } else {
            echo "<div class='message success'>$message</div>";
        }
    }
}

echo "
        <p>Esta actualización permite marcar los tickets que ya han sido incluidos en reportes de cierre de caja, evitando que se dupliquen en reportes futuros.</p>
        <a href='vistas/Estructuras/caja.php' class='btn'>Volver al Sistema</a>
    </div>
</body>
</html>";
?>
