<?php
// Incluir archivo de conexión a la base de datos
include('includes/db.php');

try {
    // Obtener los mensajes de la base de datos
    $stmt = $pdo->query("SELECT * FROM chat_messages ORDER BY timestamp ASC");

    // Verificar si hay mensajes
    if ($stmt->rowCount() > 0) {
        // Obtener todos los mensajes
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mostrar los mensajes
        foreach ($messages as $message) {
            $user = htmlspecialchars($message['user']);  // Sanitizar para evitar inyección de código
            $text = htmlspecialchars($message['message']); // Sanitizar mensaje

            echo "<div class='message'>
                    <span class='user'>$user</span>: 
                    <span class='text'>$text</span>
                  </div>";
        }
    } else {
        // Si no hay mensajes en la base de datos
        echo "No hay mensajes disponibles.";
    }
} catch (PDOException $e) {
    // Manejo de errores
    echo "Error al obtener los mensajes: " . $e->getMessage();
}
?>
