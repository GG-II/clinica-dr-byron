<?php
/**
 * EXPORTAR REPORTE DE INGRESOS A EXCEL
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Genera archivo Excel (.xlsx) del reporte de ingresos
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

session_start();
define('ACCESS_GRANTED', true);
require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'middleware.php';
require_once MODELS_PATH . 'Factura.php';

// Proteger la ruta
proteger_ruta();

// Obtener fechas
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

// Obtener datos
$filtros = [
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin,
    'estado' => 'pagado'
];

$facturas = obtener_facturas($pdo, $filtros, 10000, 0);
$totales = obtener_ingresos_periodo($pdo, $fecha_inicio, $fecha_fin);

// Calcular totales por forma de pago
$total_efectivo = 0;
$total_tarjeta_transferencia = 0;

foreach ($facturas as $factura) {
    if ($factura['estado'] === 'pagado') {
        if ($factura['forma_pago'] === 'efectivo') {
            $total_efectivo += $factura['monto'];
        } else {
            $total_tarjeta_transferencia += $factura['monto'];
        }
    }
}

// Registrar auditoría
registrar_auditoria($pdo, $_SESSION['usuario_id'], 'EXPORTAR', 'reportes', 0, 
    "Exportó reporte de ingresos del " . formatear_fecha($fecha_inicio) . " al " . formatear_fecha($fecha_fin));

// Headers para descarga de Excel
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="Reporte_Ingresos_' . $fecha_inicio . '_a_' . $fecha_fin . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Establecer encoding UTF-8
echo "\xEF\xBB\xBF"; // BOM para UTF-8

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .header {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Reporte de Ingresos</h1>
    <h2>Clínica Médica de la Mujer</h2>
    <p><strong>Período:</strong> <?= formatear_fecha($fecha_inicio) ?> - <?= formatear_fecha($fecha_fin) ?></p>
    <p><strong>Generado:</strong> <?= date('d/m/Y H:i:s') ?></p>
    <p><strong>Usuario:</strong> <?= e($_SESSION['usuario_nombre']) ?></p>
    
    <br>
    
    <!-- Resumen -->
    <table>
        <thead class="header">
            <tr>
                <th colspan="2">RESUMEN DE INGRESOS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Total del Período</strong></td>
                <td class="text-right"><strong>Q <?= number_format($totales['total_ingresado'], 2) ?></strong></td>
            </tr>
            <tr>
                <td>Efectivo</td>
                <td class="text-right">Q <?= number_format($total_efectivo, 2) ?></td>
            </tr>
            <tr>
                <td>Tarjeta / Transferencia</td>
                <td class="text-right">Q <?= number_format($total_tarjeta_transferencia, 2) ?></td>
            </tr>
            <tr class="total">
                <td>Total de Facturas</td>
                <td class="text-right"><?= count($facturas) ?></td>
            </tr>
        </tbody>
    </table>
    
    <br><br>
    
    <!-- Detalle -->
    <table>
        <thead class="header">
            <tr>
                <th>Fecha</th>
                <th>No. Recibo</th>
                <th>Paciente</th>
                <th>Concepto</th>
                <th>Forma de Pago</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($facturas as $factura): ?>
            <tr>
                <td><?= formatear_fecha($factura['fecha']) ?></td>
                <td class="text-center">#<?= str_pad($factura['numero_correlativo'], 4, '0', STR_PAD_LEFT) ?></td>
                <td><?= e($factura['paciente_nombre']) ?></td>
                <td><?= e($factura['concepto']) ?></td>
                <td class="text-center"><?= strtoupper($factura['forma_pago']) ?></td>
                <td class="text-right">Q <?= number_format($factura['monto'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot class="total">
            <tr>
                <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>Q <?= number_format($totales['total_ingresado'], 2) ?></strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>