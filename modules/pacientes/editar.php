<?php
/**
 * EDITAR PACIENTE
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para editar un paciente existente
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

// Proteger la ruta (admin, médico, asistente)
proteger_ruta();

// Verificar permisos de edición
if (!puede_editar()) {
    mensaje_error('No tiene permisos para editar pacientes.');
    header('Location: index.php');
    exit;
}

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

// Procesar formulario si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log de inicio de proceso
    log_mensaje("Iniciando edición de paciente ID: {$id}. Usuario: " . $_SESSION['usuario_nombre'], 'info');
    
    // Obtener datos del formulario
    $datos = [
        'nombre' => trim($_POST['nombre'] ?? ''),
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
        'sexo' => $_POST['sexo'] ?? '',
        'dpi' => trim($_POST['dpi'] ?? ''),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'direccion' => trim($_POST['direccion'] ?? ''),
        'contacto_emergencia' => trim($_POST['contacto_emergencia'] ?? ''),
        'telefono_emergencia' => trim($_POST['telefono_emergencia'] ?? ''),
        'tipo_sangre' => ($_POST['tipo_sangre'] ?? '') ?: null,
        'alergias' => trim($_POST['alergias'] ?? ''),
        'antecedentes_personales' => trim($_POST['antecedentes_personales'] ?? ''),
        'antecedentes_familiares' => trim($_POST['antecedentes_familiares'] ?? ''),
        'antecedentes_quirurgicos' => trim($_POST['antecedentes_quirurgicos'] ?? '')
    ];
    
    // Log de datos recibidos (sin datos sensibles)
    log_mensaje("Datos recibidos - Nombre: {$datos['nombre']}, Sexo: {$datos['sexo']}", 'info');
    
    // Combinar contacto de emergencia (nombre + teléfono)
    if (!empty($datos['contacto_emergencia']) && !empty($datos['telefono_emergencia'])) {
        $datos['contacto_emergencia'] = $datos['contacto_emergencia'] . ' - ' . $datos['telefono_emergencia'];
    }
    unset($datos['telefono_emergencia']); // Remover campo temporal
    
    // Actualizar paciente
    $resultado = actualizar_paciente($pdo, $id, $datos);
    
    if ($resultado['success']) {
        log_mensaje("Paciente actualizado exitosamente - ID: {$id}", 'info');
        mensaje_exito('Paciente actualizado correctamente.');
        header('Location: ver.php?id=' . $id);
        exit;
    } else {
        // Log detallado del error
        log_mensaje("Error al actualizar paciente - Mensaje: {$resultado['message']}", 'error');
        log_mensaje("Datos enviados: " . json_encode($datos), 'error');
        mensaje_error($resultado['message']);
    }
}

// Separar contacto de emergencia (si está combinado)
$contacto_nombre = '';
$contacto_telefono = '';
if (!empty($paciente['contacto_emergencia'])) {
    $partes = explode(' - ', $paciente['contacto_emergencia']);
    $contacto_nombre = $partes[0] ?? '';
    $contacto_telefono = $partes[1] ?? '';
}

// Variables para la página
$page_title = 'Editar Paciente';
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Pacientes', 'url' => BASE_URL . 'modules/pacientes/'],
    ['titulo' => 'Editar Paciente', 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<!-- Título centrado -->
<div class="text-center mb-4">
    <h1 class="page-title">Editar Paciente</h1>
    <p class="text-muted">Código: <strong><?= e($paciente['codigo']) ?></strong></p>
</div>

<!-- Formulario -->
<form method="POST" id="formEditarPaciente" class="needs-validation" novalidate>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- SECCIÓN 1: Datos del Paciente -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-fill text-primary me-2" style="font-size: 1.2rem;"></i>
                        <div>
                            <h5 class="mb-0">Datos del Paciente</h5>
                            <small class="text-muted">Información personal básica y contacto</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Nombre Completo -->
                        <div class="col-12">
                            <label for="nombre" class="form-label fw-semibold">
                                Nombre Completo <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="nombre" 
                                    name="nombre" 
                                    placeholder="Escriba el nombre completo del paciente..."
                                    value="<?= e($paciente['nombre']) ?>"
                                    required
                                    autofocus
                                >
                            </div>
                        </div>
                        
                        <!-- Fecha de Nacimiento y Sexo -->
                        <div class="col-md-4">
                            <label for="fecha_nacimiento" class="form-label fw-semibold">
                                Fecha de Nacimiento <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-calendar"></i>
                                </span>
                                <input 
                                    type="date" 
                                    class="form-control" 
                                    id="fecha_nacimiento" 
                                    name="fecha_nacimiento"
                                    value="<?= e($paciente['fecha_nacimiento']) ?>"
                                    max="<?= date('Y-m-d') ?>"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="sexo" class="form-label fw-semibold">
                                Sexo <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="sexo" name="sexo" required>
                                <option value="">Seleccione...</option>
                                <option value="F" <?= $paciente['sexo'] === 'F' ? 'selected' : '' ?>>
                                    Femenino
                                </option>
                                <option value="M" <?= $paciente['sexo'] === 'M' ? 'selected' : '' ?>>
                                    Masculino
                                </option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="tipo_sangre" class="form-label fw-semibold">
                                Tipo de Sangre
                            </label>
                            <select class="form-select" id="tipo_sangre" name="tipo_sangre">
                                <option value="">Seleccione...</option>
                                <?php
                                $tipos_sangre = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                foreach ($tipos_sangre as $tipo) {
                                    $selected = ($paciente['tipo_sangre'] === $tipo) ? 'selected' : '';
                                    echo "<option value=\"{$tipo}\" {$selected}>{$tipo}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <!-- DPI -->
                        <div class="col-md-6">
                            <label for="dpi" class="form-label fw-semibold">
                                DPI
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="dpi" 
                                name="dpi" 
                                placeholder="1234567890123 (13 dígitos)"
                                value="<?= e($paciente['dpi']) ?>"
                                maxlength="13"
                                pattern="[0-9]{13}"
                            >
                            <small class="text-muted">13 dígitos sin espacios (opcional)</small>
                        </div>
                        
                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <label for="telefono" class="form-label fw-semibold">
                                Teléfono <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-phone"></i>
                                </span>
                                <input 
                                    type="tel" 
                                    class="form-control" 
                                    id="telefono" 
                                    name="telefono" 
                                    placeholder="12345678 (8 dígitos)"
                                    value="<?= e($paciente['telefono']) ?>"
                                    maxlength="8"
                                    pattern="[0-9]{8}"
                                    required
                                >
                            </div>
                            <small class="text-muted">8 dígitos sin espacios</small>
                        </div>
                        
                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">
                                Email
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    placeholder="correo@ejemplo.com"
                                    value="<?= e($paciente['email']) ?>"
                                >
                            </div>
                        </div>
                        
                        <!-- Dirección -->
                        <div class="col-md-6">
                            <label for="direccion" class="form-label fw-semibold">
                                Dirección <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-geo-alt"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="direccion" 
                                    name="direccion" 
                                    placeholder="Ej: Zona 1, Huehuetenango"
                                    value="<?= e($paciente['direccion']) ?>"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SECCIÓN 2: Contacto de Emergencia -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-heart-pulse-fill text-danger me-2" style="font-size: 1.2rem;"></i>
                        <div>
                            <h5 class="mb-0">Contacto de Emergencia</h5>
                            <small class="text-muted">Persona de confianza para contactar en caso necesario</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Nombre del Contacto -->
                        <div class="col-md-6">
                            <label for="contacto_emergencia" class="form-label fw-semibold">
                                Nombre del Contacto
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="contacto_emergencia" 
                                name="contacto_emergencia" 
                                placeholder="Nombre de la persona de confianza..."
                                value="<?= e($contacto_nombre) ?>"
                            >
                        </div>
                        
                        <!-- Teléfono de Contacto -->
                        <div class="col-md-6">
                            <label for="telefono_emergencia" class="form-label fw-semibold">
                                Teléfono de Contacto
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-phone"></i>
                                </span>
                                <input 
                                    type="tel" 
                                    class="form-control" 
                                    id="telefono_emergencia" 
                                    name="telefono_emergencia" 
                                    placeholder="12345678 (8 dígitos)"
                                    value="<?= e($contacto_telefono) ?>"
                                    maxlength="8"
                                    pattern="[0-9]{8}"
                                >
                            </div>
                            <small class="text-muted">8 dígitos sin espacios</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SECCIÓN 3: Antecedentes Médicos -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clipboard2-pulse text-danger me-2" style="font-size: 1.2rem;"></i>
                        <div>
                            <h5 class="mb-0">Antecedentes Médicos</h5>
                            <small class="text-muted">Historial médico del paciente</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Antecedentes Personales -->
                        <div class="col-12">
                            <label for="antecedentes_personales" class="form-label fw-semibold">
                                Antecedentes Personales
                            </label>
                            <textarea 
                                class="form-control" 
                                id="antecedentes_personales" 
                                name="antecedentes_personales" 
                                rows="4"
                                placeholder="Enfermedades previas, cirugías, hospitalizaciones..."
                            ><?= e($paciente['antecedentes_personales']) ?></textarea>
                        </div>
                        
                        <!-- Antecedentes Familiares -->
                        <div class="col-12">
                            <label for="antecedentes_familiares" class="form-label fw-semibold">
                                Antecedentes Familiares
                            </label>
                            <textarea 
                                class="form-control" 
                                id="antecedentes_familiares" 
                                name="antecedentes_familiares" 
                                rows="4"
                                placeholder="Enfermedades hereditarias, condiciones familiares..."
                            ><?= e($paciente['antecedentes_familiares']) ?></textarea>
                        </div>
                        
                        <!-- Antecedentes Quirúrgicos -->
                        <div class="col-12">
                            <label for="antecedentes_quirurgicos" class="form-label fw-semibold">
                                Antecedentes Quirúrgicos
                            </label>
                            <textarea 
                                class="form-control" 
                                id="antecedentes_quirurgicos" 
                                name="antecedentes_quirurgicos" 
                                rows="3"
                                placeholder="Cirugías previas..."
                            ><?= e($paciente['antecedentes_quirurgicos']) ?></textarea>
                        </div>
                        
                        <!-- Alergias -->
                        <div class="col-12">
                            <label for="alergias" class="form-label fw-semibold">
                                Alergias
                            </label>
                            <textarea 
                                class="form-control" 
                                id="alergias" 
                                name="alergias" 
                                rows="3"
                                placeholder="Indique medicamentos o sustancias a las que el paciente es alérgico..."
                            ><?= e($paciente['alergias']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Botones de Acción -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="ver.php?id=<?= $id ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver al Expediente
                        </a>
                        <div class="d-flex gap-2">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</form>

<?php
// JavaScript adicional
$additional_js = <<<'JS'
<script>
// Validación de Bootstrap
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Formatear DPI mientras se escribe (solo números)
document.getElementById('dpi').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value.substring(0, 13);
});

// Formatear teléfono principal (solo números)
document.getElementById('telefono').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value.substring(0, 8);
});

// Formatear teléfono de emergencia (solo números)
document.getElementById('telefono_emergencia').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value.substring(0, 8);
});
</script>
JS;

// Incluir footer
include INCLUDES_PATH . 'footer.php';
?>