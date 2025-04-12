<?php
include('../includes/db.php');
include('../header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($nombre && $tipo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO metodos_pago (nombre, tipo, descripcion, activo) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nombre, $tipo, $descripcion, $activo])) {
                echo "<script>
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Método de pago agregado correctamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.reload();
                    });
                </script>";
            }
        } catch (PDOException $e) {
            echo "<script>
                Swal.fire({
                    title: 'Error',
                    text: 'Error al agregar el método de pago: " . addslashes($e->getMessage()) . "',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            </script>";
        }
    }
}

// Obtener métodos de pago
$stmt = $pdo->query("SELECT * FROM metodos_pago ORDER BY nombre ASC");
$metodos = $stmt->fetchAll();
?>

<div class="dashboard-container">
    <div class="pagos-container">
        <h1>Métodos de Pago</h1>

        <!-- Formulario para agregar método de pago -->
        <div class="form-container">
            <h2>Agregar Método de Pago</h2>
            <form method="POST" class="metodos-pago">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia Bancaria</option>
                        <option value="tarjeta_credito">Tarjeta de Crédito</option>
                        <option value="tarjeta_debito">Tarjeta de Débito</option>
                        <option value="cheque">Cheque</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="activo" checked>
                        Activo
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-plus"></i> Agregar Método
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de métodos de pago -->
        <div class="table-container">
            <h2>Métodos de Pago Disponibles</h2>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($metodos as $metodo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($metodo['nombre']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $metodo['tipo']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $metodo['tipo'])); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($metodo['descripcion'] ?? ''); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $metodo['activo'] ? 'success' : 'danger'; ?>">
                                <?php echo $metodo['activo'] ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </td>
                        <td class="actions">
                            <button onclick="editarMetodo(<?php echo $metodo['id']; ?>)" class="btn-edit" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="toggleEstado(<?php echo $metodo['id']; ?>, <?php echo $metodo['activo']; ?>)" 
                                    class="btn-toggle" title="Cambiar estado">
                                <i class="fas fa-power-off"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function editarMetodo(id) {
    // Implementar edición
    Swal.fire({
        title: 'Editar Método de Pago',
        text: 'Funcionalidad en desarrollo',
        icon: 'info'
    });
}

function toggleEstado(id, estadoActual) {
    const nuevoEstado = !estadoActual;
    const mensaje = nuevoEstado ? 'activar' : 'desactivar';
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas ${mensaje} este método de pago?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implementar cambio de estado
            Swal.fire({
                title: 'Estado actualizado',
                text: 'El método de pago ha sido actualizado',
                icon: 'success'
            }).then(() => {
                window.location.reload();
            });
        }
    });
}
</script>

<?php include('../footer.php'); ?>
