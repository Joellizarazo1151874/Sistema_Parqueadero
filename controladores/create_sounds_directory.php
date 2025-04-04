<?php
// Create the sounds directory if it doesn't exist
$soundsDir = '../vistas/assets/sounds';
if (!file_exists($soundsDir)) {
    mkdir($soundsDir, 0755, true);
}

// Create a sample notification sound file if it doesn't exist
$notificationFile = $soundsDir . '/notification.mp3';
if (!file_exists($notificationFile)) {
    // This is a simple approach - in a real implementation, you'd include an actual sound file
    // For now, we'll copy a placeholder sound file or create an empty one
    $placeholderSound = '../vistas/assets/sounds/notification.mp3';
    if (file_exists($placeholderSound)) {
        copy($placeholderSound, $notificationFile);
    } else {
        // Create an empty file as a placeholder
        file_put_contents($notificationFile, '');
    }
}

echo json_encode(['success' => true]);
?>
