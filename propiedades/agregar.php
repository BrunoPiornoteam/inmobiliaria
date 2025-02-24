<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $tipo = $_POST['tipo'];
    $ubicacion = $_POST['ubicacion'];
    $tamano = $_POST['tamano'];

    $uploadDir = '../src/uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imagenesGuardadas = [];

    foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
        $file_name = $_FILES['imagenes']['name'][$key];

        $file_name = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file_name);
        
        $filePath = $uploadDir . $file_name;

        if (move_uploaded_file($tmp_name, $filePath)) {
            $imagenesGuardadas[] = $file_name;
        }
    }

    $imagenes = implode(',', $imagenesGuardadas);

    $stmt = $pdo->prepare("INSERT INTO propiedades (titulo, descripcion, precio, tipo, ubicacion, tamano, imagenes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $descripcion, $precio, $tipo, $ubicacion, $tamano, $imagenes]);

    echo "Propiedad agregada con éxito.";
}
?>

<h1>Agregar Nueva Propiedad</h1>
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
    <input type="file" name="imagenes[]" multiple required>
    <button type="submit">Agregar Propiedad</button>
</form>

<?php include('../includes/footer.php'); ?>
