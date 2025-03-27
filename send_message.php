<?php
include('includes/db.php');

// Verificar si se recibió un mensaje
if (isset($_POST['message'])) {
    $message = $_POST['message'];
    $user = 'Usuario';  // O podrías ponerlo dinámicamente si tienes un sistema de login

    // Insertar el mensaje en la base de datos
    $stmt = $pdo->prepare("INSERT INTO chat_messages (user, message, timestamp) VALUES (?, ?, ?)");
    $stmt->execute([$user, $message, time()]);

    echo 'Mensaje enviado con éxito';
}
?>
