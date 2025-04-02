<?php
include('../includes/db.php');
include('../header.php');

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$id) {
    header('Location: pagos.php');
    exit;
}

// Obtener información del pago
$query = "
    SELECT 
        p.*,
        c.tipo_contrato,
        c.monto_total as monto_contrato,
        cl.nombre as cliente_nombre,
        cl.email as cliente_email,
        pr.titulo as propiedad_titulo
    FROM pagos p
    JOIN contratos c ON p.contrato_id = c.id
    JOIN clientes cl ON c.cliente_id = cl.id
    JOIN propiedades pr ON c.propiedad_id = pr.id
    WHERE p.id = ?";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $pago = $stmt->fetch();

    if (!$pago) {
        echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'Pago no encontrado',
                icon: 'error',
                confirmButtonText: 'Volver'
            }).then(() => {
                window.location.href = 'pagos.php';
            });
        </script>";
        exit;
    }
} catch (PDOException $e) {
    echo "<script>
        Swal.fire({
            title: 'Error',
            text: 'Error al obtener el pago: " . addslashes($e->getMessage()) . "',
            icon: 'error',
            confirmButtonText: 'Volver'
        }).then(() => {
            window.location.href = 'pagos.php';
        });
    </script>";
    exit;
}
?>

<div class="dashboard-container">
    <div class="content-wrapper">
        <div class="comprobante-container">
            <div class="comprobante-header">
                <h1>Comprobante de Pago</h1>
                <div class="comprobante-actions">
                    <button onclick="window.print()" class="btn-print">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                    <a href="pagos.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <div class="comprobante-body">
                <div class="comprobante-section">
                    <h2>Información del Pago</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Número de Pago:</label>
                            <span>#<?php echo str_pad($pago['id'], 6, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Fecha:</label>
                            <span><?php echo date('d/m/Y', strtotime($pago['fecha_pago'])); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Monto:</label>
                            <span>$<?php echo number_format($pago['cantidad'], 2); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Método de Pago:</label>
                            <span class="badge badge-<?php echo $pago['metodo_pago']; ?>">
                                <?php echo ucfirst($pago['metodo_pago']); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="comprobante-section">
                    <h2>Información del Contrato</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Cliente:</label>
                            <span><?php echo htmlspecialchars($pago['cliente_nombre']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Email:</label>
                            <span><?php echo htmlspecialchars($pago['cliente_email']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Propiedad:</label>
                            <span><?php echo htmlspecialchars($pago['propiedad_titulo']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Tipo de Contrato:</label>
                            <span><?php echo ucfirst($pago['tipo_contrato']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Monto Total del Contrato:</label>
                            <span>$<?php echo number_format($pago['monto_contrato'], 2); ?></span>
                        </div>
                    </div>
                </div>

                <?php if ($pago['descripcion']): ?>
                <div class="comprobante-section">
                    <h2>Descripción</h2>
                    <p><?php echo nl2br(htmlspecialchars($pago['descripcion'])); ?></p>
                </div>
                <?php endif; ?>

                <div class="comprobante-footer">
                    <p>Este comprobante fue generado el <?php echo date('d/m/Y H:i:s'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.comprobante-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin: 2rem auto;
    max-width: 800px;
}

.comprobante-header {
    padding: 1.5rem;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.comprobante-actions {
    display: flex;
    gap: 1rem;
}

.comprobante-body {
    padding: 2rem;
}

.comprobante-section {
    margin-bottom: 2rem;
}

.comprobante-section h2 {
    color: #333;
    margin-bottom: 1rem;
    font-size: 1.25rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    margin-bottom: 1rem;
}

.info-item label {
    display: block;
    color: #666;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.info-item span {
    font-weight: 500;
}

.comprobante-footer {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #ddd;
    color: #666;
    font-size: 0.875rem;
}

.btn-print,
.btn-back {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-print {
    background: #28a745;
    color: white;
}

.btn-back {
    background: #6c757d;
    color: white;
}

@media print {
    .comprobante-actions,
    header,
    footer {
        display: none !important;
    }

    .comprobante-container {
        box-shadow: none;
        margin: 0;
        padding: 0;
    }

    .comprobante-body {
        padding: 1rem;
    }
}
</style>

<?php include('../footer.php'); ?>
