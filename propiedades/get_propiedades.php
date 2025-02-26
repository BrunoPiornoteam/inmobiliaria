<?php
include('../includes/db.php');

if (isset($_POST['cliente_id'])) {
    $cliente_id = $_POST['cliente_id'];
    $stmt = $pdo->prepare("SELECT id, titulo FROM propiedades WHERE cliente_id = ?");
    $stmt->execute([$cliente_id]);
    $propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($propiedades);
}
?>
