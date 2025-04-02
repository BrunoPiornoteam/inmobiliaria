<?php
include('../includes/db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

if (!$id || !$estado || !in_array($estado, ['pendiente', 'pagada', 'cancelada'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetros inválidos']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE facturas SET estado = ? WHERE id = ?");
    if ($stmt->execute([$estado, $id])) {
        echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar el estado']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
