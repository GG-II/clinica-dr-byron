<?php
/**
 * VER EXPEDIENTE DEL PACIENTE
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Vista completa del expediente con tabs
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
require_once MODELS_PATH . 'Paciente.php';
require_once MODELS_PATH . 'Consulta.php';
require_once MODELS_PATH . 'Receta.php';
require_once MODELS_PATH . 'Ultrasonido.php';
require_once MODELS_PATH . 'Factura.php';

// Proteger la ruta
proteger_ruta();

// Obtener ID del paciente
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    mensaje_error('ID de paciente no válido.');
    header('Location: index.php');
    exit;
}

// Obtener datos del paciente
$paciente = obtener_paciente_por_id($pdo, $id);

if (!$paciente) {
    mensaje_error('El paciente no existe.');
    header('Location: index.php');
    exit;
}

// Registrar auditoría
registrar_auditoria($pdo, $_SESSION['usuario_id'], 'VER', 'pacientes', $id, 
    "Visualizó expediente de {$paciente['nombre']}");

// Obtener historial de consultas (últimas 10)
$historial = obtener_consultas_paciente($pdo, $id, 10);

// Verificar si la función existe
if (function_exists('obtener_recetas_paciente')) {
    $recetas = obtener_recetas_paciente($pdo, $id, 10);
} else {
    error_log("ERROR: Función obtener_recetas_paciente no existe");
    $recetas = [];
}

// Obtener ultrasonidos del paciente
// Temporal: verificar si existe la función
if (function_exists('obtener_ultrasonidos_paciente')) {
    $ultrasonidos = obtener_ultrasonidos_paciente($pdo, $id, 10);
} else {
    error_log("ERROR: Función obtener_ultrasonidos_paciente NO EXISTE en " . MODELS_PATH . 'Ultrasonido.php');
    $ultrasonidos = [];
}

// Obtener facturas del paciente
// Temporal: verificar si existe la función
if (function_exists('obtener_facturas_paciente')) {
    $facturas = obtener_facturas_paciente($pdo, $id, 10);
} else {
    error_log("ERROR: Función obtener_facturas_paciente NO EXISTE");
    $facturas = [];
}

// Calcular edad
$fecha_nac = new DateTime($paciente['fecha_nacimiento']);
$hoy = new DateTime();
$edad = $hoy->diff($fecha_nac)->y;

// Determinar estado
$estado_clase = $paciente['activo'] == 1 ? 'success' : 'secondary';
$estado_texto = $paciente['activo'] == 1 ? 'ACTIVA' : 'INACTIVA';

// Variables para la página
$page_title = 'Expediente - ' . $paciente['nombre'];
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Pacientes', 'url' => BASE_URL . 'modules/pacientes/'],
    ['titulo' => $paciente['nombre'], 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<!-- Header del Expediente -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <!-- Avatar y datos básicos -->
            <div class="col-auto">
                <div class="avatar-expediente">
                    <?php
                    $iniciales = '';
                    $palabras = explode(' ', $paciente['nombre']);
                    $iniciales = strtoupper(substr($palabras[0], 0, 1));
                    if (isset($palabras[1])) {
                        $iniciales .= strtoupper(substr($palabras[1], 0, 1));
                    }
                    echo $iniciales;
                    ?>
                </div>
            </div>
            
            <div class="col">
                <h2 class="mb-1"><?= e($paciente['nombre']) ?></h2>
                <div class="d-flex gap-3 align-items-center mb-2">
                    <span class="text-muted">
                        <i class="bi bi-person-badge"></i> 
                        <?= e($paciente['codigo']) ?>
                    </span>
                    <span class="text-muted">
                        <i class="bi bi-calendar-event"></i> 
                        <?= $edad ?> años
                    </span>
                    <span class="text-muted">
                        <i class="bi bi-phone"></i> 
                        <?= e($paciente['telefono']) ?>
                    </span>
                </div>
                
                <!-- Badges informativos -->
                <div class="d-flex gap-2">
                    <span class="badge bg-<?= $estado_clase ?>"><?= $estado_texto ?></span>
                    
                    <?php if ($paciente['tipo_sangre']): ?>
                        <span class="badge bg-info"><?= e($paciente['tipo_sangre']) ?></span>
                    <?php endif; ?>
                    
                    <?php if (!empty($paciente['alergias'])): ?>
                        <span class="badge bg-warning text-dark">
                            <i class="bi bi-exclamation-triangle-fill"></i> ALERGIAS
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <?php if (puede_crear()): ?>
                        <a href="<?= BASE_URL ?>modules/consultas/nueva.php?paciente_id=<?= $id ?>" 
                        class="btn btn-primary" title="Nueva Consulta">
                            <i class="bi bi-clipboard2-pulse"></i> Nueva Consulta
                        </a>
                        <a href="<?= BASE_URL ?>modules/citas/nueva.php?paciente_id=<?= $id ?>" 
                        class="btn btn-outline-primary" title="Nueva Cita">
                            <i class="bi bi-calendar-plus"></i> Cita
                        </a>
                        <a href="<?= BASE_URL ?>modules/facturacion/nuevo.php?paciente_id=<?= $id ?>" 
                        class="btn btn-outline-primary" title="Nuevo Recibo">
                            <i class="bi bi-receipt"></i> Recibo
                        </a>
                    <?php endif; ?>
                    
                    <?php if (puede_editar()): ?>
                        <a href="editar.php?id=<?= $id ?>" class="btn btn-outline-secondary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs del Expediente -->
<div class="card">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs" id="expedienteTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="consultas-tab" data-bs-toggle="tab" 
                        data-bs-target="#consultas" type="button" role="tab">
                    <i class="bi bi-clipboard2-pulse"></i> Consultas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="recetas-tab" data-bs-toggle="tab" 
                        data-bs-target="#recetas" type="button" role="tab">
                    <i class="bi bi-prescription2"></i> Recetas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ultrasonidos-tab" data-bs-toggle="tab" 
                        data-bs-target="#ultrasonidos" type="button" role="tab">
                    <i class="bi bi-hearts"></i> Ultrasonidos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="examenes-tab" data-bs-toggle="tab" 
                        data-bs-target="#examenes" type="button" role="tab">
                    <i class="bi bi-clipboard-data"></i> Exámenes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="facturacion-tab" data-bs-toggle="tab" 
                        data-bs-target="#facturacion" type="button" role="tab">
                    <i class="bi bi-receipt"></i> Facturación
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="datos-tab" data-bs-toggle="tab" 
                        data-bs-target="#datos" type="button" role="tab">
                    <i class="bi bi-person-lines-fill"></i> Datos Generales
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content" id="expedienteTabsContent">
            
            <!-- TAB 1: CONSULTAS -->
            <div class="tab-pane fade show active" id="consultas" role="tabpanel">
                <?php if (empty($historial)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-clipboard2-x text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">No hay consultas registradas</p>
                        <?php if (puede_crear()): ?>
                            <a href="<?= BASE_URL ?>modules/consultas/nueva.php?paciente_id=<?= $id ?>" 
                            class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Nueva Consulta
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- Lista de consultas estilo wireframe -->
                    <div class="consultas-list">
                        <?php 
                        // Meses en español
                        $meses_cortos = [
                            1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR',
                            5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO',
                            9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC'
                        ];
                        
                        foreach ($historial as $consulta): 
                            $mes_num = (int)date('n', strtotime($consulta['fecha']));
                            $mes_texto = $meses_cortos[$mes_num];
                        ?>
                            <div class="consulta-card mb-3 p-3 border rounded position-relative hover-shadow">
                                <!-- Fecha lateral -->
                                <div class="fecha-badge position-absolute top-0 start-0 mt-3 ms-3">
                                    <div class="text-center bg-light border rounded p-2" style="width: 60px;">
                                        <div class="text-uppercase text-primary small fw-bold">
                                            <?= $mes_texto ?>
                                        </div>
                                        <div class="h4 mb-0 text-dark">
                                            <?= date('d', strtotime($consulta['fecha'])) ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenido de la consulta -->
                                <div class="ms-5 ps-4">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="mb-1 text-primary">
                                                <?= obtener_nombre_tipo_consulta($consulta['tipo_consulta']) ?>
                                            </h5>
                                            <small class="text-muted">
                                                <?= formatear_fecha($consulta['fecha']) ?>
                                            </small>
                                        </div>
                                        <a href="<?= BASE_URL ?>modules/consultas/ver.php?id=<?= $consulta['id'] ?>" 
                                           class="btn btn-sm btn-link text-decoration-none">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                    
                                    <?php if (!empty($consulta['motivo_consulta'])): ?>
                                        <p class="mb-2 text-muted">
                                            <?= e(substr($consulta['motivo_consulta'], 0, 100)) ?><?= strlen($consulta['motivo_consulta']) > 100 ? '...' : '' ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="text-muted small">
                                        <i class="bi bi-person-badge"></i> 
                                        Atendido por: <strong><?= strtoupper(e($consulta['medico_nombre'])) ?></strong>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="<?= BASE_URL ?>modules/consultas/index.php?paciente_id=<?= $id ?>" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul"></i> Ver Todas las Consultas
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- TAB 2: RECETAS -->
            <div class="tab-pane fade" id="recetas" role="tabpanel">
                <?php if (empty($recetas)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-prescription2 text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">No hay recetas registradas</p>
                        <p class="small text-muted">Las recetas se generan desde las consultas</p>
                    </div>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach($recetas as $receta): ?>
                        <div class="timeline-item mb-3">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Receta #<?= str_pad($receta['numero_receta'], 4, '0', STR_PAD_LEFT) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 
                                                <?= formatear_fecha($receta['fecha_emision']) ?>
                                            </small>
                                        </div>
                                        <div>
                                            <a href="<?= BASE_URL ?>modules/recetas/ver.php?id=<?= $receta['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>modules/recetas/pdf.php?id=<?= $receta['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               target="_blank"
                                               title="Generar PDF">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($receta['medicamentos'])): ?>
                                        <h6 class="text-muted mb-2">Medicamentos:</h6>
                                        <ul class="mb-0">
                                            <?php 
                                            $meds = is_string($receta['medicamentos']) ? json_decode($receta['medicamentos'], true) : $receta['medicamentos'];
                                            $count = 0;
                                            foreach($meds as $med): 
                                                if ($count >= 3) break;
                                                $count++;
                                            ?>
                                                <li>
                                                    <strong><?= e($med['medicamento']) ?></strong>
                                                    <?php if (!empty($med['dosis'])): ?>
                                                        - <?= e($med['dosis']) ?>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                            <?php if (count($meds) > 3): ?>
                                                <li class="text-muted">
                                                    <small>+ <?= count($meds) - 3 ?> medicamento(s) más</small>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($receta['indicaciones_generales'])): ?>
                                        <hr>
                                        <small class="text-muted">
                                            <strong>Indicaciones:</strong> 
                                            <?= e(substr($receta['indicaciones_generales'], 0, 100)) ?>
                                            <?= strlen($receta['indicaciones_generales']) > 100 ? '...' : '' ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="<?= BASE_URL ?>modules/recetas/?paciente_id=<?= $id ?>" 
                           class="btn btn-outline-primary">
                            Ver Todas las Recetas
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- TAB 3: ULTRASONIDOS -->
            <div class="tab-pane fade" id="ultrasonidos" role="tabpanel">
                <?php if (empty($ultrasonidos)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-hearts text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">No hay ultrasonidos registrados</p>
                        <p class="small text-muted">Los ultrasonidos se registran desde las consultas</p>
                    </div>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach($ultrasonidos as $ultra): ?>
                        <div class="timeline-item mb-3">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Ultrasonido Obstétrico</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 
                                                <?= formatear_fecha($ultra['fecha']) ?>
                                            </small>
                                        </div>
                                        <div>
                                            <a href="<?= BASE_URL ?>modules/ultrasonido/ver.php?id=<?= $ultra['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>modules/ultrasonido/pdf.php?id=<?= $ultra['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               target="_blank"
                                               title="Generar PDF">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($ultra['edad_gestacional'])): ?>
                                        <p class="mb-2">
                                            <strong>Edad Gestacional:</strong> 
                                            <span class="badge bg-info"><?= e($ultra['edad_gestacional']) ?></span>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if ($ultra['dbp'] || $ultra['cc'] || $ultra['ca'] || $ultra['lf']): ?>
                                        <h6 class="text-muted mb-2">Biometría Fetal:</h6>
                                        <div class="row">
                                            <?php if ($ultra['dbp']): ?>
                                                <div class="col-6 mb-2">
                                                    <small class="text-muted">DBP:</small>
                                                    <strong><?= number_format($ultra['dbp'], 2) ?> mm</strong>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($ultra['cc']): ?>
                                                <div class="col-6 mb-2">
                                                    <small class="text-muted">CC:</small>
                                                    <strong><?= number_format($ultra['cc'], 2) ?> mm</strong>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($ultra['ca']): ?>
                                                <div class="col-6 mb-2">
                                                    <small class="text-muted">CA:</small>
                                                    <strong><?= number_format($ultra['ca'], 2) ?> mm</strong>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($ultra['lf']): ?>
                                                <div class="col-6 mb-2">
                                                    <small class="text-muted">LF:</small>
                                                    <strong><?= number_format($ultra['lf'], 2) ?> mm</strong>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($ultra['peso_estimado']): ?>
                                        <p class="mb-0 mt-2">
                                            <strong>Peso Estimado:</strong> 
                                            <?= number_format($ultra['peso_estimado'], 0) ?> g
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="<?= BASE_URL ?>modules/ultrasonido/?paciente_id=<?= $id ?>" 
                           class="btn btn-outline-primary">
                            Ver Todos los Ultrasonidos
                        </a>
                    </div>
                <?php endif; ?>
            </div> 
            
            <!-- TAB 4: EXÁMENES -->
            <div class="tab-pane fade" id="examenes" role="tabpanel">
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-data text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No hay exámenes de laboratorio registrados</p>
                </div>
            </div>
            
            <!-- TAB 5: FACTURACIÓN -->
            <div class="tab-pane fade" id="facturacion" role="tabpanel">
                <?php if (empty($facturas)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">No hay facturas registradas</p>
                    </div>
                <?php else: ?>
                    <div class="timeline">
                        <?php 
                        $total_cobrado = 0;
                        foreach($facturas as $factura): 
                            if ($factura['estado'] === 'pagado') {
                                $total_cobrado += $factura['monto'];
                            }
                        ?>
                        <div class="timeline-item mb-3">
                            <div class="card <?= $factura['estado'] === 'anulado' ? 'border-danger' : '' ?>">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Recibo #<?= str_pad($factura['numero_correlativo'], 4, '0', STR_PAD_LEFT) ?></strong>
                                            <?php
                                            $badges = [
                                                'pagado' => 'success',
                                                'pendiente' => 'warning',
                                                'anulado' => 'danger'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $badges[$factura['estado']] ?? 'secondary' ?>">
                                                <?= ucfirst($factura['estado']) ?>
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 
                                                <?= formatear_fecha($factura['fecha']) ?>
                                            </small>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 text-primary">
                                                Q <?= number_format($factura['monto'], 2) ?>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2">
                                        <strong>Concepto:</strong> <?= e($factura['concepto']) ?>
                                    </p>
                                    
                                    <?php if ($factura['tipo_consulta']): ?>
                                        <p class="mb-2">
                                            <span class="badge bg-<?= obtener_color_tipo_consulta($factura['tipo_consulta']) ?>">
                                                <?= obtener_nombre_tipo_consulta($factura['tipo_consulta']) ?>
                                            </span>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Forma de Pago:</small>
                                            <br>
                                            <?php
                                            $iconos = [
                                                'efectivo' => 'bi-cash',
                                                'tarjeta' => 'bi-credit-card',
                                                'transferencia' => 'bi-arrow-left-right'
                                            ];
                                            ?>
                                            <i class="bi <?= $iconos[$factura['forma_pago']] ?? 'bi-cash' ?>"></i>
                                            <?= ucfirst($factura['forma_pago']) ?>
                                        </div>
                                        <div class="col-6 text-end">
                                            <a href="<?= BASE_URL ?>modules/facturacion/ver.php?id=<?= $factura['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($factura['estado'] !== 'anulado'): ?>
                                                <a href="<?= BASE_URL ?>modules/facturacion/pdf.php?id=<?= $factura['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   target="_blank"
                                                   title="Generar PDF">
                                                    <i class="bi bi-file-pdf"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Total de Facturas</small>
                                    <h5><?= count($facturas) ?></h5>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted">Total Cobrado</small>
                                    <h5 class="text-success">Q <?= number_format($total_cobrado, 2) ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <a href="<?= BASE_URL ?>modules/facturacion/?paciente_id=<?= $id ?>" 
                           class="btn btn-outline-primary">
                            Ver Todo el Historial de Facturación
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- TAB 6: DATOS GENERALES -->
            <div class="tab-pane fade" id="datos" role="tabpanel">
                <div class="row">
                    <!-- Datos Personales -->
                    <div class="col-md-6 mb-4">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-person-fill text-primary"></i> Datos Personales
                        </h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Nombre:</dt>
                            <dd class="col-sm-8"><?= e($paciente['nombre']) ?></dd>
                            
                            <dt class="col-sm-4">Código:</dt>
                            <dd class="col-sm-8"><?= e($paciente['codigo']) ?></dd>
                            
                            <dt class="col-sm-4">Fecha Nacimiento:</dt>
                            <dd class="col-sm-8">
                                <?= date('d/m/Y', strtotime($paciente['fecha_nacimiento'])) ?>
                                (<?= $edad ?> años)
                            </dd>
                            
                            <dt class="col-sm-4">Sexo:</dt>
                            <dd class="col-sm-8"><?= $paciente['sexo'] === 'F' ? 'Femenino' : 'Masculino' ?></dd>
                            
                            <?php if ($paciente['dpi']): ?>
                                <dt class="col-sm-4">DPI:</dt>
                                <dd class="col-sm-8"><?= e($paciente['dpi']) ?></dd>
                            <?php endif; ?>
                            
                            <?php if ($paciente['tipo_sangre']): ?>
                                <dt class="col-sm-4">Tipo de Sangre:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-info"><?= e($paciente['tipo_sangre']) ?></span>
                                </dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                    
                    <!-- Contacto -->
                    <div class="col-md-6 mb-4">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-telephone-fill text-primary"></i> Contacto
                        </h5>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Teléfono:</dt>
                            <dd class="col-sm-8">
                                <a href="tel:<?= e($paciente['telefono']) ?>">
                                    <?= e($paciente['telefono']) ?>
                                </a>
                            </dd>
                            
                            <?php if ($paciente['email']): ?>
                                <dt class="col-sm-4">Email:</dt>
                                <dd class="col-sm-8">
                                    <a href="mailto:<?= e($paciente['email']) ?>">
                                        <?= e($paciente['email']) ?>
                                    </a>
                                </dd>
                            <?php endif; ?>
                            
                            <dt class="col-sm-4">Dirección:</dt>
                            <dd class="col-sm-8"><?= e($paciente['direccion']) ?></dd>
                            
                            <?php if ($paciente['contacto_emergencia']): ?>
                                <dt class="col-sm-4">Contacto Emergencia:</dt>
                                <dd class="col-sm-8">
                                    <span class="text-danger">
                                        <i class="bi bi-heart-pulse-fill"></i> 
                                        <?= e($paciente['contacto_emergencia']) ?>
                                    </span>
                                </dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                    
                    <!-- Antecedentes Médicos -->
<!-- Antecedentes Médicos -->
<?php if ($paciente['alergias'] || $paciente['antecedentes_personales'] || 
        $paciente['antecedentes_familiares'] || $paciente['antecedentes_quirurgicos']): ?>
    <div class="col-12">
        <h5 class="border-bottom pb-2 mb-4">
            <i class="bi bi-clipboard2-pulse text-danger"></i> Antecedentes Médicos
        </h5>
        
        <!-- ALERGIAS (Destacado con icono) -->
        <?php if ($paciente['alergias']): ?>
            <div class="card border-warning mb-4 shadow-sm">
                <div class="card-body bg-warning bg-opacity-10 p-3">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center" 
                                style="width: 50px; height: 50px;">
                                <i class="bi bi-exclamation-triangle-fill text-white fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="text-warning fw-bold mb-0">
    <i class="bi bi-exclamation-triangle-fill"></i> ALERGIAS IMPORTANTES
</h5>
                            <p class="mb-0 fs-6 text-dark" style="white-space: pre-line; line-height: 1.5;"><?= e(trim($paciente['alergias'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Resto de Antecedentes en Cards -->
        <div class="row g-3">
            <!-- Antecedentes Personales -->
            <?php if ($paciente['antecedentes_personales']): ?>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-primary bg-opacity-10 border-0 px-3 py-2">
                            <h6 class="mb-0 text-primary fw-semibold">
                                <i class="bi bi-person-fill"></i> Antecedentes Personales
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <p class="mb-0" style="white-space: pre-line; line-height: 1.5;"><?= e(trim($paciente['antecedentes_personales'])) ?></p> 
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Antecedentes Familiares -->
            <?php if ($paciente['antecedentes_familiares']): ?>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-info bg-opacity-10 border-0 px-3 py-2">
                            <h6 class="mb-0 text-info fw-semibold">
                                <i class="bi bi-people-fill"></i> Antecedentes Familiares
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <p class="mb-0" style="white-space: pre-line; line-height: 1.5;"><?= e(trim($paciente['antecedentes_familiares'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Antecedentes Quirúrgicos -->
            <?php if ($paciente['antecedentes_quirurgicos']): ?>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-danger bg-opacity-10 border-0 px-3 py-2">
                            <h6 class="mb-0 text-danger fw-semibold">
                                <i class="bi bi-scissors"></i> Antecedentes Quirúrgicos
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <p class="mb-0" style="white-space: pre-line; line-height: 1.5;"><?= e(trim($paciente['antecedentes_quirurgicos'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php
// CSS adicional para timeline y avatar
$additional_css = <<<'CSS'
<style>
/* Avatar del expediente */
.avatar-expediente {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #0077B6, #00B4D8);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(0, 119, 182, 0.3);
}

/* Timeline de consultas */
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    padding-left: 60px;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: 13px;
    top: 8px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #0077B6;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #0077B6;
}

.timeline-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 3px solid #0077B6;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

/* Tabs personalizados */
.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 0.75rem 1.25rem;
}

.nav-tabs .nav-link:hover {
    color: #0077B6;
    border-bottom-color: #00B4D8;
}

.nav-tabs .nav-link.active {
    color: #0077B6;
    border-bottom-color: #0077B6;
    background: none;
    font-weight: 600;
}
</style>

<style>
.consultas-list {
    max-height: none;
}

.consulta-card {
    background: #fff;
    transition: all 0.2s ease;
    min-height: 100px;
}

.consulta-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.hover-shadow {
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}

.fecha-badge {
    z-index: 1;
}
</style>

CSS;

// Incluir footer
include INCLUDES_PATH . 'footer.php';
?>