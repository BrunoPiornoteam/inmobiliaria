// Configuración del chatbot
const chatbot = {
    name: 'Asistente Inmobiliario IA',
    initialMessage: '👋 ¡Hola! Bienvenido/a, soy tu asistente virtual inmobiliario con IA 😊 ¿En qué puedo ayudarte hoy?'
};

// Crear elementos del chatbot
function createChatbotElements() {
    const chatbotHTML = `
        <div class="chatbot" id="chatbot">
            <div class="chatbot-header" id="chatbot-header">
                <div class="chatbot-title">
                    <i class="fas fa-robot"></i> ${chatbot.name}
                </div>
                <div class="chatbot-controls">
                    <button class="control-button" id="minimize-chatbot">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="chatbot-body">
                <div class="chatbot-messages" id="chatbot-messages"></div>
                <div class="chatbot-input">
                    <textarea 
                        id="user-message" 
                        placeholder="Escribe tu mensaje..."
                        rows="1"
                    ></textarea>
                    <button id="send-message">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', chatbotHTML);
}

// Inicializar el chatbot
function initChatbot() {
    createChatbotElements();
    
    const chatbotElement = document.getElementById('chatbot');
    const minimizeButton = document.getElementById('minimize-chatbot');
    const messagesContainer = document.getElementById('chatbot-messages');
    const userInput = document.getElementById('user-message');
    const sendButton = document.getElementById('send-message');

    // Mostrar mensaje inicial
    addMessage('bot', chatbot.initialMessage);

    // Evento para minimizar/maximizar
    minimizeButton.addEventListener('click', () => {
        chatbotElement.classList.toggle('minimized');
        minimizeButton.querySelector('i').classList.toggle('fa-minus');
        minimizeButton.querySelector('i').classList.toggle('fa-plus');
    });

    // Ajustar altura del textarea
    userInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Enviar mensaje
    async function sendMessage() {
        const message = userInput.value.trim();
        if (!message) return;

        // Limpiar input y ajustar altura
        userInput.value = '';
        userInput.style.height = 'auto';
        
        // Mostrar mensaje del usuario
        addMessage('user', message);

        // Mostrar indicador de escritura
        const typingIndicator = document.createElement('div');
        typingIndicator.className = 'message bot typing';
        typingIndicator.innerHTML = '<span>.</span><span>.</span><span>.</span>';
        messagesContainer.appendChild(typingIndicator);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        try {
            // Deshabilitar input mientras se procesa
            userInput.disabled = true;
            sendButton.disabled = true;

            // Enviar solicitud al servidor
            const response = await fetch('/inmobiliaria/api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message })
            });

            // Eliminar indicador de escritura
            typingIndicator.remove();

            if (!response.ok) {
                throw new Error('Error en la comunicación');
            }

            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error);
            }

            // Mostrar respuesta del bot
            addMessage('bot', data.response);

        } catch (error) {
            console.error('Error:', error);
            // Eliminar indicador de escritura si aún existe
            if (typingIndicator.parentNode) {
                typingIndicator.remove();
            }
            addMessage('bot', '😅 ¡Ups! Tuve un pequeño problema técnico. ¿Podrías intentarlo de nuevo?');
        } finally {
            // Rehabilitar input
            userInput.disabled = false;
            sendButton.disabled = false;
            userInput.focus();
        }
    }

    // Eventos para enviar mensaje
    sendButton.addEventListener('click', sendMessage);
    userInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
}

// Agregar mensaje al chat
function addMessage(type, text) {
    const messagesContainer = document.getElementById('chatbot-messages');
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('message', type);
    messageDiv.textContent = text;
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initChatbot);
