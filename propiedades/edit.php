<?php
include('../includes/db.php');
include('../header.php');

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

<div class="dashboard-container">
    <h1>Editar Propiedad</h1>
    <form class="editar-propiedad" action="save_edit.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id ?>">

        <input type="text" name="titulo" placeholder="Titulo" value="<?= htmlspecialchars($propiedad['titulo']) ?>">

        <input type="number" name="precio" placeholder="Precio (USD)" value="<?= htmlspecialchars($propiedad['precio']) ?>">

        <input type="text" name="ubicacion" placeholder="Ubicación" value="<?= htmlspecialchars($propiedad['ubicacion']) ?>">

        <input type="number" name="tamano" placeholder="Superficie (m²)" value="<?= htmlspecialchars($propiedad['tamano']) ?>">

        <input type="number" name="dormitorios" placeholder="Dormitorios" value="<?= htmlspecialchars($propiedad['dormitorios']) ?>">

        <input type="number" name="banos" placeholder="Baños" value="<?= htmlspecialchars($propiedad['banos']) ?>">

        <textarea name="caracteristicas" placeholder="Características"><?= htmlspecialchars($propiedad['caracteristicas'] ?? '') ?></textarea>

        <input type="file" name="imagenes[]" accept="image/*" id="agregarImg" multiple>
        <label for="agregarImg" class="imagenes">Agregar Imágenes</label>

        <button type="submit">Guardar cambios</button>
    </form>

    <div class="property-profile__back-button"><a href="single_propiedad.php?id=<?= $id ?>" class="link"><i class="fas fa-arrow-left"></i> Volver a la propiedad</a></div>
</div>

<?php include('../footer.php'); ?>
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
