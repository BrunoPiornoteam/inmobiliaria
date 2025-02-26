<?php
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit;
}

// Lista de tipos de propiedades
$tipos_propiedad = [
    "casa" => "Casa",
    "departamento" => "Departamento",
    "duplex" => "Dúplex",
    "ph" => "PH",
    "local_comercial" => "Local Comercial",
    "oficina" => "Oficina",
    "galpon" => "Galpón",
    "terreno" => "Terreno",
    "hotel" => "Hotel",
    "quinta" => "Quinta",
    "edificio" => "Edificio en Block",
    "cochera" => "Cochera",
    "finca" => "Finca",
    "campo" => "Campo"
];

$where = "";
$params = [];
$orden = "ORDER BY id DESC"; 

// Filtrar por tipo de propiedad
if (!empty($_GET['tipo']) && array_key_exists($_GET['tipo'], $tipos_propiedad)) {
    $where .= " WHERE tipo = ?";
    $params[] = $_GET['tipo'];
}

// Ordenar según selección del usuario
if (!empty($_GET['orden'])) {
    switch ($_GET['orden']) {
        case 'id_asc':
            $orden = "ORDER BY id ASC";
            break;
        case 'precio_asc':
            $orden = "ORDER BY precio ASC";
            break;
        case 'precio_desc':
            $orden = "ORDER BY precio DESC";
            break;
    }
}

// Construcción final de la consulta
$stmt = $pdo->prepare("SELECT * FROM propiedades $where $orden");
$stmt->execute($params);
$propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-container">
    <h1 class="welcome-title">Listado de Propiedades</h1>
    <a href="agregar.php" class="button--blue">Agregar Nueva Propiedad</a>
    
    <!-- Formulario de filtros -->
    <form method="GET">
        <label for="tipo">Filtrar por tipo:</label>
        <select name="tipo">
            <option value="" <?= empty($_GET['tipo']) ? 'selected' : '' ?>>Todos</option>
            <?php foreach ($tipos_propiedad as $key => $label): ?>
                <option value="<?= $key ?>" <?= ($_GET['tipo'] ?? '') == $key ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>

        <label for="orden">Ordenar por:</label>
        <select name="orden">
            <option value="id_desc" <?= ($_GET['orden'] ?? '') == 'id_desc' ? 'selected' : '' ?>>Más recientes</option>
            <option value="id_asc" <?= ($_GET['orden'] ?? '') == 'id_asc' ? 'selected' : '' ?>>Más antiguas</option>
            <option value="precio_asc" <?= ($_GET['orden'] ?? '') == 'precio_asc' ? 'selected' : '' ?>>Menor precio</option>
            <option value="precio_desc" <?= ($_GET['orden'] ?? '') == 'precio_desc' ? 'selected' : '' ?>>Mayor precio</option>
        </select>

        <button type="submit">Aplicar</button>
    </form>

    <!-- Tabla de propiedades -->
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
