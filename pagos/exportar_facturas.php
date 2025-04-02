<?php
require_once('../includes/db.php');
require_once('../vendor/autoload.php');

// Obtener parámetros de filtro
$fecha_desde = filter_input(INPUT_GET, 'fecha_desde', FILTER_SANITIZE_STRING);
$fecha_hasta = filter_input(INPUT_GET, 'fecha_hasta', FILTER_SANITIZE_STRING);
$estado = filter_input(INPUT_GET, 'estado', FILTER_SANITIZE_STRING);
$cliente = filter_input(INPUT_GET, 'cliente', FILTER_SANITIZE_STRING);
$formato = filter_input(INPUT_GET, 'formato', FILTER_SANITIZE_STRING);

// Construir la consulta
$query = "
    SELECT 
        f.id,
        c.nombre as cliente_nombre,
        f.fecha_emision,
        f.concepto,
        f.tipo as tipo_factura,
        f.monto,
        f.estado
    FROM facturas f
    JOIN clientes c ON f.cliente_id = c.id
    WHERE 1=1";

$params = [];

if ($fecha_desde) {
    $query .= " AND f.fecha_emision >= ?";
    $params[] = $fecha_desde;
}

if ($fecha_hasta) {
    $query .= " AND f.fecha_emision <= ?";
    $params[] = $fecha_hasta;
}

if ($estado) {
    $query .= " AND f.estado = ?";
    $params[] = $estado;
}

if ($cliente) {
    $query .= " AND (c.nombre LIKE ? OR c.email LIKE ?)";
    $params[] = "%$cliente%";
    $params[] = "%$cliente%";
}

$query .= " ORDER BY f.fecha_emision DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($formato === 'excel') {
        // Exportar a Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="facturas.xls"');
        header('Cache-Control: max-age=0');
        
        echo "
        <table border='1'>
            <tr>
                <th>Número</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Concepto</th>
                <th>Tipo</th>
                <th>Monto</th>
                <th>Estado</th>
            </tr>";
        
        foreach ($facturas as $factura) {
            echo "<tr>";
            echo "<td>" . str_pad($factura['id'], 6, '0', STR_PAD_LEFT) . "</td>";
            echo "<td>" . $factura['cliente_nombre'] . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($factura['fecha_emision'])) . "</td>";
            echo "<td>" . $factura['concepto'] . "</td>";
            echo "<td>Factura " . $factura['tipo_factura'] . "</td>";
            echo "<td>$" . number_format($factura['monto'], 2) . "</td>";
            echo "<td>" . ucfirst($factura['estado']) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        // Exportar a PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator('Inmobiliaria');
        $pdf->SetAuthor('Inmobiliaria');
        $pdf->SetTitle('Reporte de Facturas');
        
        $pdf->SetHeaderData('', 0, 'Reporte de Facturas', '');
        
        $pdf->setHeaderFont(Array('helvetica', '', 12));
        $pdf->setFooterFont(Array('helvetica', '', 8));
        
        $pdf->SetDefaultMonospacedFont('courier');
        
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        
        $pdf->SetAutoPageBreak(TRUE, 15);
        
        $pdf->AddPage();
        
        $pdf->SetFont('helvetica', '', 10);
        
        // Crear la tabla
        $html = '
        <table border="1" cellpadding="4">
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <th>Número</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Concepto</th>
                <th>Tipo</th>
                <th>Monto</th>
                <th>Estado</th>
            </tr>';
        
        foreach ($facturas as $factura) {
            $html .= '<tr>';
            $html .= '<td>' . str_pad($factura['id'], 6, '0', STR_PAD_LEFT) . '</td>';
            $html .= '<td>' . $factura['cliente_nombre'] . '</td>';
            $html .= '<td>' . date('d/m/Y', strtotime($factura['fecha_emision'])) . '</td>';
            $html .= '<td>' . $factura['concepto'] . '</td>';
            $html .= '<td>Factura ' . $factura['tipo_factura'] . '</td>';
            $html .= '<td>$' . number_format($factura['monto'], 2) . '</td>';
            $html .= '<td>' . ucfirst($factura['estado']) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        
        $pdf->writeHTML($html, true, false, true, false, '');
        
        $pdf->Output('Reporte_Facturas.pdf', 'D');
    }
} catch (PDOException $e) {
    die('Error al obtener las facturas: ' . $e->getMessage());
}
