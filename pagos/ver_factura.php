<?php
include('../includes/db.php');
include('../header.php');

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$id) {
    header('Location: facturas.php');
    exit;
}

// Obtener información de la factura
$query = "
    SELECT 
        f.*,
        c.nombre as cliente_nombre,
        c.email as cliente_email,
        c.direccion as cliente_direccion,
        c.telefono as cliente_telefono
    FROM facturas f
    JOIN clientes c ON f.cliente_id = c.id
    WHERE f.id = ?";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $factura = $stmt->fetch();

    if (!$factura) {
        echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'Factura no encontrada',
                icon: 'error',
                confirmButtonText: 'Volver'
            }).then(() => {
                window.location.href = 'facturas.php';
            });
        </script>";
        exit;
    }
} catch (PDOException $e) {
    echo "<script>
        Swal.fire({
            title: 'Error',
            text: 'Error al obtener la factura: " . addslashes($e->getMessage()) . "',
            icon: 'error',
            confirmButtonText: 'Volver'
        }).then(() => {
            window.location.href = 'facturas.php';
        });
    </script>";
    exit;
}
?>

<div class="dashboard-container">
    <div class="content-wrapper">
        <div class="factura-container">
            <div class="factura-header">
                <h1>Factura <?php echo $factura['tipo']; ?></h1>
                <div class="factura-actions">
                    <button onclick="window.print()" class="btn-print">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                    <a href="descargar_factura.php?id=<?php echo $factura['id']; ?>" class="btn-download">
                        <i class="fas fa-download"></i> Descargar PDF
                    </a>
                    <a href="facturas.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <div class="factura-body">
                <div class="factura-info">
                    <div class="empresa-info">
                        <h2>Inmobiliaria</h2>
                        <p>Dirección de la Inmobiliaria</p>
                        <p>Teléfono: (123) 456-7890</p>
                        <p>Email: info@inmobiliaria.com</p>
                    </div>
                    <div class="factura-detalles">
                        <h3>Factura #<?php echo str_pad($factura['id'], 6, '0', STR_PAD_LEFT); ?></h3>
                        <p>Fecha: <?php echo date('d/m/Y', strtotime($factura['fecha_emision'])); ?></p>
                        <p>Estado: 
                            <span class="badge badge-<?php echo $factura['estado']; ?>">
                                <?php echo ucfirst($factura['estado']); ?>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="cliente-info">
                    <h3>Cliente</h3>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($factura['cliente_nombre']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($factura['cliente_email']); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($factura['cliente_direccion']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($factura['cliente_telefono']); ?></p>
                </div>

                <div class="factura-detalle">
                    <h3>Detalle de la Factura</h3>
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Concepto</th>
                                <th class="text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo htmlspecialchars($factura['concepto']); ?></td>
                                <td class="text-right">$<?php echo number_format($factura['monto'], 2); ?></td>
                            </tr>
                            <tr class="total">
                                <td><strong>Total</strong></td>
                                <td class="text-right"><strong>$<?php echo number_format($factura['monto'], 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="factura-footer">
                    <p>Esta factura fue generada el <?php echo date('d/m/Y H:i:s'); ?></p>
                    <p>Gracias por su confianza</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.factura-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin: 2rem auto;
    max-width: 800px;
}

.factura-header {
    padding: 1.5rem;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.factura-actions {
    display: flex;
    gap: 1rem;
}

.factura-body {
    padding: 2rem;
}

.factura-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.empresa-info h2,
.factura-detalles h3 {
    margin: 0 0 1rem 0;
    color: #333;
}

.cliente-info {
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.cliente-info h3 {
    margin: 0 0 1rem 0;
    color: #333;
}

.factura-detalle {
    margin-bottom: 2rem;
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
}

.styled-table th,
.styled-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.styled-table th {
    background: #f8f9fa;
    font-weight: 600;
}

.text-right {
    text-align: right;
}

.total td {
    border-top: 2px solid #333;
    font-weight: bold;
}

.factura-footer {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #ddd;
    text-align: center;
    color: #666;
}

.btn-print,
.btn-download,
.btn-back {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: white;
}

.btn-print { background: #28a745; }
.btn-download { background: #007bff; }
.btn-back { background: #6c757d; }

.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
}

.badge-pendiente { background: #ffc107; color: black; }
.badge-pagada { background: #28a745; color: white; }
.badge-cancelada { background: #dc3545; color: white; }

@media print {
    .factura-actions,
    header,
    footer {
        display: none !important;
    }

    .factura-container {
        box-shadow: none;
        margin: 0;
        padding: 0;
    }

    .factura-body {
        padding: 1rem;
    }
}
</style>

<?php include('../footer.php'); ?>
