
<?php
include('includes/db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT archivo FROM contratos WHERE id = ?");
    $stmt->execute([$id]);
    $contrato = $stmt->fetch();

    if ($contrato && !empty($contrato['archivo'])) {
        unlink('uploads/contratos/' . $contrato['archivo']);
    }

    $stmt = $pdo->prepare("DELETE FROM contratos WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: contratos.php");
    exit();
}
?>
