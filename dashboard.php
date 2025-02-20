<?php
session_start();
include('includes/db.php');
include('includes/header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit;
}
?>

<div class="dashboard-container">
    <h1 class="welcome-title">Bienvenido al Panel de Administración</h1>
    <p class="welcome-description">Gestiona las propiedades, contratos, pagos y clientes de manera eficiente.</p>
    
    <p>Bienvenido, <?php echo htmlspecialchars(isset($user['username']) ? $user['username'] : 'Usuario'); ?>.</p>

</div>

<?php include('includes/footer.php'); ?>
