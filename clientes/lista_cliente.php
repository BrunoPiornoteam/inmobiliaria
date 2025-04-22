<?php
include('../includes/db.php');
include('../header.php');

// Inicializar variables de filtro
$where = [];
$params = [];

// Procesar filtros
if (!empty($_GET['nombre'])) {
    $where[] = "nombre LIKE ?";
    $params[] = "%" . $_GET['nombre'] . "%";
}

if (!empty($_GET['email'])) {
    $where[] = "email LIKE ?";
    $params[] = "%" . $_GET['email'] . "%";
}

if (!empty($_GET['telefono'])) {
    $where[] = "telefono LIKE ?";
    $params[] = "%" . $_GET['telefono'] . "%";
}

// Construir la consulta
$sql = "SELECT * FROM clientes";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$clientes = $stmt->fetchAll();
?>

<div class="dashboard-container">
    <h2 class="welcome-title">Lista de Clientes</h2>
    
    <!-- Filtros -->
    <div class="filters-container">
        
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <input type="text" name="nombre" placeholder="Filtrar por nombre" 
                       value="<?php echo htmlspecialchars($_GET['nombre'] ?? ''); ?>" class="filter-input">                       
                <button type="submit" class="filter-button">Filtrar</button>
                <a href="lista_cliente.php" class="clear-filter-button">Limpiar Filtros</a>
            </div>
        </form>
    </div>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Notas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clientes)): ?>
                <tr><td colspan="7">No hay clientes registrados.</td></tr>
            <?php else: ?>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cliente['id']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['direccion']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['notas'] ?? ''); ?></td>
                        <td class="action-buttons">
                            <a class="edit-button" href="editar_cliente.php?id=<?php echo $cliente['id']; ?>">Editar</a>
                            <a class="delete-button" href="#" onclick="confirmDelete(<?php echo $cliente['id']; ?>); return false;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function confirmDelete(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este cliente?')) {
        window.location.href = 'eliminar_cliente.php?id=' + id;
    }
}
</script>

<?php include('../footer.php'); ?>
