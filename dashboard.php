<?php
/**
 * DASHBOARD PRINCIPAL
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Panel principal con estadísticas, citas del día y recordatorios
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

session_start();
define('ACCESS_GRANTED', true);
require_once 'config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'middleware.php';

// Proteger la ruta (cualquier usuario logueado)
proteger_ruta();

// Control de pagos (si está activado)
if (file_exists(INCLUDES_PATH . 'pago_control.php')) {
    require_once INCLUDES_PATH . 'pago_control.php';
    verificar_estado_pago();
}

// Variables para la página
$page_title = 'Dashboard';
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => null],
    ['titulo' => 'Dashboard', 'url' => null]
];

// ============================================================================
// OBTENER ESTADÍSTICAS
// ============================================================================

try {
    // 1. Citas de hoy
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM citas 
        WHERE DATE(fecha) = CURDATE()
    ");
    $stmt->execute();
    $citas_hoy = $stmt->fetchColumn();
    
    // 2. Pacientes atendidos (total histórico)
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT paciente_id) 
        FROM consultas
    ");
    $stmt->execute();
    $pacientes_atendidos = $stmt->fetchColumn();
    
    // 3. Ingresos del día
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(monto), 0) 
        FROM facturas 
        WHERE DATE(fecha) = CURDATE() AND estado = 'pagado'
    ");
    $stmt->execute();
    $ingresos_dia = $stmt->fetchColumn();
    
    // 4. Citas pendientes (próximas 7 días)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM citas 
        WHERE fecha BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        AND estado IN ('programada', 'confirmada')
    ");
    $stmt->execute();
    $citas_pendientes = $stmt->fetchColumn();
    
    // ========================================================================
    // CITAS DE HOY (tabla principal)
    // ========================================================================
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            c.hora_inicio,
            c.motivo,
            c.estado,
            p.id as paciente_id,
            p.codigo as paciente_codigo,
            p.nombre as paciente_nombre
        FROM citas c
        INNER JOIN pacientes p ON c.paciente_id = p.id
        WHERE DATE(c.fecha) = CURDATE()
        ORDER BY c.hora_inicio ASC
    ");
    $stmt->execute();
    $citas_del_dia = $stmt->fetchAll();
    
    // ========================================================================
    // PRÓXIMOS RECORDATORIOS (próximas 3 citas)
    // ========================================================================
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            c.fecha,
            c.hora_inicio,
            p.nombre as paciente_nombre,
            p.telefono as paciente_telefono
        FROM citas c
        INNER JOIN pacientes p ON c.paciente_id = p.id
        WHERE c.fecha >= CURDATE() 
        AND c.estado IN ('programada', 'confirmada')
        ORDER BY c.fecha ASC, c.hora_inicio ASC
        LIMIT 3
    ");
    $stmt->execute();
    $proximos_recordatorios = $stmt->fetchAll();
    
} catch (PDOException $e) {
    log_mensaje("Error al cargar dashboard: " . $e->getMessage(), 'error');
    $citas_hoy = 0;
    $pacientes_atendidos = 0;
    $ingresos_dia = 0;
    $citas_pendientes = 0;
    $citas_del_dia = [];
    $proximos_recordatorios = [];
}

// ============================================================================
// INCLUIR HEADER
// ============================================================================
include INCLUDES_PATH . 'header.php';
?>

<!-- Título de la página -->
<h1 class="page-title">Dashboard</h1>

<!-- Estadísticas -->
<div class="stats-grid">
    <!-- Citas Hoy -->
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="bi bi-calendar-check"></i>
        </div>
        <div class="stat-details">
            <div class="stat-label">Citas Hoy</div>
            <div class="stat-value"><?= number_format($citas_hoy) ?></div>
        </div>
    </div>
    
    <!-- Pacientes Atendidos -->
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="bi bi-people-fill"></i>
        </div>
        <div class="stat-details">
            <div class="stat-label">Pacientes Atendidos</div>
            <div class="stat-value"><?= number_format($pacientes_atendidos) ?></div>
        </div>
    </div>
    
    <!-- Ingresos del Día -->
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="bi bi-currency-dollar"></i>
        </div>
        <div class="stat-details">
            <div class="stat-label">Ingresos del Día</div>
            <div class="stat-value"><?= formatear_moneda($ingresos_dia) ?></div>
        </div>
    </div>
    
    <!-- Citas Pendientes -->
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="bi bi-clock-history"></i>
        </div>
        <div class="stat-details">
            <div class="stat-label">Citas Pendientes</div>
            <div class="stat-value"><?= number_format($citas_pendientes) ?></div>
        </div>
    </div>
</div>

<!-- Contenido Principal: 2 Columnas -->
<div class="row">
    <!-- Columna Izquierda: Citas de Hoy -->
    <div class="col-lg-8 mb-4">
        <div class="table-card">
            <div class="table-card-header">
                <h2 class="table-card-title">Citas de Hoy</h2>
                <a href="<?= BASE_URL ?>modules/citas/" class="table-card-link">
                    Ver Agenda Completa
                </a>
            </div>
            <div class="table-card-body">
                <?php if (count($citas_del_dia) > 0): ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>HORA</th>
                            <th>PACIENTE</th>
                            <th>MOTIVO</th>
                            <th>ESTADO</th>
                            <th style="width: 80px;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($citas_del_dia as $cita): ?>
                        <tr>
                            <td>
                                <strong><?= date('h:i A', strtotime($cita['hora_inicio'])) ?></strong>
                            </td>
                            <td>
                                <strong><?= e($cita['paciente_nombre']) ?></strong><br>
                                <small class="text-muted"><?= e($cita['paciente_codigo']) ?></small>
                            </td>
                            <td><?= e($cita['motivo']) ?></td>
                            <td>
                                <?php
                                $badge_class = 'badge-programada';
                                $estado_texto = 'PROGRAMADA';
                                
                                switch ($cita['estado']) {
                                    case 'atendida':
                                    case 'atendido':
                                        $badge_class = 'badge-atendido';
                                        $estado_texto = 'ATENDIDO';
                                        break;
                                    case 'confirmada':
                                        $badge_class = 'badge-espera';
                                        $estado_texto = 'EN ESPERA';
                                        break;
                                    case 'cancelada':
                                        $badge_class = 'badge-cancelada';
                                        $estado_texto = 'CANCELADA';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $badge_class ?>">
                                    <?= $estado_texto ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-link text-muted" title="Más opciones">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x" style="font-size: 48px; color: #dee2e6;"></i>
                    <p class="text-muted mt-3">No hay citas programadas para hoy</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Columna Derecha: Próximos Recordatorios -->
    <div class="col-lg-4 mb-4">
        <div class="recordatorios-panel">
            <div class="recordatorios-title">
                <span>Próximos Recordatorios</span>
                <a href="<?= BASE_URL ?>modules/recordatorios/" class="table-card-link" style="font-size: 14px;">
                    Gestionar
                </a>
            </div>
            
            <?php if (count($proximos_recordatorios) > 0): ?>
                <?php foreach ($proximos_recordatorios as $recordatorio): ?>
                <div class="recordatorio-item">
                    <div class="recordatorio-icon">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="recordatorio-info">
                        <div class="recordatorio-nombre">
                            <?= e($recordatorio['paciente_nombre']) ?>
                        </div>
                        <div class="recordatorio-hora">
                            <?php
                            $fecha_cita = new DateTime($recordatorio['fecha'] . ' ' . $recordatorio['hora_inicio']);
                            $hoy = new DateTime();
                            
                            if ($fecha_cita->format('Y-m-d') === $hoy->format('Y-m-d')) {
                                echo 'Hoy ' . $fecha_cita->format('h:i A');
                            } elseif ($fecha_cita->format('Y-m-d') === $hoy->modify('+1 day')->format('Y-m-d')) {
                                echo 'Mañana ' . $fecha_cita->format('h:i A');
                            } else {
                                echo formatear_fecha($recordatorio['fecha']) . ' ' . date('h:i A', strtotime($recordatorio['hora_inicio']));
                            }
                            ?>
                        </div>
                    </div>
                    <div>
                        <?php if (!empty($recordatorio['paciente_telefono'])): ?>
                        <a 
                            href="https://wa.me/502<?= preg_replace('/[^0-9]/', '', $recordatorio['paciente_telefono']) ?>?text=<?= urlencode('Hola ' . $recordatorio['paciente_nombre'] . ', le recordamos su cita para el ' . formatear_fecha($recordatorio['fecha']) . ' a las ' . date('h:i A', strtotime($recordatorio['hora_inicio'])) . '. ¡Le esperamos!') ?>" 
                            target="_blank"
                            class="btn-whatsapp btn-sm"
                            title="Enviar recordatorio por WhatsApp"
                        >
                            <i class="bi bi-whatsapp"></i>
                            WhatsApp
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        Haz clic en WhatsApp para abrir la ventana de chat directo.
                    </small>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-check-circle" style="font-size: 48px; color: #28a745;"></i>
                    <p class="text-muted mt-3 mb-0">No hay recordatorios pendientes</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Acceso rápido (opcional, según tu diseño) -->
<?php if (es_admin() || es_medico()): ?>
<div class="row">
    <div class="col-12">
        <div class="table-card">
            <div class="table-card-header">
                <h2 class="table-card-title">Acceso Rápido</h2>
            </div>
            <div class="table-card-body p-4">
                <div class="row g-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="<?= BASE_URL ?>modules/pacientes/nuevo.php" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-person-plus-fill d-block mb-2" style="font-size: 24px;"></i>
                                Nuevo Paciente
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= BASE_URL ?>modules/citas/nueva.php" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-calendar-plus d-block mb-2" style="font-size: 24px;"></i>
                                Agendar Cita
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= BASE_URL ?>modules/consultas/nueva.php" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-clipboard2-pulse d-block mb-2" style="font-size: 24px;"></i>
                                Nueva Consulta
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= BASE_URL ?>modules/facturacion/nueva.php" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-receipt d-block mb-2" style="font-size: 24px;"></i>
                                Registrar Pago
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
// Incluir footer
include INCLUDES_PATH . 'footer.php';
?>