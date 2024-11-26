<?php
include('includes/db.php');
include('includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $tipo = $_POST['tipo'];
    $ubicacion = $_POST['ubicacion'];
    $tamano = $_POST['tamano'];
    $imagenes = implode(',', $_FILES['imagenes']['name']); 

    $stmt = $pdo->prepare("INSERT INTO propiedades (titulo, descripcion, precio, tipo, ubicacion, tamano, imagenes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $descripcion, $precio, $tipo, $ubicacion, $tamano, $imagenes]);

    foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
        $file_name = $_FILES['imagenes']['name'][$key];
        move_uploaded_file($tmp_name, "uploads/$file_name");
    }

    echo "Propiedad agregada con éxito.";
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="titulo" placeholder="Título" required>
    <textarea name="descripcion" placeholder="Descripción" required></textarea>
    <input type="number" name="precio" placeholder="Precio" required>
    <select name="tipo" required>
        <option value="residencial">Residencial</option>
        <option value="comercial">Comercial</option>
        <option value="terreno">Terreno</option>
    </select>
    <input type="text" name="ubicacion" placeholder="Ubicación" required>
    <input type="number" name="tamano" placeholder="Tamaño (m2)" required>
    <input type="file" name="imagenes[]" multiple>
    <button type="submit">Agregar Propiedad</button>
</form>
