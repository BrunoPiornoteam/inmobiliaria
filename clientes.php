<?php
include('includes/db.php');
include('includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $stmt = $pdo->prepare("INSERT INTO clientes (nombre, email, telefono, direccion) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $email, $telefono, $direccion]);
    echo "Cliente agregado con éxito.";
}

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $stmt = $pdo->prepare("UPDATE clientes SET nombre = ?, email = ?, telefono = ?, direccion = ? WHERE id = ?");
    $stmt->execute([$nombre, $email, $telefono, $direccion, $id]);
    echo "Cliente actualizado con éxito.";
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    echo "Cliente eliminado con éxito.";
}

$stmt = $pdo->query("SELECT * FROM clientes");
$clientes = $stmt->fetchAll();
?>

<h1>Gestión de Clientes</h1>
<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="tel" name="telefono" placeholder="Teléfono" required>
    <textarea name="direccion" placeholder="Dirección" required></textarea>
    <button type="submit" name="action" value="add">Agregar Cliente</button>
</form>

<h2>Lista de Clientes</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?php echo $cliente['id']; ?></td>
                <td><?php echo $cliente['nombre']; ?></td>
                <td><?php echo $cliente['email']; ?></td>
                <td><?php echo $cliente['telefono']; ?></td>
                <td>
                    <a href="?edit=<?php echo $cliente['id']; ?>">Editar</a>
                    <a href="?delete=<?php echo $cliente['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este cliente?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include('includes/footer.php'); ?>
