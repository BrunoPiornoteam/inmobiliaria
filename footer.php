</body>
<script src="/inmobiliaria/dist/js/index.js" type="text/javascript"></script>
<div class="chat-container" id="chatContainer" style="display: none;">
    <div class="messages" id="messages">
        <?php
        include('includes/db.php');
        $stmt = $pdo->query("SELECT * FROM chat_messages ORDER BY timestamp ASC");
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<div class="chat-icon" id="chatIcon">
    💬
</div>

<script>
    document.getElementById('chatIcon').addEventListener('click', function() {
        var chatContainer = document.getElementById('chatContainer');
        if (chatContainer.style.display === "none" || chatContainer.style.display === "") {
            chatContainer.style.display = "flex"; 
        } else {
            chatContainer.style.display = "none"; 
        }
    });

    document.getElementById('sendBtn').addEventListener('click', function () {
        var message = document.getElementById('message').value;
        if (message.trim() !== '') {
            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'message=' + encodeURIComponent(message)
            }).then(response => response.text())
              .then(data => {
                  document.getElementById('message').value = '';
                  loadMessages();
              });
        }
    });

    function loadMessages() {
        fetch('load_messages.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('messages').innerHTML = data;
            });
    }

    setInterval(loadMessages, 2000);
</script>
<style>
    /* styles.css */
.chat-container {
    width: 400px;
    height: 500px;
    border: 1px solid #ccc;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    position: fixed;
    bottom: 10px;
    right: 10px;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.chat-icon {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #007bff;
    color: white;
    padding: 15px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 24px;
}
</style>
</html>