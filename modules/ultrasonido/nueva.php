<?php
/**
 * NUEVO INFORME DE ULTRASONIDO
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para crear informe de ultrasonido obstétrico
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
require_once MODELS_PATH . 'Paciente.php';
require_once MODELS_PATH . 'Consulta.php';

// Proteger la ruta
proteger_ruta();

if (!puede_crear()) {
    mensaje_error('No tiene permisos para crear informes de ultrasonido.');
    header('Location: ' . BASE_URL . 'modules/ultrasonido/');
    exit;
}

// CAMBIO: Ultrasonidos solo desde consultas
if (empty($_GET['consulta_id'])) {
    mensaje_error('Los ultrasonidos deben crearse desde una consulta. Primero registre la consulta del paciente.');
    header('Location: ' . BASE_URL . 'modules/consultas/');
    exit;
}

$consulta_id = (int)$_GET['consulta_id'];
$consulta = obtener_consulta_completa($pdo, $consulta_id);

if (!$consulta) {
    mensaje_error('La consulta no existe.');
    header('Location: ' . BASE_URL . 'modules/consultas/');
    exit;
}

// Verificar si ya tiene ultrasonido
if (consulta_tiene_ultrasonido($pdo, $consulta_id)) {
    mensaje_error('Esta consulta ya tiene un informe de ultrasonido.');
    header('Location: ' . BASE_URL . 'modules/consultas/ver.php?id=' . $consulta_id);
    exit;
}

$paciente_id = $consulta['paciente_id'];
$paciente = obtener_paciente_por_id($pdo, $paciente_id);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar paciente
        $paciente_id_post = !empty($_POST['paciente_id']) ? (int)$_POST['paciente_id'] : 0;
        
        if ($paciente_id_post <= 0) {
            throw new Exception('Debe seleccionar un paciente.');
        }
        
        // Preparar datos
        $datos = [
            'consulta_id' => !empty($_POST['consulta_id']) ? (int)$_POST['consulta_id'] : null,
            'paciente_id' => $paciente_id_post,
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
        
        // Crear ultrasonido
        $ultrasonido_id = crear_ultrasonido($pdo, $datos);
        
        if (!$ultrasonido_id) {
            throw new Exception('Error al crear el informe de ultrasonido. Intente nuevamente.');
        }
        
        // Registrar auditoría
        registrar_auditoria($pdo, $_SESSION['usuario_id'], 'CREAR', 'ultrasonidos', $ultrasonido_id, 
            "Creó informe de ultrasonido para paciente ID: $paciente_id_post");
        
        mensaje_exito('Informe de ultrasonido creado exitosamente.');
        
        // Redirigir según la acción
        if (!empty($_POST['accion']) && $_POST['accion'] === 'guardar_y_pdf') {
            header('Location: pdf.php?id=' . $ultrasonido_id);
        } else {
            header('Location: ver.php?id=' . $ultrasonido_id);
        }
        exit;
        
    } catch (Exception $e) {
        mensaje_error($e->getMessage());
    }
}

// Variables para la página
$page_title = 'Nuevo Informe de Ultrasonido' . ($paciente ? ' - ' . $paciente['nombre'] : '');
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Ultrasonidos', 'url' => BASE_URL . 'modules/ultrasonido/'],
    ['titulo' => 'Nuevo Informe', 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<div class="row">
    <!-- Formulario principal -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-heart-pulse"></i> Estudio de Ultrasonido Obstétrico
                </h5>
            </div>
            
            <div class="card-body">
                <form method="POST" id="formUltrasonido">
                    <!-- Datos ocultos -->
                    <input type="hidden" name="paciente_id" value="<?= $paciente['id'] ?>">
                    <input type="hidden" name="consulta_id" value="<?= $consulta_id ?>">
                    
                    <div class="alert alert-info mb-4">
                        <strong>Paciente:</strong> <?= e($paciente['nombre']) ?> 
                        (<?= e($paciente['codigo']) ?>)
                    </div>
                    
                    <!-- DATOS DEL EXAMEN -->
                    <h6 class="text-muted text-uppercase mb-3">Datos del Examen</h6>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="edad_gestacional" class="form-label">Edad Gestacional (semanas)</label>
                            <input type="text" class="form-control" id="edad_gestacional" name="edad_gestacional" 
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
                                   placeholder="mm">
                            <small class="text-muted">Diámetro biparietal</small>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="cc" class="form-label">CC</label>
                            <input type="number" step="0.01" class="form-control" id="cc" name="cc" 
                                   placeholder="mm">
                            <small class="text-muted">Circunferencia cefálica</small>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="ca" class="form-label">CA</label>
                            <input type="number" step="0.01" class="form-control" id="ca" name="ca" 
                                   placeholder="mm">
                            <small class="text-muted">Circunferencia abdominal</small>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="lf" class="form-label">LF</label>
                            <input type="number" step="0.01" class="form-control" id="lf" name="lf" 
                                   placeholder="mm">
                            <small class="text-muted">Longitud del fémur</small>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="peso_estimado" class="form-label">Peso Estimado (g)</label>
                            <input type="number" step="1" class="form-control" id="peso_estimado" name="peso_estimado" 
                                   placeholder="gramos">
                        </div>
                    </div>
                    
                    <!-- OBSERVACIONES DETALLADAS -->
                    <h6 class="text-muted text-uppercase mb-3">Observaciones Detalladas</h6>
                    
                    <div class="mb-4">
                        <textarea class="form-control" id="observaciones" name="observaciones" 
                                  rows="6" 
                                  placeholder="Situación, presentación, posición, placenta, líquido amniótico..."></textarea>
                        <small class="text-muted">Descripción detallada del estudio</small>
                    </div>
                    
                    <!-- CONCLUSIÓN -->
                    <h6 class="text-muted text-uppercase mb-3">Conclusión / Impresión Diagnóstica</h6>
                    
                    <div class="mb-4">
                        <textarea class="form-control" id="conclusion" name="conclusion" 
                                  rows="4" 
                                  placeholder="Ej: Embarazo normoevolutivo de X semanas..."></textarea>
                    </div>
                    
                    <!-- Botones -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= $consulta_id ? BASE_URL . 'modules/consultas/ver.php?id=' . $consulta_id : 'index.php' ?>" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        
                        <div class="btn-group">
                            <button type="submit" name="accion" value="guardar" class="btn btn-outline-primary">
                                <i class="bi bi-save"></i> Guardar Informe
                            </button>
                            <button type="submit" name="accion" value="guardar_y_pdf" class="btn btn-success">
                                <i class="bi bi-file-pdf"></i> Guardar y Generar PDF
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar: Info del paciente -->
    <div class="col-lg-4">
        <?php if ($paciente): ?>
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-person-circle"></i> Información del Paciente</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Nombre</small>
                        <strong><?= e($paciente['nombre']) ?></strong>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Código</small>
                        <strong><?= e($paciente['codigo']) ?></strong>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Edad</small>
                        <strong><?= calcular_edad($paciente['fecha_nacimiento']) ?> años</strong>
                    </div>
                    
                    <?php if (!empty($paciente['telefono'])): ?>
                        <div class="mb-3">
                            <small class="text-muted d-block">Teléfono</small>
                            <strong><?= e($paciente['telefono']) ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($paciente['alergias'])): ?>
                        <div class="alert alert-warning mb-0">
                            <small class="text-muted d-block"><strong>Alergias:</strong></small>
                            <?= nl2br(e($paciente['alergias'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($consulta): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-clipboard2-pulse"></i> Consulta Asociada</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">Fecha</small>
                        <strong><?= formatear_fecha($consulta['fecha']) ?></strong>
                    </div>
                    
                    <?php if (!empty($consulta['diagnostico'])): ?>
                        <div class="mt-3">
                            <small class="text-muted d-block"><strong>Diagnóstico:</strong></small>
                            <p class="mb-0 small"><?= nl2br(e($consulta['diagnostico'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php include INCLUDES_PATH . 'footer.php'; ?>