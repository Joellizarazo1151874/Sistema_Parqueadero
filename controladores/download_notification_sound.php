<?php
// Directorio donde se guardarán los sonidos
$soundsDir = '../vistas/assets/sounds';

// Asegurarse de que el directorio existe
if (!file_exists($soundsDir)) {
    mkdir($soundsDir, 0755, true);
}

// URL de un sonido de notificación de ejemplo (un sonido simple de dominio público)
$soundUrl = 'https://soundbible.com/grab.php?id=2156&type=mp3';
$notificationFile = $soundsDir . '/notification.mp3';

// Descargar el archivo
$fileContent = file_get_contents($soundUrl);
if ($fileContent !== false) {
    file_put_contents($notificationFile, $fileContent);
    echo json_encode(['success' => true, 'message' => 'Sonido de notificación descargado correctamente']);
} else {
    // Si no se puede descargar, crear un archivo vacío como respaldo
    file_put_contents($notificationFile, '');
    echo json_encode(['success' => false, 'message' => 'No se pudo descargar el sonido, se ha creado un archivo vacío']);
}
?>
