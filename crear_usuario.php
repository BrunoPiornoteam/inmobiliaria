<?php
// Conexión a la base de datos (ajusta los parámetros)
$host = 'localhost';
$dbname = 'inmobiliaria';  // El nombre de tu base de datos
$username = 'root';  // Tu usuario de MySQL
$password = 'root';  // Tu contraseña de MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Datos del nuevo usuario
    $nombre_usuario = 'admin';  // Nombre de usuario que desees
    $email = 'admin@inmobiliaria.com';  // Correo del usuario
    $contrasena = 'admin123';  // Contraseña en texto plano
    $rol = 'administrador'; // Rol del usuario
    $fecha_creacion = date('Y-m-d H:i:s'); // Fecha de creación actual

    // Encriptar la contraseña
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO usuarios (nombre, email, password, rol, fecha_creacion, nombre_usuario, correo) 
            VALUES (:nombre, :email, :password, :rol, :fecha_creacion, :nombre_usuario, :correo)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre_usuario);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $contrasena_hash);
    $stmt->bindParam(':rol', $rol);
    $stmt->bindParam(':fecha_creacion', $fecha_creacion);
    $stmt->bindParam(':nombre_usuario', $nombre_usuario);
    $stmt->bindParam(':correo', $email);

    $stmt->execute();

    echo "Usuario creado correctamente!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
