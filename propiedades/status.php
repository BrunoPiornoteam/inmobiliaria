<?php
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($id && $status) {
        $query = $pdo->prepare("UPDATE propiedades SET estado = ? WHERE id = ?");
        $query->execute([$status, $id]);
    }
}

header("Location: single_propiedad.php?id=$id");
exit;
