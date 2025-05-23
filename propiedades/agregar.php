<?php
include('../includes/db.php');
include('../header.php');

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
    $titulo = htmlspecialchars($_POST['titulo']);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $precio = filter_var($_POST['precio'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $tipo = htmlspecialchars($_POST['tipo']);
    $ubicacion = htmlspecialchars($_POST['ubicacion']);
    $tamano = filter_var($_POST['tamano'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $iframe = $_POST['iframe'];

    if (!isset($_POST['tipo_operacion']) || !in_array($_POST['tipo_operacion'], ['Venta', 'Alquiler'])) {
        echo "<script>alert('Debe seleccionar si la propiedad es de Venta o Alquiler.');</script>";
        exit;
    }
    $tipo_operacion = $_POST['tipo_operacion']; 

    // Subir las imágenes
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

    $imagenes = implode(',', $imagenesGuardadas);  // Convertir las imágenes a un string separado por comas

    try {
        $stmt = $pdo->prepare("INSERT INTO propiedades (titulo, descripcion, precio, tipo, ubicacion, tamano, tipo_operacion, imagenes, iframe) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $descripcion, $precio, $tipo, $ubicacion, $tamano, $tipo_operacion, $imagenes, $iframe]);

        echo "<script>
            Swal.fire({
                title: 'Éxito!',
                text: 'Propiedad agregada con éxito.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigir a la página index.php después de la confirmación
                    window.location.href = 'index.php'; 
                }
            });
          </script>";
    } catch (PDOException $e) {
        echo "Error al agregar propiedad: " . $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT tipo FROM tipos_propiedades");
$tiposPropiedades = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="dashboard-container">
    <h1>Agregar Nueva Propiedad</h1>
    <form method="POST" enctype="multipart/form-data" class="agregar">
        <input type="text" name="titulo" placeholder="Título" required>
        <input type="file" name="imagenes[]" multiple id="agregarImg">
        <label for="agregarImg" class="imagenes">Agregar Imágenes</label>
        <textarea name="descripcion" placeholder="Descripción" required></textarea>
        <fieldset name="tipo_operacion">
            <input type="radio" name="tipo_operacion" value="Venta" required id="venta">
            <label for="venta">Venta</label>
            <input type="radio" name="tipo_operacion" value="Alquiler" required id="alquiler">
            <label for="alquiler">Alquiler</label>
        </fieldset>
        <select name="tipo" required>
            <option value="" selected disabled>Tipo de Propiedad</option>
            <?php foreach ($tiposPropiedades as $tipo): ?>
                <option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo htmlspecialchars($tipo); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="precio" placeholder="Precio" required step="1000">
        <input type="text" name="ubicacion" placeholder="Ubicación" required>
        <textarea name="iframe" placeholder="Pega aquí el iframe del mapa (opcional)"></textarea>
        <input type="number" name="tamano" placeholder="Tamaño (m²)" required step="1">
        
        <button type="submit">Agregar Propiedad</button>
    </form>
</div>

<?php include('../footer.php'); ?>
