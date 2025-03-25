<?php

include('includes/db.php');
include('header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit;
}

$totalPropiedades = $totalClientes = $totalContratos = 0;
$recentProperties = $eventos = $contratos = [];

try {
    $propiedadesStmt = $pdo->query("SELECT COUNT(*) as total_propiedades FROM propiedades");
    $totalPropiedades = $propiedadesStmt->fetch()['total_propiedades'];

    $clientesStmt = $pdo->query("SELECT COUNT(*) as total_clientes FROM clientes");
    $totalClientes = $clientesStmt->fetch()['total_clientes'];

    $contratosStmt = $pdo->query("SELECT COUNT(*) as total_contratos FROM contratos");
    $totalContratos = $contratosStmt->fetch()['total_contratos'];

    $recentProperties = $pdo->query("SELECT * FROM propiedades ORDER BY id DESC LIMIT 5")->fetchAll();

    $eventosQuery = "SELECT * FROM eventos WHERE fecha >= CURRENT_DATE ORDER BY fecha ASC LIMIT 5";
    $eventosStmt = $pdo->prepare($eventosQuery);
    if ($eventosStmt->execute()) {
        $eventos = $eventosStmt->fetchAll();
    }

    $contratos = $pdo->query("SELECT c.*, p.titulo as propiedad, cl.nombre as cliente 
                             FROM contratos c 
                             LEFT JOIN propiedades p ON c.propiedad_id = p.id 
                             LEFT JOIN clientes cl ON c.cliente_id = cl.id 
                             ORDER BY c.id DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
?>

<div class="dashboard-container">
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-building"></i>
            <h3>Propiedades</h3>
            <p class="stat-number"><?php echo $totalPropiedades; ?></p>
        </div>
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <h3>Clientes</h3>
            <p class="stat-number"><?php echo $totalClientes; ?></p>
        </div>
        <div class="stat-card">
            <i class="fas fa-file-contract"></i>
            <h3>Contratos</h3>
            <p class="stat-number"><?php echo $totalContratos; ?></p>
        </div>
        <div class="stat-card">
            <i class="fas fa-calendar-check"></i>
            <h3>Eventos Pendientes</h3>
            <p class="stat-number"><?php echo count($eventos); ?></p>
        </div>
    </div>

    <div class="quick-actions">
        <h2>Acciones Rápidas</h2>
        <div class="action-buttons">
            <a href="/inmobiliaria/propiedades/agregar.php" class="action-btn">
                <i class="fas fa-plus"></i> Nueva Propiedad
            </a>
            <a href="/inmobiliaria/clientes/clientes.php" class="action-btn">
                <i class="fas fa-user-plus"></i> Nuevo Cliente
            </a>
            <a href="/inmobiliaria/contratos/agregar_contrato.php" class="action-btn">
                <i class="fas fa-file-signature"></i> Nuevo Contrato
            </a>
            <a href="/inmobiliaria/calendario.php" class="action-btn">
                <i class="fas fa-calendar-plus"></i> Nuevo Evento
            </a>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h2>Propiedades Recientes</h2>
            <div class="property-list">
                <?php if (!empty($recentProperties)): ?>
                    <?php foreach ($recentProperties as $property): ?>
                    <div class="property-item">
                        <img src="<?php echo htmlspecialchars($property['imagen_principal'] ?? '/inmobiliaria/src/uploads/default-property.jpg'); ?>" alt="Propiedad">
                        <div class="property-info">
                            <h3><?php echo htmlspecialchars($property['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($property['direccion'] ?? 'Sin dirección', ENT_QUOTES, 'UTF-8'); ?></p>
                            <span class="price">$<?php echo number_format($property['precio'], 2); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-data">No hay propiedades registradas</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Contracts -->
        <div class="dashboard-card">
            <h2>Contratos Recientes</h2>
            <div class="contracts-list">
                <?php if (!empty($contratos)): ?>
                    <?php foreach ($contratos as $contrato): ?>
                    <div class="contract-item">
                        <div class="contract-info">
                            <h3><?php echo htmlspecialchars($contrato['propiedad']); ?></h3>
                            <p>Cliente: <?php echo htmlspecialchars($contrato['cliente']); ?></p>
                            <p>Fecha: <?php echo date('d/m/Y', strtotime($contrato['fecha_inicio'])); ?></p>
                        </div>
                        <a href="/inmobiliaria/contratos/ver_contrato.php?id=<?php echo $contrato['id']; ?>" class="btn-view">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-data">No hay contratos registrados</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="dashboard-card">
            <h2>Próximos Eventos</h2>
            <div class="events-list">
                <?php if (!empty($eventos)): ?>
                    <?php foreach ($eventos as $evento): ?>
                    <div class="event-item">
                        <div class="event-date">
                            <span class="day"><?php echo date('d', strtotime($evento['fecha'])); ?></span>
                            <span class="month"><?php echo date('M', strtotime($evento['fecha'])); ?></span>
                        </div>
                        <div class="event-info">
                            <h3><?php echo htmlspecialchars($evento['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($evento['descripcion']); ?></p>
                            <span class="time"><i class="far fa-clock"></i> <?php echo date('H:i', strtotime($evento['hora'])); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-data">No hay eventos próximos</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>
