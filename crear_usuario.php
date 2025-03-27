<?php
include('../db.php');

try {
    // Datos del nuevo usuario
    $nombre_usuario = 'admin';  
    $email = 'admin@inmobiliaria.com';  
    $contrasena = 'admin123';  
    $rol = 'administrador'; 
    $fecha_creacion = date('Y-m-d H:i:s'); 

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
