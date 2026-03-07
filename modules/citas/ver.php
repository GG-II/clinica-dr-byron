<?php
/**
 * MÓDULO DE CITAS - VER DETALLES
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Vista detallada de una cita
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

// Verificar si tiene consulta asociada
$stmt = $pdo->prepare("
    SELECT c.*, u.nombre as medico_nombre
    FROM consultas c
    LEFT JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.cita_id = ?
");
$stmt->execute([$cita_id]);
$consulta_asociada = $stmt->fetch();

// Título de la página
$titulo_pagina = "Detalles de Cita";

// Incluir header
include '../../includes/header.php';
?>

<div class="container-fluid mt-4">
    <!-- Breadcrumb y título -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="index.php">Citas</a></li>
            <li class="breadcrumb-item active">Detalles</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><?= $titulo_pagina ?></h2>
                    <p class="text-muted mb-0">
                        Cita del <?= formatear_fecha($cita['fecha']) ?> - 
                        <?= date('H:i', strtotime($cita['hora_inicio'])) ?>
                    </p>
                </div>
                <div>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <?php if ($cita['estado'] !== 'atendida' && $cita['estado'] !== 'cancelada'): ?>
                    <a href="editar.php?id=<?= $cita['id'] ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Columna principal -->
        <div class="col-lg-8">
            <!-- Datos de la cita -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Información de la Cita</h5>
                    <?php
                    $badge_class = [
                        'programada' => 'bg-primary',
                        'confirmada' => 'bg-info',
                        'atendida' => 'bg-success',
                        'cancelada' => 'bg-danger'
                    ];
                    $clase = $badge_class[$cita['estado']] ?? 'bg-secondary';
                    ?>
                    <span class="badge <?= $clase ?> fs-6">
                        <?= obtener_nombre_estado_cita($cita['estado']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Fecha</label>
                            <p class="mb-0 fw-semibold">
                                <i class="bi bi-calendar3 text-primary"></i>
                                <?= formatear_fecha($cita['fecha']) ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Horario</label>
                            <p class="mb-0 fw-semibold">
                                <i class="bi bi-clock text-primary"></i>
                                <?= date('H:i', strtotime($cita['hora_inicio'])) ?> - 
                                <?= date('H:i', strtotime($cita['hora_fin'])) ?>
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small mb-1">Estado</label>
                        <p class="mb-0">
                            <span class="badge <?= $clase ?>">
                                <?= obtener_nombre_estado_cita($cita['estado']) ?>
                            </span>
                        </p>
                    </div>

                    <?php if ($cita['motivo']): ?>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Motivo de la Cita</label>
                        <p class="mb-0"><?= e(trim($cita['motivo'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($cita['notas']): ?>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Notas Adicionales</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0"><?= e(trim($cita['notas'])) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Fecha de Creación</label>
                            <p class="mb-0">
                                <small><?= formatear_fecha_hora($cita['created_at']) ?></small>
                            </p>
                        </div>
                        <?php if ($cita['updated_at']): ?>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Última Modificación</label>
                            <p class="mb-0">
                                <small><?= formatear_fecha_hora($cita['updated_at']) ?></small>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Consulta asociada -->
            <?php if ($consulta_asociada): ?>
            <div class="card mb-4 border-success">
                <div class="card-header bg-success bg-opacity-10">
                    <h5 class="mb-0 text-success">
                        <i class="bi bi-check-circle"></i> Consulta Realizada
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Médico</label>
                            <p class="mb-0">Dr. <?= e($consulta_asociada['medico_nombre']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Fecha de Consulta</label>
                            <p class="mb-0"><?= formatear_fecha_hora($consulta_asociada['fecha']) ?></p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="<?= BASE_URL ?>modules/consultas/ver.php?id=<?= $consulta_asociada['id'] ?>" 
                           class="btn btn-success btn-sm">
                            <i class="bi bi-eye"></i> Ver Consulta Completa
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Columna lateral -->
        <div class="col-lg-4">
            <!-- Datos del paciente -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Paciente</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle bg-primary bg-opacity-10 mx-auto mb-3" 
                             style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person-fill text-primary" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="mb-1"><?= e($cita['paciente_nombre']) ?></h5>
                        <p class="text-muted mb-0"><?= e($cita['paciente_codigo']) ?></p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small mb-1">Teléfono</label>
                        <p class="mb-0">
                            <i class="bi bi-telephone"></i>
                            <?= e($cita['paciente_telefono']) ?>
                        </p>
                    </div>

                    <?php if ($cita['paciente_email']): ?>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Email</label>
                        <p class="mb-0">
                            <i class="bi bi-envelope"></i>
                            <?= e($cita['paciente_email']) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $cita['paciente_id'] ?>" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-person-lines-fill"></i> Ver Expediente Completo
                        </a>

                        <a href="https://wa.me/502<?= preg_replace('/[^0-9]/', '', $cita['paciente_telefono']) ?>" 
                           target="_blank"
                           class="btn btn-outline-success btn-sm">
                            <i class="bi bi-whatsapp"></i> Enviar WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($cita['estado'] === 'programada'): ?>
                        <!-- Confirmar cita -->
                        <form method="POST" action="acciones.php" onsubmit="return confirm('¿Confirmar esta cita?')">
                            <input type="hidden" name="accion" value="confirmar">
                            <input type="hidden" name="id" value="<?= $cita['id'] ?>">
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-check-circle"></i> Confirmar Cita
                            </button>
                        </form>
                        <?php endif; ?>

                        <?php if ($cita['estado'] === 'programada' || $cita['estado'] === 'confirmada'): ?>
                        <!-- Atender -->
                        <button type="button" 
                                class="btn btn-success" 
                                onclick="atenderCita(<?= $cita['id'] ?>)">
                            <i class="bi bi-clipboard-pulse"></i> Atender y Crear Consulta
                        </button>

                        <!-- Cancelar -->
                        <form method="POST" action="acciones.php" onsubmit="return confirm('¿Está seguro de cancelar esta cita?')">
                            <input type="hidden" name="accion" value="cancelar">
                            <input type="hidden" name="id" value="<?= $cita['id'] ?>">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-x-circle"></i> Cancelar Cita
                            </button>
                        </form>
                        <?php endif; ?>

                        <?php if ($cita['estado'] === 'programada' && !$consulta_asociada): ?>
                        <!-- Eliminar -->
                        <form method="POST" action="acciones.php" onsubmit="return confirm('¿Está seguro de eliminar esta cita? Esta acción no se puede deshacer.')">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= $cita['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash"></i> Eliminar Cita
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Atender cita (cambiar estado y redirigir a crear consulta)
function atenderCita(citaId) {
    if (confirm('¿Marcar esta cita como atendida y crear consulta?')) {
        // Cambiar estado
        fetch('acciones.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'accion=atender&id=' + citaId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirigir a crear consulta
                window.location.href = '<?= BASE_URL ?>modules/consultas/nueva.php?cita_id=' + citaId;
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        });
    }
}
</script>

<?php include '../../includes/footer.php'; ?>