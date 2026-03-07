<?php
/**
 * MÓDULO DE CONSULTAS - EDITAR
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para editar consulta médica existente
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

// Verificar autenticación
verificar_sesion();

// Obtener ID de la consulta
$consulta_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($consulta_id === 0) {
    mensaje_error('ID de consulta no válido');
    header('Location: index.php');
    exit;
}

// Obtener datos de la consulta
$consulta = obtener_consulta_completa($pdo, $consulta_id);

if (!$consulta) {
    mensaje_error('La consulta no existe');
    header('Location: index.php');
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
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
    
    $resultado = actualizar_consulta($pdo, $consulta_id, $datos);
    
    if ($resultado['success']) {
        mensaje_exito($resultado['message']);
        header('Location: ver.php?id=' . $consulta_id);
        exit;
    } else {
        mensaje_error($resultado['message']);
    }
}

// Calcular edad del paciente
$edad = null;
if ($consulta['paciente_fecha_nacimiento']) {
    $fecha_nac = new DateTime($consulta['paciente_fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nac)->y;
}

// Título de la página
$titulo_pagina = "Editar Consulta";

// Incluir header
include '../../includes/header.php';
?>

<div class="container-fluid mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="index.php">Consultas</a></li>
            <li class="breadcrumb-item"><a href="ver.php?id=<?= $consulta['id'] ?>">Detalles</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><?= $titulo_pagina ?></h2>
                    <p class="text-muted mb-0">
                        Modificar datos de la consulta del <?= formatear_fecha_hora($consulta['fecha']) ?>
                    </p>
                </div>
                <div>
                    <a href="ver.php?id=<?= $consulta['id'] ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                </div>
            </div>
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
                                <h5 class="mb-0">Editar Consulta Médica</h5>
                                <small class="text-muted">Fecha: <?= formatear_fecha($consulta['fecha']) ?></small>
                            </div>
                            <div class="ms-auto text-end">
                                <div class="text-muted small">Expediente</div>
                                <div class="fw-bold text-primary"><?= e($consulta['paciente_codigo']) ?></div>
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
                        <div class="row">
                            <div class="col-12">
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="tipo_consulta" id="tipo_primera" value="primera_vez" <?= $consulta['tipo_consulta'] === 'primera_vez' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary" for="tipo_primera">
                                        <i class="bi bi-person-plus"></i> Primera Vez
                                    </label>

                                    <input type="radio" class="btn-check" name="tipo_consulta" id="tipo_control" value="control" <?= $consulta['tipo_consulta'] === 'control' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-info" for="tipo_control">
                                        <i class="bi bi-arrow-repeat"></i> Control
                                    </label>

                                    <input type="radio" class="btn-check" name="tipo_consulta" id="tipo_urgencia" value="urgencia" <?= $consulta['tipo_consulta'] === 'urgencia' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-danger" for="tipo_urgencia">
                                        <i class="bi bi-exclamation-triangle"></i> Urgencia
                                    </label>

                                    <input type="radio" class="btn-check" name="tipo_consulta" id="tipo_procedimiento" value="procedimiento" <?= $consulta['tipo_consulta'] === 'procedimiento' ? 'checked' : '' ?>>
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
                        <?php $sv = $consulta['signos_vitales'] ?? []; ?>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="peso" class="form-label">Peso (kg)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="peso" 
                                       name="peso" 
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00"
                                       value="<?= $sv['peso'] ?? '' ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="talla" class="form-label">Talla (cm)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="talla" 
                                       name="talla" 
                                       step="0.01"
                                       min="0"
                                       placeholder="0"
                                       value="<?= $sv['talla'] ?? '' ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="presion_arterial" class="form-label">Presión Arterial</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="presion_arterial" 
                                       name="presion_arterial" 
                                       placeholder="120/80"
                                       maxlength="20"
                                       value="<?= e($sv['presion_arterial'] ?? '') ?>">
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
                                       placeholder="36.5"
                                       value="<?= $sv['temperatura'] ?? '' ?>">
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
                                      placeholder="Describa el motivo principal de la visita..."><?= e(trim($consulta['motivo_consulta'])) ?></textarea>
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
                                      placeholder="Detalles de la exploración física y hallazgos..."><?= e(trim($consulta['notas'])) ?></textarea>
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
                                      placeholder="Impresión clínica y diagnósticos definitivos..."><?= e(trim($consulta['diagnostico'])) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="ver.php?id=<?= $consulta['id'] ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna lateral - Info del paciente -->
            <div class="col-lg-4">
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
                            <h5 class="mb-1"><?= e($consulta['paciente_nombre']) ?></h5>
                            <p class="text-muted mb-0"><?= e($consulta['paciente_codigo']) ?></p>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Edad</small>
                            <div><?= $edad ? $edad . ' años' : 'No registrada' ?></div>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Teléfono</small>
                            <div><?= e($consulta['paciente_telefono']) ?></div>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Fecha de Consulta</small>
                            <div><?= formatear_fecha_hora($consulta['fecha']) ?></div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $consulta['paciente_id'] ?>" 
                               class="btn btn-outline-primary btn-sm"
                               target="_blank">
                                <i class="bi bi-box-arrow-up-right"></i> Ver Expediente Completo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>