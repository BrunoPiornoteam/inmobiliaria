<?php
include('../includes/db.php');
include('../header.php');

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

$where = [];
$params = [];
$orden = "ORDER BY id DESC"; 

// Filtrar por tipo de propiedad
if (!empty($_GET['tipo']) && array_key_exists($_GET['tipo'], $tipos_propiedad)) {
    $where[] = "tipo = ?";  // Agregar condición al array
    $params[] = $_GET['tipo'];
}

// Filtrar por estado (Venta o Alquiler)
if (!empty($_GET['tipo_operacion']) && in_array($_GET['tipo_operacion'], ['Venta', 'Alquiler'])) {
    $where[] = "tipo_operacion = ?";
    $params[] = $_GET['tipo_operacion'];
}

// Concatenar la cláusula WHERE
$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

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
$stmt = $pdo->prepare("SELECT * FROM propiedades $where_sql $orden");
$stmt->execute($params);
$propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-container">
   <div class="propiedades-content">
        <h1 class="welcome-title">Listado de Propiedades</h1>
        <a href="agregar.php" class="button--blue">Agregar Nueva Propiedad</a>
        
        <!-- Formulario de filtros -->
        <form method="GET" class="listado">
            <fieldset>
                <label for="tipo">Filtrar por Tipo:</label>
                <select name="tipo">
                    <option value="" <?= empty($_GET['tipo']) ? 'selected' : '' ?>>Todos</option>
                    <?php foreach ($tipos_propiedad as $key => $label): ?>
                        <option value="<?= $key ?>" <?= ($_GET['tipo'] ?? '') == $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </fieldset>

            <fieldset>
                <label for="tipo_operacion">Filtrar por Estado:</label>
                <select name="tipo_operacion">
                    <option value="" <?= empty($_GET['tipo_operacion']) ? 'selected' : '' ?>>Todos</option>
                    <option value="Venta" <?= ($_GET['tipo_operacion'] ?? '') == 'Venta' ? 'selected' : '' ?>>Venta</option>
                    <option value="Alquiler" <?= ($_GET['tipo_operacion'] ?? '') == 'Alquiler' ? 'selected' : '' ?>>Alquiler</option>
                </select>
            </fieldset>

            <fieldset>
                <label for="orden">Ordenar:</label>
                <select name="orden">
                    <option value="id_desc" <?= ($_GET['orden'] ?? '') == 'id_desc' ? 'selected' : '' ?>>Más recientes</option>
                    <option value="id_asc" <?= ($_GET['orden'] ?? '') == 'id_asc' ? 'selected' : '' ?>>Más antiguas</option>
                    <option value="precio_asc" <?= ($_GET['orden'] ?? '') == 'precio_asc' ? 'selected' : '' ?>>Menor precio</option>
                    <option value="precio_desc" <?= ($_GET['orden'] ?? '') == 'precio_desc' ? 'selected' : '' ?>>Mayor precio</option>
                </select>
            </fieldset>

            <?php if (!empty($_GET['tipo']) || !empty($_GET['tipo_operacion']) || !empty($_GET['orden'])): ?>
                <button type="button" onclick="window.location.href='index.php'" class="button--blue">Limpiar filtros</button>
            <?php endif; ?>

            <button type="submit" class="button--yellow">Aplicar</button>
        </form>

        <?php if (empty($propiedades)): ?>
            <script>
                Swal.fire({
                    title: 'Sin resultados',
                    text: 'No hay propiedades disponibles con los filtros seleccionados.',
                    icon: 'warning',
                    confirmButtonText: 'Aceptar'
                });
            </script>
        <?php else: ?>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Precio</th>
                    <th>Tipo</th>
                    <th>Ubicación</th>
                    <th>Tipo de operación</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($propiedades as $propiedad): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($propiedad['titulo']); ?></td>
                        <td><?php echo number_format($propiedad['precio'], 2, ',', '.'); ?> USD</td>
                        <td><?php echo htmlspecialchars($propiedad['tipo']); ?></td>
                        <td><?php echo htmlspecialchars($propiedad['ubicacion']); ?></td>
                        <td><?php echo htmlspecialchars($propiedad['tipo_operacion']); ?></td>
                        <td><?php echo htmlspecialchars($propiedad['estado']); ?></td>
                        <td>
                            <a href="single_propiedad.php?id=<?= $propiedad['id'] ?>" class="link">Ver más</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
   </div>
</div>

<?php include('../footer.php'); ?>
