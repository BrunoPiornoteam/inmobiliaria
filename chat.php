<?php
// Incluir archivo de conexión a la base de datos
include('includes/db.php');

// Obtener los mensajes de la base de datos
$stmt = $pdo->query("SELECT * FROM chat_messages ORDER BY timestamp ASC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat de Soporte en Vivo</title>
    <style>
        /* Estilos para el chat */
        body {
            font-family: Arial, sans-serif;
        }
        .chat-container {
            width: 400px;
            height: 500px;
            border: 1px solid #ccc;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            position: fixed;
            bottom: 0;
            right: 20px;
            display: none; /* Inicialmente oculto */
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .messages {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            background-color: #f9f9f9;
            border-bottom: 1px solid #ddd;
            height: 350px;
        }
        .message {
            padding: 5px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .message .user {
            font-weight: bold;
        }
        .message .text {
            margin-left: 10px;
        }
        .chat-input {
            display: flex;
            padding: 10px;
            background-color: #f1f1f1;
        }
        .chat-input textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
        }
        .chat-input button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            margin-left: 5px;
            cursor: pointer;
        }
        /* Estilo para el icono flotante */
        .chat-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background-color: #007bff;
            color: white;
            font-size: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

<!-- Contenedor del chat -->
<div class="chat-container" id="chatContainer">
    <div class="messages" id="messages">
        <?php
        // Mostrar los mensajes existentes
        foreach ($messages as $message) {
            $user = sanitizeMessage($message['user']);
            $text = sanitizeMessage($message['message']);
            echo "<div class='message'><span class='user'>$user</span>: <span class='text'>$text</span></div>";
        }
        ?>
    </div>

    <div class="chat-input">
        <textarea id="message" rows="3" placeholder="Escribe un mensaje..."></textarea>
        <button id="sendBtn">Enviar</button>
    </div>
</div>

<!-- Icono flotante del chat -->
<div class="chat-icon" id="chatIcon">
    💬
</div>

<script>
    // Abrir y cerrar el chat al hacer clic en el icono flotante
    document.getElementById('chatIcon').addEventListener('click', function() {
        var chatContainer = document.getElementById('chatContainer');
        // Alternar la visibilidad del chat
        if (chatContainer.style.display === "none" || chatContainer.style.display === "") {
            chatContainer.style.display = "flex"; // Mostrar el chat
        } else {
            chatContainer.style.display = "none"; // Ocultar el chat
        }
    });

    document.getElementById('sendBtn').addEventListener('click', function () {
        var message = document.getElementById('message').value;
        if (message.trim() !== '') {
            // Enviar el mensaje al servidor
            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'message=' + encodeURIComponent(message)
            }).then(response => response.text())
              .then(data => {
                  // Limpiar el campo y actualizar los mensajes
                  document.getElementById('message').value = '';
                  loadMessages();
              });
        }
    });

    // Función para cargar los mensajes más recientes
    function loadMessages() {
        fetch('load_messages.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('messages').innerHTML = data;
            });
    }
    
    // Actualizar los mensajes cada 2 segundos
    setInterval(loadMessages, 2000);
</script>

</body>
</html>
