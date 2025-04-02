<?php
include('../includes/db.php');
include('../header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = filter_input(INPUT_POST, 'cliente_id', FILTER_SANITIZE_NUMBER_INT);
    $monto = filter_input(INPUT_POST, 'monto', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $fecha_emision = filter_input(INPUT_POST, 'fecha_emision', FILTER_SANITIZE_STRING);
    $concepto = filter_input(INPUT_POST, 'concepto', FILTER_SANITIZE_STRING);
    $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);

    if ($cliente_id && $monto && $fecha_emision && $concepto && $tipo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO facturas (cliente_id, monto, fecha_emision, concepto, tipo, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')");
            if ($stmt->execute([$cliente_id, $monto, $fecha_emision, $concepto, $tipo])) {
                $factura_id = $pdo->lastInsertId();
                echo "<script>
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Factura generada correctamente',
                        icon: 'success',
                        confirmButtonText: 'Ver Factura',
                        showCancelButton: true,
                        cancelButtonText: 'Cerrar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'ver_factura.php?id=" . $factura_id . "';
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
                    text: 'Error al generar la factura: " . addslashes($e->getMessage()) . "',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            </script>";
        }
    }
}

// Obtener facturas con información detallada
$query = "
    SELECT 
        f.id,
        f.monto,
        f.fecha_emision,
        f.concepto,
        f.tipo,
        f.estado,
        c.nombre as cliente_nombre,
        c.email as cliente_email
    FROM facturas f
    JOIN clientes c ON f.cliente_id = c.id
    ORDER BY f.fecha_emision DESC";

$stmt = $pdo->query($query);
$facturas = $stmt->fetchAll();

// Calcular totales por estado
$totales = [
    'pendiente' => 0,
    'pagada' => 0,
    'cancelada' => 0,
    'total' => 0
];

foreach ($facturas as $factura) {
    if ($factura['estado'] !== 'cancelada') {
        $totales[$factura['estado']] += $factura['monto'];
        $totales['total'] += $factura['monto'];
    }
}
?>

<div class="dashboard-container">
    <div class="content-wrapper">
        <h1>Facturación</h1>

        <!-- Resumen de facturación -->
        <div class="summary-cards">
            <div class="summary-card">
                <i class="fas fa-clock"></i>
                <h3>Pendiente</h3>
                <p>$<?php echo number_format($totales['pendiente'], 2); ?></p>
            </div>
            <div class="summary-card">
                <i class="fas fa-check-circle"></i>
                <h3>Pagado</h3>
                <p>$<?php echo number_format($totales['pagada'], 2); ?></p>
            </div>
            <div class="summary-card total">
                <i class="fas fa-calculator"></i>
                <h3>Total</h3>
                <p>$<?php echo number_format($totales['total'], 2); ?></p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-container">
            <form id="filters-form" class="filters-form">
                <div class="filter-group">
                    <label for="fecha_desde">Desde:</label>
                    <input type="date" id="fecha_desde" name="fecha_desde">
                </div>
                <div class="filter-group">
                    <label for="fecha_hasta">Hasta:</label>
                    <input type="date" id="fecha_hasta" name="fecha_hasta">
                </div>
                <div class="filter-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="pagada">Pagada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="cliente">Cliente:</label>
                    <input type="text" id="cliente" name="cliente" placeholder="Buscar por cliente...">
                </div>
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <button type="button" class="btn-reset" onclick="resetFilters()">
                    <i class="fas fa-undo"></i> Reiniciar
                </button>
            </form>
        </div>

        <!-- Botones de exportación -->
        <div class="export-buttons">
            <button onclick="exportarExcel()" class="btn-export">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </button>
            <button onclick="exportarPDF()" class="btn-export">
                <i class="fas fa-file-pdf"></i> Exportar a PDF
            </button>
        </div>

        <!-- Formulario para generar factura -->
        <div class="form-container">
            <h2>Generar Nueva Factura</h2>
            <form method="POST" class="styled-form">
                <div class="form-group">
                    <label for="cliente_id">Cliente:</label>
                    <select name="cliente_id" id="cliente_id" required>
                        <option value="">Selecciona un cliente</option>
                        <?php
                        $stmt = $pdo->query("SELECT id, nombre, email FROM clientes ORDER BY nombre ASC");
                        $clientes = $stmt->fetchAll();
                        foreach ($clientes as $cliente):
                        ?>
                            <option value="<?php echo $cliente['id']; ?>">
                                <?php echo htmlspecialchars($cliente['nombre']) . ' (' . htmlspecialchars($cliente['email']) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="monto">Monto:</label>
                    <input type="number" step="0.01" id="monto" name="monto" required>
                </div>

                <div class="form-group">
                    <label for="fecha_emision">Fecha de emisión:</label>
                    <input type="date" id="fecha_emision" name="fecha_emision" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="concepto">Concepto:</label>
                    <textarea id="concepto" name="concepto" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo de factura:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="A">Factura A</option>
                        <option value="B">Factura B</option>
                        <option value="C">Factura C</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-file-invoice"></i> Generar Factura
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de facturas -->
        <div class="table-container">
            <h2>Historial de Facturas</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Concepto</th>
                        <th>Tipo</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="facturas-tbody">
                    <?php foreach ($facturas as $factura): ?>
                    <tr>
                        <td>#<?php echo str_pad($factura['id'], 6, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($factura['cliente_nombre']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($factura['fecha_emision'])); ?></td>
                        <td><?php echo htmlspecialchars($factura['concepto']); ?></td>
                        <td>
                            <span class="badge badge-tipo">
                                Factura <?php echo $factura['tipo']; ?>
                            </span>
                        </td>
                        <td>$<?php echo number_format($factura['monto'], 2); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $factura['estado']; ?>">
                                <?php echo ucfirst($factura['estado']); ?>
                            </span>
                        </td>
                        <td class="actions">
                            <button onclick="verFactura(<?php echo $factura['id']; ?>)" class="btn-view" title="Ver factura">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="descargarFactura(<?php echo $factura['id']; ?>)" class="btn-download" title="Descargar PDF">
                                <i class="fas fa-download"></i>
                            </button>
                            <?php if ($factura['estado'] === 'pendiente'): ?>
                            <button onclick="marcarComoPagada(<?php echo $factura['id']; ?>)" class="btn-pay" title="Marcar como pagada">
                                <i class="fas fa-check"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function verFactura(id) {
    window.location.href = `ver_factura.php?id=${id}`;
}

function descargarFactura(id) {
    window.location.href = `descargar_factura.php?id=${id}`;
}

function marcarComoPagada(id) {
    Swal.fire({
        title: '¿Marcar como pagada?',
        text: '¿Estás seguro de marcar esta factura como pagada?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, marcar como pagada',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('estado', 'pagada');

            fetch('actualizar_estado_factura.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Actualizada!',
                        text: 'La factura ha sido marcada como pagada.',
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.error || 'Error al actualizar la factura');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: error.message,
                    icon: 'error'
                });
            });
        }
    });
}

