<?php
include('../includes/db.php');
include('../header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo = trim($_POST['tipo']);
    
    // Validar que el tipo no esté vacío
    if (empty($tipo)) {
        $message = "El nombre del tipo no puede estar vacío.";
    } else {
        // Comprobar si el tipo ya existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tipos_propiedades WHERE tipo = ?");
        $stmt->execute([$tipo]);
        $existe = $stmt->fetchColumn();

        if ($existe > 0) {
            $message = "El tipo de propiedad ya existe.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO tipos_propiedades (tipo) VALUES (?)");
                $stmt->execute([$tipo]);
                $message = "Tipo de propiedad agregado con éxito.";
            } catch (PDOException $e) {
                $message = "Error al agregar tipo: " . $e->getMessage();
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM tipos_propiedades");
$tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-container listado-propiedades">
    <h1 class="welcome-title">Tipos de Propiedades</h1>
    
    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <form method="POST" class="tipo-propiedades">
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

<?php include('../footer.php'); ?>
