<?php
/**
 * MÓDULO DE CITAS - CREAR NUEVA
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para crear nueva cita con TomSelect
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
require_once '../../models/Cita.php';

// Verificar autenticación
verificar_sesion();

// Paciente preseleccionado (si viene de expediente)
$paciente_id_preseleccionado = isset($_GET['paciente_id']) ? (int)$_GET['paciente_id'] : 0;
$paciente_preseleccionado = null;

if ($paciente_id_preseleccionado > 0) {
    $stmt = $pdo->prepare("SELECT id, codigo, nombre FROM pacientes WHERE id = ? AND activo = 1");
    $stmt->execute([$paciente_id_preseleccionado]);
    $paciente_preseleccionado = $stmt->fetch();
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'paciente_id' => (int)$_POST['paciente_id'],
        'fecha' => $_POST['fecha'],
        'hora_inicio' => $_POST['hora_inicio'],
        'hora_fin' => $_POST['hora_fin'],
        'motivo' => trim($_POST['motivo']),
        'estado' => $_POST['estado'] ?? 'programada',
        'notas' => !empty($_POST['notas']) ? trim($_POST['notas']) : null
    ];
    
    $resultado = crear_cita($pdo, $datos);
    
    if ($resultado['success']) {
        mensaje_exito($resultado['message']);
        header('Location: ver.php?id=' . $resultado['id']);
        exit;
    } else {
        mensaje_error($resultado['message']);
    }
}

// Título de la página
$titulo_pagina = "Nueva Cita";

// Incluir header
include '../../includes/header.php';
?>

<!-- TomSelect CSS -->
<link href="<?= ASSETS_URL ?>tomselect/tom-select.bootstrap5.min.css" rel="stylesheet">

<div class="container-fluid mt-4">
    <!-- Breadcrumb y título -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="index.php">Citas</a></li>
            <li class="breadcrumb-item active">Nueva Cita</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><?= $titulo_pagina ?></h2>
                    <p class="text-muted mb-0">Registrar una nueva cita médica</p>
                </div>
                <div>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Datos de la Cita</h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="formCita">
                        <!-- Paciente -->
                        <div class="mb-4">
                            <label for="paciente_id" class="form-label">
                                Paciente <span class="text-danger">*</span>
                            </label>
                            
                            <?php if ($paciente_preseleccionado): ?>
                            <!-- Paciente ya seleccionado -->
                            <input type="hidden" name="paciente_id" value="<?= $paciente_preseleccionado['id'] ?>">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="avatar-circle bg-primary bg-opacity-10" 
                                                 style="width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-person-fill text-primary" style="font-size: 1.5rem;"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= e($paciente_preseleccionado['nombre']) ?></h6>
                                            <small class="text-muted"><?= e($paciente_preseleccionado['codigo']) ?></small>
                                        </div>
                                        <div class="ms-auto">
                                            <a href="nueva.php" class="btn btn-sm btn-outline-secondary">
                                                Cambiar paciente
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php else: ?>
                            <!-- Selector con TomSelect -->
                            <select id="paciente_id" 
                                    name="paciente_id" 
                                    required
                                    placeholder="Buscar paciente por nombre o código...">
                                <option value="">Seleccione un paciente...</option>
                            </select>
                            <small class="text-muted">
                                Escriba al menos 2 caracteres para buscar. 
                                ¿No encuentra al paciente? <a href="<?= BASE_URL ?>modules/pacientes/nuevo.php" target="_blank">Registrar nuevo paciente</a>
                            </small>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <!-- Fecha -->
                            <div class="col-md-6 mb-3">
                                <label for="fecha" class="form-label">
                                    Fecha <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fecha" 
                                       name="fecha" 
                                       required
                                       min="<?= date('Y-m-d') ?>"
                                       value="<?= date('Y-m-d') ?>">
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label">
                                    Estado <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="programada" selected>Programada</option>
                                    <option value="confirmada">Confirmada</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Hora Inicio -->
                            <div class="col-md-6 mb-3">
                                <label for="hora_inicio" class="form-label">
                                    Hora Inicio <span class="text-danger">*</span>
                                </label>
                                <input type="time" 
                                       class="form-control" 
                                       id="hora_inicio" 
                                       name="hora_inicio" 
                                       required
                                       value="09:00">
                            </div>

                            <!-- Hora Fin -->
                            <div class="col-md-6 mb-3">
                                <label for="hora_fin" class="form-label">
                                    Hora Fin <span class="text-danger">*</span>
                                </label>
                                <input type="time" 
                                       class="form-control" 
                                       id="hora_fin" 
                                       name="hora_fin" 
                                       required
                                       value="09:30">
                            </div>
                        </div>

                        <!-- Motivo -->
                        <div class="mb-3">
                            <label for="motivo" class="form-label">
                                Motivo de la Cita <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="motivo" 
                                   name="motivo" 
                                   placeholder="Ej: Control prenatal, Primera consulta, Ultrasonido..."
                                   required
                                   maxlength="200">
                            <small class="text-muted">Máximo 200 caracteres</small>
                        </div>

                        <!-- Notas -->
                        <div class="mb-4">
                            <label for="notas" class="form-label">Notas Adicionales</label>
                            <textarea class="form-control" 
                                      id="notas" 
                                      name="notas" 
                                      rows="3"
                                      placeholder="Información adicional sobre la cita (opcional)"></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Guardar Cita
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
<?php if (!$paciente_preseleccionado): ?>
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
    }
});
<?php endif; ?>

// Validación de horas
document.getElementById('hora_inicio').addEventListener('change', validarHoras);
document.getElementById('hora_fin').addEventListener('change', validarHoras);

function validarHoras() {
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;
    
    if (horaInicio && horaFin && horaFin <= horaInicio) {
        alert('La hora de fin debe ser posterior a la hora de inicio');
        document.getElementById('hora_fin').value = '';
    }
}

// Autocompletar hora_fin (30 minutos después de hora_inicio)
document.getElementById('hora_inicio').addEventListener('change', function() {
    const horaInicio = this.value;
    if (!horaInicio) return;
    
    const [horas, minutos] = horaInicio.split(':');
    let nuevaHora = parseInt(horas);
    let nuevosMinutos = parseInt(minutos) + 30;
    
    if (nuevosMinutos >= 60) {
        nuevaHora += 1;
        nuevosMinutos -= 60;
    }
    
    const horaFin = String(nuevaHora).padStart(2, '0') + ':' + String(nuevosMinutos).padStart(2, '0');
    document.getElementById('hora_fin').value = horaFin;
});
</script>

<?php include '../../includes/footer.php'; ?>