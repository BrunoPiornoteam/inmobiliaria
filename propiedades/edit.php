<?php
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Propiedad no encontrada.";
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM propiedades WHERE id = ?");
$stmt->execute([$id]);
$propiedad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$propiedad) {
    echo "Propiedad no encontrada.";
    exit;
}
?>

<div class="edit-container">
    <h1>Editar Propiedad</h1>
    <form action="save_edit.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id ?>">

        <label>Título:</label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($propiedad['titulo']) ?>">

        <label>Precio (USD):</label>
        <input type="number" name="precio" value="<?= htmlspecialchars($propiedad['precio']) ?>">

        <label>Ubicación:</label>
        <input type="text" name="ubicacion" value="<?= htmlspecialchars($propiedad['ubicacion']) ?>">

        <label>Superficie (m²):</label>
        <input type="number" name="tamano" value="<?= htmlspecialchars($propiedad['tamano']) ?>">

        <label>Dormitorios:</label>
        <input type="number" name="dormitorios" value="<?= htmlspecialchars($propiedad['dormitorios']) ?>">

        <label>Baños:</label>
        <input type="number" name="banos" value="<?= htmlspecialchars($propiedad['banos']) ?>">

        <label>Características:</label>
        <textarea name="caracteristicas"><?= htmlspecialchars($propiedad['caracteristicas'] ?? '') ?></textarea>

        <!-- Campo para imágenes -->
        <label>Imágenes (separa con comas):</label>
        <input type="file" name="imagenes[]" accept="image/*" multiple>

        <button type="submit">Guardar cambios</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
<?php if (isset($_GET['success']) && $_GET['success'] == 'true') : ?>
    <script>
        Swal.fire({
            title: 'Éxito!',
            text: 'La propiedad ha sido editada con éxito.',
            icon: 'success',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'single_propiedad.php?id=<?= $id ?>'; 
            }
        });
    </script>
<?php endif; ?>
