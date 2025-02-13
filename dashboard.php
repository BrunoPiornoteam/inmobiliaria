<?php
session_start();
include('includes/db.php');  // Incluir archivo de conexión a la base de datos
include('includes/header.php');  // Incluir encabezado

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Si no está logueado, redirige al login
    exit;
}

// Puedes opcionalmente obtener los detalles del usuario si los necesitas
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Si no se encuentra al usuario, redirige al login
    header('Location: login.php');
    exit;
}
?>

<div class="dashboard-container">
    <h1 class="welcome-title">Bienvenido al Panel de Administración</h1>
    <p class="welcome-description">Gestiona las propiedades, contratos, pagos y clientes de manera eficiente.</p>
    
    <!-- Muestra el nombre de usuario si lo necesitas -->
    <p>Bienvenido, <?php echo htmlspecialchars($user['username']); ?>.</p>

    <!-- Opcionalmente, puedes agregar enlaces para acceder a diferentes secciones del panel -->
    <p><a href="logout.php">Cerrar sesión</a></p>
</div>

<?php include('includes/footer.php'); ?>
