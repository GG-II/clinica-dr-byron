<?php
/**
 * LABORATORIOS - Listado
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Lista de exámenes de laboratorio con filtros
 */

define('ACCESS_GRANTED', true);
session_start();

require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php'; 
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once MODELS_PATH . 'Examen.php';
require_once MODELS_PATH . 'Consulta.php';

// Verificar autenticación
verificar_sesion();

// Paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 20;
$offset = ($pagina - 1) * $por_pagina;

// Filtros
$filtros = [];

if (!empty($_GET['paciente_id'])) {
    $filtros['paciente_id'] = (int)$_GET['paciente_id'];
}

if (!empty($_GET['estado']) && $_GET['estado'] !== 'todos') {
    $filtros['estado'] = $_GET['estado'];
}

if (!empty($_GET['fecha_desde'])) {
    $filtros['fecha_desde'] = $_GET['fecha_desde'];
}

if (!empty($_GET['fecha_hasta'])) {
    $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
}

// Obtener exámenes
$examenes = obtener_examenes($pdo, $filtros, $por_pagina, $offset);
$total_examenes = contar_examenes($pdo, $filtros);
$total_paginas = ceil($total_examenes / $por_pagina);

// Variables para el header
$titulo = 'Laboratorios';
$breadcrumb = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard.php'],
    ['titulo' => 'Laboratorios', 'url' => '']
];

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Contenido principal -->
<div class="container-fluid mt-4">
    
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><?= $titulo ?></h2>
        </div>
        <div class="col-md-6 text-end">
            <?php if (!es_asistente()): ?>
                <a href="<?= BASE_URL ?>modules/laboratorios/nuevo.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Examen
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                
                <!-- Paciente -->
                <div class="col-md-3">
                    <label class="form-label text-muted small mb-1">
                        <i class="bi bi-person"></i> PACIENTE
                    </label>
                    <select name="paciente_id" id="filtro-paciente" class="form-select">
                        <option value="">Todos los pacientes...</option>
                    </select>
                </div>
                
                <!-- Estado -->
                <div class="col-md-2">
                    <label class="form-label text-muted small mb-1">ESTADO</label>
                    <select name="estado" class="form-select">
                        <option value="todos" <?= (!isset($_GET['estado']) || $_GET['estado'] === 'todos') ? 'selected' : '' ?>>
                            Todos
                        </option>
                        <option value="solicitado" <?= (isset($_GET['estado']) && $_GET['estado'] === 'solicitado') ? 'selected' : '' ?>>
                            Solicitado
                        </option>
                        <option value="recibido" <?= (isset($_GET['estado']) && $_GET['estado'] === 'recibido') ? 'selected' : '' ?>>
                            Recibido
                        </option>
                    </select>
                </div>
                
                <!-- Fecha desde -->
                <div class="col-md-2">
                    <label class="form-label text-muted small mb-1">
                        <i class="bi bi-calendar"></i> DESDE
                    </label>
                    <input type="date" name="fecha_desde" class="form-control" 
                           value="<?= $_GET['fecha_desde'] ?? '' ?>">
                </div>
                
                <!-- Fecha hasta -->
                <div class="col-md-2">
                    <label class="form-label text-muted small mb-1">
                        <i class="bi bi-calendar"></i> HASTA
                    </label>
                    <input type="date" name="fecha_hasta" class="form-control" 
                           value="<?= $_GET['fecha_hasta'] ?? '' ?>">
                </div>
                
                <!-- Botón filtrar -->
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    <a href="<?= BASE_URL ?>modules/laboratorios/index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
                
            </form>
        </div>
    </div>
    
    <!-- Tabla de exámenes -->
    <div class="card">
        <div class="card-body">
            
            <?php if (empty($examenes)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-clipboard2-pulse text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No hay exámenes registrados</p>
                    <?php if (!es_asistente()): ?>
                        <a href="<?= BASE_URL ?>modules/laboratorios/nuevo.php" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle"></i> Solicitar Primer Examen
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>FECHA</th>
                                <th>PACIENTE</th>
                                <th>TIPO DE EXAMEN</th>
                                <th>ESTADO</th>
                                <th>FECHA RESULTADO</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($examenes as $examen): ?>
                                <tr>
                                    <!-- Fecha solicitud -->
                                    <td>
                                        <div class="text-dark">
                                            <?= formatear_fecha($examen['fecha_solicitud']) ?>
                                        </div>
                                    </td>
                                    
                                    <!-- Paciente -->
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-2">
                                                <?= strtoupper(substr($examen['paciente_nombre'], 0, 2)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    <?= e($examen['paciente_nombre']) ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?= e($examen['paciente_codigo']) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Tipo examen -->
                                    <td>
                                        <span class="text-dark">
                                            <?= e($examen['tipo_examen']) ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Estado -->
                                    <td>
                                        <?php if ($examen['estado'] === 'solicitado'): ?>
                                            <span class="badge bg-warning text-dark">
                                                SOLICITADO
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                RECIBIDO
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Fecha resultado -->
                                    <td>
                                        <?php if ($examen['fecha_resultado']): ?>
                                            <span class="text-dark">
                                                <?= formatear_fecha($examen['fecha_resultado']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Acciones -->
                                    <td class="text-center">
                                        <a href="<?= BASE_URL ?>modules/laboratorios/ver.php?id=<?= $examen['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <?php if ($total_paginas > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                                    <a class="page-link" href="?pagina=<?= $i ?><?= !empty($_GET['paciente_id']) ? '&paciente_id=' . $_GET['paciente_id'] : '' ?><?= !empty($_GET['estado']) ? '&estado=' . $_GET['estado'] : '' ?><?= !empty($_GET['fecha_desde']) ? '&fecha_desde=' . $_GET['fecha_desde'] : '' ?><?= !empty($_GET['fecha_hasta']) ? '&fecha_hasta=' . $_GET['fecha_hasta'] : '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    
                    <p class="text-center text-muted small">
                        Mostrando <?= count($examenes) ?> de <?= $total_examenes ?> exámenes
                    </p>
                <?php endif; ?>
                
            <?php endif; ?>
            
        </div>
    </div>
    
</div>

<!-- TomSelect para filtro de paciente -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filtroPaciente = document.getElementById('filtro-paciente');
    
    if (filtroPaciente && !filtroPaciente.tomselect) {
        new TomSelect('#filtro-paciente', {
            valueField: 'id',
            labelField: 'nombre',
            searchField: ['nombre', 'codigo', 'telefono'],
            placeholder: 'Todos los pacientes...',
            load: function(query, callback) {
                if (!query.length || query.length < 2) return callback();
                
                fetch('<?= BASE_URL ?>api/pacientes/buscar.php?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        callback(data.pacientes || []);
                    })
                    .catch(() => callback());
            },
            render: {
                option: function(item, escape) {
                    return `<div>
                        <strong>${escape(item.nombre)}</strong><br>
                        <small class="text-muted">${escape(item.codigo)} • ${escape(item.telefono)}</small>
                    </div>`;
                },
                item: function(item, escape) {
                    return `<div>${escape(item.nombre)}</div>`;
                }
            }
        });
        
        // Si hay paciente seleccionado en URL, agregarlo
        <?php if (!empty($_GET['paciente_id'])): ?>
            fetch('<?= BASE_URL ?>api/pacientes/buscar.php?id=<?= $_GET['paciente_id'] ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.paciente) {
                        filtroPaciente.tomselect.addOption(data.paciente);
                        filtroPaciente.tomselect.setValue(data.paciente.id);
                    }
                });
        <?php endif; ?>
    }
});
</script>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.875rem;
}
</style>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
