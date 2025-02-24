<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit;
}

$stmt = $pdo->query("SELECT * FROM propiedades ORDER BY id DESC");
$propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-container">
    <h1 class="welcome-title">Listado de Propiedades</h1>
    <a href="agregar.php" class="button--blue">Agregar Nueva Propiedad</a>
    
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Tipo</th>
                <th>Ubicación</th>
                <th>Tamaño</th>
                <th>Imágenes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($propiedades as $propiedad): ?>
                <tr>
                    <td><?php echo htmlspecialchars($propiedad['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($propiedad['descripcion']); ?></td>
                    <td><?php echo number_format($propiedad['precio'], 2, ',', '.'); ?> USD</td>
                    <td><?php echo htmlspecialchars($propiedad['tipo']); ?></td>
                    <td><?php echo htmlspecialchars($propiedad['ubicacion']); ?></td>
                    <td><?php echo htmlspecialchars($propiedad['tamano']); ?> m²</td>
                    <td>
                        <?php 
                        $imagenes = explode(',', $propiedad['imagenes']);
                        foreach ($imagenes as $imagen) {
                            echo "<a href='../src/uploads/$imagen' data-fancybox='gallery'>
                            <img src='../src/uploads/$imagen' width='50' alt='Imagen de la propiedad'>
                          </a>";
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
