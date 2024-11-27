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

$stmt = $pdo->query("SELECT * FROM propiedades");
$propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Agregar Propiedad</h1>
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
<h2>Listado de Propiedades</h2>
<table>
    <thead>
        <tr>
            <th>Título</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Tipo</th>
            <th>Ubicación</th>
            <th>Tamaño (m2)</th>
            <th>Imágenes</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($propiedades as $propiedad): ?>
            <tr>
                <td><?php echo htmlspecialchars($propiedad['titulo']); ?></td>
                <td><?php echo htmlspecialchars($propiedad['descripcion']); ?></td>
                <td><?php echo htmlspecialchars($propiedad['precio']); ?></td>
                <td><?php echo htmlspecialchars($propiedad['tipo']); ?></td>
                <td><?php echo htmlspecialchars($propiedad['ubicacion']); ?></td>
                <td><?php echo htmlspecialchars($propiedad['tamano']); ?></td>
                <td>
                    <?php 
                    $imagenes = explode(',', $propiedad['imagenes']);
                    foreach ($imagenes as $imagen): ?>
                        <img src="uploads/<?php echo htmlspecialchars($imagen); ?>" alt="Imagen de la propiedad" width="100">
                    <?php endforeach; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include('includes/footer.php'); ?>