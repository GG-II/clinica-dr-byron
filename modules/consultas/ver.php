<?php
/**
 * MÓDULO DE CONSULTAS - VER DETALLES
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Vista detallada de consulta médica
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

// Obtener datos completos de la consulta
$consulta = obtener_consulta_completa($pdo, $consulta_id);

if (!$consulta) {
    mensaje_error('La consulta no existe');
    header('Location: index.php');
    exit;
}

// Calcular edad del paciente
$edad = null;
if ($consulta['paciente_fecha_nacimiento']) {
    $fecha_nac = new DateTime($consulta['paciente_fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nac)->y;
}

// Título de la página
$titulo_pagina = "Consulta Médica";

// Incluir header
include '../../includes/header.php';
?>

<div class="container-fluid mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="index.php">Consultas</a></li>
            <li class="breadcrumb-item active">Detalles</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><?= $titulo_pagina ?></h2>
                    <p class="text-muted mb-0">
                        <?= formatear_fecha_hora($consulta['fecha']) ?> - 
                        <span class="badge bg-<?= obtener_color_tipo_consulta($consulta['tipo_consulta']) ?>">
                            <?= obtener_nombre_tipo_consulta($consulta['tipo_consulta']) ?>
                        </span>
                    </p>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $consulta['paciente_id'] ?>" 
                       class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Expediente
                    </a>
                    <a href="editar.php?id=<?= $consulta['id'] ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Columna principal -->
        <div class="col-lg-8">
            <!-- Info de la consulta -->
            <div class="card mb-4">
                <div class="card-header bg-primary bg-opacity-10">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clipboard-pulse text-primary me-2" style="font-size: 1.5rem;"></i>
                        <h5 class="mb-0">Información de la Consulta</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Fecha y Hora</label>
                            <p class="mb-0 fw-semibold">
                                <i class="bi bi-calendar3"></i>
                                <?= formatear_fecha_hora($consulta['fecha']) ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Tipo de Consulta</label>
                            <p class="mb-0">
                                <span class="badge bg-<?= obtener_color_tipo_consulta($consulta['tipo_consulta']) ?>">
                                    <?= obtener_nombre_tipo_consulta($consulta['tipo_consulta']) ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Atendido Por</label>
                            <p class="mb-0">Dr. <?= e($consulta['medico_nombre']) ?></p>
                        </div>
                        <?php if ($consulta['fecha_cita']): ?>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Cita Programada</label>
                            <p class="mb-0">
                                <i class="bi bi-calendar-check text-success"></i>
                                <?= formatear_fecha($consulta['fecha_cita']) ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Signos Vitales -->
            <?php if ($consulta['signos_vitales']): 
                $sv = $consulta['signos_vitales'];
            ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-activity"></i> Signos Vitales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="border rounded p-3">
                                <i class="bi bi-speedometer2 text-primary" style="font-size: 1.5rem;"></i>
                                <div class="mt-2">
                                    <small class="text-muted d-block">Peso</small>
                                    <strong><?= $sv['peso'] ? $sv['peso'] . ' kg' : '-' ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded p-3">
                                <i class="bi bi-arrows-vertical text-info" style="font-size: 1.5rem;"></i>
                                <div class="mt-2">
                                    <small class="text-muted d-block">Talla</small>
                                    <strong><?= $sv['talla'] ? $sv['talla'] . ' cm' : '-' ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded p-3">
                                <i class="bi bi-heart-pulse text-danger" style="font-size: 1.5rem;"></i>
                                <div class="mt-2">
                                    <small class="text-muted d-block">Presión</small>
                                    <strong><?= $sv['presion_arterial'] ?? '-' ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="border rounded p-3">
                                <i class="bi bi-thermometer-half text-warning" style="font-size: 1.5rem;"></i>
                                <div class="mt-2">
                                    <small class="text-muted d-block">Temperatura</small>
                                    <strong><?= $sv['temperatura'] ? $sv['temperatura'] . ' °C' : '-' ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Motivo de Consulta -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-left-text"></i> Motivo de Consulta
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(e($consulta['motivo_consulta'])) ?></p>
                </div>
            </div>

            <!-- Evaluación y Notas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text"></i> Evaluación y Notas Médicas
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(e($consulta['notas'])) ?></p>
                </div>
            </div>

            <!-- Diagnóstico -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary bg-opacity-10">
                    <h5 class="mb-0 text-primary">
                        <i class="bi bi-clipboard-check"></i> Diagnóstico
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(e($consulta['diagnostico'])) ?></p>
                </div>
            </div>

            <!-- Receta (si existe) -->
            <?php if (!empty($consulta['receta'])): ?>
            <div class="card mb-4 border-success">
                <div class="card-header bg-success bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-success">
                            <i class="bi bi-file-earmark-medical"></i> Receta Médica
                        </h5>
                        <a href="<?= BASE_URL ?>modules/recetas/ver.php?id=<?= $consulta['receta'][0]['id'] ?>" 
                           class="btn btn-sm btn-success">
                            <i class="bi bi-eye"></i> Ver Receta Completa
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Medicamento</th>
                                    <th>Dosis</th>
                                    <th>Vía</th>
                                    <th>Frecuencia</th>
                                    <th>Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($consulta['receta'] as $med): ?>
                                <tr>
                                    <td><?= e($med['medicamento']) ?></td>
                                    <td><?= e($med['dosis']) ?></td>
                                    <td><?= e($med['via']) ?></td>
                                    <td><?= e($med['frecuencia']) ?></td>
                                    <td><?= e($med['duracion']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Ultrasonido (si existe) -->
            <?php if ($consulta['ultrasonido']): 
                $us = $consulta['ultrasonido'];
            ?>
            <div class="card mb-4 border-info">
                <div class="card-header bg-info bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-info">
                            <i class="bi bi-soundwave"></i> Ultrasonido
                        </h5>
                        <a href="<?= BASE_URL ?>modules/ultrasonido/ver.php?id=<?= $us['id'] ?>" 
                           class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i> Ver Informe Completo
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <small class="text-muted">Edad Gestacional</small>
                            <div><strong><?= e($us['edad_gestacional']) ?></strong></div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <small class="text-muted">Peso Estimado</small>
                            <div><strong><?= $us['peso_estimado'] ? number_format($us['peso_estimado'], 0) . ' g' : '-' ?></strong></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Exámenes (si hay) -->
            <?php if (!empty($consulta['examenes'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-clipboard2-pulse"></i> Exámenes Solicitados
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Examen</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($consulta['examenes'] as $examen): ?>
                                <tr>
                                    <td><?= e($examen['tipo_examen']) ?></td>
                                    <td><?= formatear_fecha($examen['fecha_solicitud']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $examen['estado'] === 'recibido' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($examen['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>modules/laboratorios/ver.php?id=<?= $examen['id'] ?>" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Columna lateral -->
        <div class="col-lg-4">
            <!-- Datos del paciente -->
            <div class="card mb-4 sticky-top" style="top: 20px;">
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

                    <div class="d-grid gap-2 mt-3">
                        <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $consulta['paciente_id'] ?>" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-person-lines-fill"></i> Ver Expediente Completo
                        </a>

                        <a href="https://wa.me/502<?= preg_replace('/[^0-9]/', '', $consulta['paciente_telefono']) ?>" 
                           target="_blank"
                           class="btn btn-outline-success btn-sm">
                            <i class="bi bi-whatsapp"></i> Enviar WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if (empty($consulta['receta'])): ?>
                        <a href="<?= BASE_URL ?>modules/recetas/nueva.php?consulta_id=<?= $consulta['id'] ?>" 
                           class="btn btn-success">
                            <i class="bi bi-file-earmark-medical"></i> Crear Receta
                        </a>
                        <?php endif; ?>

                        <?php if (!$consulta['ultrasonido']): ?>
                        <a href="<?= BASE_URL ?>modules/ultrasonido/nueva.php?consulta_id=<?= $consulta['id'] ?>" 
                           class="btn btn-info">
                            <i class="bi bi-soundwave"></i> Registrar Ultrasonido
                        </a>
                        <?php endif; ?>

                        <a href="<?= BASE_URL ?>modules/laboratorios/nuevo.php?consulta_id=<?= $consulta['id'] ?>" 
                           class="btn btn-warning">
                            <i class="bi bi-clipboard2-pulse"></i> Solicitar Examen
                        </a>

                        <a href="<?= BASE_URL ?>modules/facturacion/nueva.php?consulta_id=<?= $consulta['id'] ?>" 
                           class="btn btn-primary">
                            <i class="bi bi-receipt"></i> Generar Recibo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>