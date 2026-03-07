<?php
/**
 * GENERAR PDF DE RECIBO
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Genera PDF profesional del recibo de pago
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

// NO INICIAR SESIÓN TODAVÍA - primero verificamos includes
define('ACCESS_GRANTED', true);

// Buffer de salida para capturar cualquier output accidental
ob_start();

require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'funciones.php';

// Ahora sí iniciamos sesión
session_start();

require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'middleware.php';
require_once MODELS_PATH . 'Factura.php';

// Proteger la ruta
proteger_ruta();

// Obtener ID de la factura
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    ob_end_clean();
    mensaje_error('ID de factura no válido.');
    header('Location: index.php');
    exit;
}

// Obtener datos completos de la factura
$factura = obtener_factura_completa($pdo, $id);

if (!$factura) {
    ob_end_clean();
    mensaje_error('La factura no existe.');
    header('Location: index.php');
    exit;
}

// Registrar auditoría
registrar_auditoria($pdo, $_SESSION['usuario_id'], 'PDF', 'facturas', $id, 
    "Generó PDF de recibo #{$factura['numero_correlativo']}");

// Calcular edad del paciente
$edad_paciente = calcular_edad($factura['paciente_fecha_nac']);

// Limpiar cualquier output buffer antes de TCPDF
ob_end_clean();

// Incluir TCPDF
require_once __DIR__ . '/../../vendor/tcpdf/tcpdf.php';

// Crear instancia de PDF
class ReciboPDF extends TCPDF {
    private $factura_data;
    
    public function setFacturaData($data) {
        $this->factura_data = $data;
    }
    
    // Header del PDF
    public function Header() {
        // Logo (si existe)
        $logo_path = __DIR__ . '/../../assets/img/logo-clinica.png';
        if (file_exists($logo_path)) {
            $this->Image($logo_path, 15, 10, 30, '', 'PNG');
        }
        
        // Información del doctor
        $this->SetFont('helvetica', 'B', 16);
        $this->SetXY(50, 12);
        $this->Cell(0, 5, 'Clínica Médica de la Mujer', 0, 1, 'L');
        
        $this->SetFont('helvetica', '', 11);
        $this->SetXY(50, 18);
        $this->Cell(0, 5, 'Dr. Byron Daniel Castillo Perea', 0, 1, 'L');
        
        $this->SetFont('helvetica', '', 9);
        $this->SetXY(50, 23);
        $this->Cell(0, 4, 'Ginecología y Obstetricia', 0, 1, 'L');
        
        $this->SetXY(50, 27);
        $this->Cell(0, 4, 'Huehuetenango, Guatemala', 0, 1, 'L');
        
        // Línea separadora
        $this->SetLineWidth(0.5);
        $this->Line(15, 38, 195, 38);
    }
    
    // Footer del PDF
    public function Footer() {
        $this->SetY(-25);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        
        // Línea separadora
        $this->SetLineWidth(0.3);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        
        $this->Ln(3);
        $this->Cell(0, 4, 'Clínica Médica de la Mujer - Zona 5, Huehuetenango', 0, 1, 'C');
        $this->Cell(0, 4, 'Tel: 7764-0000 | Email: clinica@medicadelamujer.com', 0, 0, 'C');
    }
}

// Crear PDF
$pdf = new ReciboPDF('P', 'mm', 'Letter', true, 'UTF-8', false);
$pdf->setFacturaData($factura);

// Configuración del documento
$pdf->SetCreator('Sistema Clínico - GG-Systems');
$pdf->SetAuthor('Clínica Médica de la Mujer');
$pdf->SetTitle('Recibo #' . str_pad($factura['numero_correlativo'], 4, '0', STR_PAD_LEFT));
$pdf->SetSubject('Recibo de Pago');

// Configuración de página
$pdf->SetMargins(15, 45, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(15);
$pdf->SetAutoPageBreak(true, 25);

// Agregar página
$pdf->AddPage();

// Título del documento
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetTextColor(0, 119, 182);
$pdf->Cell(0, 8, 'RECIBO DE PAGO', 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(2);

// Número de recibo y fecha
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(95, 6, 'Recibo No: #' . str_pad($factura['numero_correlativo'], 4, '0', STR_PAD_LEFT), 0, 0, 'L');
$pdf->Cell(95, 6, 'Fecha: ' . formatear_fecha($factura['fecha']), 0, 1, 'R');

// Estado (si está anulado)
if ($factura['estado'] === 'anulado') {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor(220, 53, 69);
    $pdf->Cell(0, 8, 'RECIBO ANULADO', 0, 1, 'C');
    $pdf->SetTextColor(0, 0, 0);
}

$pdf->Ln(3);

// Datos del paciente
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'DATOS DEL PACIENTE', 0, 1, 'L', true);
$pdf->Ln(1);

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, 5, 'Nombre:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 5, $factura['paciente_nombre'], 0, 1, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, 5, 'Código:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(60, 5, $factura['paciente_codigo'], 0, 0, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(30, 5, 'Edad:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 5, $edad_paciente . ' años', 0, 1, 'L');

if (!empty($factura['paciente_telefono'])) {
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 5, 'Teléfono:', 0, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 5, $factura['paciente_telefono'], 0, 1, 'L');
}

$pdf->Ln(5);

// Concepto
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'CONCEPTO', 0, 1, 'L', true);
$pdf->Ln(1);

$pdf->SetFont('helvetica', '', 11);
$pdf->MultiCell(0, 5, $factura['concepto'], 0, 'L');

$pdf->Ln(3);

// Detalles del pago
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'DETALLES DEL PAGO', 0, 1, 'L', true);
$pdf->Ln(2);

// Tabla de montos
$pdf->SetFont('helvetica', '', 10);

// Monto Total
$pdf->Cell(100, 7, 'Monto Total:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 7, 'Q ' . number_format($factura['monto'], 2), 0, 1, 'R');

// Monto Pagado
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(100, 7, 'Monto Pagado:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor(40, 167, 69);
$pdf->Cell(0, 7, 'Q ' . number_format($factura['monto_pagado'], 2), 0, 1, 'R');
$pdf->SetTextColor(0, 0, 0);

// Cambio o Pendiente
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(100, 7, $factura['cambio'] >= 0 ? 'Cambio:' : 'Saldo Pendiente:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor($factura['cambio'] >= 0 ? 23 : 255, $factura['cambio'] >= 0 ? 162 : 193, $factura['cambio'] >= 0 ? 184 : 7);
$pdf->Cell(0, 7, 'Q ' . number_format(abs($factura['cambio']), 2), 0, 1, 'R');
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(3);

// Forma de pago
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(100, 6, 'Forma de Pago:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, ucfirst($factura['forma_pago']), 0, 1, 'R');

// Estado
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(100, 6, 'Estado:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$color = $factura['estado'] === 'pagado' ? [40, 167, 69] : ($factura['estado'] === 'anulado' ? [220, 53, 69] : [255, 193, 7]);
$pdf->SetTextColor($color[0], $color[1], $color[2]);
$pdf->Cell(0, 6, strtoupper($factura['estado']), 0, 1, 'R');
$pdf->SetTextColor(0, 0, 0);

// Notas (si hay y no está anulado)
if (!empty($factura['notas']) && $factura['estado'] !== 'anulado') {
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'NOTAS:', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell(0, 4, $factura['notas'], 0, 'L');
}

// Motivo de anulación (si está anulado)
if ($factura['estado'] === 'anulado' && !empty($factura['notas'])) {
    $pdf->Ln(5);
    $pdf->SetFillColor(248, 215, 218);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'MOTIVO DE ANULACIÓN:', 0, 1, 'L', true);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(114, 28, 36);
    $pdf->MultiCell(0, 4, $factura['notas'], 0, 'L');
    $pdf->SetTextColor(0, 0, 0);
}

// Espacio para firma
$pdf->Ln(15);

// Línea de firma
$pdf->Line(120, $pdf->GetY(), 180, $pdf->GetY());
$pdf->Ln(2);

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 5, $factura['usuario_nombre'], 0, 1, 'R');
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 5, 'Firma y Sello', 0, 1, 'R');

// Nota al pie
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(128, 128, 128);
$pdf->MultiCell(0, 4, 'Este recibo es un comprobante de pago. Consérvelo para futuras referencias. Para cualquier aclaración, presente este documento en nuestras instalaciones.', 0, 'J');

// Nombre del archivo
$nombre_archivo = 'Recibo_' . str_pad($factura['numero_correlativo'], 4, '0', STR_PAD_LEFT) . '_' . 
                  preg_replace('/[^A-Za-z0-9]/', '', $factura['paciente_nombre']) . '.pdf';

// Salida del PDF
$pdf->Output($nombre_archivo, 'I'); // 'I' = inline en navegador, 'D' = descarga
exit;