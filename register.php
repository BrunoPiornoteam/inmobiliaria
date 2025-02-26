<?php
include('includes/db.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); 

    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, correo, contrasena) VALUES (?, ?, ?)");
    $stmt->execute([$nombre_usuario, $correo, $contrasena]);

    echo "Usuario registrado con éxito.";
}
?>

<h1>Registrar Usuario</h1>
<form method="POST">
    <input type="text" name="nombre_usuario" placeholder="Nombre de usuario" required>
    <input type="email" name="correo" placeholder="Correo electrónico" required>
    <input type="password" name="contrasena" placeholder="Contraseña" required>
    <button type="submit">Registrar</button>
</form>