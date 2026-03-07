<?php
/**
 * LABORATORIOS - Editar (Agregar Resultado)
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Agregar resultado a un examen solicitado
 */

define('ACCESS_GRANTED', true);
session_start();

require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once MODELS_PATH . 'Examen.php';

// Verificar autenticación y permisos
verificar_sesion();

if (es_asistente()) {
    $_SESSION['mensaje_error'] = 'No tienes permisos para agregar resultados';
    header('Location: ' . BASE_URL . 'modules/laboratorios/index.php');
    exit;
}

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

// Si ya tiene resultado, redirigir a ver
if ($examen['estado'] === 'recibido') {
    $_SESSION['mensaje_error'] = 'Este examen ya tiene resultado registrado';
    header('Location: ' . BASE_URL . 'modules/laboratorios/ver.php?id=' . $id);
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        // Validar datos obligatorios
        if (empty($_POST['resultado'])) {
            throw new Exception('Debe ingresar el resultado del examen');
        }
        
        // Preparar datos
        $datos = [
            'resultado' => trim($_POST['resultado']),
            'fecha_resultado' => !empty($_POST['fecha_resultado']) ? $_POST['fecha_resultado'] : date('Y-m-d'),
            'estado' => 'recibido'
        ];
        
        // Actualizar examen
        $resultado = actualizar_examen($pdo, $id, $datos);
        
        if ($resultado) {
            $_SESSION['mensaje_exito'] = 'Resultado agregado correctamente';
            header('Location: ' . BASE_URL . 'modules/laboratorios/ver.php?id=' . $id);
            exit;
        } else {
            throw new Exception('Error al guardar el resultado');
        }
        
    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = $e->getMessage();
    }
}

// Variables para el header
$titulo = 'Agregar Resultado';
$breadcrumb = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard.php'],
    ['titulo' => 'Laboratorios', 'url' => BASE_URL . 'modules/laboratorios/index.php'],
    ['titulo' => 'Agregar Resultado', 'url' => '']
];

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Contenido principal -->
<div class="container-fluid mt-4">
    
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="<?= BASE_URL ?>modules/laboratorios/ver.php?id=<?= $id ?>" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <h2><?= $titulo ?></h2>
            <p class="text-muted">Laboratorios / Agregar Resultado</p>
        </div>
    </div>
    
    <!-- Mensajes flash -->
    <?php mostrar_mensajes(); ?>
    
    <div class="row">
        
        <!-- Formulario -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clipboard2-check"></i>
                        Información del Examen
                    </h5>
                </div>
                <div class="card-body">
                    
                    <!-- Info del examen (solo lectura) -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-muted small">PACIENTE</label>
                            <p class="mb-0 fw-bold">
                                <?= e($examen['paciente_nombre']) ?>
                            </p>
                            <small class="text-muted"><?= e($examen['paciente_codigo']) ?></small>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">TIPO DE EXAMEN</label>
                            <p class="mb-0 fw-bold">
                                <?= e($examen['tipo_examen']) ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-muted small">FECHA SOLICITUD</label>
                            <p class="mb-0">
                                <?= formatear_fecha($examen['fecha_solicitud']) ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">ESTADO ACTUAL</label>
                            <p class="mb-0">
                                <span class="badge bg-warning text-dark">SOLICITADO</span>
                            </p>
                        </div>
                    </div>
                    
                    <?php if (!empty($examen['descripcion'])): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="text-muted small">DESCRIPCIÓN / INSTRUCCIONES</label>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0" style="white-space: pre-line;">
                                        <?= e(trim($examen['descripcion'])) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <!-- Formulario de resultado -->
                    <form method="POST" id="form-resultado">
                        
                        <h5 class="mb-3 text-primary">
                            <i class="bi bi-file-earmark-text"></i>
                            Ingresar Resultado
                        </h5>
                        
                        <div class="row g-3">
                            
                            <!-- Fecha resultado -->
                            <div class="col-md-4">
                                <label for="fecha_resultado" class="form-label text-muted small mb-1">
                                    <i class="bi bi-calendar"></i> FECHA RESULTADO
                                </label>
                                <input type="date" name="fecha_resultado" id="fecha_resultado" 
                                       class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            
                            <!-- Resultado (textarea grande) -->
                            <div class="col-md-12">
                                <label for="resultado" class="form-label text-muted small mb-1">
                                    RESULTADO DEL EXAMEN <span class="text-danger">*</span>
                                </label>
                                <textarea name="resultado" id="resultado" class="form-control" 
                                          rows="12" required
                                          placeholder="Escriba el resultado completo del examen de laboratorio...&#10;&#10;Ejemplo:&#10;&#10;HEMOGRAMA COMPLETO&#10;Hemoglobina: 14.5 g/dL&#10;Hematocrito: 42%&#10;Leucocitos: 7,200 /mm³&#10;Plaquetas: 250,000 /mm³&#10;&#10;INTERPRETACIÓN:&#10;Valores dentro de rangos normales."></textarea>
                                <small class="text-muted">
                                    Ingrese todos los valores y observaciones del resultado
                                </small>
                            </div>
                            
                        </div>
                        
                        <!-- Botones -->
                        <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                            <a href="<?= BASE_URL ?>modules/laboratorios/ver.php?id=<?= $id ?>" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Guardar Resultado
                            </button>
                        </div>
                        
                    </form>
                    
                </div>
            </div>
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
                            <i class="bi bi-eye"></i> Ver Expediente
                        </a>
                    </div>
                    
                </div>
            </div>
            
            <!-- Ayuda -->
            <div class="card border-info">
                <div class="card-header bg-info bg-opacity-10">
                    <h6 class="mb-0 text-info">
                        <i class="bi bi-info-circle"></i>
                        Información
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">
                        <strong>¿Qué incluir en el resultado?</strong>
                    </p>
                    <ul class="small mb-0 ps-3">
                        <li>Todos los valores medidos</li>
                        <li>Rangos de referencia (si aplica)</li>
                        <li>Observaciones del laboratorio</li>
                        <li>Interpretación preliminar</li>
                    </ul>
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

<script>
// Validación del formulario
document.getElementById('form-resultado').addEventListener('submit', function(e) {
    const resultado = document.getElementById('resultado').value.trim();
    
    if (resultado.length < 10) {
        e.preventDefault();
        alert('El resultado debe tener al menos 10 caracteres');
        document.getElementById('resultado').focus();
        return false;
    }
});
</script>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>