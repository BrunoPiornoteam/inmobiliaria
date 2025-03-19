<?php
include('../includes/db.php');
include('../header.php');
include('../src/functions/functions.php');

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_NUMBER_INT);
        $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $notas = filter_input(INPUT_POST, 'notas', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';

        if (!$nombre || !$email || !$telefono || !$direccion) {
            echo "<script>
                Swal.fire({
                    title: 'Error',
                    text: 'Datos inválidos. Verifica la información ingresada.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            </script>";
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM clientes WHERE email = ?");
            $stmt->execute([$email]);
            $existe = $stmt->fetchColumn();

            if ($existe > 0) {
                echo "<script>
                    Swal.fire({
                        title: 'Error',
                        text: 'El email ya está registrado. Intenta con otro.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                </script>";
            } else {
                $stmt = $pdo->prepare("INSERT INTO clientes (nombre, email, telefono, direccion, notas) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$nombre, $email, $telefono, $direccion, $notas])) {
                    echo "<script>
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Cliente agregado correctamente.',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        });
                    </script>";
                } else {
                    echo "<script>
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al agregar el cliente.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    </script>";
                }
            }
        }
    }

    $stmt = $pdo->query("SELECT * FROM clientes ORDER BY id DESC");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []; 

} catch (PDOException $e) {
    echo "<script>
        Swal.fire({
            title: 'Error en la base de datos',
            text: '" . addslashes($e->getMessage()) . "',
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    </script>";
}
?>

<section class="dashboard-container">
    <div class="clientes-content">
        <h1>Gestión de Clientes</h1>
        <form method="POST" class="clientes">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="tel" name="telefono" placeholder="Teléfono" required>
            <textarea name="direccion" placeholder="Dirección" required></textarea>
            <textarea name="notas" placeholder="Notas adicionales"></textarea> 
            <button type="submit" name="action" value="add">Agregar Cliente</button>
        </form>

        <h2 class="clientes-title">Lista de Clientes</h2>
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
                    <tr><td colspan="6">No hay clientes registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cliente['id']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['direccion']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['notas'] ?? ''); ?></td>
                            <td>
                                <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="link">Editar</a>
                                <a href="#" onclick="confirmDelete(<?php echo $cliente['id']; ?>); return false;" class="link">Eliminar</a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include('../footer.php'); ?>
