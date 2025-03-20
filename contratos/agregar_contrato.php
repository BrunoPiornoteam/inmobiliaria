<?php
include('../includes/db.php');
include('../header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit;
}

// Obtén todas las propiedades
$stmt = $pdo->query("SELECT id, titulo FROM propiedades");
$propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtén todos los clientes
$stmt = $pdo->query("SELECT id, nombre FROM clientes ORDER BY id DESC");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []; 

// Procesar la solicitud POST cuando se agrega un contrato
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $propiedad_id = $_POST['propiedad_id'];
    $cliente_id = $_POST['cliente_id'];
    $tipo_contrato = $_POST['tipo_contrato'];
    $precio = $_POST['precio'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    $archivo_nombre = null;
    if (!empty($_FILES['archivo']['name'])) {
        $directorio = 'uploads/contratos/';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        $archivo_nombre = time() . "_" . basename($_FILES['archivo']['name']);
        $archivo_ruta = $directorio . $archivo_nombre;

        if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $archivo_ruta)) {
            echo "<script>Swal.fire('Error', 'Error al subir el archivo.', 'error');</script>";
            $archivo_nombre = null;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO contratos (propiedad_id, cliente_id, tipo_contrato, precio, fecha_inicio, fecha_fin, archivo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$propiedad_id, $cliente_id, $tipo_contrato, $precio, $fecha_inicio, $fecha_fin, $archivo_nombre])) {
        echo "<script>Swal.fire('Éxito', 'Contrato agregado con éxito.', 'success');</script>";
    } else {
        echo "<script>Swal.fire('Error', 'Hubo un problema al agregar el contrato.', 'error');</script>";
    }
}
?>

<section class="dashboard-container">
    <div class="contrato-content">
        <h1>Agregar Contrato</h1>
        <form method="POST" enctype="multipart/form-data" class="contrato">
            <select name="cliente_id" id="cliente_id" required>
                <option value="">Selecciona un cliente</option>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?php echo htmlspecialchars($cliente['id']); ?>">
                        <?php echo htmlspecialchars($cliente['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="propiedad_id" id="propiedad_id" required>
                <option value="">Selecciona una propiedad</option>
                <?php foreach ($propiedades as $propiedad): ?>
                    <option value="<?php echo htmlspecialchars($propiedad['id']); ?>">
                        <?php echo htmlspecialchars($propiedad['titulo']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="tipo_contrato" required>
                <option value="venta">Venta</option>
                <option value="alquiler">Alquiler</option>
            </select>

            <input type="number" name="precio" placeholder="Precio" required>
            <input type="date" name="fecha_inicio" required>
            <input type="date" name="fecha_fin" required>
            
            <input type="file" name="archivo" accept=".pdf,.jpg,.png,.jpeg" id="agregarImg">
            <label class="imagenes" for="agregarImg">Adjuntar contrato (PDF, imagen):</label>

            <button type="submit" name="action" value="add">Agregar Contrato</button>
        </form>

        <a href="ver_contratos.php" class="link">Ver Contratos</a>
    </div>
</section>
<?php include('../footer.php'); ?>
