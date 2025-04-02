<?php
include('../includes/db.php');
include('../header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $contrato_id = filter_input(INPUT_POST, 'contrato_id', FILTER_SANITIZE_NUMBER_INT);
    $monto = filter_input(INPUT_POST, 'monto', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $fecha_pago = filter_input(INPUT_POST, 'fecha_pago', FILTER_SANITIZE_STRING);
    $metodo_pago = filter_input(INPUT_POST, 'metodo_pago', FILTER_SANITIZE_STRING);
    $concepto = filter_input(INPUT_POST, 'concepto', FILTER_SANITIZE_STRING);

    if ($contrato_id && $monto && $fecha_pago && $metodo_pago && $concepto) {
        try {
            $stmt = $pdo->prepare("INSERT INTO pagos (contrato_id, monto, fecha_pago, metodo_pago, concepto) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$contrato_id, $monto, $fecha_pago, $metodo_pago, $concepto])) {
                $pago_id = $pdo->lastInsertId();
                echo "<script>
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Pago registrado correctamente',
                        icon: 'success',
                        confirmButtonText: 'Ver Comprobante',
                        showCancelButton: true,
                        cancelButtonText: 'Cerrar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'ver_comprobante.php?id=" . $pago_id . "';
                        } else {
                            window.location.reload();
                        }
                    });
                </script>";
            }
        } catch (PDOException $e) {
            echo "<script>
                Swal.fire({
                    title: 'Error',
                    text: 'Error al registrar el pago: " . addslashes($e->getMessage()) . "',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            </script>";
        }
    }
}

// Obtener pagos con información detallada
$query = "
    SELECT 
        p.id,
        p.monto,
        p.fecha_pago,
        p.metodo_pago,
        p.concepto,
        c.tipo_contrato,
        CONCAT(cl.nombre, ' - ', pr.titulo) as contrato_detalle
    FROM pagos p
    JOIN contratos c ON p.contrato_id = c.id
    JOIN clientes cl ON c.cliente_id = cl.id
    JOIN propiedades pr ON c.propiedad_id = pr.id
    ORDER BY p.fecha_pago DESC";

$stmt = $pdo->query($query);
$pagos = $stmt->fetchAll();

// Calcular totales
$totales = [
    'efectivo' => 0,
    'transferencia' => 0,
    'tarjeta' => 0,
    'total' => 0
];

foreach ($pagos as $pago) {
    $totales[$pago['metodo_pago']] += $pago['monto'];
    $totales['total'] += $pago['monto'];
}
?>

<div class="dashboard-container">
    <div class="content-wrapper">
        <h1>Gestión de Pagos</h1>

        <!-- Resumen de pagos -->
        <div class="summary-cards">
            <div class="summary-card">
                <i class="fas fa-money-bill-wave"></i>
                <h3>Efectivo</h3>
                <p>$<?php echo number_format($totales['efectivo'], 2); ?></p>
            </div>
            <div class="summary-card">
                <i class="fas fa-exchange-alt"></i>
                <h3>Transferencia</h3>
                <p>$<?php echo number_format($totales['transferencia'], 2); ?></p>
            </div>
            <div class="summary-card">
                <i class="fas fa-credit-card"></i>
                <h3>Tarjeta</h3>
                <p>$<?php echo number_format($totales['tarjeta'], 2); ?></p>
            </div>
            <div class="summary-card total">
                <i class="fas fa-calculator"></i>
                <h3>Total</h3>
                <p>$<?php echo number_format($totales['total'], 2); ?></p>
            </div>
        </div>

        <!-- Formulario para registrar pago -->
        <div class="form-container">
            <h2>Registrar Nuevo Pago</h2>
            <form method="POST" class="styled-form">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="contrato_id">Contrato:</label>
                    <select name="contrato_id" id="contrato_id" required>
                        <option value="">Selecciona un contrato</option>
                        <?php
                        $stmt = $pdo->query("
                            SELECT 
                                c.id,
                                CONCAT(cl.nombre, ' - ', p.titulo, ' (', c.tipo_contrato, ')') as detalle
                            FROM contratos c
                            JOIN clientes cl ON c.cliente_id = cl.id
                            JOIN propiedades p ON c.propiedad_id = p.id
                            WHERE c.estado = 'activo'
                            ORDER BY cl.nombre ASC");
                        $contratos = $stmt->fetchAll();
                        foreach ($contratos as $contrato):
                        ?>
                            <option value="<?php echo $contrato['id']; ?>">
                                <?php echo htmlspecialchars($contrato['detalle']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="monto">Monto:</label>
                    <input type="number" step="0.01" id="monto" name="monto" required>
                </div>

                <div class="form-group">
                    <label for="fecha_pago">Fecha de pago:</label>
                    <input type="date" id="fecha_pago" name="fecha_pago" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="metodo_pago">Método de pago:</label>
                    <select id="metodo_pago" name="metodo_pago" required>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="concepto">Concepto:</label>
                    <textarea id="concepto" name="concepto" rows="3" required></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Registrar Pago
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de pagos -->
        <div class="table-container">
            <h2>Historial de Pagos</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Contrato</th>
                        <th>Fecha</th>
                        <th>Concepto</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $pago): ?>
                    <tr>
                        <td>#<?php echo str_pad($pago['id'], 6, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($pago['contrato_detalle']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($pago['fecha_pago'])); ?></td>
                        <td><?php echo htmlspecialchars($pago['concepto']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $pago['metodo_pago']; ?>">
                                <?php echo ucfirst($pago['metodo_pago']); ?>
                            </span>
                        </td>
                        <td>$<?php echo number_format($pago['monto'], 2); ?></td>
                        <td class="actions">
                            <button onclick="verComprobante(<?php echo $pago['id']; ?>)" class="btn-view" title="Ver comprobante">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="imprimirComprobante(<?php echo $pago['id']; ?>)" class="btn-print" title="Imprimir comprobante">
                                <i class="fas fa-print"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function verComprobante(id) {
    window.location.href = `ver_comprobante.php?id=${id}`;
}

function imprimirComprobante(id) {
    window.location.href = `imprimir_comprobante.php?id=${id}`;
}
</script>

<style>
.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.summary-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.summary-card i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #4a90e2;
}

.summary-card.total {
    background: #4a90e2;
    color: white;
}

.summary-card.total i {
    color: white;
}

.styled-form {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
}

.badge-efectivo {
    background: #28a745;
    color: white;
}

.badge-transferencia {
    background: #4a90e2;
    color: white;
}

.badge-tarjeta {
    background: #f39c12;
    color: white;
}

.btn-view,
.btn-print {
    padding: 0.25rem 0.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin: 0 0.25rem;
}

.btn-view {
    background: #4a90e2;
    color: white;
}

.btn-print {
    background: #28a745;
    color: white;
}

.table-container {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
</style>

<?php include('../footer.php'); ?>