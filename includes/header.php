<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../dist/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
   
</head>
<body>
<?php 
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<div class="navigation-buttons">
    <div class="admin-profile">
        <img src="/inmobiliaria/src/uploads/default-profile.jpeg" alt="Perfil" class="profile-image">
        <p><?php echo isset($user) ? htmlspecialchars($user['nombre']) : 'Invitado'; ?></p>
    </div>

    <div class="nav-container">
        <a class="nav-button" href="/inmobiliaria/dashboard.php">
            <i class="fas fa-home"></i> Inicio
        </a>

        <div class="nav-item active">
            <div class="nav-button">
                <i class="fas fa-building"></i> Propiedades
            </div>
            <div class="submenu">
                <a href="/inmobiliaria/propiedades/index.php">Ver propiedades</a> 
                <a href="/inmobiliaria/propiedades/agregar.php">Agregar propiedad</a> 
                <a href="/inmobiliaria/propiedades/tipos.php">Tipos de propiedad</a> 
                <a href="/inmobiliaria/propiedades/zonas.php">Zonas</a>
            </div>
        </div>

        <div class="nav-item">
            <div class="nav-button">
                <i class="fas fa-users"></i> Clientes
            </div>
            <div class="submenu">
                <a href="clientes.php">Lista de clientes</a>
                <a href="agregar_cliente.php">Agregar cliente</a>
            </div>
        </div>

        <div class="nav-item">
            <div class="nav-button">
                <i class="fas fa-file-contract"></i> Contratos
            </div>
            <div class="submenu">
                <a href="contratos.php">Ver contratos</a>
                <a href="crear_contrato.php">Adjuntar contrato</a>
                <a href="documentos.php">Documentos adjuntos</a>
            </div>
        </div>

        <div class="nav-item">
            <div class="nav-button">
                <i class="fas fa-money-bill-wave"></i> Pagos
            </div>
            <div class="submenu">
                <a href="pagos.php">Historial de pagos</a>
                <a href="metodos_pago.php">Métodos de pago</a>
                <a href="facturacion.php">Facturación</a>
            </div>
        </div>

        <div class="nav-item">
            <div class="nav-button">
                <i class="fas fa-user"></i> Usuarios
            </div>
            <div class="submenu">
                <a href="usuarios.php">Lista de usuarios</a>
                <a href="agregar_usuario.php">Agregar usuario</a>
            </div>
        </div>

        <div class="nav-item">
            <div class="nav-button">
                <i class="fas fa-calendar-alt"></i> Calendario
            </div>
            <div class="submenu">
                <a href="calendario.php">Ver calendario</a>
                <a href="agregar_evento.php">Agregar evento</a>
            </div>
        </div>

        <div class="nav-item">
            <div class="nav-button">
                <i class="fas fa-balance-scale"></i> Tasaciones
            </div>
            <div class="submenu">
                <a href="tasaciones.php">Ver tasaciones</a>
                <a href="nueva_tasacion.php">Nueva tasación</a>
            </div>
        </div>

        <div class="nav-item">
            <div class="nav-button">
                <i class="fas fa-cog"></i> Configuración
            </div>
            <div class="submenu">
                <a href="configuracion.php">Ajustes generales</a>
                <a href="soporte.php">Soporte</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
