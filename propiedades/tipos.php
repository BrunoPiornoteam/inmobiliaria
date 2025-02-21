<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo = $_POST['tipo'];
    $stmt = $pdo->prepare("INSERT INTO tipos_propiedades (tipo) VALUES (?)");
    $stmt->execute([$tipo]);

    echo "Tipo de propiedad agregado con éxito.";
}

$stmt = $pdo->query("SELECT * FROM tipos_propiedades");
$tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-container">
    <h1 class="welcome-title">Tipos de Propiedades</h1>
    <form method="POST">
        <input type="text" name="tipo" placeholder="Nombre del tipo" required>
        <button type="submit">Agregar Tipo</button>
    </form>

    <h2>Listado de Tipos</h2>
    <ul>
        <?php foreach ($tipos as $tipo): ?>
            <li><?php echo htmlspecialchars($tipo['tipo']); ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include('../includes/footer.php'); ?>
