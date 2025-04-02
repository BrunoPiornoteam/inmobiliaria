<?php
include('../includes/db.php');

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
        die('Pago no encontrado');
    }
} catch (PDOException $e) {
    die('Error al obtener el pago: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pago #<?php echo str_pad($pago['id'], 6, '0', STR_PAD_LEFT); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        .comprobante {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            margin: 0;
            color: #333;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-section h2 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-item label {
            font-weight: bold;
            color: #666;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }

        .badge-efectivo { background: #28a745; color: white; }
        .badge-transferencia { background: #007bff; color: white; }
        .badge-tarjeta { background: #6c757d; color: white; }

        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        @media print {
            body {
                padding: 0;
            }

            .comprobante {
                max-width: 100%;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="comprobante">
        <div class="header">
            <h1>Comprobante de Pago</h1>
            <p>Número: #<?php echo str_pad($pago['id'], 6, '0', STR_PAD_LEFT); ?></p>
        </div>

        <div class="info-section">
            <h2>Información del Pago</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Fecha:</label>
                    <div><?php echo date('d/m/Y', strtotime($pago['fecha_pago'])); ?></div>
                </div>
                <div class="info-item">
                    <label>Monto:</label>
                    <div>$<?php echo number_format($pago['cantidad'], 2); ?></div>
                </div>
                <div class="info-item">
                    <label>Método de Pago:</label>
                    <div>
                        <span class="badge badge-<?php echo $pago['metodo_pago']; ?>">
                            <?php echo ucfirst($pago['metodo_pago']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h2>Información del Contrato</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Cliente:</label>
                    <div><?php echo htmlspecialchars($pago['cliente_nombre']); ?></div>
                </div>
                <div class="info-item">
                    <label>Email:</label>
                    <div><?php echo htmlspecialchars($pago['cliente_email']); ?></div>
                </div>
                <div class="info-item">
                    <label>Propiedad:</label>
                    <div><?php echo htmlspecialchars($pago['propiedad_titulo']); ?></div>
                </div>
                <div class="info-item">
                    <label>Tipo de Contrato:</label>
                    <div><?php echo ucfirst($pago['tipo_contrato']); ?></div>
                </div>
                <div class="info-item">
                    <label>Monto Total del Contrato:</label>
                    <div>$<?php echo number_format($pago['monto_contrato'], 2); ?></div>
                </div>
            </div>
        </div>

        <?php if ($pago['descripcion']): ?>
        <div class="info-section">
            <h2>Descripción</h2>
            <p><?php echo nl2br(htmlspecialchars($pago['descripcion'])); ?></p>
        </div>
        <?php endif; ?>

        <div class="footer">
            <p>Este comprobante fue generado el <?php echo date('d/m/Y H:i:s'); ?></p>
            <p>Documento válido como comprobante de pago</p>
        </div>
    </div>
</body>
</html>
