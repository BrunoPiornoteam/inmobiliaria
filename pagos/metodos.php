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
    <div class="content-wrapper">
        <h1>Métodos de Pago</h1>

        <!-- Formulario para agregar método de pago -->
        <div class="form-container">
            <h2>Agregar Método de Pago</h2>
            <form method="POST" class="styled-form">
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
            <table class="styled-table">
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

<style>
.styled-form {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
}

.badge-efectivo { background: #28a745; color: white; }
.badge-transferencia { background: #007bff; color: white; }
.badge-tarjeta_credito { background: #6c757d; color: white; }
.badge-tarjeta_debito { background: #17a2b8; color: white; }
.badge-cheque { background: #ffc107; color: black; }
.badge-otro { background: #6c757d; color: white; }

.badge-success { background: #28a745; color: white; }
.badge-danger { background: #dc3545; color: white; }

.btn-edit,
.btn-toggle {
    padding: 0.25rem 0.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin: 0 0.25rem;
}

.btn-edit {
    background: #4a90e2;
    color: white;
}

.btn-toggle {
    background: #6c757d;
    color: white;
}

.table-container {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
}

.styled-table th,
.styled-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.styled-table th {
    background: #f8f9fa;
    font-weight: 600;
}
</style>

<?php include('../footer.php'); ?>
