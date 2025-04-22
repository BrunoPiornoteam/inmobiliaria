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
    // Primero verificamos que el cliente exista
    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    
    if (!$stmt->fetch()) {
        echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'Cliente no encontrado.',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.href = 'lista_cliente.php';
            });
        </script>";
        exit;
    }

    // Verificar si el cliente tiene contratos asociados
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contratos WHERE cliente_id = ?");
    $stmt->execute([$id]);
    $tieneContratos = $stmt->fetchColumn() > 0;

    if ($tieneContratos) {
        echo "<script>
            Swal.fire({
                title: 'No se puede eliminar',
                text: 'Este cliente tiene contratos asociados. Debe eliminar primero los contratos antes de poder eliminar el cliente.',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            }).then(() => {
                window.location.href = 'lista_cliente.php';
            });
        </script>";
        exit;
    }

    // Si no tiene contratos, procedemos a eliminar el cliente
    $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo "<script>
            Swal.fire({
                title: '¡Éxito!',
                text: 'Cliente eliminado correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                window.location.href = 'lista_cliente.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'Error al eliminar el cliente.',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.href = 'lista_cliente.php';
            });
        </script>";
    }
} catch (PDOException $e) {
    $mensaje = "Error en la base de datos. ";
    if (strpos($e->getMessage(), "foreign key constraint fails") !== false) {
        $mensaje = "No se puede eliminar el cliente porque tiene contratos asociados. Por favor, elimine primero los contratos.";
    } else {
        $mensaje .= $e->getMessage();
    }
    
    echo "<script>
        Swal.fire({
            title: 'Error',
            text: '" . addslashes($mensaje) . "',
            icon: 'error',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            window.location.href = 'lista_cliente.php';
        });
    </script>";
}
?>

<?php include('../footer.php'); ?>
