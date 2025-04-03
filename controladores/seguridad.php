<?php
// Verificar si existe la sesión 'datos_login'
if (!isset($_SESSION['datos_login'])) {
    header("location: ../formularios/index.php");
    exit();
}

$arregloUsuario = $_SESSION['datos_login'];


// Si el usuario no es administrador, redirigir
if ($arregloUsuario['rol'] != 'operador' && $arregloUsuario['rol'] != 'administrador') {
    header("location:../formularios/index.php");
    exit();
}
?>