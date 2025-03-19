<?php
include('../includes/db.php');
include('../header.php');

if (!isset($_GET['id'])) {
    die("ID de cliente no proporcionado.");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID inválido.");
}

try {
    $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: clientes.php?mensaje=cliente_eliminado");
    exit;
} catch (PDOException $e) {
    die("Error al eliminar el cliente: " . $e->getMessage());
}
?>
