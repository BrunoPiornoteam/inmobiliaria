<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <!-- Enlaza el archivo CSS -->
    <link rel="stylesheet" href="dist/css/app.css">
</head>
<body>
<?php 
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<div class="navigation-buttons">
    <div class="admin-profile">
        <img src="src/uploads/default-profile.jpeg" alt="Perfil" class="profile-image">
        <p>Bienvenido, <?php echo isset($user) ? htmlspecialchars($user['nombre']) : 'Invitado'; ?>.</p>
        <p>Rol: <?php echo isset($user) ? htmlspecialchars($user['rol']) : 'No definido'; ?></p>
        <a href="logout.php">Cerrar sesión</a>
    </div>

    <div class="nav-container">
        <div class="nav-item">
            <button class="nav-button">Propiedades</button>
            <div class="submenu">
                <a href="propiedades.php">Ver propiedades</a>
                <a href="agregar_propiedad.php">Agregar propiedad</a>
                <a href="tipos_propiedades.php">Tipos de propiedad</a>
                <a href="zonas.php">Zonas</a>
            </div>
        </div>

        <div class="nav-item">
            <button class="nav-button">Clientes</button>
            <div class="submenu">
                <a href="clientes.php">Lista de clientes</a>
                <a href="agregar_cliente.php">Agregar cliente</a>
            </div>
        </div>

        <div class="nav-item">
            <button class="nav-button">Contratos</button>
            <div class="submenu">
                <a href="contratos.php">Ver contratos</a>
                <a href="crear_contrato.php">Adjuntar contrato</a>
                <a href="documentos.php">Documentos adjuntos</a>
            </div>
        </div>

        <div class="nav-item">
            <button class="nav-button">Pagos</button>
            <div class="submenu">
                <a href="pagos.php">Historial de pagos</a>
                <a href="metodos_pago.php">Métodos de pago</a>
                <a href="facturacion.php">Facturación</a>
            </div>
        </div>

        <div class="nav-item">
            <button class="nav-button">Configuración</button>
            <div class="submenu">
                <a href="configuracion.php">Ajustes generales</a>
                <a href="soporte.php">Soporte</a>
            </div>
        </div>
    </div>
</div>