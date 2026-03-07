<?php
/**
 * MÓDULO DE CONSULTAS - CREAR NUEVA
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para registrar nueva consulta médica
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

session_start();
define('ACCESS_GRANTED', true);

require_once '../../config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/funciones.php';
require_once '../../models/Consulta.php';
require_once '../../models/Cita.php';

// Verificar autenticación
verificar_sesion();

// Variables
$paciente = null;
$cita = null;
$cita_id = isset($_GET['cita_id']) ? (int)$_GET['cita_id'] : 0;
$paciente_id = isset($_GET['paciente_id']) ? (int)$_GET['paciente_id'] : 0;

// Si viene de una cita
if ($cita_id > 0) {
    $cita = obtener_cita_por_id($pdo, $cita_id);
    if ($cita) {
        $paciente_id = $cita['paciente_id'];
    }
}

// Obtener datos del paciente
if ($paciente_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = ? AND activo = 1");
    $stmt->execute([$paciente_id]);
    $paciente = $stmt->fetch();
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'paciente_id' => (int)$_POST['paciente_id'],
        'cita_id' => !empty($_POST['cita_id']) ? (int)$_POST['cita_id'] : null,
        'usuario_id' => $_SESSION['usuario_id'],
        'fecha' => date('Y-m-d H:i:s'),
        'tipo_consulta' => $_POST['tipo_consulta'],
        'motivo_consulta' => trim($_POST['motivo_consulta']),
        'notas' => trim($_POST['notas']),
        'diagnostico' => trim($_POST['diagnostico']),
        'signos_vitales' => [
            'peso' => !empty($_POST['peso']) ? (float)$_POST['peso'] : null,
            'talla' => !empty($_POST['talla']) ? (float)$_POST['talla'] : null,
            'presion_arterial' => !empty($_POST['presion_arterial']) ? $_POST['presion_arterial'] : null,
            'temperatura' => !empty($_POST['temperatura']) ? (float)$_POST['temperatura'] : null
        ]
    ];
    
    $resultado = crear_consulta($pdo, $datos);
    
    if ($resultado['success']) {
        mensaje_exito($resultado['message']);
        header('Location: ver.php?id=' . $resultado['id']);
        exit;
    } else {
        mensaje_error($resultado['message']);
    }
}

// Título de la página
$titulo_pagina = "Nueva Consulta";

// Incluir header
include '../../includes/header.php';
?>

<!-- TomSelect CSS -->
<link href="<?= ASSETS_URL ?>tomselect/tom-select.bootstrap5.min.css" rel="stylesheet">

<div class="container-fluid mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="index.php">Consultas</a></li>
            <li class="breadcrumb-item active">Nueva Consulta</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <?php if ($paciente): ?>
            <h2 class="mb-1">Nueva Consulta - <?= e($paciente['nombre']) ?></h2>
            <p class="text-muted mb-0">
                <i class="bi bi-card-list"></i> Expediente: <?= e($paciente['codigo']) ?>
            </p>
            <?php else: ?>
            <h2 class="mb-1"><?= $titulo_pagina ?></h2>
            <p class="text-muted mb-0">Registrar consulta médica</p>
            <?php endif; ?>
        </div>
    </div>

    <form method="POST" id="formConsulta">
        <div class="row">
            <!-- Columna principal -->
            <div class="col-lg-8">
                <!-- Header de la consulta -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="bi bi-clipboard-pulse text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Registro de Consulta Médica</h5>
                                <small class="text-muted">Fecha: <?= formatear_fecha(date('Y-m-d')) ?></small>
                            </div>
                            <div class="ms-auto text-end">
                                <div class="text-muted small">Expediente</div>
                                <div class="fw-bold text-primary">#<?= $paciente ? $paciente['codigo'] : '----' ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 1. Tipo de Consulta -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">1</span>
                            <h5 class="mb-0">Tipo de Consulta</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="cita_id" value="<?= $cita_id ?>">
                        
                        <?php if ($paciente): ?>
                        <input type="hidden" name="paciente_id" value="<?= $paciente['id'] ?>">
                        <?php else: ?>
                        <!-- Selector de paciente -->
                        <div class="mb-4">
                            <label for="paciente_id" class="form-label">
                                Paciente <span class="text-danger">*</span>
                            </label>
                            <select id="paciente_id" 
                                    name="paciente_id" 
                                    required
                                    placeholder="Buscar paciente...">
                                <option value="">Seleccione un paciente...</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="tipo_consulta" id="tipo_primera" value="primera_vez" <?= $cita ? '' : 'checked' ?>>
                                    <label class="btn btn-outline-primary" for="tipo_primera">
                                        <i class="bi bi-person-plus"></i> Primera Vez
                                    </label>

                                    <input type="radio" class="btn-check" name="tipo_consulta" id="tipo_control" value="control" <?= $cita ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-info" for="tipo_control">
                                        <i class="bi bi-arrow-repeat"></i> Control
                                    </label>

                                    <input type="radio" class="btn-check" name="tipo_consulta" id="tipo_urgencia" value="urgencia">
                                    <label class="btn btn-outline-danger" for="tipo_urgencia">
                                        <i class="bi bi-exclamation-triangle"></i> Urgencia
                                    </label>

                                    <input type="radio" class="btn-check" name="tipo_consulta" id="tipo_procedimiento" value="procedimiento">
                                    <label class="btn btn-outline-warning" for="tipo_procedimiento">
                                        <i class="bi bi-tools"></i> Procedimiento
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Signos Vitales -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">2</span>
                            <h5 class="mb-0">Signos Vitales</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="peso" class="form-label">Peso (kg)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="peso" 
                                       name="peso" 
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="talla" class="form-label">Talla (cm)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="talla" 
                                       name="talla" 
                                       step="0.01"
                                       min="0"
                                       placeholder="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="presion_arterial" class="form-label">Presión Arterial</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="presion_arterial" 
                                       name="presion_arterial" 
                                       placeholder="120/80"
                                       maxlength="20">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="temperatura" class="form-label">Temperatura (°C)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="temperatura" 
                                       name="temperatura" 
                                       step="0.1"
                                       min="30"
                                       max="45"
                                       placeholder="36.5">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Notas Clínicas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">3</span>
                            <h5 class="mb-0">Notas Clínicas</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Motivo -->
                        <div class="mb-3">
                            <label for="motivo_consulta" class="form-label">
                                Motivo de Consulta <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="motivo_consulta" 
                                      name="motivo_consulta" 
                                      rows="3"
                                      required
                                      placeholder="Describa el motivo principal de la visita..."><?= $cita ? e($cita['motivo']) : '' ?></textarea>
                        </div>

                        <!-- Evaluación -->
                        <div class="mb-3">
                            <label for="notas" class="form-label">
                                Evaluación y Notas Médicas <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="notas" 
                                      name="notas" 
                                      rows="5"
                                      required
                                      placeholder="Detalles de la exploración física y hallazgos..."></textarea>
                        </div>

                        <!-- Diagnóstico -->
                        <div class="mb-3">
                            <label for="diagnostico" class="form-label">
                                Diagnóstico <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="diagnostico" 
                                      name="diagnostico" 
                                      rows="4"
                                      required
                                      placeholder="Impresión clínica y diagnósticos definitivos..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= $paciente ? BASE_URL . 'modules/pacientes/ver.php?id=' . $paciente['id'] : 'index.php' ?>" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-save"></i> Solo Guardar
                                </button>
                                <button type="button" class="btn btn-primary" onclick="guardarYCrearReceta()">
                                    <i class="bi bi-file-earmark-medical"></i> Guardar y Crear Receta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna lateral - Info del paciente -->
            <div class="col-lg-4">
                <?php if ($paciente): ?>
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary bg-opacity-10">
                        <h5 class="mb-0 text-primary">
                            <i class="bi bi-person-fill"></i> Paciente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="avatar-circle bg-primary bg-opacity-10 mx-auto mb-2" 
                                 style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-fill text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="mb-1"><?= e($paciente['nombre']) ?></h5>
                            <p class="text-muted mb-0"><?= e($paciente['codigo']) ?></p>
                        </div>

                        <?php
                        $edad = null;
                        if ($paciente['fecha_nacimiento']) {
                            $fecha_nac = new DateTime($paciente['fecha_nacimiento']);
                            $hoy = new DateTime();
                            $edad = $hoy->diff($fecha_nac)->y;
                        }
                        ?>

                        <div class="mb-2">
                            <small class="text-muted">Edad</small>
                            <div><?= $edad ? $edad . ' años' : 'No registrada' ?></div>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Teléfono</small>
                            <div><?= e($paciente['telefono']) ?></div>
                        </div>

                        <?php if ($paciente['tipo_sangre']): ?>
                        <div class="mb-2">
                            <small class="text-muted">Tipo de Sangre</small>
                            <div><span class="badge bg-danger"><?= e($paciente['tipo_sangre']) ?></span></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($paciente['alergias']): ?>
                        <div class="card bg-warning bg-opacity-10 border-warning mt-3">
                            <div class="card-body p-2">
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-exclamation-triangle text-warning"></i> Alergias
                                </small>
                                <small><?= e($paciente['alergias']) ?></small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 mt-3">
                            <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $paciente['id'] ?>" 
                               class="btn btn-outline-primary btn-sm"
                               target="_blank">
                                <i class="bi bi-box-arrow-up-right"></i> Ver Expediente Completo
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- TomSelect JS -->
<script src="<?= ASSETS_URL ?>tomselect/tom-select.complete.min.js"></script>

<script>
// Inicializar TomSelect si no hay paciente preseleccionado
<?php if (!$paciente): ?>
const tomSelectPaciente = new TomSelect('#paciente_id', {
    valueField: 'id',
    labelField: 'nombre',
    searchField: ['nombre', 'codigo'],
    placeholder: 'Buscar paciente...',
    loadThrottle: 300,
    preload: false,
    
    load: function(query, callback) {
        if (query.length < 2) {
            callback();
            return;
        }
        
        fetch('<?= BASE_URL ?>api/pacientes/buscar.php?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    callback(data.pacientes);
                } else {
                    callback();
                }
            })
            .catch(() => {
                callback();
            });
    },
    
    render: {
        option: function(item, escape) {
            return `
                <div class="py-2">
                    <div class="fw-semibold">${escape(item.nombre)}</div>
                    <div class="small text-muted">
                        ${escape(item.codigo)} 
                        ${item.telefono ? ' • ' + escape(item.telefono) : ''}
                        ${item.edad ? ' • ' + item.edad + ' años' : ''}
                    </div>
                </div>
            `;
        },
        item: function(item, escape) {
            return `<div>${escape(item.nombre)} <small class="text-muted">(${escape(item.codigo)})</small></div>`;
        }
    }
});
<?php endif; ?>

// Guardar y crear receta
function guardarYCrearReceta() {
    // Agregar campo hidden para indicar que se creará receta
    const form = document.getElementById('formConsulta');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'crear_receta';
    input.value = '1';
    form.appendChild(input);
    
    form.submit();
}
</script>

<?php include '../../includes/footer.php'; ?>