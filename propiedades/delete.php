<?php
include('../includes/db.php');

if (isset($_GET['id'])) {
    $propertyId = intval($_GET['id']);

    try {
        // Eliminar primero los contratos relacionados con esta propiedad
        $stmt = $pdo->prepare("DELETE FROM contratos WHERE propiedad_id = :id");
        $stmt->bindParam(':id', $propertyId, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $pdo->prepare("DELETE FROM propiedades WHERE id = :id");
        $stmt->bindParam(':id', $propertyId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header('Location: index.php?msg=Propiedad eliminada con éxito');
            exit();
        } else {
            header('Location: index.php?msg=Error al eliminar la propiedad');
            exit();
        }
    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }
} else {
    header('Location: index.php?msg=ID de propiedad no válido');
    exit();
}
?>
