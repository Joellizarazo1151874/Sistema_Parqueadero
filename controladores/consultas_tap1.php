<?php
// Consulta para obtener los registros activos con la información del vehículo
$query = "SELECT r.*, v.placa, v.tipo 
FROM registros_parqueo r 
INNER JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
WHERE r.estado = 'activo' 
ORDER BY r.hora_ingreso DESC";

$resultado = $conexion->query($query);

// Obtener el término de búsqueda si existe
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$tipo_vehiculo = isset($_GET['tipo_vehiculo']) ? $_GET['tipo_vehiculo'] : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'desc'; // desc = últimos primero, asc = primeros primero

// Definir la variable $categoria_cerrados
$categoria_cerrados = isset($_GET['categoria_cerrados']) ? $_GET['categoria_cerrados'] : 'todos';

// Validar que orden solo pueda ser 'asc' o 'desc'
$orden = ($orden === 'asc') ? 'ASC' : 'DESC';

// Consulta para tickets abiertos con búsqueda opcional
$sql_abiertos = "SELECT rp.*, rp.tipo AS tipo_registro, v.placa, v.tipo, v.descripcion
                 FROM registros_parqueo rp 
                 JOIN vehiculos v ON rp.id_vehiculo = v.id_vehiculo 
                 WHERE rp.estado = 'activo'";

// Añadir condiciones de filtrado
$params = [];
$tipos_params = "";

// Filtrar por matrícula si hay búsqueda
if (!empty($busqueda)) {
    $sql_abiertos .= " AND v.placa LIKE ?";
    $params[] = "%$busqueda%";
    $tipos_params .= "s";
}

// Filtrar por tipo de vehículo si se ha seleccionado
if (!empty($tipo_vehiculo)) {
    $sql_abiertos .= " AND v.tipo = ?";
    $params[] = $tipo_vehiculo;
    $tipos_params .= "s";
}

// Aplicar orden
$sql_abiertos .= " ORDER BY rp.hora_ingreso {$orden}";

// Preparar y ejecutar la consulta
$stmt = $conexion->prepare($sql_abiertos);
if (!empty($params)) {
    $stmt->bind_param($tipos_params, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();

// Consulta para obtener los tipos de vehículos desde la tabla tarifas
$sql_tipos_vehiculo = "SELECT DISTINCT tipo_vehiculo FROM tarifas";
$resultado_tipos_vehiculo = $conexion->query($sql_tipos_vehiculo);
$tipos_vehiculo = [];
if ($resultado_tipos_vehiculo->num_rows > 0) {
    while ($row = $resultado_tipos_vehiculo->fetch_assoc()) {
        $tipos_vehiculo[] = $row['tipo_vehiculo'];
    }
}

// Obtener todas las tarifas
$sql_tarifas = "SELECT * FROM tarifas";
$resultado_tarifas = $conexion->query($sql_tarifas);
$tarifas_por_tipo = [];

if ($resultado_tarifas && $resultado_tarifas->num_rows > 0) {
    while ($row = $resultado_tarifas->fetch_assoc()) {
        $tarifas_por_tipo[$row['tipo_vehiculo']] = $row;
    }
}

// Obtener todas las tolerancias
$sql_tolerancias = "SELECT tipo, tolerancia FROM tolerancia";
$resultado_tolerancias = $conexion->query($sql_tolerancias);
$tolerancias_por_tipo = [];

if ($resultado_tolerancias && $resultado_tolerancias->num_rows > 0) {
    while ($row = $resultado_tolerancias->fetch_assoc()) {
        $tolerancias_por_tipo[$row['tipo']] = intval($row['tolerancia']);
    }
}
?>