<?php
/**
 * EDITAR INFORME DE ULTRASONIDO
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para editar informe de ultrasonido existente
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

if (!puede_editar()) {
    mensaje_error('No tiene permisos para editar informes de ultrasonido.');
    header('Location: ' . BASE_URL . 'modules/ultrasonido/');
    exit;
}

// Obtener ID del ultrasonido
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    mensaje_error('ID de ultrasonido no válido.');
    header('Location: index.php');
    exit;
}

// Obtener datos del ultrasonido
$ultrasonido = obtener_ultrasonido_completo($pdo, $id);

if (!$ultrasonido) {
    mensaje_error('El informe de ultrasonido no existe.');
    header('Location: index.php');
    exit;
}

// Obtener datos de la consulta
$consulta = obtener_consulta_completa($pdo, $ultrasonido['consulta_id']);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Preparar datos
        $datos = [
            'fecha' => !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d'),
            'edad_gestacional' => !empty($_POST['edad_gestacional']) ? trim($_POST['edad_gestacional']) : null,
            'dbp' => !empty($_POST['dbp']) ? (float)$_POST['dbp'] : null,
            'cc' => !empty($_POST['cc']) ? (float)$_POST['cc'] : null,
            'ca' => !empty($_POST['ca']) ? (float)$_POST['ca'] : null,
            'lf' => !empty($_POST['lf']) ? (float)$_POST['lf'] : null,
            'peso_estimado' => !empty($_POST['peso_estimado']) ? (float)$_POST['peso_estimado'] : null,
            'observaciones' => !empty($_POST['observaciones']) ? trim($_POST['observaciones']) : null,
            'conclusion' => !empty($_POST['conclusion']) ? trim($_POST['conclusion']) : null
        ];
        
        // Actualizar ultrasonido
        $resultado = actualizar_ultrasonido($pdo, $id, $datos);
        
        if (!$resultado) {
            throw new Exception('Error al actualizar el informe de ultrasonido. Intente nuevamente.');
        }
        
        // Registrar auditoría
        registrar_auditoria($pdo, $_SESSION['usuario_id'], 'EDITAR', 'ultrasonidos', $id, 
            "Editó informe de ultrasonido del paciente {$ultrasonido['paciente_nombre']}");
        
        mensaje_exito('Informe de ultrasonido actualizado exitosamente.');
        header('Location: ver.php?id=' . $id);
        exit;
        
    } catch (Exception $e) {
        mensaje_error($e->getMessage());
    }
}

// Variables para la página
$page_title = 'Editar Informe de Ultrasonido - ' . $ultrasonido['paciente_nombre'];
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Ultrasonidos', 'url' => BASE_URL . 'modules/ultrasonido/'],
    ['titulo' => 'Editar Informe', 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<div class="row">
    <!-- Formulario principal -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-pencil"></i> Editar Estudio de Ultrasonido Obstétrico
                </h5>
            </div>
            
            <div class="card-body">
                <form method="POST" id="formUltrasonido">
                    <div class="alert alert-info mb-4">
                        <strong>Paciente:</strong> <?= e($ultrasonido['paciente_nombre']) ?> 
                        (<?= e($ultrasonido['paciente_codigo']) ?>)
                    </div>
                    
                    <!-- DATOS DEL EXAMEN -->
                    <h6 class="text-muted text-uppercase mb-3">Datos del Examen</h6>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" 
                                   value="<?= e($ultrasonido['fecha']) ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="edad_gestacional" class="form-label">Edad Gestacional (semanas)</label>
                            <input type="text" class="form-control" id="edad_gestacional" name="edad_gestacional" 
                                   value="<?= e($ultrasonido['edad_gestacional'] ?? '') ?>"
                                   placeholder="Ej: 24.2, 20 semanas 3 días">
                            <small class="text-muted">Formato libre</small>
                        </div>
                    </div>
                    
                    <!-- BIOMETRÍA FETAL -->
                    <h6 class="text-muted text-uppercase mb-3">Biometría Fetal (mm)</h6>
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="dbp" class="form-label">DBP</label>
                            <input type="number" step="0.01" class="form-control" id="dbp" name="dbp" 
                                   value="<?= e($ultrasonido['dbp'] ?? '') ?>"
                                   placeholder="mm">
                            <small class="text-muted">Diámetro biparietal</small>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="cc" class="form-label">CC</label>
                            <input type="number" step="0.01" class="form-control" id="cc" name="cc" 
                                   value="<?= e($ultrasonido['cc'] ?? '') ?>"
                                   placeholder="mm">
                            <small class="text-muted">Circunferencia cefálica</small>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="ca" class="form-label">CA</label>
                            <input type="number" step="0.01" class="form-control" id="ca" name="ca" 
                                   value="<?= e($ultrasonido['ca'] ?? '') ?>"
                                   placeholder="mm">
                            <small class="text-muted">Circunferencia abdominal</small>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="lf" class="form-label">LF</label>
                            <input type="number" step="0.01" class="form-control" id="lf" name="lf" 
                                   value="<?= e($ultrasonido['lf'] ?? '') ?>"
                                   placeholder="mm">
                            <small class="text-muted">Longitud del fémur</small>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="peso_estimado" class="form-label">Peso Estimado (g)</label>
                            <input type="number" step="1" class="form-control" id="peso_estimado" name="peso_estimado" 
                                   value="<?= e($ultrasonido['peso_estimado'] ?? '') ?>"
                                   placeholder="gramos">
                        </div>
                    </div>
                    
                    <!-- OBSERVACIONES DETALLADAS -->
                    <h6 class="text-muted text-uppercase mb-3">Observaciones Detalladas</h6>
                    
                    <div class="mb-4">
                        <textarea class="form-control" id="observaciones" name="observaciones" 
                                  rows="6" 
                                  placeholder="Situación, presentación, posición, placenta, líquido amniótico..."><?= e($ultrasonido['observaciones'] ?? '') ?></textarea>
                        <small class="text-muted">Descripción detallada del estudio</small>
                    </div>
                    
                    <!-- CONCLUSIÓN -->
                    <h6 class="text-muted text-uppercase mb-3">Conclusión / Impresión Diagnóstica</h6>
                    
                    <div class="mb-4">
                        <textarea class="form-control" id="conclusion" name="conclusion" 
                                  rows="4" 
                                  placeholder="Ej: Embarazo normoevolutivo de X semanas..."><?= e($ultrasonido['conclusion'] ?? '') ?></textarea>
                    </div>
                    
                    <!-- Botones -->
                    <div class="d-flex justify-content-between">
                        <a href="ver.php?id=<?= $id ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar: Info del paciente y consulta -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-person-circle"></i> Información del Paciente</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Nombre</small>
                    <strong><?= e($ultrasonido['paciente_nombre']) ?></strong>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Código</small>
                    <strong><?= e($ultrasonido['paciente_codigo']) ?></strong>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Edad</small>
                    <strong><?= calcular_edad($ultrasonido['paciente_fecha_nac']) ?> años</strong>
                </div>
            </div>
        </div>
        
        <?php if ($consulta): ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-clipboard2-pulse"></i> Consulta Asociada</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">Fecha</small>
                        <strong><?= formatear_fecha($consulta['fecha']) ?></strong>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted d-block">Tipo</small>
                        <span class="badge bg-<?= obtener_color_tipo_consulta($consulta['tipo_consulta']) ?>">
                            <?= obtener_nombre_tipo_consulta($consulta['tipo_consulta']) ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include INCLUDES_PATH . 'footer.php'; ?>