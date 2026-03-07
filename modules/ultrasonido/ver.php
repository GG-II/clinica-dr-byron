<?php
/**
 * VER INFORME DE ULTRASONIDO
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Vista completa de un informe de ultrasonido
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
require_once MODELS_PATH . 'Ultrasonido.php';
require_once MODELS_PATH . 'Consulta.php';

// Proteger la ruta
proteger_ruta();

// Obtener ID del ultrasonido
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    mensaje_error('ID de ultrasonido no válido.');
    header('Location: index.php');
    exit;
}

// Obtener datos completos
$ultrasonido = obtener_ultrasonido_completo($pdo, $id);

if (!$ultrasonido) {
    mensaje_error('El informe de ultrasonido no existe.');
    header('Location: index.php');
    exit;
}

// Registrar auditoría
registrar_auditoria($pdo, $_SESSION['usuario_id'], 'VER', 'ultrasonidos', $id, 
    "Visualizó informe de ultrasonido del paciente {$ultrasonido['paciente_nombre']}");

// Calcular edad del paciente
$edad_paciente = calcular_edad($ultrasonido['paciente_fecha_nac']);

// Variables para la página
$page_title = 'Informe de Ultrasonido - ' . $ultrasonido['paciente_nombre'];
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Ultrasonidos', 'url' => BASE_URL . 'modules/ultrasonido/'],
    ['titulo' => 'Informe', 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<!-- Header del informe -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1">
                    <i class="bi bi-heart-pulse"></i> 
                    Informe de Ultrasonido
                </h2>
                <div class="d-flex gap-3 align-items-center text-muted">
                    <span>
                        <i class="bi bi-calendar-event"></i> 
                        <?= formatear_fecha($ultrasonido['fecha']) ?>
                    </span>
                    <span>
                        <i class="bi bi-person"></i> 
                        <?= e($ultrasonido['paciente_nombre']) ?>
                    </span>
                    <?php if (!empty($ultrasonido['edad_gestacional'])): ?>
                        <span class="badge bg-info">
                            <?= e($ultrasonido['edad_gestacional']) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <?php if (puede_editar()): ?>
                        <a href="editar.php?id=<?= $ultrasonido['id'] ?>" 
                           class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                    <?php endif; ?>
                    <a href="pdf.php?id=<?= $ultrasonido['id'] ?>" 
                       class="btn btn-success" 
                       target="_blank">
                        <i class="bi bi-file-pdf"></i> Generar PDF
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Contenido principal -->
    <div class="col-lg-8">
        <!-- Encabezado del estudio -->
        <div class="card mb-4">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-heart-pulse text-primary" style="font-size: 3rem;"></i>
                </div>
                <h4 class="text-uppercase text-primary mb-1">Estudio de Ultrasonido Obstétrico</h4>
                <p class="text-muted mb-0">Fecha: <?= formatear_fecha($ultrasonido['fecha']) ?></p>
            </div>
        </div>
        
        <!-- Datos del examen -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 text-uppercase text-muted">Datos del Examen</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Fecha</small>
                        <strong><?= formatear_fecha($ultrasonido['fecha']) ?></strong>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Edad Gestacional (semanas)</small>
                        <?php if (!empty($ultrasonido['edad_gestacional'])): ?>
                            <strong class="text-primary"><?= e($ultrasonido['edad_gestacional']) ?></strong>
                        <?php else: ?>
                            <span class="text-muted">No especificada</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Biometría fetal -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0 text-uppercase text-muted">Biometría Fetal (mm)</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="p-3 border rounded">
                            <small class="text-muted d-block mb-1">DBP</small>
                            <h4 class="text-primary mb-0">
                                <?= !empty($ultrasonido['dbp']) ? number_format($ultrasonido['dbp'], 1) : '-' ?>
                            </h4>
                        </div>
                    </div>
                    
                    <div class="col-3">
                        <div class="p-3 border rounded">
                            <small class="text-muted d-block mb-1">CC</small>
                            <h4 class="text-primary mb-0">
                                <?= !empty($ultrasonido['cc']) ? number_format($ultrasonido['cc'], 1) : '-' ?>
                            </h4>
                        </div>
                    </div>
                    
                    <div class="col-3">
                        <div class="p-3 border rounded">
                            <small class="text-muted d-block mb-1">CA</small>
                            <h4 class="text-primary mb-0">
                                <?= !empty($ultrasonido['ca']) ? number_format($ultrasonido['ca'], 1) : '-' ?>
                            </h4>
                        </div>
                    </div>
                    
                    <div class="col-3">
                        <div class="p-3 border rounded">
                            <small class="text-muted d-block mb-1">LF</small>
                            <h4 class="text-primary mb-0">
                                <?= !empty($ultrasonido['lf']) ? number_format($ultrasonido['lf'], 1) : '-' ?>
                            </h4>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($ultrasonido['peso_estimado'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="p-3 border rounded bg-light text-center">
                                <small class="text-muted d-block mb-1">Peso Estimado (g)</small>
                                <h3 class="text-success mb-0">
                                    <?= number_format($ultrasonido['peso_estimado'], 0) ?> g
                                </h3>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Observaciones detalladas -->
        <?php if (!empty($ultrasonido['observaciones'])): ?>
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-uppercase text-muted">Observaciones Detalladas</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-line;"><?= e($ultrasonido['observaciones']) ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Conclusión -->
        <?php if (!empty($ultrasonido['conclusion'])): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0 text-uppercase">Conclusión / Impresión Diagnóstica</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-line;"><?= e($ultrasonido['conclusion']) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar derecho -->
    <div class="col-lg-4">
        <!-- Info del paciente -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-person-circle"></i> Paciente
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="avatar-paciente mx-auto" style="width: 80px; height: 80px; background: #0dcaf0; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-fill text-white" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
                
                <h5 class="mb-1">
                    <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $ultrasonido['paciente_id'] ?>" 
                       class="text-decoration-none">
                        <?= e($ultrasonido['paciente_nombre']) ?>
                    </a>
                </h5>
                <p class="text-muted mb-3"><?= e($ultrasonido['paciente_codigo']) ?></p>
                
                <div class="text-start">
                    <div class="mb-2">
                        <small class="text-muted d-block">Edad</small>
                        <strong><?= $edad_paciente ?> años</strong>
                    </div>
                    
                    <?php if (!empty($ultrasonido['paciente_telefono'])): ?>
                        <div class="mb-2">
                            <small class="text-muted d-block">Teléfono</small>
                            <strong><?= e($ultrasonido['paciente_telefono']) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="d-grid gap-2 mt-3">
                    <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $ultrasonido['paciente_id'] ?>" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-folder2-open"></i> Ver Expediente Completo
                    </a>
                    
                    <a href="https://wa.me/502<?= preg_replace('/[^0-9]/', '', $ultrasonido['paciente_telefono']) ?>" 
                       class="btn btn-sm btn-success"
                       target="_blank">
                        <i class="bi bi-whatsapp"></i> Enviar WhatsApp
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Consulta asociada -->
        <?php if (!empty($ultrasonido['consulta_id']) && !empty($ultrasonido['tipo_consulta'])): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-clipboard2-pulse"></i> Consulta Asociada
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">Fecha de Consulta</small>
                        <strong><?= formatear_fecha_hora($ultrasonido['consulta_fecha']) ?></strong>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Tipo de Consulta</small>
                        <span class="badge bg-<?= obtener_color_tipo_consulta($ultrasonido['tipo_consulta']) ?>">
                            <?= obtener_nombre_tipo_consulta($ultrasonido['tipo_consulta']) ?>
                        </span>
                    </div>
                    
                    <div class="d-grid">
                        <a href="<?= BASE_URL ?>modules/consultas/ver.php?id=<?= $ultrasonido['consulta_id'] ?>" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> Ver Consulta
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Botón PDF principal -->
        <div class="d-grid gap-2">
            <a href="pdf.php?id=<?= $ultrasonido['id'] ?>" 
               class="btn btn-success btn-lg" 
               target="_blank">
                <i class="bi bi-file-pdf"></i> Descargar PDF
            </a>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . 'footer.php'; ?>