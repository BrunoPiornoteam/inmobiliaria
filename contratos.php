<?php
include('includes/db.php');
include('includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $propiedad_id = $_POST['propiedad_id'];
    $cliente_id = $_POST['cliente_id'];
    $tipo_contrato = $_POST['tipo_contrato'];
    $precio = $_POST['precio'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    $stmt = $pdo->prepare("INSERT INTO contratos (propiedad_id, cliente_id, tipo_contrato, precio, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$propiedad_id, $cliente_id, $tipo_contrato, $precio, $fecha_inicio, $fecha_fin]);
    echo "Contrato agregado con éxito.";
}

// Mostrar contratos
$stmt = $pdo->query("SELECT c.id, p.titulo AS propiedad, cl.nombre AS cliente, c.tipo_contrato, c.precio, c.fecha_inicio, c.fecha_fin, c.estado FROM contratos c JOIN propiedades p ON c.propiedad_id = p.id JOIN clientes cl ON c.cliente_id = cl.id");
$contratos = $stmt->fetchAll();
?>

<h1>Gestión de Contratos</h1>
<form method="POST">
    <select name="propiedad_id" required>
        <option value="">Selecciona una propiedad</option>
        <?php
        $stmt = $pdo->query("SELECT id, titulo FROM propiedades");
        $propiedades = $stmt->fetchAll();
        foreach ($propiedades as $propiedad):
        ?>
            <option value="<?php echo $propiedad['id']; ?>"><?php echo $propiedad['titulo']; ?></option>
        <?php endforeach; ?>
    </select>
    <select name="cliente_id" required>
        <option value="">Selecciona un cliente</option>
        <?php
        $stmt = $pdo->query("SELECT id, nombre FROM clientes");
        $clientes = $stmt->fetchAll();
        foreach ($clientes as $cliente):
        ?>
            <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nombre']; ?></option>
        <?php endforeach; ?>
    </select>
    <select name="tipo_contrato" required>
        <option value="venta">Venta</option>
        <option value="alquiler">Alquiler</option>
    </select>
    <input type="number" name="precio" placeholder="Precio" required>
    <input type="date" name="fecha_inicio" placeholder="Fecha de inicio" required>
    <input type="date" name="fecha_fin" placeholder="Fecha de fin" required>
    <button type="submit" name="action" value="add">Agregar Contrato</button>
</form>

<h2>Lista de Contratos</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Propiedad</th>
            <th>Cliente</th>
            <th>Tipo</th>
            <th>Precio</th>
            <th>Fechas</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($contratos as $contrato): ?>
            <tr>
                <td><?php echo $contrato['id']; ?></td>
                <td><?php echo $contrato['propiedad']; ?></td>
                <td><?php echo $contrato['cliente']; ?></td>
                <td><?php echo ucfirst($contrato['tipo_contrato']); ?></td>
                <td><?php echo $contrato['precio']; ?></td>
                <td><?php echo $contrato['fecha_inicio']; ?> - <?php echo $contrato['fecha_fin']; ?></td>
                <td><?php echo ucfirst($contrato['estado']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include('includes/footer.php'); ?>
