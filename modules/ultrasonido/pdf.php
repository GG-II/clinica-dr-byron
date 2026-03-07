<?php
/**
 * GENERAR PDF DE INFORME DE ULTRASONIDO
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Genera PDF profesional del informe de ultrasonido obstétrico
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
require_once MODELS_PATH . 'Ultrasonido.php';

// Proteger la ruta
proteger_ruta();

// Obtener ID del ultrasonido
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    ob_end_clean();
    mensaje_error('ID de ultrasonido no válido.');
    header('Location: index.php');
    exit;
}

// Obtener datos completos del ultrasonido
$ultrasonido = obtener_ultrasonido_completo($pdo, $id);

if (!$ultrasonido) {
    ob_end_clean();
    mensaje_error('El informe de ultrasonido no existe.');
    header('Location: index.php');
    exit;
}

// Registrar auditoría
registrar_auditoria($pdo, $_SESSION['usuario_id'], 'PDF', 'ultrasonidos', $id, 
    "Generó PDF de ultrasonido del paciente {$ultrasonido['paciente_nombre']}");

// Calcular edad del paciente
$edad_paciente = calcular_edad($ultrasonido['paciente_fecha_nac']);

// Limpiar cualquier output buffer antes de TCPDF
ob_end_clean();

// Incluir TCPDF
require_once __DIR__ . '/../../vendor/tcpdf/tcpdf.php';

// Crear instancia de PDF
class UltrasonidoPDF extends TCPDF {
    private $ultrasonido_data;
    
    public function setUltrasonidoData($data) {
        $this->ultrasonido_data = $data;
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
        $this->Cell(0, 5, 'Dr. ' . ($this->ultrasonido_data['doctor_nombre'] ?? 'Byron Daniel Castillo Perea'), 0, 1, 'L');
        
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
$pdf = new UltrasonidoPDF('P', 'mm', 'Letter', true, 'UTF-8', false);
$pdf->setUltrasonidoData($ultrasonido);

// Configuración del documento
$pdf->SetCreator('Sistema Clínico - GG-Systems');
$pdf->SetAuthor('Dr. ' . ($ultrasonido['doctor_nombre'] ?? 'Byron Daniel Castillo Perea'));
$pdf->SetTitle('Informe de Ultrasonido - ' . $ultrasonido['paciente_nombre']);
$pdf->SetSubject('Informe de Ultrasonido Obstétrico');

// Configuración de página
$pdf->SetMargins(15, 45, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(15);
$pdf->SetAutoPageBreak(true, 25);

// Agregar página
$pdf->AddPage();

// Título del documento
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor(0, 119, 182);
$pdf->Cell(0, 8, 'INFORME DE ULTRASONIDO OBSTÉTRICO', 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(3);

// Fecha del examen
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 5, 'Fecha del examen: ' . formatear_fecha($ultrasonido['fecha']), 0, 1, 'R');
$pdf->Ln(3);

// Datos del paciente
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'DATOS DEL PACIENTE', 0, 1, 'L', true);
$pdf->Ln(1);

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, 5, 'Nombre:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 5, $ultrasonido['paciente_nombre'], 0, 1, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, 5, 'Código:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(60, 5, $ultrasonido['paciente_codigo'], 0, 0, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(30, 5, 'Edad:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 5, $edad_paciente . ' años', 0, 1, 'L');

if (!empty($ultrasonido['paciente_telefono'])) {
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 5, 'Teléfono:', 0, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 5, $ultrasonido['paciente_telefono'], 0, 1, 'L');
}

$pdf->Ln(5);

// Edad gestacional (destacada)
if (!empty($ultrasonido['edad_gestacional'])) {
    $pdf->SetFillColor(230, 240, 255);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'EDAD GESTACIONAL', 0, 1, 'L', true);
    $pdf->Ln(1);
    
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor(0, 100, 200);
    $pdf->Cell(0, 6, $ultrasonido['edad_gestacional'], 0, 1, 'L');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(3);
}

// Biometría fetal
$tiene_medidas = $ultrasonido['dbp'] || $ultrasonido['cc'] || $ultrasonido['ca'] || $ultrasonido['lf'];

if ($tiene_medidas) {
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'BIOMETRÍA FETAL', 0, 1, 'L', true);
    $pdf->Ln(2);
    
    // Tabla de medidas
    $pdf->SetFillColor(220, 220, 220);
    $pdf->SetFont('helvetica', 'B', 9);
    
    // Headers
    $pdf->Cell(95, 6, 'Parámetro', 1, 0, 'L', true);
    $pdf->Cell(40, 6, 'Medida', 1, 0, 'C', true);
    $pdf->Cell(25, 6, 'Unidad', 1, 1, 'C', true);
    
    $pdf->SetFont('helvetica', '', 9);
    
    // DBP
    if ($ultrasonido['dbp']) {
        $pdf->Cell(95, 5, 'Diámetro Biparietal (DBP)', 1, 0, 'L');
        $pdf->Cell(40, 5, number_format($ultrasonido['dbp'], 2), 1, 0, 'C');
        $pdf->Cell(25, 5, 'mm', 1, 1, 'C');
    }
    
    // CC
    if ($ultrasonido['cc']) {
        $pdf->Cell(95, 5, 'Circunferencia Cefálica (CC)', 1, 0, 'L');
        $pdf->Cell(40, 5, number_format($ultrasonido['cc'], 2), 1, 0, 'C');
        $pdf->Cell(25, 5, 'mm', 1, 1, 'C');
    }
    
    // CA
    if ($ultrasonido['ca']) {
        $pdf->Cell(95, 5, 'Circunferencia Abdominal (CA)', 1, 0, 'L');
        $pdf->Cell(40, 5, number_format($ultrasonido['ca'], 2), 1, 0, 'C');
        $pdf->Cell(25, 5, 'mm', 1, 1, 'C');
    }
    
    // LF
    if ($ultrasonido['lf']) {
        $pdf->Cell(95, 5, 'Longitud del Fémur (LF)', 1, 0, 'L');
        $pdf->Cell(40, 5, number_format($ultrasonido['lf'], 2), 1, 0, 'C');
        $pdf->Cell(25, 5, 'mm', 1, 1, 'C');
    }
    
    // Peso estimado (destacado)
    if ($ultrasonido['peso_estimado']) {
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(230, 240, 255);
        $pdf->Cell(95, 6, 'Peso Fetal Estimado', 1, 0, 'L', true);
        $pdf->Cell(40, 6, number_format($ultrasonido['peso_estimado'], 0), 1, 0, 'C', true);
        $pdf->Cell(25, 6, 'gramos', 1, 1, 'C', true);
    }
    
    $pdf->Ln(4);
}

// Observaciones
if (!empty($ultrasonido['observaciones'])) {
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'OBSERVACIONES', 0, 1, 'L', true);
    $pdf->Ln(2);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 5, trim($ultrasonido['observaciones']), 0, 'J');
    $pdf->Ln(3);
}

// Conclusión
if (!empty($ultrasonido['conclusion'])) {
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'CONCLUSIÓN / IMPRESIÓN DIAGNÓSTICA', 0, 1, 'L', true);
    $pdf->Ln(2);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 5, trim($ultrasonido['conclusion']), 0, 'J');
    $pdf->Ln(5);
}

// Espacio para firma
$pdf->Ln(10);

// Línea de firma
$pdf->Line(120, $pdf->GetY(), 180, $pdf->GetY());
$pdf->Ln(2);

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 5, 'Dr. ' . ($ultrasonido['doctor_nombre'] ?? 'Byron Daniel Castillo Perea'), 0, 1, 'R');
$pdf->Cell(0, 5, 'Ginecología y Obstetricia', 0, 1, 'R');

// Nota al pie
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(128, 128, 128);
$pdf->MultiCell(0, 4, 'Este informe de ultrasonido obstétrico es un documento médico que debe ser interpretado por un profesional de la salud. Las medidas y conclusiones aquí presentadas corresponden a la evaluación realizada en la fecha indicada.', 0, 'J');

// Nombre del archivo
$nombre_archivo = 'Ultrasonido_' . $ultrasonido['paciente_codigo'] . '_' . 
                  date('Ymd', strtotime($ultrasonido['fecha'])) . '.pdf';

// Salida del PDF
$pdf->Output($nombre_archivo, 'I'); // 'I' = inline en navegador, 'D' = descarga
exit;