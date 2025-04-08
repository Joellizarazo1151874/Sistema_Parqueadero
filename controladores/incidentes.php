<?php
session_start();
include '../modelo/conexion.php';

// Configurar la zona horaria para Colombia
date_default_timezone_set('America/Bogota');

// Verificar si hay una conexión a la base de datos
if (!isset($conexion)) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

// Obtener la acción a realizar
$accion = isset($_GET['accion']) ? $_GET['accion'] : (isset($_POST['accion']) ? $_POST['accion'] : '');

switch ($accion) {
    case 'obtener_tickets_activos':
        obtenerTicketsActivos();
        break;
    case 'obtener_clientes':
        obtenerClientes();
        break;
    case 'obtener_incidentes_pendientes':
        obtenerIncidentesPendientes();
        break;
    case 'obtener_incidentes_resueltos':
        obtenerIncidentesResueltos();
        break;
    case 'obtener_detalle_incidente':
        obtenerDetalleIncidente();
        break;
    case 'registrar_incidente':
        registrarIncidente();
        break;
    case 'marcar_incidente_resuelto':
        marcarIncidenteResuelto();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

// Función para obtener tickets activos
function obtenerTicketsActivos() {
    global $conexion;
    
    $query = "SELECT r.id_registro, v.placa, v.tipo as tipo_vehiculo, v.id_cliente,
                     c.nombre as cliente_nombre, c.telefono as cliente_telefono
              FROM registros_parqueo r 
              LEFT JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo 
              LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
              WHERE r.estado = 'activo'";
    
    $result = $conexion->query($query);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener tickets: ' . $conexion->error]);
        return;
    }
    
    $tickets = [];
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
    
    echo json_encode($tickets);
}

// Función para obtener clientes
function obtenerClientes() {
    global $conexion;
    
    $query = "SELECT id_cliente, nombre, telefono FROM clientes ORDER BY nombre";
    
    $result = $conexion->query($query);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener clientes: ' . $conexion->error]);
        return;
    }
    
    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
    
    echo json_encode($clientes);
}

// Función para obtener incidentes pendientes
function obtenerIncidentesPendientes() {
    global $conexion;
    
    $query = "SELECT * FROM incidentes WHERE estado = 'pendiente' ORDER BY fecha_registro DESC LIMIT 20";
    
    $result = $conexion->query($query);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener incidentes: ' . $conexion->error]);
        return;
    }
    
    $incidentes = [];
    while ($row = $result->fetch_assoc()) {
        $incidentes[] = $row;
    }
    
    echo json_encode($incidentes);
}

// Función para obtener incidentes resueltos
function obtenerIncidentesResueltos() {
    global $conexion;
    
    $query = "SELECT * FROM incidentes WHERE estado = 'resuelto' ORDER BY fecha_registro DESC LIMIT 20";
    
    $result = $conexion->query($query);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener incidentes resueltos: ' . $conexion->error]);
        return;
    }
    
    $incidentes = [];
    while ($row = $result->fetch_assoc()) {
        $incidentes[] = $row;
    }
    
    echo json_encode($incidentes);
}

