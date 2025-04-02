<?php
ob_start(); // Iniciar buffer de salida
require_once('../includes/db.php');
require_once('../vendor/autoload.php');

// No necesitamos el use TCPDF ya que extendemos la clase directamente
class MYPDF extends \TCPDF {
    public function Header() {
        // No implementamos el header aquí porque lo haremos en el contenido principal
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C');
    }
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$id) {
    die('ID de factura no proporcionado');
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
        die('Factura no encontrada');
    }
} catch (PDOException $e) {
    die('Error al obtener la factura: ' . $e->getMessage());
}

// Crear nuevo documento PDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Establecer información del documento
$pdf->SetCreator('Inmobiliaria');
$pdf->SetAuthor('Inmobiliaria');
$pdf->SetTitle('Factura #' . str_pad($factura['id'], 6, '0', STR_PAD_LEFT));

// Establecer márgenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Establecer saltos de página automáticos
$pdf->SetAutoPageBreak(TRUE, 15);

// Agregar página
$pdf->AddPage();

// Establecer fuente
$pdf->SetFont('helvetica', '', 12);

// Logo y datos de la empresa
if (file_exists('../assets/img/logo.png')) {
    $pdf->Image('../assets/img/logo.png', 15, 15, 30);
}

$pdf->SetXY(50, 15);
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'INMOBILIARIA', 0, 1);

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(50, 25);
$pdf->Cell(0, 6, 'Dirección de la Inmobiliaria', 0, 1);
$pdf->SetX(50);
$pdf->Cell(0, 6, 'Teléfono: +XX XXX XXXXXX', 0, 1);
$pdf->SetX(50);
$pdf->Cell(0, 6, 'Email: contacto@inmobiliaria.com', 0, 1);

// Información de la factura
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'FACTURA ' . $factura['tipo'], 0, 1, 'R');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'Nº: ' . str_pad($factura['id'], 6, '0', STR_PAD_LEFT), 0, 1, 'R');
$pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y', strtotime($factura['fecha_emision'])), 0, 1, 'R');

// Información del cliente
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'DATOS DEL CLIENTE:', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'Nombre: ' . $factura['cliente_nombre'], 0, 1);
$pdf->Cell(0, 6, 'Email: ' . $factura['cliente_email'], 0, 1);
$pdf->Cell(0, 6, 'Dirección: ' . $factura['cliente_direccion'], 0, 1);
$pdf->Cell(0, 6, 'Teléfono: ' . $factura['cliente_telefono'], 0, 1);

// Detalles de la factura
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'DETALLES DE LA FACTURA:', 0, 1);
$pdf->SetFont('helvetica', '', 10);

// Cabecera de la tabla
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(120, 8, 'Concepto', 1, 0, 'C', true);
$pdf->Cell(55, 8, 'Monto', 1, 1, 'C', true);

// Contenido de la tabla
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(120, 8, $factura['concepto'], 1);
$pdf->Cell(55, 8, '$ ' . number_format($factura['monto'], 2), 1, 1, 'R');

// Total
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(120);
$pdf->Cell(25, 8, 'TOTAL:', 0);
$pdf->Cell(30, 8, '$ ' . number_format($factura['monto'], 2), 0, 1, 'R');

// Estado de la factura
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$estadoTexto = strtoupper($factura['estado']);
$pdf->Cell(0, 8, 'ESTADO: ' . $estadoTexto, 0, 1);

// Términos y condiciones
$pdf->Ln(15);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'Términos y Condiciones:', 0, 1);
$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(0, 4, 'Esta factura debe ser pagada en un plazo máximo de 30 días desde su emisión. El pago puede realizarse mediante transferencia bancaria o en efectivo en nuestras oficinas.', 0, 'L');

// Limpiar cualquier salida anterior
ob_end_clean();

// Generar el PDF
$pdf->Output('Factura_' . str_pad($factura['id'], 6, '0', STR_PAD_LEFT) . '.pdf', 'D');
