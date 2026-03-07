<?php
/**
 * LABORATORIOS - Nuevo Examen
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Solicitar nuevo examen de laboratorio
 */

define('ACCESS_GRANTED', true);
session_start();

require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once MODELS_PATH . 'Examen.php';
require_once MODELS_PATH . 'Paciente.php';

// Verificar autenticación y permisos
verificar_sesion();

if (es_asistente()) {
    $_SESSION['mensaje_error'] = 'No tienes permisos para solicitar exámenes';
    header('Location: ' . BASE_URL . 'modules/laboratorios/index.php');
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        // Validar datos obligatorios
        if (empty($_POST['paciente_id'])) {
            throw new Exception('Debe seleccionar un paciente');
        }
        
        if (empty($_POST['tipo_examen'])) {
            throw new Exception('Debe especificar el tipo de examen');
        }
        
        // Si tipo_examen es "Otro", usar el campo de texto libre
        $tipo_examen = $_POST['tipo_examen'];
        if ($tipo_examen === 'Otro' && !empty($_POST['tipo_examen_otro'])) {
            $tipo_examen = trim($_POST['tipo_examen_otro']);
        }
        
        if (empty($tipo_examen) || $tipo_examen === 'Otro') {
            throw new Exception('Debe especificar el nombre del examen');
        }
        
        // Preparar datos
        $datos = [
            'paciente_id' => (int)$_POST['paciente_id'],
            'consulta_id' => !empty($_POST['consulta_id']) ? (int)$_POST['consulta_id'] : null,
            'tipo_examen' => $tipo_examen,
            'fecha_solicitud' => !empty($_POST['fecha_solicitud']) ? $_POST['fecha_solicitud'] : date('Y-m-d'),
            'descripcion' => !empty($_POST['descripcion']) ? trim($_POST['descripcion']) : null
        ];
        
        // Crear examen
        $examen_id = crear_examen($pdo, $datos);
        
        if ($examen_id) {
            $_SESSION['mensaje_exito'] = 'Examen solicitado correctamente';
            header('Location: ' . BASE_URL . 'modules/laboratorios/ver.php?id=' . $examen_id);
            exit;
        } else {
            throw new Exception('Error al solicitar el examen');
        }
        
    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = $e->getMessage();
    }
}

// Obtener tipos de exámenes comunes
$tipos_examenes = obtener_tipos_examenes_comunes();

// Variables para el header
$titulo = 'Solicitar Examen';
$breadcrumb = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard.php'],
    ['titulo' => 'Laboratorios', 'url' => BASE_URL . 'modules/laboratorios/index.php'],
    ['titulo' => 'Nuevo', 'url' => '']
];

require_once INCLUDES_PATH . 'header.php';
?>

<!-- TomSelect CSS -->
<link href="<?= ASSETS_URL ?>tomselect/tom-select.bootstrap5.min.css" rel="stylesheet">

