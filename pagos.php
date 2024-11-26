<?php
include('includes/db.php');
include('includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $contrato_id = $_POST['contrato_id'];
    $monto = $_POST['monto'];
    $fecha_pago = $_POST['fecha_pago'];
    $metodo_pago = $_POST['metodo_pago'];

    $stmt = $pdo->prepare("INSERT INTO pagos (contrato_id, cantidad, fecha_pago, metodo_pago) VALUES (?, ?, ?, ?)");
    $stmt->execute([$contrato_id, $monto, $fecha_pago, $metodo_pago]);
    echo "Pago registrado con éxito.";
}

// Mostrar pagos
$stmt = $pdo->query("SELECT p.id, c.id AS contrato_id, c.tipo_contrato, p.cantidad, p.fecha_pago, p.metodo_pago FROM pagos p JOIN contratos c ON p.contrato_id = c.id");
$pagos = $stmt->fetchAll();
?>

<h1>Gestión de Pagos</h1>
<form method="POST">
    <select name="contrato_id" required>
        <option value="">Selecciona un contrato</option>
        <?php
        $stmt = $pdo->query("SELECT id, tipo_contrato FROM contratos");
        $contratos = $stmt->fetchAll();
        foreach ($contratos as $contrato):
        ?>
            <option value="<?php echo $contrato['id']; ?>"><?php echo ucfirst($contrato['tipo_contrato']); ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" name="monto" placeholder="Monto" required>
    <input type="date" name="fecha_pago" placeholder="Fecha de pago" required>
    <select name="metodo_pago" required>
        <option value="efectivo">Efectivo</option>
        <option value="transferencia">Transferencia</option>
        <option value="tarjeta">Tarjeta</option>
    </select>
    <button type="submit" name="action" value="add">Registrar Pago</button>
</form>

<h2>Lista de Pagos</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Contrato</th>
            <th>Cantidad</th>
            <th>Fecha</th>
            <th>Metodo de Pago</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pagos as $pago): ?>
            <tr>
                <td><?php echo $pago['id']; ?></td>
                <td><?php echo ucfirst($pago['tipo_contrato']); ?></td>
                <td><?php echo $pago['cantidad']; ?></td>
                <td><?php echo $pago['fecha_pago']; ?></td>
                <td><?php echo ucfirst($pago['metodo_pago']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include('includes/footer.php'); ?>