<?php
include('../includes/db.php');
include('../header.php');

if (!isset($_GET['id'])) {
    die("ID de cliente no proporcionado.");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID inválido.");
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required_fields = ['nombre', 'email', 'telefono', 'direccion'];
        $errors = [];

        // Validar campos requeridos
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = "El campo " . ucfirst($field) . " es requerido.";
            }
        }

        // Validar formato de email
        if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El formato del email no es válido.";
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("UPDATE clientes SET nombre = ?, email = ?, telefono = ?, direccion = ?, notas = ? WHERE id = ?");
            
            if ($stmt->execute([
                $_POST['nombre'],
                $_POST['email'],
                $_POST['telefono'],
                $_POST['direccion'],
                $_POST['notas'] ?? null,
                $id
            ])) {
                echo "<script>
                    Swal.fire({
                        title: 'Éxito',
                        text: 'Cliente actualizado correctamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'lista_cliente.php';
                        }
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al actualizar el cliente.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                </script>";
            }
        } else {
            echo "<script>
                Swal.fire({
                    title: 'Error de validación',
                    html: '" . implode("<br>", array_map('htmlspecialchars', $errors)) . "',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            </script>";
        }
    }

    // Obtener datos del cliente
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch();

    if (!$cliente) {
        die("Cliente no encontrado.");
    }

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
        <h1 class="welcome-title">Editar Cliente</h1>
        <form method="POST" class="clientes">
            <input type="text" name="nombre" placeholder="Nombre" required 
                   value="<?php echo htmlspecialchars($cliente['nombre']); ?>">
            <input type="email" name="email" placeholder="Email" required 
                   value="<?php echo htmlspecialchars($cliente['email']); ?>">
            <input type="tel" name="telefono" placeholder="Teléfono" required 
                   value="<?php echo htmlspecialchars($cliente['telefono']); ?>">
            <textarea name="direccion" placeholder="Dirección" required><?php echo htmlspecialchars($cliente['direccion']); ?></textarea>
            <textarea name="notas" placeholder="Notas adicionales"><?php echo htmlspecialchars($cliente['notas'] ?? ''); ?></textarea>
            <div class="button-group">
                <button type="submit" class="submit-button">Actualizar Cliente</button>
                <a href="lista_cliente.php" class="cancel-button">Cancelar</a>
            </div>
        </form>
    </div>
</section>

<?php include('../footer.php'); ?>