<!-- Contenido principal -->
<div class="container-fluid mt-4">
    
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="<?= BASE_URL ?>modules/laboratorios/index.php" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <h2><?= $titulo ?></h2>
            <p class="text-muted">Laboratorios / <span class="text-primary">Nuevo</span></p>
        </div>
    </div>
    
    <!-- Mensajes flash -->
    <?php mostrar_mensajes(); ?>
    
    <!-- Formulario -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    
                    <form method="POST" id="form-examen">
                        
                        <!-- DATOS DEL EXAMEN -->
                        <h5 class="border-bottom pb-2 mb-4">
                            <i class="bi bi-clipboard2-pulse text-primary"></i>
                            Datos del Examen
                        </h5>
                        
                        <div class="row g-3">
                            
                            <!-- Paciente -->
                            <div class="col-md-12">
                                <label for="paciente_id" class="form-label text-muted small mb-1">
                                    <i class="bi bi-person"></i> PACIENTE <span class="text-danger">*</span>
                                </label>
                                <select id="paciente_id" 
                                        name="paciente_id" 
                                        required
                                        placeholder="Buscar paciente por nombre o código...">
                                    <option value="">Seleccione un paciente...</option>
                                </select>
                                <small class="text-muted">
                                    Escriba al menos 2 caracteres para buscar.
                                </small>
                            </div>
                            
                            <!-- Consulta asociada (opcional) -->
                            <div class="col-md-12" id="container-consulta" style="display: none;">
                                <label for="consulta_id" class="form-label text-muted small mb-1">
                                    <i class="bi bi-clipboard2-check"></i> CONSULTA ASOCIADA (OPCIONAL)
                                </label>
                                <select name="consulta_id" id="consulta_id" class="form-select">
                                    <option value="">Sin consulta asociada</option>
                                </select>
                                <small class="text-muted">
                                    Si el examen fue solicitado en una consulta específica
                                </small>
                            </div>
                            
                            <!-- Tipo de examen -->
                            <div class="col-md-8">
                                <label for="tipo_examen" class="form-label text-muted small mb-1">
                                    TIPO DE EXAMEN <span class="text-danger">*</span>
                                </label>
                                <select name="tipo_examen" id="tipo_examen" class="form-select" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <?php foreach ($tipos_examenes as $tipo): ?>
                                        <option value="<?= e($tipo) ?>"><?= e($tipo) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Tipo examen otro (texto libre) -->
                            <div class="col-md-8" id="container-tipo-otro" style="display: none;">
                                <label for="tipo_examen_otro" class="form-label text-muted small mb-1">
                                    ESPECIFICAR TIPO DE EXAMEN <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="tipo_examen_otro" id="tipo_examen_otro" 
                                       class="form-control" maxlength="150"
                                       placeholder="Ej: Prueba de VPH">
                            </div>
                            
                            <!-- Fecha solicitud -->
                            <div class="col-md-4">
                                <label for="fecha_solicitud" class="form-label text-muted small mb-1">
                                    <i class="bi bi-calendar"></i> FECHA SOLICITUD
                                </label>
                                <input type="date" name="fecha_solicitud" id="fecha_solicitud" 
                                       class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            
                        </div>
                        
                        <!-- OBSERVACIONES -->
                        <h5 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="bi bi-chat-left-text text-primary"></i>
                            Observaciones
                        </h5>
                        
                        <div class="row g-3">
                            
                            <!-- Descripción/Instrucciones -->
                            <div class="col-md-12">
                                <label for="descripcion" class="form-label text-muted small mb-1">
                                    DESCRIPCIÓN/INSTRUCCIONES PARA EL PACIENTE
                                </label>
                                <textarea name="descripcion" id="descripcion" class="form-control" 
                                          rows="4" maxlength="500"
                                          placeholder="Escriba las instrucciones o preparación requerida..."></textarea>
                                <small class="text-muted">
                                    Ejemplo: "Acudir en ayunas de 8 horas"
                                </small>
                            </div>
                            
                        </div>
                        
                        <!-- Botones -->
                        <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                            <a href="<?= BASE_URL ?>modules/laboratorios/index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Examen
                            </button>
                        </div>
                        
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- TomSelect JS -->
<script src="<?= ASSETS_URL ?>tomselect/tom-select.complete.min.js"></script>

<script>
// Inicializar TomSelect para búsqueda de pacientes
const tomSelectPaciente = new TomSelect('#paciente_id', {
    valueField: 'id',
    labelField: 'nombre',
    searchField: ['nombre', 'codigo'],
    placeholder: 'Buscar paciente por nombre o código...',
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
    },
    
    onChange: function(value) {
        if (value) {
            cargarConsultasPaciente(value);
        } else {
            document.getElementById('container-consulta').style.display = 'none';
            document.getElementById('consulta_id').innerHTML = '<option value="">Sin consulta asociada</option>';
        }
    }
});

// Cargar consultas del paciente
function cargarConsultasPaciente(pacienteId) {
    fetch('<?= BASE_URL ?>api/pacientes/consultas.php?paciente_id=' + pacienteId)
        .then(response => response.json())
        .then(data => {
            const selectConsulta = document.getElementById('consulta_id');
            selectConsulta.innerHTML = '<option value="">Sin consulta asociada</option>';
            
            if (data.consultas && data.consultas.length > 0) {
                data.consultas.forEach(consulta => {
                    const option = document.createElement('option');
                    option.value = consulta.id;
                    option.textContent = `${consulta.fecha_formateada} - ${consulta.tipo_nombre}`;
                    selectConsulta.appendChild(option);
                });
                
                document.getElementById('container-consulta').style.display = 'block';
            } else {
                document.getElementById('container-consulta').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error al cargar consultas:', error);
        });
}

// Mostrar/ocultar campo "Otro"
const selectTipo = document.getElementById('tipo_examen');
const containerOtro = document.getElementById('container-tipo-otro');
const inputOtro = document.getElementById('tipo_examen_otro');

selectTipo.addEventListener('change', function() {
    if (this.value === 'Otro') {
        containerOtro.style.display = 'block';
        inputOtro.required = true;
    } else {
        containerOtro.style.display = 'none';
        inputOtro.required = false;
        inputOtro.value = '';
    }
});

// Validación del formulario
const form = document.getElementById('form-examen');
form.addEventListener('submit', function(e) {
    const tipoExamen = selectTipo.value;
    
    if (tipoExamen === 'Otro' && !inputOtro.value.trim()) {
        e.preventDefault();
        alert('Debe especificar el tipo de examen');
        inputOtro.focus();
        return false;
    }
});
</script>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
