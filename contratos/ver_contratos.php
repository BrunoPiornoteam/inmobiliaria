<?php
include('includes/db.php');
include('includes/header.php');

$stmt = $pdo->query("SELECT c.id, p.titulo AS propiedad, cl.nombre AS cliente, c.tipo_contrato, c.precio, c.fecha_inicio, c.fecha_fin, c.estado FROM contratos c JOIN propiedades p ON c.propiedad_id = p.id JOIN clientes cl ON c.cliente_id = cl.id");
$contratos = $stmt->fetchAll();
?>

<h1>Lista de Contratos</h1>
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
            <th>Archivo</th>
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
                <td>
                    <?php if (!empty($contrato['archivo'])): ?>
                        <a href="uploads/contratos/<?php echo $contrato['archivo']; ?>" target="_blank">📄 Ver Archivo</a>
                    <?php else: ?>
                        No adjunto
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="agregar_contrato.php">Agregar Contrato</a>

<?php include('../includes/footer.php'); ?>