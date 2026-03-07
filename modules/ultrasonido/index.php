<?php
/**
 * LISTADO DE ULTRASONIDOS
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Lista todos los informes de ultrasonido con filtros
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
require_once MODELS_PATH . 'Ultrasonido.php';
require_once MODELS_PATH . 'Paciente.php';

// Proteger la ruta
proteger_ruta();

// Configuración de paginación
$registros_por_pagina = 20;
$pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Capturar filtros
$filtros = [];

if (!empty($_GET['paciente_id'])) {
    $filtros['paciente_id'] = (int)$_GET['paciente_id'];
}

if (!empty($_GET['fecha_desde'])) {
    $filtros['fecha_desde'] = $_GET['fecha_desde'];
}

if (!empty($_GET['fecha_hasta'])) {
    $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
}

// Obtener ultrasonidos
$ultrasonidos = obtener_ultrasonidos($pdo, $filtros, $registros_por_pagina, $offset);
$total_ultrasonidos = contar_ultrasonidos($pdo, $filtros);
$total_paginas = ceil($total_ultrasonidos / $registros_por_pagina);

// Obtener nombre del paciente si hay filtro
$nombre_paciente = null;
if (!empty($filtros['paciente_id'])) {
    $paciente = obtener_paciente_por_id($pdo, $filtros['paciente_id']);
    $nombre_paciente = $paciente ? $paciente['nombre'] : null;
}

// Variables para la página
$page_title = 'Ultrasonidos';
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Ultrasonidos', 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="fecha_desde" class="form-label">Desde</label>
                <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                       value="<?= isset($_GET['fecha_desde']) ? e($_GET['fecha_desde']) : '' ?>">
            </div>
            
            <div class="col-md-4">
                <label for="fecha_hasta" class="form-label">Hasta</label>
                <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                       value="<?= isset($_GET['fecha_hasta']) ? e($_GET['fecha_hasta']) : '' ?>">
            </div>
            
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Resumen de filtros activos -->
<?php if (!empty($filtros)): ?>
    <div class="alert alert-info d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-funnel"></i> <strong>Filtros activos:</strong>
            <?php if ($nombre_paciente): ?>
                <span class="badge bg-primary">Paciente: <?= e($nombre_paciente) ?></span>
            <?php endif; ?>
            <?php if (!empty($filtros['fecha_desde'])): ?>
                <span class="badge bg-primary">Desde: <?= formatear_fecha($filtros['fecha_desde']) ?></span>
            <?php endif; ?>
            <?php if (!empty($filtros['fecha_hasta'])): ?>
                <span class="badge bg-primary">Hasta: <?= formatear_fecha($filtros['fecha_hasta']) ?></span>
            <?php endif; ?>
        </div>
        <a href="index.php" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-x"></i> Quitar filtros
        </a>
    </div>
<?php endif; ?>

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary mb-0"><?= number_format($total_ultrasonidos) ?></h3>
                <p class="text-muted mb-0 small">Ultrasonidos <?= !empty($filtros) ? 'Filtrados' : 'Totales' ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success mb-0"><?= count($ultrasonidos) ?></h3>
                <p class="text-muted mb-0 small">En esta página</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info mb-0"><?= $total_paginas ?></h3>
                <p class="text-muted mb-0 small">Páginas totales</p>
            </div>
        </div>
    </div>
    
</div>

<!-- Tabla de ultrasonidos -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-heart-pulse"></i> Lista de Ultrasonidos
        </h5>
        <?php if (puede_crear()): ?>
            <a href="<?= BASE_URL ?>modules/consultas/" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Crear desde Consulta
            </a>
        <?php endif; ?>
    </div>
    
    <div class="card-body p-0">
        <?php if (empty($ultrasonidos)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">
                    <?= !empty($filtros) ? 'No se encontraron ultrasonidos con los filtros aplicados' : 'No hay ultrasonidos registrados' ?>
                </p>
                <?php if (puede_crear()): ?>
                    <a href="nueva.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Crear Primer Ultrasonido
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Paciente</th>
                            <th>Edad Gestacional</th>
                            <th>Peso Estimado</th>
                            <th>Médico</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultrasonidos as $ultra): ?>
                            <tr>
                                <td>
                                    <strong><?= formatear_fecha($ultra['fecha']) ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $ultra['paciente_id'] ?>" 
                                           class="text-decoration-none">
                                            <?= e($ultra['paciente_nombre']) ?>
                                        </a>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-person-badge"></i> <?= e($ultra['paciente_codigo']) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if (!empty($ultra['edad_gestacional'])): ?>
                                        <span class="badge bg-info"><?= e($ultra['edad_gestacional']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($ultra['peso_estimado'])): ?>
                                        <strong><?= number_format($ultra['peso_estimado'], 0) ?> g</strong>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        Dr. <?= e($ultra['medico_nombre'] ?? 'No especificado') ?>
                                    </small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="ver.php?id=<?= $ultra['id'] ?>" 
                                           class="btn btn-outline-primary" 
                                           title="Ver informe">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (puede_editar()): ?>
                                            <a href="editar.php?id=<?= $ultra['id'] ?>" 
                                               class="btn btn-outline-warning" 
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="pdf.php?id=<?= $ultra['id'] ?>" 
                                           class="btn btn-outline-success" 
                                           target="_blank"
                                           title="Descargar PDF">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($total_paginas > 1): ?>
        <div class="card-footer">
            <nav aria-label="Navegación de ultrasonidos">
                <ul class="pagination pagination-sm mb-0 justify-content-center">
                    <!-- Botón anterior -->
                    <li class="page-item <?= $pagina_actual == 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina_actual - 1 ?><?= http_build_query(array_diff_key($_GET, ['pagina' => ''])) ? '&' . http_build_query(array_diff_key($_GET, ['pagina' => ''])) : '' ?>">
                            <i class="bi bi-chevron-left"></i> Anterior
                        </a>
                    </li>
                    
                    <!-- Números de página -->
                    <?php
                    $rango = 2;
                    $inicio = max(1, $pagina_actual - $rango);
                    $fin = min($total_paginas, $pagina_actual + $rango);
                    
                    if ($inicio > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?pagina=1<?= http_build_query(array_diff_key($_GET, ['pagina' => ''])) ? '&' . http_build_query(array_diff_key($_GET, ['pagina' => ''])) : '' ?>">1</a>
                        </li>
                        <?php if ($inicio > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                        <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $i ?><?= http_build_query(array_diff_key($_GET, ['pagina' => ''])) ? '&' . http_build_query(array_diff_key($_GET, ['pagina' => ''])) : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($fin < $total_paginas): ?>
                        <?php if ($fin < $total_paginas - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?pagina=<?= $total_paginas ?><?= http_build_query(array_diff_key($_GET, ['pagina' => ''])) ? '&' . http_build_query(array_diff_key($_GET, ['pagina' => ''])) : '' ?>">
                                <?= $total_paginas ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Botón siguiente -->
                    <li class="page-item <?= $pagina_actual == $total_paginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina_actual + 1 ?><?= http_build_query(array_diff_key($_GET, ['pagina' => ''])) ? '&' . http_build_query(array_diff_key($_GET, ['pagina' => ''])) : '' ?>">
                            Siguiente <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php include INCLUDES_PATH . 'footer.php'; ?>