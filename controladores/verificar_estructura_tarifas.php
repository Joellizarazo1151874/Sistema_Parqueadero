<?php
// Habilitar los mensajes de error para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión
include_once '../modelo/conexion.php';

// Establecer el encabezado para respuesta JSON
header('Content-Type: application/json');

// Verificar que la conexión esté disponible
if (!isset($conexion) || $conexion->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos: ' . ($conexion->connect_error ?? 'Conexión no disponible')]);
    exit;
}

// Verificar que la tabla tarifas existe
$sqlCheckTable = "SHOW TABLES LIKE 'tarifas'";
$resultCheckTable = $conexion->query($sqlCheckTable);

if (!$resultCheckTable || $resultCheckTable->num_rows === 0) {
    // La tabla no existe, crearla
    $sqlCreateTable = "
    CREATE TABLE `tarifas` (
      `id_tarifa` int(11) NOT NULL AUTO_INCREMENT,
      `tipo_vehiculo` varchar(50) NOT NULL,
      `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id_tarifa`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    if (!$conexion->query($sqlCreateTable)) {
        echo json_encode(['success' => false, 'error' => 'Error al crear la tabla tarifas: ' . $conexion->error]);
        exit;
    }
    
    echo json_encode(['success' => true, 'message' => 'Tabla tarifas creada correctamente.']);
    exit;
}

// Obtener las columnas actuales de la tabla tarifas
$sqlColumnas = "SHOW COLUMNS FROM tarifas";
$resultColumnas = $conexion->query($sqlColumnas);

if (!$resultColumnas) {
    echo json_encode(['success' => false, 'error' => 'Error al consultar columnas de la tabla tarifas: ' . $conexion->error]);
    exit;
}

// Obtener los nombres de las columnas existentes
$columnasExistentes = [];
while ($rowColumna = $resultColumnas->fetch_assoc()) {
    $columnasExistentes[] = strtolower($rowColumna['Field']);
}

// Obtener todos los tipos de tolerancia
$sqlTiposTarifa = "SELECT tipo FROM tolerancia";
$resultTiposTarifa = $conexion->query($sqlTiposTarifa);

if (!$resultTiposTarifa) {
    echo json_encode(['success' => false, 'error' => 'Error al consultar tipos de tarifa: ' . $conexion->error]);
    exit;
}

// Verificar si cada tipo de tarifa existe como columna
$columnasAgregadas = [];
while ($rowTipo = $resultTiposTarifa->fetch_assoc()) {
    $tipoTarifa = strtolower($rowTipo['tipo']);
    
    // Si la columna no existe, agregarla
    if (!in_array($tipoTarifa, $columnasExistentes)) {
        $sqlAddColumn = "ALTER TABLE tarifas ADD COLUMN `$tipoTarifa` int(11) DEFAULT 0";
        
        if (!$conexion->query($sqlAddColumn)) {
            echo json_encode(['success' => false, 'error' => "Error al agregar columna $tipoTarifa: " . $conexion->error]);
            exit;
        }
        
        $columnasAgregadas[] = $tipoTarifa;
    }
}

// Responder con éxito
echo json_encode([
    'success' => true,
    'message' => count($columnasAgregadas) > 0 
        ? 'Columnas agregadas: ' . implode(', ', $columnasAgregadas)
        : 'La estructura de la tabla tarifas está completa.'
]);

$conexion->close(); 