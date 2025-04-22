<?php session_start(); 
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = null;
} ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="/inmobiliaria/dist/css/app.css">
    <link rel="stylesheet" href="/inmobiliaria/dist/css/search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if (localStorage.getItem("menuOpen") === "true") {
            document.documentElement.classList.add("menu-open");
        }
    </script>
</head>
<body>
<?php 
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="header <?php echo (isset($_COOKIE['menuOpen']) && $_COOKIE['menuOpen'] === 'true') ? 'open' : ''; ?>">
    <div class="navigation-buttons sidebar">
        <div class="admin-profile">
            <img src="/inmobiliaria/src/uploads/default-profile.jpeg" alt="Perfil" class="profile-image">
            <p><?php echo isset($user) ? htmlspecialchars($user['nombre']) : 'Invitado'; ?></p>
        </div>

        <div class="nav-container">
            <a class="nav-button" href="/inmobiliaria">
                <i class="fas fa-home"></i> Inicio
            </a>

            <div class="nav-item" data-id="menu1">
                <div class="nav-button">
                    <i class="fas fa-building"></i> Propiedades
                </div>
                <div class="submenu">
                    <a href="/inmobiliaria/propiedades/agregar.php" class="<?= $currentPage === 'agregar.php' ? 'active' : '' ?>">Agregar propiedad</a> 
                    <a href="/inmobiliaria/propiedades/index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Ver propiedades</a> 
                    <a href="/inmobiliaria/propiedades/tipos.php" class="<?= $currentPage === 'tipos.php' ? 'active' : '' ?>">Tipos de propiedad</a> 
                    <a href="/inmobiliaria/propiedades/zonas.php" class="<?= $currentPage === 'zonas.php' ? 'active' : '' ?>">Zonas</a>
                </div>
            </div>

            <div class="nav-item" data-id="menu2">
                <div class="nav-button">
                    <i class="fas fa-users"></i> Clientes
                </div>
                <div class="submenu">
                    <a href="/inmobiliaria/clientes/clientes.php" class="<?= $currentPage === 'clientes.php' ? 'active' : '' ?>">Agregar cliente</a>
                    <a href="/inmobiliaria/clientes/lista_cliente.php" class="<?= $currentPage === 'lista_cliente.php' ? 'active' : '' ?>">Lista de clientes</a>
                </div>
            </div>

            <div class="nav-item" data-id="menu3">
                <div class="nav-button">
                    <i class="fas fa-file-contract"></i> Contratos
                </div>
                <div class="submenu">
                    <a href="/inmobiliaria/contratos/agregar_contrato.php" class="<?= $currentPage === 'agregar_contrato.php' ? 'active' : '' ?>">Agregar contrato</a>
                    <a href="/inmobiliaria/contratos/ver_contratos.php" class="<?= $currentPage === 'ver_contratos.php' ? 'active' : '' ?>">Ver contratos</a>
                    <a href="/inmobiliaria/contratos/documentos.php" class="<?= $currentPage === 'documentos.php' ? 'active' : '' ?>">Documentos adjuntos</a>
                </div>
            </div>

            <div class="nav-item" data-id="menu4">
                <div class="nav-button">
                    <i class="fas fa-money-bill-wave"></i> Pagos
                </div>
                <div class="submenu">
                    <a href="/inmobiliaria/pagos/historial.php" class="<?= $currentPage === 'historial.php' ? 'active' : '' ?>">Historial de pagos</a>
                    <a href="/inmobiliaria/pagos/metodos.php" class="<?= $currentPage === 'metodos.php' ? 'active' : '' ?>">Métodos de pago</a>
                    <a href="/inmobiliaria/pagos/facturas.php" class="<?= $currentPage === 'facturas.php' ? 'active' : '' ?>">Facturación</a>
                </div>
            </div>

            <div class="nav-item" data-id="menu5">
                <div class="nav-button">
                    <i class="fas fa-user"></i> Usuarios
                </div>
                <div class="submenu">
                    <a href="/inmobiliaria/usuarios.php" class="<?= $currentPage === 'usuarios.php' ? 'active' : '' ?>">Lista de usuarios</a>
                    <a href="/inmobiliaria/agregar_usuario.php" class="<?= $currentPage === 'agregar_usuario.php' ? 'active' : '' ?>">Agregar usuario</a>
                </div>
            </div>

            <div class="nav-item" data-id="menu6">
                <div class="nav-button">
                    <i class="fas fa-calendar-alt"></i> Calendario
                </div>
                <div class="submenu">
                    <a href="/inmobiliaria/calendario.php" class="<?= $currentPage === 'calendario.php' ? 'active' : '' ?>">Ver calendario</a>
                    <a href="/inmobiliaria/agregar_evento.php" class="<?= $currentPage === 'agregar_evento.php' ? 'active' : '' ?>">Agregar evento</a>
                </div>
            </div>

            <div class="nav-item" data-id="menu7">
                <div class="nav-button">
                    <i class="fas fa-balance-scale"></i> Tasaciones
                </div>
                <div class="submenu">
                    <a href="tasaciones.php" class="<?= $currentPage === 'tasaciones.php' ? 'active' : '' ?>">Ver tasaciones</a>
                    <a href="nueva_tasacion.php" class="<?= $currentPage === 'nueva_tasacion.php' ? 'active' : '' ?>">Nueva tasación</a>
                </div>
            </div>

            <div class="nav-item" data-id="menu8">
                <div class="nav-button">
                    <i class="fas fa-cog"></i> Configuración
                </div>
                <div class="submenu">
                    <a href="configuracion.php" class="<?= $currentPage === 'configuracion.php' ? 'active' : '' ?>">Ajustes generales</a>
                    <a href="/inmobiliaria/soporte.php" class="<?= $currentPage === 'soporte.php' ? 'active' : '' ?>">Soporte</a>
                </div>
            </div>
        </div>
    
    </div>

    <div class="second-header">
        <button id="toggle-menu" class="menu-button">
            <i class="fas fa-bars"></i>
        </button>

        <div class="search-container">
            <input type="text" id="search" placeholder="Buscar...">
            <div id="search-results" class="search-results-dropdown"></div>
        </div>

        <a href="logout.php" class="logout-button">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </a>
    </div>
</div>

<script src="/inmobiliaria/dist/js/search.js"></script>
</body>
</html>
