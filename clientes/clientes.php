<?php
include('../includes/db.php');
include('../header.php');

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
            $stmt = $pdo->prepare("INSERT INTO clientes (nombre, email, telefono, direccion, notas) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt->execute([
                $_POST['nombre'],
                $_POST['email'],
                $_POST['telefono'],
                $_POST['direccion'],
                $_POST['notas'] ?? null
            ])) {
                echo "<script>
                    Swal.fire({
                        title: 'Éxito',
                        text: 'Cliente agregado correctamente',
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
                        text: 'Error al agregar el cliente.',
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
        <h1 class="welcome-title">Gestión de Clientes</h1>
        <form method="POST" class="clientes">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="tel" name="telefono" placeholder="Teléfono" required>
            <textarea name="direccion" placeholder="Dirección" required></textarea>
            <textarea name="notas" placeholder="Notas adicionales"></textarea>
            <button type="submit" class="submit-button">Agregar Cliente</button>
        </form>
    </div>
</section>

<?php include('../footer.php'); ?>
