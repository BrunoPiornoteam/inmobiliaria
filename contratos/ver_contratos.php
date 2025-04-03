<?php
include('../includes/db.php');
include('../header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit;
}

if (isset($_GET['eliminado']) && $_GET['eliminado'] == '1') {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire('Eliminado', 'El contrato ha sido eliminado.', 'success');
        });
    </script>";
}

$stmt = $pdo->query("SELECT c.id, p.titulo AS propiedad, cl.nombre AS cliente, 
                            c.tipo_contrato, c.precio, c.fecha_inicio, c.fecha_fin, c.estado, c.archivo 
                     FROM contratos c
                     JOIN propiedades p ON c.propiedad_id = p.id
                     JOIN clientes cl ON c.cliente_id = cl.id
                     ORDER BY c.fecha_creacion DESC");

$contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="dashboard-container">
    <div class="listado-contratos">
        <h1 class="welcome-title">Listado de Contratos</h1>
        <a href="agregar_contrato.php" class="button--blue">Agregar Nuevo Contrato</a>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Propiedad</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Precio</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <th>Documento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contratos as $contrato): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contrato['propiedad']); ?></td>
                        <td><?php echo htmlspecialchars($contrato['cliente']); ?></td>
                        <td><?php echo htmlspecialchars($contrato['tipo_contrato']); ?></td>
                        <td>$<?php echo number_format($contrato['precio'], 2); ?></td>
                        <td><?php echo htmlspecialchars($contrato['fecha_inicio']); ?></td>
                        <td><?php echo htmlspecialchars($contrato['fecha_fin']); ?></td>
                        <td><?php echo htmlspecialchars($contrato['estado']); ?></td>
                        <td>
                            <?php if ($contrato['archivo']): ?>
                                <a href="uploads/contratos/<?php echo htmlspecialchars($contrato['archivo']); ?>" target="_blank">📄 Ver Documento</a>
                            <?php else: ?>
                                No adjunto
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="editar_contrato.php?id=<?php echo $contrato['id']; ?>">Editar</a>
                            <a href="#" onclick="confirmarEliminacion(<?php echo $contrato['id']; ?>)">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <script>
        function confirmarEliminacion(contratoId) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Esta acción no se puede deshacer.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "eliminar_contrato.php?id=" + contratoId;
                }
            });
        }
        </script>

    </div>

</section>

<?php include('../footer.php'); ?>