<?php
include('includes/db.php');

// Verificar si se recibió un mensaje
if (isset($_POST['message'])) {
    $message = $_POST['message'];
    $user_id = 1;

    // Convertir el timestamp Unix a formato de fecha y hora
    $timestamp = date('Y-m-d H:i:s', time());

    $stmt = $pdo->prepare("INSERT INTO chat_messages (user_id, message, timestamp) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $message, $timestamp]);

    // Debug: Verificar si la inserción fue exitosa
    if ($stmt->rowCount() > 0) {    
        echo 'Mensaje enviado con éxito';
    } else {
        echo 'Error al enviar el mensaje';
    }
} else {
    echo 'No se recibió ningún mensaje';
};
?>
