<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar configuración
$config = require_once __DIR__ . '/../config.php';
$apiKey = $config['huggingface_token'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Mensaje no proporcionado']);
    exit;
}

// Contexto del sistema para el asistente
$systemContext = "Eres un asistente virtual especializado en el sistema de gestión inmobiliaria. 
Debes responder en español de Argentina (usando 'vos' en lugar de 'tú'). 
Conoces todas las funcionalidades del sistema:
- Gestión de propiedades: listado, búsqueda, filtros
- Contratos: creación, seguimiento, renovación
- Pagos: estado de cuenta, facturas, recordatorios
- Clientes: datos, historial, comunicaciones
Sé amable, profesional y usa emojis ocasionalmente para hacer la conversación más amena.";

// Preparar el mensaje completo
$message = $systemContext . "\n\nUsuario: " . $input['message'] . "\n\nAsistente:";

// Debug: Mostrar los datos que se enviarán
error_log("Mensaje a enviar: " . $message);

// Configurar la solicitud a Hugging Face
$ch = curl_init('https://api-inference.huggingface.co/models/google/flan-t5-small');

// Habilitar el seguimiento de errores de CURL
curl_setopt($ch, CURLOPT_VERBOSE, true);
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

// Parámetros para el modelo
$parameters = [
    'inputs' => $message,
    'parameters' => [
        'max_length' => 200,
        'temperature' => 0.7,
        'top_p' => 0.9,
        'return_full_text' => false
    ]
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

// Debug: Mostrar los parámetros
error_log("Parámetros enviados: " . json_encode($parameters));

// Realizar la solicitud
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Debug: Mostrar información detallada
rewind($verbose);
$verboseLog = stream_get_contents($verbose);
error_log("Verbose curl log: " . $verboseLog);
error_log("HTTP Code: " . $httpCode);
error_log("Response: " . $response);

// Manejar errores de CURL
if (curl_errno($ch)) {
    error_log('Error CURL: ' . curl_error($ch));
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en la comunicación con el servicio',
        'details' => curl_error($ch)
    ]);
    exit;
}

curl_close($ch);
fclose($verbose);

// Verificar la respuesta
if ($httpCode !== 200) {
    error_log('Error en la API de Hugging Face: ' . $response);
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al procesar la solicitud',
        'details' => $response
    ]);
    exit;
}

// Procesar la respuesta
try {
    $responseData = json_decode($response, true);
    
    if (isset($responseData[0]['generated_text'])) {
        // Extraer solo la respuesta del asistente
        $fullResponse = $responseData[0]['generated_text'];
        $assistantResponse = trim(substr($fullResponse, strpos($fullResponse, 'Asistente:') + 10));
        
        echo json_encode(['response' => $assistantResponse]);
    } else {
        throw new Exception('Formato de respuesta inválido: ' . $response);
    }
} catch (Exception $e) {
    error_log('Error al procesar la respuesta: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al procesar la respuesta',
        'details' => $e->getMessage()
    ]);
}
