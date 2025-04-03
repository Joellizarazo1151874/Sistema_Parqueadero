<?php
// Habilitar los mensajes de error para depuración
error_reporting(E_ALL);
ini_set('display_errors', 0); // Desactivamos para evitar que los errores PHP interfieran con el JSON
ini_set('log_errors', 1);

// Establecer encabezado de respuesta JSON
header('Content-Type: application/json');

// Iniciar buffer de salida para capturar cualquier error o salida inesperada
ob_start();

// Registrar datos recibidos para depuración
error_log("agregar_usuario.php - POST recibido: " . print_r($_POST, true));

// Incluir el archivo de conexión
include_once '../modelo/conexion.php';

// Verificar que la conexión esté disponible
if (!isset($conexion) || $conexion->connect_error) {
    ob_end_clean(); // Limpiamos cualquier salida
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos: ' . ($conexion->connect_error ?? 'Conexión no disponible')]);
    exit;
}

// Log para depurar - modificamos para aceptar nombres de campos específicos según el formulario
if (isset($_POST['nombre']) && isset($_POST['correo']) && isset($_POST['contrasena']) && isset($_POST['rol'])) {
    error_log("agregar_usuario.php - Todos los campos requeridos están presentes");
    
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $correo = $conexion->real_escape_string($_POST['correo']);
    $contrasena = $_POST['contrasena']; // No escapamos porque la vamos a cifrar
    $rol = $conexion->real_escape_string($_POST['rol']);
    
    error_log("agregar_usuario.php - Valores: nombre=$nombre, correo=$correo, rol=$rol");
    
    // Validar campos
    if (empty($nombre) || empty($correo) || empty($contrasena) || empty($rol)) {
        error_log("agregar_usuario.php - Algún campo está vacío");
        ob_end_clean(); // Limpiamos cualquier salida
        echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios.']);
        exit;
    }
    
    // Validar formato de correo electrónico
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        error_log("agregar_usuario.php - Formato de correo inválido: $correo");
        ob_end_clean(); // Limpiamos cualquier salida
        echo json_encode(['success' => false, 'error' => 'El formato del correo electrónico no es válido.']);
        exit;
    }
    
    // Verificar si el correo ya existe
    $checkCorreo = $conexion->query("SELECT correo FROM usuarios WHERE correo = '$correo'");
    if ($checkCorreo->num_rows > 0) {
        error_log("agregar_usuario.php - Correo ya existe: $correo");
        ob_end_clean(); // Limpiamos cualquier salida
        echo json_encode(['success' => false, 'error' => 'Este correo electrónico ya está registrado. Por favor use otro.']);
        exit;
    }
    
    // Cifrar la contraseña
    $contrasena_cifrada = SHA1($contrasena);
    
    // Fecha actual para el registro
    $fecha_registro = date('Y-m-d H:i:s');
    
    // Consulta para insertar el nuevo usuario - Corregimos el nombre del campo a 'contraseña'
    $sql = "INSERT INTO usuarios (nombre, correo, contraseña, rol, fecha_registro) 
            VALUES ('$nombre', '$correo', '$contrasena_cifrada', '$rol', '$fecha_registro')";
    
    error_log("agregar_usuario.php - Ejecutando SQL: $sql");
    
    if ($conexion->query($sql) === TRUE) {
        error_log("agregar_usuario.php - Usuario agregado correctamente");
        ob_end_clean(); // Limpiamos cualquier salida
        echo json_encode(['success' => true, 'message' => 'Usuario agregado correctamente.']);
    } else {
        error_log("agregar_usuario.php - Error al agregar usuario: " . $conexion->error);
        ob_end_clean(); // Limpiamos cualquier salida
        echo json_encode(['success' => false, 'error' => 'Error al agregar el usuario: ' . $conexion->error]);
    }
} else {
    // Detalle exactamente qué parámetros faltan
    $parametros_faltantes = [];
    if (!isset($_POST['nombre'])) $parametros_faltantes[] = 'nombre';
    if (!isset($_POST['correo'])) $parametros_faltantes[] = 'correo';
    if (!isset($_POST['contrasena'])) $parametros_faltantes[] = 'contrasena';
    if (!isset($_POST['rol'])) $parametros_faltantes[] = 'rol';
    
    error_log("agregar_usuario.php - Faltan parámetros: " . implode(', ', $parametros_faltantes));
    ob_end_clean(); // Limpiamos cualquier salida
    echo json_encode([
        'success' => false, 
        'error' => 'Faltan parámetros necesarios para agregar el usuario: ' . implode(', ', $parametros_faltantes),
        'received' => array_keys($_POST)
    ]);
}

$conexion->close();
?> 