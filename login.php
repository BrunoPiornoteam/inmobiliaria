<?php
session_start();
include('includes/db.php');  

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');  
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_usuario = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ?");
    $stmt->execute([$nombre_usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];  
        header('Location: index.php'); 
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inmobiliaria</title>
    <link rel="stylesheet" href="dist/css/app.css">
</head>
<body>
    <div class="login-container">
        <!-- Logo y texto descriptivo del sistema inmobiliario -->
        <div class="logo-container">
            <img src="src/uploads/inmobiliaria.svg" alt="Logo Inmobiliaria" class="logo-inm">
            <h3 class="system-description">Sistema Inmobiliario</h3>
        </div>

        <!-- Mostrar error si las credenciales son incorrectas -->
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulario de login -->
        <form method="POST" class="login">
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
    <div class="login-bg">
        <img src="src/uploads/inmobiliaria-bg.jpg" alt="Inmobiliaria" class="bg-inm">
    </div>
</body>
</html>
