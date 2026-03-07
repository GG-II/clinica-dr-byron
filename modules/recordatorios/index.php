<?php
/**
 * RECORDATORIOS WHATSAPP
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Lista de citas próximas con botón para enviar recordatorio por WhatsApp
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
require_once MODELS_PATH . 'Cita.php';

// Proteger la ruta
proteger_ruta();

// Determinar pestaña activa
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'manana';

// Búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Función para obtener citas según rango
function obtener_citas_por_rango($pdo, $fecha_inicio, $fecha_fin, $busqueda = '') {
    try {
        $sql = "SELECT 
                    c.id,
                    c.fecha,
                    c.hora_inicio,
                    c.motivo,
                    c.estado,
                    p.nombre as paciente_nombre,
                    p.telefono as paciente_telefono,
                    p.codigo as paciente_codigo
                FROM citas c
                INNER JOIN pacientes p ON c.paciente_id = p.id
                WHERE c.fecha BETWEEN ? AND ?
                AND c.estado IN ('programada', 'confirmada')";
        
        $params = [$fecha_inicio, $fecha_fin];
        
        if (!empty($busqueda)) {
            $sql .= " AND p.nombre LIKE ?";
            $params[] = '%' . $busqueda . '%';
        }
        
        $sql .= " ORDER BY c.fecha ASC, c.hora_inicio ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error al obtener citas: " . $e->getMessage());
        return [];
    }
}

// Obtener fechas según pestaña
$hoy = date('Y-m-d');
$manana = date('Y-m-d', strtotime('+1 day'));
$en_dos_dias = date('Y-m-d', strtotime('+2 days'));
$fin_semana = date('Y-m-d', strtotime('+7 days'));

// Obtener citas según pestaña activa
switch ($tab) {
    case 'manana':
        $citas = obtener_citas_por_rango($pdo, $manana, $manana, $busqueda);
        $titulo_tab = 'Mañana';
        break;
    case 'dos_dias':
        $citas = obtener_citas_por_rango($pdo, $en_dos_dias, $en_dos_dias, $busqueda);
        $titulo_tab = 'En 2 Días';
        break;
    case 'semana':
        $citas = obtener_citas_por_rango($pdo, $hoy, $fin_semana, $busqueda);
        $titulo_tab = 'Esta Semana';
        break;
    default:
        $citas = obtener_citas_por_rango($pdo, $manana, $manana, $busqueda);
        $titulo_tab = 'Mañana';
        $tab = 'manana';
}

// Contar citas por pestaña
$count_manana = count(obtener_citas_por_rango($pdo, $manana, $manana));
$count_dos_dias = count(obtener_citas_por_rango($pdo, $en_dos_dias, $en_dos_dias));
$count_semana = count(obtener_citas_por_rango($pdo, $hoy, $fin_semana));

// Variables para la página
$page_title = 'Recordatorios WhatsApp';
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Recordatorios WhatsApp', 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<div class="row mb-3">
    <div class="col-md-8">
        <h2><i class="bi bi-bell"></i> Recordatorios WhatsApp</h2>
        <p class="text-muted">Envía recordatorios de citas próximas por WhatsApp</p>
    </div>
</div>

<!-- Información de cómo funciona -->
<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle"></i> 
    <strong>¿Cómo funciona?</strong> 
    Selecciona una cita y envía el recordatorio directamente al WhatsApp del paciente. 
    Al hacer clic en el botón, se abrirá WhatsApp Web con el mensaje preconfigurado listo para ser enviado.
</div>

<!-- Pestañas -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'manana' ? 'active' : '' ?>" href="?tab=manana">
            Mañana 
            <?php if ($count_manana > 0): ?>
                <span class="badge bg-primary"><?= $count_manana ?></span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'dos_dias' ? 'active' : '' ?>" href="?tab=dos_dias">
            En 2 Días 
            <?php if ($count_dos_dias > 0): ?>
                <span class="badge bg-primary"><?= $count_dos_dias ?></span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'semana' ? 'active' : '' ?>" href="?tab=semana">
            Esta Semana 
            <?php if ($count_semana > 0): ?>
                <span class="badge bg-primary"><?= $count_semana ?></span>
            <?php endif; ?>
        </a>
    </li>
</ul>

<!-- Barra de búsqueda -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="tab" value="<?= e($tab) ?>">
            
            <div class="col-md-10">
                <input type="text" class="form-control" name="busqueda" 
                       value="<?= e($busqueda) ?>"
                       placeholder="Filtrar por nombre...">
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de citas -->
<div class="card">
    <div class="card-body">
        <?php if (empty($citas)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                No hay citas programadas para <?= strtolower($titulo_tab) ?>.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Paciente</th>
                            <th>Teléfono</th>
                            <th>Fecha Cita</th>
                            <th>Hora</th>
                            <th>Motivo</th>
                            <th>Recordatorio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($citas as $cita): ?>
                        <tr>
                            <td>
                                <strong><?= e($cita['paciente_nombre']) ?></strong><br>
                                <small class="text-muted"><?= e($cita['paciente_codigo']) ?></small>
                            </td>
                            <td>
                                <i class="bi bi-telephone"></i> 
                                <?= e($cita['paciente_telefono']) ?>
                            </td>
                            <td><?= formatear_fecha($cita['fecha']) ?></td>
                            <td>
                                <strong><?= date('h:i A', strtotime($cita['hora_inicio'])) ?></strong>
                            </td>
                            <td>
                                <?= e($cita['motivo'] ?? 'Sin motivo especificado') ?>
                            </td>
                            <td>
                                <?php
                                // Formatear teléfono (eliminar espacios, guiones, etc)
                                $telefono_limpio = preg_replace('/[^0-9]/', '', $cita['paciente_telefono']);
                                
                                // Si el teléfono tiene 8 dígitos, agregar código de país Guatemala (502)
                                if (strlen($telefono_limpio) === 8) {
                                    $telefono_limpio = '502' . $telefono_limpio;
                                }
                                
                                // Crear mensaje de WhatsApp
                                $mensaje = "Hola *{$cita['paciente_nombre']}*, le recordamos que tiene una cita programada para el *" . 
                                           formatear_fecha($cita['fecha']) . "* a las *" . 
                                           date('h:i A', strtotime($cita['hora_inicio'])) . "* con el Dr. Byron Castillo.\n\n" .
                                           "Clínica Médica de la Mujer\n" .
                                           "Zona 5, Huehuetenango\n\n" .
                                           "¡Le esperamos!";
                                
                                // Codificar mensaje para URL
                                $mensaje_encoded = urlencode($mensaje);
                                
                                // Generar URL de WhatsApp
                                $whatsapp_url = "https://wa.me/{$telefono_limpio}?text={$mensaje_encoded}";
                                ?>
                                
                                <a href="<?= $whatsapp_url ?>" 
                                   class="btn btn-success btn-sm" 
                                   target="_blank"
                                   onclick="registrarRecordatorio(<?= $cita['id'] ?>)">
                                    <i class="bi bi-whatsapp"></i> Enviar Recordatorio
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <small class="text-muted">
                    <i class="bi bi-info-circle"></i> 
                    Al hacer clic se abrirá WhatsApp Web con el mensaje listo para enviar
                </small>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Registrar que se envió recordatorio (auditoría)
function registrarRecordatorio(citaId) {
    // Hacer petición AJAX para registrar en auditoría
    fetch('<?= BASE_URL ?>api/citas/registrar_recordatorio.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cita_id: citaId
        })
    }).catch(error => {
        console.log('Error al registrar recordatorio:', error);
    });
}
</script>

<?php include INCLUDES_PATH . 'footer.php'; ?>