<?php
/**
 * MÓDULO DE CITAS - VISTA PRINCIPAL
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Vista de calendario: Día, Semana o Mes
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

// Obtener vista (dia, semana, mes)
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'semana';
if (!in_array($vista, ['dia', 'semana', 'mes'])) {
    $vista = 'semana';
}

// Obtener fecha actual o la seleccionada
$fecha_actual = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Variables para cada vista
$fecha_inicio = '';
$fecha_fin = '';
$titulo_fecha = '';

// Configurar según vista
switch ($vista) {
    case 'dia':
        $fecha_inicio = $fecha_actual;
        $fecha_fin = $fecha_actual;
        $titulo_fecha = formatear_fecha($fecha_actual);
        $citas = obtener_citas_del_dia($pdo, $fecha_actual);
        break;
        
    case 'semana':
        // Calcular inicio de la semana (lunes)
        $fecha_obj = new DateTime($fecha_actual);
        $dia_semana = $fecha_obj->format('N'); // 1 (lunes) a 7 (domingo)
        $fecha_obj->modify('-' . ($dia_semana - 1) . ' days');
        $fecha_inicio = $fecha_obj->format('Y-m-d');
        $fecha_fin = date('Y-m-d', strtotime($fecha_inicio . ' +6 days'));
        
        $inicio = new DateTime($fecha_inicio);
        $fin = new DateTime($fecha_fin);
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $titulo_fecha = $inicio->format('d') . ' - ' . $fin->format('d') . ' de ' . 
                       $meses[(int)$inicio->format('m')] . ' ' . $inicio->format('Y');
        
        $citas = obtener_citas_semana($pdo, $fecha_inicio);
        break;
        
    case 'mes':
        // Primer y último día del mes
        $fecha_obj = new DateTime($fecha_actual);
        $fecha_inicio = $fecha_obj->format('Y-m-01');
        $fecha_fin = $fecha_obj->format('Y-m-t');
        
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $titulo_fecha = $meses[(int)$fecha_obj->format('m')] . ' ' . $fecha_obj->format('Y');
        
        $citas = obtener_citas($pdo, [
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ], 500);
        break;
}

// Obtener citas del día actual (para la tabla inferior)
$citas_hoy = obtener_citas_del_dia($pdo, date('Y-m-d'));

// Contar citas pendientes de hoy
$citas_pendientes_hoy = array_filter($citas_hoy, function($cita) {
    return $cita['estado'] === 'programada' || $cita['estado'] === 'confirmada';
});
$total_pendientes = count($citas_pendientes_hoy);

// Título de la página
$titulo_pagina = "Agenda de Citas";

// Incluir header
include '../../includes/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><?= $titulo_pagina ?></h2>
                    <p class="text-muted mb-0">Gestión del calendario de citas médicas</p>
                </div>
                <div>
                    <a href="nueva.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Nueva Cita
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Controles del calendario -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <a href="?vista=dia&fecha=<?= $fecha_actual ?>" 
                           class="btn <?= $vista === 'dia' ? 'btn-primary' : 'btn-outline-primary' ?>">
                            Día
                        </a>
                        <a href="?vista=semana&fecha=<?= $fecha_actual ?>" 
                           class="btn <?= $vista === 'semana' ? 'btn-primary' : 'btn-outline-primary' ?>">
                            Semana
                        </a>
                        <a href="?vista=mes&fecha=<?= $fecha_actual ?>" 
                           class="btn <?= $vista === 'mes' ? 'btn-primary' : 'btn-outline-primary' ?>">
                            Mes
                        </a>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group">
                        <?php
                        // Calcular fechas para navegación
                        switch ($vista) {
                            case 'dia':
                                $anterior = date('Y-m-d', strtotime($fecha_actual . ' -1 day'));
                                $siguiente = date('Y-m-d', strtotime($fecha_actual . ' +1 day'));
                                break;
                            case 'semana':
                                $anterior = date('Y-m-d', strtotime($fecha_inicio . ' -7 days'));
                                $siguiente = date('Y-m-d', strtotime($fecha_inicio . ' +7 days'));
                                break;
                            case 'mes':
                                $anterior = date('Y-m-d', strtotime($fecha_inicio . ' -1 month'));
                                $siguiente = date('Y-m-d', strtotime($fecha_inicio . ' +1 month'));
                                break;
                        }
                        ?>
                        <a href="?vista=<?= $vista ?>&fecha=<?= $anterior ?>" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-chevron-left"></i> Anterior
                        </a>
                        <a href="?vista=<?= $vista ?>&fecha=<?= date('Y-m-d') ?>" 
                           class="btn btn-outline-secondary">
                            Hoy
                        </a>
                        <a href="?vista=<?= $vista ?>&fecha=<?= $siguiente ?>" 
                           class="btn btn-outline-secondary">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <h5 class="mb-0"><?= $titulo_fecha ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- VISTA: DÍA -->
    <?php if ($vista === 'dia'): ?>
    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="row g-0 border-bottom">
                <div class="col-2 border-end p-2 bg-light">
                    <small class="text-muted fw-semibold">Hora</small>
                </div>
                <div class="col p-2 text-center">
                    <div class="fw-bold"><?= formatear_fecha($fecha_actual) ?></div>
                </div>
            </div>
            
            <div class="calendario-grid" style="max-height: 700px; overflow-y: auto;">
                <?php
                // Horas de trabajo (7am - 7pm para día completo)
                for ($hora = 7; $hora <= 19; $hora++):
                    $hora_formato = str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00';
                    
                    // Buscar citas para esta hora
                    $citas_hora = array_filter($citas, function($cita) use ($hora) {
                        $hora_inicio_cita = (int)substr($cita['hora_inicio'], 0, 2);
                        return $hora_inicio_cita === $hora;
                    });
                ?>
                <div class="row g-0 border-bottom" style="min-height: 80px;">
                    <div class="col-2 border-end p-2 bg-light">
                        <strong class="text-muted"><?= $hora_formato ?></strong>
                    </div>
                    <div class="col p-2">
                        <?php if (!empty($citas_hora)): ?>
                            <?php foreach ($citas_hora as $cita): 
                                $color = obtener_color_estado_cita($cita['estado']);
                                $opacidad = $cita['estado'] === 'cancelada' ? '0.5' : '1';
                            ?>
                            <div class="cita-bloque-dia p-2 mb-2 rounded border" 
                                 style="background-color: <?= $color ?>20; border-color: <?= $color ?> !important; cursor: pointer; opacity: <?= $opacidad ?>;"
                                 onclick="verCita(<?= $cita['id'] ?>)">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold" style="color: <?= $color ?>;">
                                            <?= date('H:i', strtotime($cita['hora_inicio'])) ?> - 
                                            <?= date('H:i', strtotime($cita['hora_fin'])) ?>
                                        </div>
                                        <div class="mt-1">
                                            <strong><?= e($cita['paciente_nombre']) ?></strong>
                                        </div>
                                        <div class="text-muted small">
                                            <?= e($cita['motivo']) ?>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge" style="background-color: <?= $color ?>;">
                                            <?= obtener_nombre_estado_cita($cita['estado']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-muted small">Sin citas</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- VISTA: SEMANA -->
    <?php if ($vista === 'semana'): ?>
    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="calendario-semana">
                <!-- Header de días -->
                <div class="row g-0 border-bottom">
                    <div class="col-1 border-end p-2">
                        <small class="text-muted">Hora</small>
                    </div>
                    <?php
                    $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                    for ($i = 0; $i < 7; $i++):
                        $fecha_dia = date('Y-m-d', strtotime($fecha_inicio . " +{$i} days"));
                        $es_hoy = $fecha_dia === date('Y-m-d');
                    ?>
                    <div class="col border-end p-2 text-center <?= $es_hoy ? 'bg-primary bg-opacity-10' : '' ?>">
                        <div class="fw-bold <?= $es_hoy ? 'text-primary' : '' ?>">
                            <?= $dias_semana[$i] ?>
                        </div>
                        <div class="<?= $es_hoy ? 'text-primary' : 'text-muted' ?>">
                            <?= date('d/m', strtotime($fecha_dia)) ?>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>

                <!-- Grid de horas -->
                <div class="calendario-grid" style="max-height: 600px; overflow-y: auto;">
                    <?php
                    // Horas de trabajo (8am - 6pm)
                    for ($hora = 8; $hora <= 18; $hora++):
                        $hora_formato = str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00';
                    ?>
                    <div class="row g-0 border-bottom" style="min-height: 60px;">
                        <div class="col-1 border-end p-2 bg-light">
                            <small class="text-muted"><?= $hora_formato ?></small>
                        </div>
                        <?php for ($dia = 0; $dia < 7; $dia++):
                            $fecha_dia = date('Y-m-d', strtotime($fecha_inicio . " +{$dia} days"));
                            
                            // Buscar citas para este día y hora
                            $citas_bloque = array_filter($citas, function($cita) use ($fecha_dia, $hora) {
                                if ($cita['fecha'] !== $fecha_dia) return false;
                                
                                $hora_inicio_cita = (int)substr($cita['hora_inicio'], 0, 2);
                                return $hora_inicio_cita === $hora;
                            });
                        ?>
                        <div class="col border-end p-1 position-relative" style="min-height: 60px;">
                            <?php foreach ($citas_bloque as $cita): 
                                $color = obtener_color_estado_cita($cita['estado']);
                                $opacidad = $cita['estado'] === 'cancelada' ? '0.3' : '1';
                            ?>
                            <div class="cita-bloque p-1 mb-1 rounded" 
                                 style="background-color: <?= $color ?>; opacity: <?= $opacidad ?>; cursor: pointer;"
                                 onclick="verCita(<?= $cita['id'] ?>)">
                                <small class="text-white d-block fw-bold">
                                    <?= date('H:i', strtotime($cita['hora_inicio'])) ?>
                                </small>
                                <small class="text-white d-block" style="font-size: 0.75rem;">
                                    <?= e(substr($cita['paciente_nombre'], 0, 20)) ?>
                                </small>
                                <?php if ($cita['motivo']): ?>
                                <small class="text-white d-block" style="font-size: 0.7rem; opacity: 0.9;">
                                    <?= e(substr($cita['motivo'], 0, 15)) ?>...
                                </small>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- VISTA: MES -->
    <?php if ($vista === 'mes'): ?>
    <div class="card mb-4">
        <div class="card-body p-0">
            <?php
            // Calcular calendario del mes
            $primer_dia = new DateTime($fecha_inicio);
            $ultimo_dia = new DateTime($fecha_fin);
            
            // Día de la semana del primer día (1=lunes, 7=domingo)
            $dia_semana_inicio = (int)$primer_dia->format('N');
            
            // Calcular desde qué lunes empezar
            $inicio_calendario = clone $primer_dia;
            $inicio_calendario->modify('-' . ($dia_semana_inicio - 1) . ' days');
            
            // Días del mes
            $dias_mes = (int)$primer_dia->format('t');
            ?>
            
            <!-- Header días de la semana -->
            <div class="row g-0 border-bottom bg-light">
                <?php
                $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                foreach ($dias_semana as $dia):
                ?>
                <div class="col border-end p-2 text-center">
                    <strong class="text-muted small"><?= $dia ?></strong>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Grid de días -->
            <div style="max-height: 700px; overflow-y: auto;">
                <?php
                $fecha_celda = clone $inicio_calendario;
                $semanas = 0;
                
                while ($semanas < 6 && $fecha_celda <= $ultimo_dia->modify('+7 days')):
                    $semanas++;
                ?>
                <div class="row g-0 border-bottom" style="min-height: 120px;">
                    <?php for ($i = 0; $i < 7; $i++):
                        $fecha_str = $fecha_celda->format('Y-m-d');
                        $es_mes_actual = $fecha_celda->format('m') === $primer_dia->format('m');
                        $es_hoy = $fecha_str === date('Y-m-d');
                        
                        // Citas del día
                        $citas_dia = array_filter($citas, function($cita) use ($fecha_str) {
                            return $cita['fecha'] === $fecha_str;
                        });
                        $num_citas = count($citas_dia);
                    ?>
                    <div class="col border-end p-2 <?= !$es_mes_actual ? 'bg-light' : '' ?> <?= $es_hoy ? 'border-primary border-2' : '' ?>" 
                         style="min-height: 120px;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="<?= $es_hoy ? 'badge bg-primary' : 'text-muted' ?> <?= !$es_mes_actual ? 'opacity-50' : '' ?>">
                                <?= $fecha_celda->format('d') ?>
                            </span>
                            <?php if ($num_citas > 0): ?>
                            <span class="badge bg-secondary"><?= $num_citas ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="citas-mes">
                            <?php 
                            $mostradas = 0;
                            foreach ($citas_dia as $cita): 
                                if ($mostradas >= 3) break; // Máximo 3 citas visibles
                                $color = obtener_color_estado_cita($cita['estado']);
                                $mostradas++;
                            ?>
                            <div class="cita-mes mb-1 p-1 rounded" 
                                 style="background-color: <?= $color ?>20; border-left: 3px solid <?= $color ?>; cursor: pointer; font-size: 0.75rem;"
                                 onclick="verCita(<?= $cita['id'] ?>)">
                                <div class="fw-semibold" style="color: <?= $color ?>;">
                                    <?= date('H:i', strtotime($cita['hora_inicio'])) ?>
                                </div>
                                <div class="text-truncate" style="max-width: 100%;">
                                    <?= e(substr($cita['paciente_nombre'], 0, 15)) ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if ($num_citas > 3): ?>
                            <div class="text-muted small text-center">
                                +<?= $num_citas - 3 ?> más
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                        $fecha_celda->modify('+1 day');
                    endfor;
                    ?>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Citas de Hoy (en todas las vistas) -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Citas de Hoy</h5>
            <?php if ($total_pendientes > 0): ?>
            <span class="badge bg-warning text-dark">
                <?= $total_pendientes ?> Pendiente<?= $total_pendientes > 1 ? 's' : '' ?>
            </span>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?php if (empty($citas_hoy)): ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">No hay citas programadas para hoy</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="100">Hora</th>
                            <th>Paciente</th>
                            <th>Motivo</th>
                            <th width="120">Estado</th>
                            <th width="150" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($citas_hoy as $cita): 
                            $clase_fila = '';
                            if ($cita['estado'] === 'atendida') {
                                $clase_fila = 'table-secondary';
                            } elseif ($cita['estado'] === 'cancelada') {
                                $clase_fila = 'table-danger';
                            }
                        ?>
                        <tr class="<?= $clase_fila ?>">
                            <td>
                                <strong><?= date('H:i', strtotime($cita['hora_inicio'])) ?></strong>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $cita['paciente_id'] ?>" 
                                   class="text-decoration-none">
                                    <?= e($cita['paciente_nombre']) ?>
                                </a>
                                <br>
                                <small class="text-muted"><?= e($cita['paciente_codigo']) ?></small>
                            </td>
                            <td><?= e($cita['motivo'] ?? '-') ?></td>
                            <td>
                                <?php
                                $badge_class = [
                                    'programada' => 'bg-primary',
                                    'confirmada' => 'bg-info',
                                    'atendida' => 'bg-success',
                                    'cancelada' => 'bg-danger'
                                ];
                                $clase = $badge_class[$cita['estado']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $clase ?>">
                                    <?= obtener_nombre_estado_cita($cita['estado']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="ver.php?id=<?= $cita['id'] ?>" 
                                       class="btn btn-outline-info" 
                                       title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <?php if ($cita['estado'] !== 'atendida' && $cita['estado'] !== 'cancelada'): ?>
                                    <a href="editar.php?id=<?= $cita['id'] ?>" 
                                       class="btn btn-outline-warning" 
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <button type="button" 
                                            class="btn btn-outline-success" 
                                            onclick="atenderCita(<?= $cita['id'] ?>)"
                                            title="Atender">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Ver detalles de cita
function verCita(id) {
    window.location.href = 'ver.php?id=' + id;
}

// Atender cita (cambiar estado a atendida y redirigir a crear consulta)
function atenderCita(citaId) {
    if (confirm('¿Marcar esta cita como atendida y crear consulta?')) {
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

// Scroll automático a hora actual (solo vista día y semana)
document.addEventListener('DOMContentLoaded', function() {
    const vista = '<?= $vista ?>';
    const horaActual = new Date().getHours();
    const esHoy = '<?= date('Y-m-d') ?>' === '<?= $fecha_actual ?>';
    
    if ((vista === 'dia' || vista === 'semana') && esHoy && horaActual >= 7 && horaActual <= 19) {
        const calendarioGrid = document.querySelector('.calendario-grid');
        if (calendarioGrid) {
            const offsetHora = (horaActual - (vista === 'dia' ? 7 : 8)) * (vista === 'dia' ? 80 : 60);
            calendarioGrid.scrollTop = offsetHora;
        }
    }
});
</script>

<style>
.cita-bloque {
    font-size: 0.85rem;
    transition: all 0.2s;
}

.cita-bloque:hover,
.cita-bloque-dia:hover,
.cita-mes:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.calendario-semana .col {
    min-width: 100px;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.cita-bloque-dia {
    transition: all 0.2s;
}

.cita-mes {
    transition: all 0.2s;
}
</style>

<?php include '../../includes/footer.php'; ?>