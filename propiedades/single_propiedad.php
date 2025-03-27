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

$query = $pdo->prepare("SELECT id, titulo, precio, ubicacion, tipo, tipo_operacion, descripcion, imagenes, estado, dormitorios, banos, superficie, pileta, quincho, placard FROM propiedades WHERE id = ?");
$query->execute([$id]);
$propiedad = $query->fetch(PDO::FETCH_ASSOC);

if (!$propiedad) {
    die("Propiedad no encontrada.");
}
$success = isset($_GET['success']) ? $_GET['success'] : 0;
?>

<div class="property-profile">
    <a href="index.php" class="property-profile__back-button"><i class="fas fa-arrow-left"></i> Volver</a>

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
        <?php
        $imagenes = explode(',', $propiedad['imagenes']); 
        foreach ($imagenes as $imagen) {
            echo "<img src='uploads/$imagen' alt='Imagen de la propiedad' class='property-profile__gallery-img'>";
        }
        ?>
    </div>

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
</div>
<script src="/inmobiliaria/src/js/propiedades.js"></script>

<style>
.property-profile {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.property-profile__back-button {
    display: inline-flex;
    align-items: center;
    font-size: 14px;
    color: #007bff;
    text-decoration: none;
    margin-bottom: 15px;
    transition: color 0.3s;
}

.property-profile__back-button:hover {
    color: #0056b3;
}

.property-profile__title {
    font-size: 24px;
    color: #333;
    text-align: center;
}

.property-profile__price {
    font-size: 20px;
    color: #28a745;
    font-weight: bold;
    text-align: center;
}

.property-profile__location,
.property-profile__type,
.property-profile__operation {
    font-size: 16px;
    color: #666;
    text-align: center;
}

.property-profile__description {
    margin-top: 20px;
}

.property-profile__description h3 {
    font-size: 18px;
    color: #333;
}

.property-profile__description p {
    font-size: 16px;
    color: #444;
    line-height: 1.5;
    text-align: justify;
}

.property-profile__features {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin: 20px 0;
}

.property-profile__feature {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    font-size: 14px;
}

.property-profile__feature i {
    margin-right: 8px;
    color: #28a745;
}

.property-profile__gallery {
    display: flex;
    overflow-x: auto;
    gap: 10px;
    padding: 10px 0;
}

.property-profile__gallery-img {
    width: 100%;
    max-height: 300px;
    object-fit: cover;
    border-radius: 8px;
}

.property-profile__actions {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin: 20px 0;
}

.property-profile__button {
    display: inline-flex;
    align-items: center;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
    color: white;
    transition: 0.3s;
}

.property-profile__button i {
    margin-right: 5px;
}

.property-profile__button--edit {
    background: #007bff;
}

.property-profile__button--edit:hover {
    background: #0056b3;
}

.property-profile__button--delete {
    background: #dc3545;
}

.property-profile__button--delete:hover {
    background: #a71d2a;
}

.property-profile__status-form {
    text-align: center;
}

.property-profile__button-status {
    background: #ffc107;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    color: #333;
    font-weight: bold;
}

.property-profile__button-status:hover {
    background: #e0a800;
}
</style>


<?php include('../footer.php'); ?>
