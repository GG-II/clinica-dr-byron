<?php
/**
 * REPORTE DE INGRESOS
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Reporte detallado de ingresos por período con exportación a Excel
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
require_once MODELS_PATH . 'Consulta.php';

// Proteger la ruta
proteger_ruta();

// Obtener fechas del filtro
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01'); // Primer día del mes
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t'); // Último día del mes

// Validar fechas
if (!empty($fecha_inicio) && !empty($fecha_fin) && $fecha_inicio > $fecha_fin) {
    $temp = $fecha_inicio;
    $fecha_inicio = $fecha_fin;
    $fecha_fin = $temp;
}

// Obtener datos del reporte
$filtros = [
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin,
    'estado' => 'pagado' // Solo facturas pagadas
];

$facturas = obtener_facturas($pdo, $filtros, 1000, 0); // Límite alto para reportes
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

// Variables para la página
$page_title = 'Reporte de Ingresos';
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Reportes', 'url' => BASE_URL . 'modules/reportes/'],
    ['titulo' => 'Reporte de Ingresos', 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<div class="row mb-3">
    <div class="col-md-8">
        <h2><i class="bi bi-graph-up"></i> Reporte de Ingresos</h2>
    </div>
    <div class="col-md-4 text-end">
        <?php if (!empty($facturas)): ?>
            <a href="exportar_excel.php?fecha_inicio=<?= e($fecha_inicio) ?>&fecha_fin=<?= e($fecha_fin) ?>" 
               class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Exportar Excel
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="fecha_inicio" class="form-label">Desde</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                       value="<?= e($fecha_inicio) ?>" required>
            </div>
            
            <div class="col-md-4">
                <label for="fecha_fin" class="form-label">Hasta</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                       value="<?= e($fecha_fin) ?>" required>
            </div>
            
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Generar Reporte
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tarjetas de totales -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-currency-dollar fs-1 me-3"></i>
                    <div>
                        <small class="d-block opacity-75">Total del Período</small>
                        <h3 class="mb-0">Q <?= number_format($totales['total_ingresado'], 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-cash-stack fs-1 me-3"></i>
                    <div>
                        <small class="d-block opacity-75">Efectivo</small>
                        <h3 class="mb-0">Q <?= number_format($total_efectivo, 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-credit-card fs-1 me-3"></i>
                    <div>
                        <small class="d-block opacity-75">Tarjeta / Transferencia</small>
                        <h3 class="mb-0">Q <?= number_format($total_tarjeta_transferencia, 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detalle de movimientos -->
<div class="card">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list-ul"></i> Detalle de Movimientos
            </h5>
            <small class="text-muted">
                <?= formatear_fecha($fecha_inicio) ?> - <?= formatear_fecha($fecha_fin) ?>
            </small>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (empty($facturas)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                No hay facturas pagadas en el período seleccionado.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>No. Recibo</th>
                            <th>Paciente</th>
                            <th>Concepto</th>
                            <th>Forma Pago</th>
                            <th class="text-end">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($facturas as $factura): ?>
                        <tr>
                            <td><?= formatear_fecha($factura['fecha']) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>modules/facturacion/ver.php?id=<?= $factura['id'] ?>" 
                                   class="text-decoration-none">
                                    <strong>#<?= str_pad($factura['numero_correlativo'], 4, '0', STR_PAD_LEFT) ?></strong>
                                </a>
                            </td>
                            <td><?= e($factura['paciente_nombre']) ?></td>
                            <td>
                                <?= e($factura['concepto']) ?>
                                <?php if ($factura['tipo_consulta']): ?>
                                    <br>
                                    <small class="text-muted">
                                        <?= obtener_nombre_tipo_consulta($factura['tipo_consulta']) ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $colores = [
                                    'efectivo' => 'success',
                                    'tarjeta' => 'primary',
                                    'transferencia' => 'info'
                                ];
                                ?>
                                <span class="badge bg-<?= $colores[$factura['forma_pago']] ?? 'secondary' ?>">
                                    <?= strtoupper($factura['forma_pago']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <strong>Q <?= number_format($factura['monto'], 2) ?></strong>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
                            <td class="text-end">
                                <h5 class="mb-0 text-primary">
                                    Q <?= number_format($totales['total_ingresado'], 2) ?>
                                </h5>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include INCLUDES_PATH . 'footer.php'; ?>