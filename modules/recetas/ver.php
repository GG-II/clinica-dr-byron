<?php
/**
 * VER RECETA MÉDICA
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Vista completa de una receta con botón para generar PDF
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
require_once MODELS_PATH . 'Receta.php';
require_once MODELS_PATH . 'Consulta.php';

// Proteger la ruta
proteger_ruta();

// Obtener ID de la receta
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    mensaje_error('ID de receta no válido.');
    header('Location: index.php');
    exit;
}

// Obtener datos completos de la receta
$receta = obtener_receta_completa($pdo, $id);

if (!$receta) {
    mensaje_error('La receta no existe.');
    header('Location: index.php');
    exit;
}

// Registrar auditoría
registrar_auditoria($pdo, $_SESSION['usuario_id'], 'VER', 'recetas', $id, 
    "Visualizó receta #{$receta['numero_receta']} del paciente {$receta['paciente_nombre']}");

// Calcular edad del paciente
$edad_paciente = calcular_edad($receta['paciente_fecha_nac']);

// Variables para la página
$page_title = 'Receta #' . str_pad($receta['numero_receta'], 4, '0', STR_PAD_LEFT);
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Recetas', 'url' => BASE_URL . 'modules/recetas/'],
    ['titulo' => 'Receta #' . $receta['numero_receta'], 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<!-- Header de la receta -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1">
                    <i class="bi bi-prescription2"></i> 
                    Receta #<?= str_pad($receta['numero_receta'], 4, '0', STR_PAD_LEFT) ?>
                </h2>
                <div class="d-flex gap-3 align-items-center text-muted">
                    <span>
                        <i class="bi bi-calendar-event"></i> 
                        <?= formatear_fecha($receta['fecha_emision']) ?>
                    </span>
                    <span>
                        <i class="bi bi-person"></i> 
                        <?= e($receta['paciente_nombre']) ?>
                    </span>
                    <span>
                        <i class="bi bi-person-badge"></i> 
                        Dr. <?= e($receta['medico_nombre'] ?? 'No especificado') ?>
                    </span>
                </div>
            </div>
            
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <a href="pdf.php?id=<?= $receta['id'] ?>" 
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
        <!-- Medicamentos recetados -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-capsule"></i> Medicamentos Recetados
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($receta['medicamentos'])): ?>
                    <div class="text-center py-4">
                        <i class="bi bi-exclamation-circle text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">No hay medicamentos registrados en esta receta</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="30%">Medicamento</th>
                                    <th>Dosis</th>
                                    <th>Vía</th>
                                    <th>Frecuencia</th>
                                    <th>Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($receta['medicamentos'] as $index => $med): ?>
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?= $index + 1 ?></span>
                                        </td>
                                        <td>
                                            <strong><?= e($med['medicamento']) ?></strong>
                                        </td>
                                        <td><?= e($med['dosis'] ?? '-') ?></td>
                                        <td><?= e($med['via'] ?? '-') ?></td>
                                        <td><?= e($med['frecuencia'] ?? '-') ?></td>
                                        <td><?= e($med['duracion'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Indicaciones generales -->
        <?php if (!empty($receta['indicaciones_generales'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-clipboard-check"></i> Indicaciones Generales
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-line;"><?= e($receta['indicaciones_generales']) ?></p>
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
                <!-- Avatar -->
                <div class="mb-3">
                    <div class="avatar-paciente mx-auto" style="width: 80px; height: 80px; background: #0dcaf0; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-fill text-white" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
                
                <!-- Nombre -->
                <h5 class="mb-1">
                    <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $receta['paciente_id'] ?>" 
                       class="text-decoration-none">
                        <?= e($receta['paciente_nombre']) ?>
                    </a>
                </h5>
                <p class="text-muted mb-3"><?= e($receta['paciente_codigo']) ?></p>
                
                <!-- Datos -->
                <div class="text-start">
                    <div class="mb-2">
                        <small class="text-muted d-block">Edad</small>
                        <strong><?= $edad_paciente ?> años</strong>
                    </div>
                    
                    <?php if (!empty($receta['paciente_telefono'])): ?>
                        <div class="mb-2">
                            <small class="text-muted d-block">Teléfono</small>
                            <strong><?= e($receta['paciente_telefono']) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Botones de acción -->
                <div class="d-grid gap-2 mt-3">
                    <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $receta['paciente_id'] ?>" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-folder2-open"></i> Ver Expediente Completo
                    </a>
                    
                    <a href="https://wa.me/502<?= preg_replace('/[^0-9]/', '', $receta['paciente_telefono']) ?>" 
                       class="btn btn-sm btn-success"
                       target="_blank">
                        <i class="bi bi-whatsapp"></i> Enviar WhatsApp
                    </a>
                </div>
            </div>
        </div>
        
        <!-- CONSULTA ASOCIADA -->
        <?php if (!empty($receta['consulta_id'])): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-clipboard2-pulse"></i> Consulta Asociada
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">Fecha de Consulta</small>
                        <strong><?= formatear_fecha_hora($receta['consulta_fecha']) ?></strong>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Tipo de Consulta</small>
                        <span class="badge bg-<?= obtener_color_tipo_consulta($receta['tipo_consulta']) ?>">
                            <?= obtener_nombre_tipo_consulta($receta['tipo_consulta']) ?>
                        </span>
                    </div>
                    
                    <div class="d-grid">
                        <a href="<?= BASE_URL ?>modules/consultas/ver.php?id=<?= $receta['consulta_id'] ?>" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> Ver Consulta
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include INCLUDES_PATH . 'footer.php'; ?>