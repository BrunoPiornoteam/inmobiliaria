<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <!-- Enlaza el archivo CSS -->
    <link rel="stylesheet" href="dist/css/app.css">
</head>
<body>
<?php 
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<div class="navigation-buttons">
    <div class="admin-profile">
        <img src="src/uploads/default-profile.jpeg" alt="Perfil" class="profile-image">
        <p>Bienvenido, <?php echo isset($user) ? htmlspecialchars($user['nombre']) : 'Invitado'; ?>.</p>
        <p>Rol: <?php echo isset($user) ? htmlspecialchars($user['rol']) : 'No definido'; ?></p>
        <a href="logout.php">Cerrar sesión</a>
    </div>

    <button class="nav-button" onclick="window.location.href='dashboard.php'">Inicio</button>
    <button class="nav-button" onclick="window.location.href='propiedades.php'">Propiedades</button>
    <button class="nav-button" onclick="window.location.href='clientes.php'">Clientes</button>
    <button class="nav-button" onclick="window.location.href='contratos.php'">Contratos</button>
    <button class="nav-button" onclick="window.location.href='pagos.php'">Pagos</button>
</div>