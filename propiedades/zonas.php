<?php
include('../includes/db.php');
include('../includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];

    $stmt = $pdo->prepare("INSERT INTO zonas (nombre) VALUES (?)");
    $stmt->execute([$nombre]);

    echo "Zona agregada con éxito.";
}

$stmt = $pdo->query("SELECT * FROM zonas");
$zonas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Zonas</h1>
<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre de la zona" required>
    <button type="submit">Agregar Zona</button>
</form>

<h2>Listado de Zonas</h2>
<ul>
    <?php foreach ($zonas as $zona): ?>
        <li><?php echo htmlspecialchars($zona['nombre']); ?></li>
    <?php endforeach; ?>
</ul>

<?php include('../includes/footer.php'); ?>