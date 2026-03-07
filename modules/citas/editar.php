<?php
/**
 * MÓDULO DE CITAS - EDITAR
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para editar cita existente
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

// Obtener ID de la cita
$cita_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cita_id === 0) {
    mensaje_error('ID de cita no válido');
    header('Location: index.php');
    exit;
}

// Obtener datos de la cita
$cita = obtener_cita_por_id($pdo, $cita_id);

if (!$cita) {
    mensaje_error('La cita no existe');
    header('Location: index.php');
    exit;
}

// Verificar que la cita no esté atendida o cancelada
if ($cita['estado'] === 'atendida') {
    mensaje_advertencia('No se puede editar una cita que ya fue atendida');
    header('Location: ver.php?id=' . $cita_id);
    exit;
}

if ($cita['estado'] === 'cancelada') {
    mensaje_advertencia('No se puede editar una cita cancelada');
    header('Location: ver.php?id=' . $cita_id);
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'paciente_id' => (int)$_POST['paciente_id'],
        'fecha' => $_POST['fecha'],
        'hora_inicio' => $_POST['hora_inicio'],
        'hora_fin' => $_POST['hora_fin'],
        'motivo' => trim($_POST['motivo']),
        'estado' => $_POST['estado'],
        'notas' => !empty($_POST['notas']) ? trim($_POST['notas']) : null
    ];
    
    $resultado = actualizar_cita($pdo, $cita_id, $datos);
    
    if ($resultado['success']) {
        mensaje_exito($resultado['message']);
        header('Location: ver.php?id=' . $cita_id);
        exit;
    } else {
        mensaje_error($resultado['message']);
    }
}

// Título de la página
$titulo_pagina = "Editar Cita";

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
            <li class="breadcrumb-item"><a href="ver.php?id=<?= $cita['id'] ?>">Detalles</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><?= $titulo_pagina ?></h2>
                    <p class="text-muted mb-0">
                        Modificar datos de la cita del <?= formatear_fecha($cita['fecha']) ?>
                    </p>
                </div>
                <div>
                    <a href="ver.php?id=<?= $cita['id'] ?>" class="btn btn-secondary">
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
                        <!-- Paciente (ya seleccionado, solo mostrar) -->
                        <div class="mb-4">
                            <label class="form-label">
                                Paciente <span class="text-danger">*</span>
                            </label>
                            <input type="hidden" name="paciente_id" value="<?= $cita['paciente_id'] ?>">
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
                                            <h6 class="mb-0"><?= e($cita['paciente_nombre']) ?></h6>
                                            <small class="text-muted"><?= e($cita['paciente_codigo']) ?></small>
                                        </div>
                                        <div class="ms-auto">
                                            <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $cita['paciente_id'] ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               target="_blank">
                                                <i class="bi bi-box-arrow-up-right"></i> Ver expediente
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">No se puede cambiar el paciente de una cita existente</small>
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
                                       value="<?= $cita['fecha'] ?>">
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label">
                                    Estado <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="programada" <?= $cita['estado'] === 'programada' ? 'selected' : '' ?>>
                                        Programada
                                    </option>
                                    <option value="confirmada" <?= $cita['estado'] === 'confirmada' ? 'selected' : '' ?>>
                                        Confirmada
                                    </option>
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
                                       value="<?= date('H:i', strtotime($cita['hora_inicio'])) ?>">
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
                                       value="<?= date('H:i', strtotime($cita['hora_fin'])) ?>">
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
                                   maxlength="200"
                                   value="<?= e($cita['motivo']) ?>">
                            <small class="text-muted">Máximo 200 caracteres</small>
                        </div>

                        <!-- Notas -->
                        <div class="mb-4">
                            <label for="notas" class="form-label">Notas Adicionales</label>
                            <textarea class="form-control" 
                                      id="notas" 
                                      name="notas" 
                                      rows="3"
                                      placeholder="Información adicional sobre la cita (opcional)"><?= e(trim($cita['notas'] ?? '')) ?></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="ver.php?id=<?= $cita['id'] ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
</script>

<?php include '../../includes/footer.php'; ?>