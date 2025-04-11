<?php
include('../includes/db.php');
include('../header.php');

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Propiedad no encontrada.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $estado = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE propiedades SET estado = ? WHERE id = ?");
    $stmt->execute([$estado, $id]);

    header("Location: single_propiedad.php?id=$id&success=1");
    exit;
}

$query = $pdo->prepare("SELECT id, titulo, precio, ubicacion, tipo, tipo_operacion, descripcion, imagenes, estado, dormitorios, banos, superficie, pileta, quincho, placard, iframe FROM propiedades WHERE id = ?");
$query->execute([$id]);
$propiedad = $query->fetch(PDO::FETCH_ASSOC);

if (!$propiedad) {
    die("Propiedad no encontrada.");
}
$success = isset($_GET['success']) ? $_GET['success'] : 0;
?>

<div class="dashboard-container">
    <div class="property-profile">
        <h1 class="property-profile__title"><?php echo htmlspecialchars($propiedad['titulo']); ?></h1>

        <p class="property-profile__price"><?php echo number_format($propiedad['precio'], 2, ',', '.'); ?> USD</p>
        <p class="property-profile__location">Ubicación: <?php echo htmlspecialchars($propiedad['ubicacion']); ?></p>
        <p class="property-profile__type">Tipo: <?php echo htmlspecialchars($propiedad['tipo']); ?></p>
        <p class="property-profile__operation">Operación: <?php echo htmlspecialchars($propiedad['tipo_operacion']); ?></p>
        
        <div class="property-profile__description">
            <h3>Descripción</h3>
            <p><?php echo nl2br(htmlspecialchars($propiedad['descripcion'])); ?></p>
        </div>

        <div class="property-profile__features">
            <div class="property-profile__feature"><i class="fas fa-bed"></i> Dormitorios: <?php echo htmlspecialchars($propiedad['dormitorios'] ?? 'No especificado'); ?></div>
            <div class="property-profile__feature"><i class="fas fa-bath"></i> Baños: <?php echo htmlspecialchars($propiedad['banos'] ?? 'No especificado'); ?></div>
            <div class="property-profile__feature"><i class="fas fa-home"></i> Superficie: <?php echo htmlspecialchars($propiedad['superficie'] ?? 'No especificado'); ?> m²</div>
            <div class="property-profile__feature"><i class="fas fa-swimmer"></i> Pileta: <?php echo ($propiedad['pileta'] ?? 0) ? 'Sí' : 'No'; ?></div>
            <div class="property-profile__feature"><i class="fas fa-fireplace"></i> Quincho: <?php echo ($propiedad['quincho'] ?? 0) ? 'Sí' : 'No'; ?></div>
            <div class="property-profile__feature"><i class="fas fa-closet"></i> Placard: <?php echo ($propiedad['placard'] ?? 0) ? 'Sí' : 'No'; ?></div>
        </div>

        <div class="property-profile__gallery">
            <?php $imagenes = explode(',', $propiedad['imagenes']); 
            foreach ($imagenes as $imagen) {
                echo "<img src='uploads/$imagen' alt='Imagen de la propiedad' class='property-profile__gallery-img'>";
            } ?>
        </div>

        <?php if (!empty($propiedad['iframe'])): ?>
            <div class="property-profile__map">
                <h3>Ubicación en el mapa</h3>
                    <div class="iframe-container">
                        <?php echo html_entity_decode($propiedad['iframe']); ?>
                    </div>
                </div>
            <?php endif; ?>

        <div class="property-profile__actions">
            <a href="edit.php?id=<?= $propiedad['id'] ?>" class="property-profile__button property-profile__button--edit">Editar <i class="fas fa-edit"></i></a>
            <a href="#" class="property-profile__button property-profile__button--delete" onclick="confirmDelete(<?= $propiedad['id'] ?>)">
                Eliminar <i class="fas fa-trash-alt"></i>
            </a>

            <script>
            function confirmDelete(propertyId) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirige al enlace de eliminación si el usuario confirma
                        window.location.href = 'delete.php?id=' + propertyId;
                    }
                });
            }
            </script>
        </div>

        <form action="status.php" method="post" class="property-profile__status-form" id="status-form">
            <input type="hidden" name="id" value="<?= $propiedad['id'] ?>">

            <select name="status" class="property-profile__button-status">
                <option value="Disponible" <?= $propiedad['estado'] == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                <option value="Vendida" <?= $propiedad['estado'] == 'Vendida' ? 'selected' : '' ?>>Vendida</option>
                <option value="Alquilada" <?= $propiedad['estado'] == 'Alquilada' ? 'selected' : '' ?>>Alquilada</option>
            </select>

            <button type="submit" class="property-profile__button-status" id="update-button">Actualizar</button>
        </form>

        <div class="property-profile__back-button">
            <a href="index.php" class="link"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

    </div>
</div>
<script src="/inmobiliaria/src/js/propiedades.js"></script>

<?php include('../footer.php'); ?>
