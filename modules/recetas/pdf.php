<?php
/**
 * GENERAR PDF DE RECETA MÉDICA
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Genera PDF profesional de receta con logo y firma del doctor
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
require_once MODELS_PATH . 'Receta.php';

// Proteger la ruta
proteger_ruta();

// Obtener ID de la receta
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    ob_end_clean();
    mensaje_error('ID de receta no válido.');
    header('Location: index.php');
    exit;
}

// Obtener datos completos de la receta
$receta = obtener_receta_completa($pdo, $id);

if (!$receta) {
    ob_end_clean();
    mensaje_error('La receta no existe.');
    header('Location: index.php');
    exit;
}

// Registrar auditoría
registrar_auditoria($pdo, $_SESSION['usuario_id'], 'PDF', 'recetas', $id, 
    "Generó PDF de receta #{$receta['numero_receta']}");

// Calcular edad del paciente
$edad_paciente = calcular_edad($receta['paciente_fecha_nac']);

// Limpiar cualquier output buffer antes de TCPDF
ob_end_clean();

// Incluir TCPDF
require_once __DIR__ . '/../../vendor/tcpdf/tcpdf.php';

// Crear instancia de PDF
class RecetaPDF extends TCPDF {
    private $receta_data;
    
    public function setRecetaData($data) {
        $this->receta_data = $data;
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
        $this->Cell(0, 5, 'Dr. ' . ($this->receta_data['medico_nombre'] ?? 'Byron Castillo'), 0, 1, 'L');
        
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
$pdf = new RecetaPDF('P', 'mm', 'Letter', true, 'UTF-8', false);
$pdf->setRecetaData($receta);

// Configuración del documento
$pdf->SetCreator('Sistema Clínico - GG-Systems');
$pdf->SetAuthor('Dr. ' . ($receta['medico_nombre'] ?? 'Byron Castillo'));
$pdf->SetTitle('Receta Médica #' . $receta['numero_receta']);
$pdf->SetSubject('Receta Médica');

// Configuración de página
$pdf->SetMargins(15, 45, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(15);
$pdf->SetAutoPageBreak(true, 25);

// Agregar página
$pdf->AddPage();

// Información de la receta
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 6, 'RECETA MÉDICA', 0, 1, 'C');
$pdf->Ln(2);

// Número de receta y fecha
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(95, 6, 'Receta No: #' . str_pad($receta['numero_receta'], 4, '0', STR_PAD_LEFT), 0, 0, 'L');
$pdf->Cell(95, 6, 'Fecha: ' . formatear_fecha($receta['fecha_emision']), 0, 1, 'R');
$pdf->Ln(3);

// Datos del paciente
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'DATOS DEL PACIENTE', 0, 1, 'L', true);
$pdf->Ln(1);

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, 5, 'Nombre:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 5, $receta['paciente_nombre'], 0, 1, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, 5, 'Código:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(60, 5, $receta['paciente_codigo'], 0, 0, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(30, 5, 'Edad:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 5, $edad_paciente . ' años', 0, 1, 'L');

if (!empty($receta['paciente_telefono'])) {
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 5, 'Teléfono:', 0, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 5, $receta['paciente_telefono'], 0, 1, 'L');
}

$pdf->Ln(5);

// Símbolo Rx
$pdf->SetFont('helvetica', 'B', 24);
$pdf->SetTextColor(0, 119, 182); // Color azul
$pdf->Cell(0, 10, 'Rx', 0, 1, 'L');
$pdf->SetTextColor(0, 0, 0); // Volver a negro
$pdf->Ln(2);

// Medicamentos
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'MEDICAMENTOS RECETADOS', 0, 1, 'L', true);
$pdf->Ln(2);

if (!empty($receta['medicamentos'])) {
    $pdf->SetFont('helvetica', '', 10);
    
    foreach ($receta['medicamentos'] as $index => $med) {
        // Número del medicamento
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(10, 5, ($index + 1) . '.', 0, 0, 'R');
        
        // Nombre del medicamento
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 5, $med['medicamento'], 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetX(25);
        
        // Dosis, vía, frecuencia, duración
        $detalles = [];
        if (!empty($med['dosis'])) $detalles[] = 'Dosis: ' . $med['dosis'];
        if (!empty($med['via'])) $detalles[] = 'Vía: ' . $med['via'];
        if (!empty($med['frecuencia'])) $detalles[] = 'Frecuencia: ' . $med['frecuencia'];
        if (!empty($med['duracion'])) $detalles[] = 'Duración: ' . $med['duracion'];
        
        if (!empty($detalles)) {
            $pdf->MultiCell(0, 4, implode(' | ', $detalles), 0, 'L');
        }
        
        $pdf->Ln(2);
    }
} else {
    $pdf->SetFont('helvetica', 'I', 10);
    $pdf->Cell(0, 5, 'No se registraron medicamentos', 0, 1, 'L');
}

$pdf->Ln(3);

// Indicaciones generales
if (!empty($receta['indicaciones_generales'])) {
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'INDICACIONES GENERALES', 0, 1, 'L', true);
    $pdf->Ln(2);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 5, $receta['indicaciones_generales'], 0, 'L');
    $pdf->Ln(3);
}

// Espacio para firma
$pdf->Ln(15);

// Línea de firma
$pdf->Line(120, $pdf->GetY(), 180, $pdf->GetY());
$pdf->Ln(2);

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 5, 'Dr. ' . ($receta['medico_nombre'] ?? 'Byron Castillo'), 0, 1, 'R');
$pdf->Cell(0, 5, 'Ginecología y Obstetricia', 0, 1, 'R');

// Nota al pie
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(128, 128, 128);
$pdf->MultiCell(0, 4, 'Nota: Esta receta es válida únicamente con la firma y sello del médico. No se automedique. Consulte a su médico ante cualquier duda.', 0, 'L');

// Nombre del archivo
$nombre_archivo = 'Receta_' . str_pad($receta['numero_receta'], 4, '0', STR_PAD_LEFT) . '_' . 
                  preg_replace('/[^A-Za-z0-9]/', '', $receta['paciente_nombre']) . '.pdf';

// Salida del PDF
$pdf->Output($nombre_archivo, 'I'); // 'I' = inline en navegador, 'D' = descarga
exit;