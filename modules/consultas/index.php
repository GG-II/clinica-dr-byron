<?php
/**
 * MÓDULO DE CONSULTAS - LISTA
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Lista de consultas médicas con filtros
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

// Filtros
$filtros = [];
$filtro_texto = '';

if (!empty($_GET['buscar'])) {
    $filtro_texto = trim($_GET['buscar']);
}

if (!empty($_GET['tipo'])) {
    $filtros['tipo_consulta'] = $_GET['tipo'];
}

if (!empty($_GET['fecha_desde'])) {
    $filtros['fecha_desde'] = $_GET['fecha_desde'];
}

if (!empty($_GET['fecha_hasta'])) {
    $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
}

if (!empty($_GET['paciente_id'])) {
    $filtros['paciente_id'] = (int)$_GET['paciente_id'];
}

// Paginación
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 25;
$offset = ($pagina_actual - 1) * $por_pagina;

// Obtener consultas
$consultas = obtener_consultas($pdo, $filtros, $por_pagina, $offset);

// Si hay búsqueda por texto, filtrar en PHP (simple)
if ($filtro_texto) {
    $consultas = array_filter($consultas, function($consulta) use ($filtro_texto) {
        $texto_lower = strtolower($filtro_texto);
        return stripos($consulta['paciente_nombre'], $filtro_texto) !== false ||
               stripos($consulta['paciente_codigo'], $filtro_texto) !== false ||
               stripos($consulta['motivo_consulta'], $filtro_texto) !== false;
    });
}

// Contar total
$total_consultas = count($consultas);

// Obtener consultas de hoy
$consultas_hoy = obtener_consultas($pdo, [
    'fecha_desde' => date('Y-m-d'),
    'fecha_hasta' => date('Y-m-d')
], 100);

// Título de la página
$titulo_pagina = "Consultas Médicas";

// Incluir header
include '../../includes/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><?= $titulo_pagina ?></h2>
                    <p class="text-muted mb-0">Registro de consultas médicas realizadas</p>
                </div>
                <div>
                    <a href="nueva.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Nueva Consulta
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-calendar-check text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Hoy</h6>
                            <h3 class="mb-0"><?= count($consultas_hoy) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clipboard-pulse text-info" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Consultas</h6>
                            <h3 class="mb-0"><?= $total_consultas ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="buscar" class="form-label">Buscar</label>
                    <input type="text" 
                           class="form-control" 
                           id="buscar" 
                           name="buscar" 
                           placeholder="Nombre, código o motivo..."
                           value="<?= e($filtro_texto) ?>">
                </div>
                <div class="col-md-2">
                    <label for="tipo" class="form-label">Tipo</label>
                    <select class="form-select" id="tipo" name="tipo">
                        <option value="">Todos</option>
                        <option value="primera_vez" <?= ($filtros['tipo_consulta'] ?? '') === 'primera_vez' ? 'selected' : '' ?>>
                            Primera Vez
                        </option>
                        <option value="control" <?= ($filtros['tipo_consulta'] ?? '') === 'control' ? 'selected' : '' ?>>
                            Control
                        </option>
                        <option value="urgencia" <?= ($filtros['tipo_consulta'] ?? '') === 'urgencia' ? 'selected' : '' ?>>
                            Urgencia
                        </option>
                        <option value="procedimiento" <?= ($filtros['tipo_consulta'] ?? '') === 'procedimiento' ? 'selected' : '' ?>>
                            Procedimiento
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date" 
                           class="form-control" 
                           id="fecha_desde" 
                           name="fecha_desde"
                           value="<?= $filtros['fecha_desde'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date" 
                           class="form-control" 
                           id="fecha_hasta" 
                           name="fecha_hasta"
                           value="<?= $filtros['fecha_hasta'] ?? '' ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </form>
            <?php if (!empty($filtros) || $filtro_texto): ?>
            <div class="mt-2">
                <a href="index.php" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Limpiar filtros
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabla de consultas -->
    <div class="card">
        <div class="card-body p-0">
            <?php if (empty($consultas)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">No hay consultas registradas</p>
                <?php if (!empty($filtros) || $filtro_texto): ?>
                <p class="text-muted">Intenta con otros filtros de búsqueda</p>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="120">Fecha</th>
                            <th>Paciente</th>
                            <th width="130">Tipo</th>
                            <th>Motivo</th>
                            <th>Médico</th>
                            <th width="150" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($consultas as $consulta): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= date('d/m/Y', strtotime($consulta['fecha'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($consulta['fecha'])) ?></small>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $consulta['paciente_id'] ?>" 
                                   class="text-decoration-none">
                                    <?= e($consulta['paciente_nombre']) ?>
                                </a>
                                <br>
                                <small class="text-muted"><?= e($consulta['paciente_codigo']) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-<?= obtener_color_tipo_consulta($consulta['tipo_consulta']) ?>">
                                    <?= obtener_nombre_tipo_consulta($consulta['tipo_consulta']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 300px;">
                                    <?= e($consulta['motivo_consulta']) ?>
                                </div>
                            </td>
                            <td>
                                <small>Dr. <?= e($consulta['medico_nombre']) ?></small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="ver.php?id=<?= $consulta['id'] ?>" 
                                       class="btn btn-outline-info" 
                                       title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="editar.php?id=<?= $consulta['id'] ?>" 
                                       class="btn btn-outline-warning" 
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>modules/pacientes/ver.php?id=<?= $consulta['paciente_id'] ?>" 
                                       class="btn btn-outline-primary" 
                                       title="Ver expediente">
                                        <i class="bi bi-person-lines-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación simple -->
            <?php if ($total_consultas > $por_pagina): ?>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Mostrando <?= count($consultas) ?> de <?= $total_consultas ?> consultas
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php if ($pagina_actual > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=<?= $pagina_actual - 1 ?><?= $filtro_texto ? '&buscar=' . urlencode($filtro_texto) : '' ?>">
                                    Anterior
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <li class="page-item active">
                                <span class="page-link"><?= $pagina_actual ?></span>
                            </li>
                            
                            <?php if (count($consultas) === $por_pagina): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=<?= $pagina_actual + 1 ?><?= $filtro_texto ? '&buscar=' . urlencode($filtro_texto) : '' ?>">
                                    Siguiente
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>

<?php include '../../includes/footer.php'; ?>