function resetFilters() {
    document.getElementById('filters-form').reset();
    // Recargar la tabla con los valores por defecto
    cargarFacturas();
}

function cargarFacturas() {
    const formData = new FormData(document.getElementById('filters-form'));
    
    fetch('obtener_facturas.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.message);
        }
        // Actualizar la tabla con los resultados filtrados
        actualizarTablaFacturas(data);
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: error.message || 'Error al cargar las facturas',
            icon: 'error'
        });
    });
}

function actualizarTablaFacturas(facturas) {
    const tbody = document.querySelector('.styled-table tbody');
    tbody.innerHTML = '';
    
    facturas.forEach(factura => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>#${String(factura.id).padStart(6, '0')}</td>
            <td>${factura.cliente_nombre}</td>
            <td>${new Date(factura.fecha_emision).toLocaleDateString()}</td>
            <td>${factura.concepto}</td>
            <td>
                <span class="badge badge-${factura.estado}">
                    ${factura.estado.charAt(0).toUpperCase() + factura.estado.slice(1)}
                </span>
            </td>
            <td>$${Number(factura.monto).toLocaleString('es-AR', {minimumFractionDigits: 2})}</td>
            <td class="actions">
                <button onclick="verFactura(${factura.id})" class="btn-view" title="Ver factura">
                    <i class="fas fa-eye"></i>
                </button>
                <button onclick="descargarFactura(${factura.id})" class="btn-download" title="Descargar PDF">
                    <i class="fas fa-download"></i>
                </button>
                ${factura.estado === 'pendiente' ? `
                    <button onclick="marcarComoPagada(${factura.id})" class="btn-pay" title="Marcar como pagada">
                        <i class="fas fa-check"></i>
                    </button>
                ` : ''}
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function exportarExcel() {
    const formData = new FormData(document.getElementById('filters-form'));
    formData.append('formato', 'excel');
    
    window.location.href = `exportar_facturas.php?${new URLSearchParams(formData)}`;
}

function exportarPDF() {
    const formData = new FormData(document.getElementById('filters-form'));
    formData.append('formato', 'pdf');
    
    window.location.href = `exportar_facturas.php?${new URLSearchParams(formData)}`;
}

// Inicializar los filtros
document.getElementById('filters-form').addEventListener('submit', function(e) {
    e.preventDefault();
    cargarFacturas();
});

// Cargar facturas al inicio
cargarFacturas();
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

.badge-pendiente {
    background: #ffc107;
    color: black;
}

.badge-pagada {
    background: #28a745;
    color: white;
}

.badge-cancelada {
    background: #dc3545;
    color: white;
}

.badge-tipo {
    background: #6c757d;
    color: white;
}

.btn-view,
.btn-download,
.btn-pay {
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

.btn-download {
    background: #28a745;
    color: white;
}

.btn-pay {
    background: #ffc107;
    color: black;
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

.filters-container {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.filters-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.filter-group input,
.filter-group select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.btn-filter,
.btn-reset,
.btn-export {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-filter {
    background: #4a90e2;
    color: white;
}

.btn-reset {
    background: #6c757d;
    color: white;
}

.btn-export {
    background: #28a745;
    color: white;
    margin-right: 0.5rem;
}

.export-buttons {
    margin-bottom: 1.5rem;
}
</style>

<?php include('../footer.php'); ?>