// Función para obtener detalle de un incidente
function obtenerDetalleIncidente() {
    global $conexion;
    
    try {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            throw new Exception('ID de incidente no proporcionado');
        }
        
        $idIncidente = $conexion->real_escape_string($_GET['id']);
        
        // Obtener información del incidente
        $query = "SELECT * FROM incidentes WHERE id_incidente = '$idIncidente'";
        
        $result = $conexion->query($query);
        
        if (!$result) {
            throw new Exception('Error en la consulta: ' . $conexion->error);
        }
        
        if ($result->num_rows == 0) {
            throw new Exception('Incidente no encontrado');
        }
        
        $incidente = $result->fetch_assoc();
        
        // Procesar evidencias desde el campo JSON
        $evidencias = [];
        if (!empty($incidente['evidencia'])) {
            // Intentar decodificar el JSON
            $evidenciasJson = json_decode($incidente['evidencia'], true);
            
            // Si es un array válido, procesarlo
            if (is_array($evidenciasJson)) {
                foreach ($evidenciasJson as $evidencia) {
                    // Construir la URL completa para la evidencia si tiene una URL
                    if (isset($evidencia['url'])) {
                        $evidencia['url_completa'] = '../../' . $evidencia['url'];
                    }
                    $evidencias[] = $evidencia;
                }
            } else {
                // Si no es un array válido, registrar el error
                error_log('Error al decodificar JSON de evidencias: ' . json_last_error_msg());
                error_log('Contenido del campo evidencia: ' . $incidente['evidencia']);
            }
        }
        
        // Obtener información del cliente si está asociado
        $cliente = null;
        if (!empty($incidente['id_cliente'])) {
            $queryCliente = "SELECT * FROM clientes WHERE id_cliente = '{$incidente['id_cliente']}'";
            $resultCliente = $conexion->query($queryCliente);
            
            if (!$resultCliente) {
                error_log('Error al consultar cliente: ' . $conexion->error);
            } else if ($resultCliente->num_rows > 0) {
                $cliente = $resultCliente->fetch_assoc();
            }
        }
        
        // Obtener información del ticket si está asociado
        $ticket = null;
        if (!empty($incidente['id_registro'])) {
            $queryTicket = "SELECT r.id_registro, v.placa, v.tipo as tipo_vehiculo 
                            FROM registros_parqueo r 
                            LEFT JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo 
                            WHERE r.id_registro = '{$incidente['id_registro']}'";
            
            $resultTicket = $conexion->query($queryTicket);
            
            if (!$resultTicket) {
                error_log('Error al consultar ticket: ' . $conexion->error);
            } else if ($resultTicket->num_rows > 0) {
                $ticket = $resultTicket->fetch_assoc();
            }
        }
        
        // Agregar información adicional al incidente
        $incidente['evidencias'] = $evidencias;
        $incidente['cliente'] = $cliente;
        $incidente['ticket'] = $ticket;
        
        echo json_encode($incidente);
        
    } catch (Exception $e) {
        error_log('Error en obtenerDetalleIncidente: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Función para registrar un nuevo incidente
function registrarIncidente() {
    global $conexion;
    
    // Verificar que se hayan enviado los datos necesarios
    if (!isset($_POST['tipo']) || empty($_POST['tipo']) || !isset($_POST['descripcion']) || empty($_POST['descripcion'])) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        return;
    }
    
    // Obtener datos del formulario
    $tipo = $conexion->real_escape_string($_POST['tipo']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $idRegistro = isset($_POST['id_registro']) && !empty($_POST['id_registro']) ? $conexion->real_escape_string($_POST['id_registro']) : null;
    $idCliente = isset($_POST['id_cliente']) && !empty($_POST['id_cliente']) ? $conexion->real_escape_string($_POST['id_cliente']) : null;
    
    // Iniciar transacción
    $conexion->begin_transaction();
    
    try {
        // Insertar incidente en la base de datos
        $query = "INSERT INTO incidentes (tipo, descripcion, id_registro, id_cliente, fecha_registro, estado) 
                  VALUES ('$tipo', '$descripcion', " . 
                  ($idRegistro ? "'$idRegistro'" : "NULL") . ", " . 
                  ($idCliente ? "'$idCliente'" : "NULL") . ", 
                  NOW(), 'pendiente')";
        
        if (!$conexion->query($query)) {
            throw new Exception('Error al registrar incidente: ' . $conexion->error);
        }
        
        $idIncidente = $conexion->insert_id;
        
        // Procesar evidencias si se han enviado
        $evidencias = [];
        if (isset($_FILES['evidencia']) && !empty($_FILES['evidencia']['name'][0])) {
            // Crear directorio para evidencias si no existe
            $directorioBase = '../uploads/evidencias/incidentes/';
            $directorioIncidente = $directorioBase . 'INC_' . $idIncidente . '/';
            
            if (!file_exists($directorioBase)) {
                mkdir($directorioBase, 0777, true);
            }
            
            if (!file_exists($directorioIncidente)) {
                mkdir($directorioIncidente, 0777, true);
            }
            
            // Procesar cada archivo
            foreach ($_FILES['evidencia']['name'] as $key => $nombre) {
                if ($_FILES['evidencia']['error'][$key] === UPLOAD_ERR_OK) {
                    $nombreOriginal = $nombre;
                    $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
                    $nombreArchivo = 'evidencia_' . uniqid() . '.' . $extension;
                    $rutaArchivo = $directorioIncidente . $nombreArchivo;
                    $rutaRelativa = 'uploads/evidencias/incidentes/INC_' . $idIncidente . '/' . $nombreArchivo;
                    
                    // Mover archivo al directorio
                    if (move_uploaded_file($_FILES['evidencia']['tmp_name'][$key], $rutaArchivo)) {
                        // Guardar información del archivo en la base de datos
                        $tipoArchivo = $_FILES['evidencia']['type'][$key];
                        $tamanoArchivo = $_FILES['evidencia']['size'][$key];
                        
                        $queryEvidencia = "INSERT INTO evidencias_incidentes (id_incidente, nombre, url, tipo, tamano) 
                                          VALUES ('$idIncidente', '$nombreOriginal', '$rutaRelativa', '$tipoArchivo', '$tamanoArchivo')";
                        
                        if (!$conexion->query($queryEvidencia)) {
                            throw new Exception('Error al registrar evidencia: ' . $conexion->error);
                        }
                        
                        $evidencias[] = [
                            'nombre' => $nombreOriginal,
                            'url' => $rutaRelativa,
                            'tipo' => $tipoArchivo
                        ];
                    } else {
                        throw new Exception('Error al subir archivo: ' . $nombreOriginal);
                    }
                }
            }
        }
        
        // Confirmar transacción
        $conexion->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Incidente registrado correctamente', 
            'id_incidente' => $idIncidente,
            'evidencias' => !empty($evidencias) ? count($evidencias) : 0
        ]);
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conexion->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Función para marcar un incidente como resuelto
function marcarIncidenteResuelto() {
    global $conexion;
    
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de incidente no proporcionado']);
        return;
    }
    
    $idIncidente = $conexion->real_escape_string($_POST['id']);
    
    $query = "UPDATE incidentes SET estado = 'resuelto' WHERE id_incidente = '$idIncidente'";
    
    $result = $conexion->query($query);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Error al marcar incidente como resuelto: ' . $conexion->error]);
        return;
    }
    
    echo json_encode(['success' => true, 'message' => 'Incidente marcado como resuelto correctamente']);
}
?>
