<?php
/**
 * LABORATORIOS - Ver Examen
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Vista detallada de un examen de laboratorio
 */

define('ACCESS_GRANTED', true);
session_start();

require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once MODELS_PATH . 'Examen.php';

// Verificar autenticación
verificar_sesion();

// Obtener ID del examen
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['mensaje_error'] = 'ID de examen inválido';
    header('Location: ' . BASE_URL . 'modules/laboratorios/index.php');
    exit;
}

// Obtener datos del examen
$examen = obtener_examen_completo($pdo, $id);

if (!$examen) {
    $_SESSION['mensaje_error'] = 'Examen no encontrado';
    header('Location: ' . BASE_URL . 'modules/laboratorios/index.php');
    exit;
}

// Variables para el header
$titulo = 'Detalle de Examen';
$breadcrumb = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard.php'],
    ['titulo' => 'Laboratorios', 'url' => BASE_URL . 'modules/laboratorios/index.php'],
    ['titulo' => 'Ver', 'url' => '']
];

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Contenido principal -->
<div class="container-fluid mt-4">
    
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="<?= BASE_URL ?>modules/laboratorios/index.php" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <h2><?= $titulo ?></h2>
            <p class="text-muted">Laboratorios / Ver</p>
        </div>
        <div class="col-md-6 text-end">
            <?php if ($examen['estado'] === 'solicitado' && !es_asistente()): ?>
                <a href="<?= BASE_URL ?>modules/laboratorios/editar.php?id=<?= $id ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Agregar Resultado
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Mensajes flash -->
    <?php mostrar_mensajes(); ?>
    
    <div class="row">
        
        <!-- Columna principal -->
        <div class="col-lg-8">
            
            <!-- Información del Examen -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clipboard2-pulse"></i>
                        Información del Examen
                    </h5>
                </div>
                <div class="card-body">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">TIPO DE EXAMEN</label>
                            <p class="mb-0 fw-bold fs-5">
                                <?= e($examen['tipo_examen']) ?>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">FECHA SOLICITUD</label>
                            <p class="mb-0">
                                <?= formatear_fecha($examen['fecha_solicitud']) ?>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">ESTADO</label>
                            <p class="mb-0">
                                <?php if ($examen['estado'] === 'solicitado'): ?>
                                    <span class="badge bg-warning text-dark">SOLICITADO</span>
                                <?php else: ?>
                                    <span class="badge bg-success">RECIBIDO</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if (!empty($examen['descripcion'])): ?>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="text-muted small">DESCRIPCIÓN / INSTRUCCIONES</label>
                                <p class="mb-0" style="white-space: pre-line;">
                                    <?= e(trim($examen['descripcion'])) ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($examen['estado'] === 'recibido'): ?>
                        <hr>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="text-muted small">FECHA RESULTADO</label>
                                <p class="mb-0">
                                    <?= formatear_fecha($examen['fecha_resultado']) ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <label class="text-muted small">RESULTADO</label>
                                <div class="border rounded p-3 bg-light">
                                    <p class="mb-0" style="white-space: pre-line;">
                                        <?= e(trim($examen['resultado'])) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
            
            <!-- Consulta Asociada (si existe) -->
            <?php if (!empty($examen['consulta_id'])): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-clipboard2-check"></i>
                            Consulta Asociada
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="text-muted small">FECHA CONSULTA</label>
                                <p class="mb-0">
                                    <?= formatear_fecha($examen['consulta_fecha']) ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">TIPO</label>
                                <p class="mb-0">
                                    <?= obtener_nombre_tipo_consulta($examen['tipo_consulta']) ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="<?= BASE_URL ?>modules/consultas/ver.php?id=<?= $examen['consulta_id'] ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Ver Consulta
                                </a>
                            </div>
                        </div>
                        
                        <?php if (!empty($examen['motivo_consulta'])): ?>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <label class="text-muted small">MOTIVO</label>
                                    <p class="mb-0 text-muted">
                                        <?= e($examen['motivo_consulta']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            
            <!-- Datos del Paciente -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-person-circle"></i>
                        Datos del Paciente
                    </h6>
                </div>
                <div class="card-body">
                    
                    <div class="text-center mb-3">
                        <div class="avatar-circle bg-primary text-white mx-auto mb-2" 
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            <?= strtoupper(substr($examen['paciente_nombre'], 0, 2)) ?>
                        </div>
                        <h5 class="mb-1"><?= e($examen['paciente_nombre']) ?></h5>
                        <p class="text-muted mb-0"><?= e($examen['paciente_codigo']) ?></p>
                    </div>
                    
                    <hr>
                    
                    <?php if ($examen['paciente_fecha_nac']): ?>
                        <div class="mb-2">
                            <small class="text-muted">EDAD</small>
                            <p class="mb-0">
                                <?= calcular_edad($examen['paciente_fecha_nac']) ?> años
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($examen['paciente_telefono']): ?>
                        <div class="mb-2">
                            <small class="text-muted">TELÉFONO</small>
                            <p class="mb-0">
                                <a href="tel:<?= e($examen['paciente_telefono']) ?>">
                                    <?= e($examen['paciente_telefono']) ?>
                                </a>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $examen['paciente_id'] ?>" 
                           class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-eye"></i> Ver Expediente Completo
                        </a>
                    </div>
                    
                </div>
            </div>
            
            <!-- Acciones Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning"></i>
                        Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    
                    <?php if ($examen['estado'] === 'solicitado' && !es_asistente()): ?>
                        <a href="<?= BASE_URL ?>modules/laboratorios/editar.php?id=<?= $id ?>" 
                           class="btn btn-warning w-100 mb-2">
                            <i class="bi bi-pencil"></i> Agregar Resultado
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= BASE_URL ?>modules/laboratorios/nuevo.php" 
                       class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-plus-circle"></i> Nuevo Examen
                    </a>
                    
                    <a href="<?= BASE_URL ?>modules/laboratorios/index.php" 
                       class="btn btn-outline-secondary w-100">
                        <i class="bi bi-list-ul"></i> Ver Todos los Exámenes
                    </a>
                    
                </div>
            </div>
            
        </div>
        
    </div>
    
</div>

<style>
.avatar-circle {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
</style>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>