<?php
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_GET['id'])) {
    die("ID de cliente no proporcionado.");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID inválido.");
}

$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    die("Cliente no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_NUMBER_INT);
    $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $notas = filter_input(INPUT_POST, 'notas', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($nombre && $email && $telefono && $direccion) {
        $stmt = $pdo->prepare("UPDATE clientes SET nombre = ?, email = ?, telefono = ?, direccion = ?, notas = ? WHERE id = ?");
        $stmt->execute([$nombre, $email, $telefono, $direccion, $notas, $id]);
        echo "<script>
            Swal.fire({
                title: '¡Éxito!',
                text: 'Cliente actualizado correctamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'Datos inválidos. Verifica la información ingresada.',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        </script>";
    }
} else {
    echo "<script>
        Swal.fire({
            title: 'Error',
            text: 'Error en los datos ingresados. Verifica la información.',
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    </script>";
}
?>

<h1>Editar Cliente</h1>
<form method="POST">
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
    <input type="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
    <input type="tel" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" required>
    <textarea name="direccion" required><?php echo htmlspecialchars($cliente['direccion']); ?></textarea>
    <textarea name="notas" placeholder="Notas adicionales"><?php echo htmlspecialchars($cliente['notas']); ?></textarea>
    <button type="submit">Actualizar Cliente</button>
</form>

<a class="button" href="clientes.php">Volver a la lista</a>

<?php include('../includes/footer.php'); ?>
