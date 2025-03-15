<?php
session_start(); // Inicia la sesión o reanuda una sesión existente.

include "../modelo/conexion.php"; // Incluye el archivo de conexión a la base de datos.

if (isset($_POST['usuario']) && isset($_POST['password'])) {
    // Verifica que se hayan enviado los campos 'usuario' y 'password' a través de POST.

    // Prepara la consulta SQL para evitar inyección SQL
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ? AND contraseña = ?");
    
    // Verifica si la preparación de la consulta fue exitosa
    if ($stmt === false) {
        die('Error en la preparación de la consulta: ' . $conexion->error);
    }
    
    // Genera el hash de la contraseña
    $passwordHash = sha1($_POST['password']);
    
    // Vincula los parámetros a la consulta
    $stmt->bind_param("ss", $_POST['usuario'], $passwordHash);
    
    // Ejecuta la consulta
    if (!$stmt->execute()) {
        die('Error al ejecutar la consulta: ' . $stmt->error);
    }
    
    // Obtiene el resultado
    $resultado = $stmt->get_result();

    // Verifica si hay un usuario con esas credenciales
    if ($resultado->num_rows > 0) {
        $datos_usuario = $resultado->fetch_assoc();
        // Asegúrate de usar los nombres correctos de las columnas
        $_SESSION['nombre'] = $datos_usuario['nombre']; // Usa el nombre real de la columna
        $_SESSION['id_usuario'] = $datos_usuario['id']; // Usa el nombre real de la columna
        $_SESSION['rol'] = $datos_usuario['rol']; // Usa el nombre real de la columna
        

        // Redirige al usuario a la página de registro.
        header("location: ../vistas/estructuras/gestion.php");
    } else {
        // Si no hay resultados, redirige al usuario a la página de inicio de sesión con un mensaje de error.
        header("location: ../vistas/formularios/index.php?error=Credenciales incorrectas");
    }

    // Cierra la declaración
    $stmt->close();
} else {
    // Si los campos 'usuario' o 'password' no están definidos, redirige al usuario a la página de inicio de sesión.
    header("location: ../vistas/formularios/index.php");
}
$conexion->close();
?>