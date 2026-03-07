<?php
/**
 * NUEVA RECETA MÉDICA
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para crear receta (desde consulta o manual)
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
require_once MODELS_PATH . 'Paciente.php';
require_once MODELS_PATH . 'Consulta.php';

// Proteger la ruta
proteger_ruta();

// Verificar que puede crear (admin o médico)
if (!puede_crear()) {
    mensaje_error('No tiene permisos para crear recetas.');
    header('Location: ' . BASE_URL . 'modules/recetas/');
    exit;
}

// Variables iniciales
$paciente = null;
$consulta = null;
$paciente_id = null;
$consulta_id = null;

// Si viene desde una consulta
if (!empty($_GET['consulta_id'])) {
    $consulta_id = (int)$_GET['consulta_id'];
    $consulta = obtener_consulta_completa($pdo, $consulta_id);
    
    if ($consulta) {
        // Verificar si ya tiene receta
        if (consulta_tiene_receta($pdo, $consulta_id)) {
            mensaje_error('Esta consulta ya tiene una receta emitida.');
            header('Location: ' . BASE_URL . 'modules/consultas/ver.php?id=' . $consulta_id);
            exit;
        }
        
        $paciente_id = $consulta['paciente_id'];
        $paciente = obtener_paciente_por_id($pdo, $paciente_id);
    }
}

// Si viene con paciente_id directo (receta sin consulta)
if (empty($paciente) && !empty($_GET['paciente_id'])) {
    $paciente_id = (int)$_GET['paciente_id'];
    $paciente = obtener_paciente_por_id($pdo, $paciente_id);
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar paciente
        $paciente_id_post = !empty($_POST['paciente_id']) ? (int)$_POST['paciente_id'] : 0;
        
        if ($paciente_id_post <= 0) {
            throw new Exception('Debe seleccionar un paciente.');
        }
        
        // Validar que haya al menos un medicamento
        $medicamentos_post = !empty($_POST['medicamentos']) ? $_POST['medicamentos'] : [];
        
        if (empty($medicamentos_post)) {
            throw new Exception('Debe agregar al menos un medicamento.');
        }
        
        // Preparar datos de la receta
        $datos_receta = [
            'consulta_id' => !empty($_POST['consulta_id']) ? (int)$_POST['consulta_id'] : null,
            'paciente_id' => $paciente_id_post,
            'indicaciones_generales' => !empty($_POST['indicaciones_generales']) ? trim($_POST['indicaciones_generales']) : null,
            'fecha_emision' => date('Y-m-d')
        ];
        
        // Preparar medicamentos
        $medicamentos = [];
        foreach ($medicamentos_post as $index => $med) {
            if (!empty($med['medicamento'])) {
                $medicamentos[] = [
                    'medicamento' => trim($med['medicamento']),
                    'dosis' => !empty($med['dosis']) ? trim($med['dosis']) : null,
                    'via' => !empty($med['via']) ? trim($med['via']) : null,
                    'frecuencia' => !empty($med['frecuencia']) ? trim($med['frecuencia']) : null,
                    'duracion' => !empty($med['duracion']) ? trim($med['duracion']) : null,
                    'orden' => $index + 1
                ];
            }
        }
        
        if (empty($medicamentos)) {
            throw new Exception('Debe agregar al menos un medicamento válido.');
        }
        
        // Crear receta
        $receta_id = crear_receta($pdo, $datos_receta, $medicamentos);
        
        if (!$receta_id) {
            throw new Exception('Error al crear la receta. Intente nuevamente.');
        }
        
        // Registrar auditoría
        registrar_auditoria($pdo, $_SESSION['usuario_id'], 'CREAR', 'recetas', $receta_id, 
            "Creó receta para paciente ID: $paciente_id_post");
        
        mensaje_exito('Receta creada exitosamente.');
        
        // Redirigir según la acción del botón
        if (!empty($_POST['accion']) && $_POST['accion'] === 'guardar_y_pdf') {
            header('Location: pdf.php?id=' . $receta_id);
        } else {
            header('Location: ver.php?id=' . $receta_id);
        }
        exit;
        
    } catch (Exception $e) {
        mensaje_error($e->getMessage());
    }
}

// Variables para la página
$page_title = 'Nueva Receta' . ($paciente ? ' - ' . $paciente['nombre'] : '');
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Recetas', 'url' => BASE_URL . 'modules/recetas/'],
    ['titulo' => 'Nueva Receta', 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<div class="row">
    <!-- Formulario principal -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-prescription2"></i> Nueva Receta
                    <?php if ($paciente): ?>
                        - <span class="text-primary"><?= e($paciente['nombre']) ?></span>
                    <?php endif; ?>
                </h5>
            </div>
            
            <div class="card-body">
                <form method="POST" id="formReceta">
                    <!-- Paciente (hidden o select según el caso) -->
                    <?php if ($paciente): ?>
                        <input type="hidden" name="paciente_id" value="<?= $paciente['id'] ?>">
                        <?php if ($consulta_id): ?>
                            <input type="hidden" name="consulta_id" value="<?= $consulta_id ?>">
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Selector de paciente con TomSelect -->
                        <div class="mb-4">
                            <label for="paciente_id" class="form-label">
                                Paciente <span class="text-danger">*</span>
                            </label>
                            <select id="paciente_id" name="paciente_id" class="form-select" required>
                                <option value="">Buscar paciente...</option>
                            </select>
                            <small class="text-muted">Escriba el nombre o código del paciente</small>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Sección: Medicamentos -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">
                                <i class="bi bi-capsule"></i> Medicamentos Recetados
                            </h6>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAgregarMedicamento">
                                <i class="bi bi-plus-circle"></i> Agregar Medicamento
                            </button>
                        </div>
                        
                        <div id="listaMedicamentos">
                            <!-- Los medicamentos se agregan aquí dinámicamente -->
                        </div>
                    </div>
                    
                    <!-- Sección: Indicaciones Generales -->
                    <div class="mb-4">
                        <label for="indicaciones_generales" class="form-label">
                            <i class="bi bi-clipboard-check"></i> Indicaciones Generales
                        </label>
                        <textarea class="form-control" id="indicaciones_generales" name="indicaciones_generales" 
                                  rows="4" 
                                  placeholder="Dieta, reposo, o advertencias generales para el paciente..."></textarea>
                        <small class="text-muted">Opcional. Ejemplo: "Tomar con alimentos", "Evitar el sol", etc.</small>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= $consulta_id ? BASE_URL . 'modules/consultas/ver.php?id=' . $consulta_id : 'index.php' ?>" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        
                        <div class="btn-group">
                            <button type="submit" name="accion" value="guardar" class="btn btn-outline-primary">
                                <i class="bi bi-save"></i> Solo Guardar
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
                <div class="card-header bg-primary text-white">
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
                    
                    <?php if (!empty($paciente['tipo_sangre'])): ?>
                        <div class="mb-3">
                            <small class="text-muted d-block">Tipo de Sangre</small>
                            <span class="badge bg-danger"><?= e($paciente['tipo_sangre']) ?></span>
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
        
        <!-- Info de la consulta si viene desde ahí -->
        <?php if ($consulta): ?>
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
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

<script>
// Contador para IDs únicos de medicamentos
let medicamentoIndex = 0;

// Agregar medicamento
document.getElementById('btnAgregarMedicamento').addEventListener('click', function() {
    agregarMedicamento();
});

function agregarMedicamento() {
    const container = document.getElementById('listaMedicamentos');
    
    const medicamentoHTML = `
        <div class="medicamento-item card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Medicamento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="medicamentos[${medicamentoIndex}][medicamento]" 
                               placeholder="Nombre del medicamento" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Dosis</label>
                        <input type="text" class="form-control" name="medicamentos[${medicamentoIndex}][dosis]" 
                               placeholder="Ej: 5mg">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Vía</label>
                        <select class="form-select" name="medicamentos[${medicamentoIndex}][via]">
                            <option value="">Seleccione...</option>
                            <option value="Oral">Oral</option>
                            <option value="Sublingual">Sublingual</option>
                            <option value="Tópica">Tópica</option>
                            <option value="Intramuscular">Intramuscular</option>
                            <option value="Intravenosa">Intravenosa</option>
                            <option value="Subcutánea">Subcutánea</option>
                            <option value="Vaginal">Vaginal</option>
                            <option value="Rectal">Rectal</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Frecuencia</label>
                        <input type="text" class="form-control" name="medicamentos[${medicamentoIndex}][frecuencia]" 
                               placeholder="Ej: Cada 8 horas">
                    </div>
                    
                    <div class="col-md-10">
                        <label class="form-label">Duración</label>
                        <input type="text" class="form-control" name="medicamentos[${medicamentoIndex}][duracion]" 
                               placeholder="Ej: 7 días, Todo el embarazo">
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger w-100 btnEliminarMedicamento">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', medicamentoHTML);
    medicamentoIndex++;
    
    // Agregar evento de eliminar
    actualizarEventosEliminar();
}

function actualizarEventosEliminar() {
    document.querySelectorAll('.btnEliminarMedicamento').forEach(btn => {
        btn.onclick = function() {
            this.closest('.medicamento-item').remove();
        };
    });
}

// Agregar primer medicamento al cargar
document.addEventListener('DOMContentLoaded', function() {
    agregarMedicamento();
    
    <?php if (!$paciente): ?>
    // Inicializar TomSelect para búsqueda de pacientes
    new TomSelect('#paciente_id', {
        valueField: 'id',
        labelField: 'nombre',
        searchField: ['nombre', 'codigo', 'telefono'],
        load: function(query, callback) {
            if (!query.length) return callback();
            
            fetch('<?= BASE_URL ?>api/pacientes/buscar.php?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    callback(data);
                })
                .catch(() => {
                    callback();
                });
        },
        render: {
            option: function(item, escape) {
                return `<div>
                    <strong>${escape(item.nombre)}</strong><br>
                    <small class="text-muted">${escape(item.codigo)} - ${escape(item.telefono || 'Sin teléfono')}</small>
                </div>`;
            },
            item: function(item, escape) {
                return `<div>${escape(item.nombre)}</div>`;
            }
        },
        placeholder: 'Buscar paciente por nombre, código o teléfono...'
    });
    <?php endif; ?>
});

// Validar formulario antes de enviar
document.getElementById('formReceta').addEventListener('submit', function(e) {
    const medicamentos = document.querySelectorAll('.medicamento-item');
    
    if (medicamentos.length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un medicamento.');
        return false;
    }
});
</script>

<?php include INCLUDES_PATH . 'footer.php'; ?>