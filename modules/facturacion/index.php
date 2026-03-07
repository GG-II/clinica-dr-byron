<?php
/**
 * LISTADO DE FACTURAS
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Lista de recibos/facturas con filtros y búsqueda
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
require_once MODELS_PATH . 'Factura.php';
require_once MODELS_PATH . 'Consulta.php';

// Proteger la ruta
proteger_ruta();

// Construir filtros
$filtros = [];

if (!empty($_GET['estado'])) {
    $filtros['estado'] = $_GET['estado'];
}

if (!empty($_GET['fecha_inicio'])) {
    $filtros['fecha_inicio'] = $_GET['fecha_inicio'];
}

if (!empty($_GET['fecha_fin'])) {
    $filtros['fecha_fin'] = $_GET['fecha_fin'];
}

if (!empty($_GET['busqueda'])) {
    $filtros['busqueda'] = trim($_GET['busqueda']);
}

// Paginación
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 50;
$offset = ($page - 1) * $per_page;

// Obtener facturas
$facturas = obtener_facturas($pdo, $filtros, $per_page, $offset);
$total_facturas = contar_facturas($pdo, $filtros);
$total_pages = ceil($total_facturas / $per_page);

// Obtener ingresos del período si hay filtro de fecha
$ingresos = null;
if (!empty($filtros['fecha_inicio'])) {
    $ingresos = obtener_ingresos_periodo(
        $pdo, 
        $filtros['fecha_inicio'], 
        $filtros['fecha_fin'] ?? date('Y-m-d')
    );
}

// Variables para la página
$page_title = 'Facturación';
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Facturación', 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<div class="row mb-3">
    <div class="col-md-6">
        <h2><i class="bi bi-receipt"></i> Facturación</h2>
        <p class="text-muted">Gestión de recibos y cobros</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= BASE_URL ?>modules/consultas/" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Crear desde Consulta
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="busqueda" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="busqueda" name="busqueda" 
                       value="<?= e($_GET['busqueda'] ?? '') ?>"
                       placeholder="Paciente, número, concepto...">
            </div>
            
            <div class="col-md-2">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="">Todos</option>
                    <option value="pagado" <?= ($filtros['estado'] ?? '') === 'pagado' ? 'selected' : '' ?>>Pagado</option>
                    <option value="pendiente" <?= ($filtros['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="anulado" <?= ($filtros['estado'] ?? '') === 'anulado' ? 'selected' : '' ?>>Anulado</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                       value="<?= e($_GET['fecha_inicio'] ?? '') ?>">
            </div>
            
            <div class="col-md-2">
                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                       value="<?= e($_GET['fecha_fin'] ?? '') ?>">
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Estadísticas del período (si hay filtro de fecha) -->
<?php if ($ingresos): ?>
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="card-title">Total Ingresado</h6>
                <h3>Q <?= number_format($ingresos['total_ingresado'], 2) ?></h3>
                <small><?= $ingresos['total_facturas'] ?> facturas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h6 class="card-title">Pendiente</h6>
                <h3>Q <?= number_format($ingresos['total_pendiente'], 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h6 class="card-title">Anulado</h6>
                <h3>Q <?= number_format($ingresos['total_anulado'], 2) ?></h3>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Tabla de facturas -->
<div class="card">
    <div class="card-body">
        <?php if (empty($facturas)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No se encontraron facturas con los filtros seleccionados.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Recibo #</th>
                            <th>Fecha</th>
                            <th>Paciente</th>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>Forma de Pago</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($facturas as $factura): ?>
                        <tr class="<?= $factura['estado'] === 'anulado' ? 'table-danger' : '' ?>">
                            <td>
                                <strong>#<?= str_pad($factura['numero_correlativo'], 4, '0', STR_PAD_LEFT) ?></strong>
                            </td>
                            <td><?= formatear_fecha($factura['fecha']) ?></td>
                            <td>
                                <strong><?= e($factura['paciente_nombre']) ?></strong><br>
                                <small class="text-muted"><?= e($factura['paciente_codigo']) ?></small>
                            </td>
                            <td>
                                <?= e($factura['concepto']) ?>
                                <?php if ($factura['tipo_consulta']): ?>
                                    <br>
                                    <span class="badge bg-<?= obtener_color_tipo_consulta($factura['tipo_consulta']) ?>">
                                        <?= obtener_nombre_tipo_consulta($factura['tipo_consulta']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><strong>Q <?= number_format($factura['monto'], 2) ?></strong></td>
                            <td>
                                <?php
                                $iconos = [
                                    'efectivo' => 'bi-cash',
                                    'tarjeta' => 'bi-credit-card',
                                    'transferencia' => 'bi-arrow-left-right'
                                ];
                                ?>
                                <i class="bi <?= $iconos[$factura['forma_pago']] ?? 'bi-cash' ?>"></i>
                                <?= ucfirst($factura['forma_pago']) ?>
                            </td>
                            <td>
                                <?php
                                $badges = [
                                    'pagado' => 'success',
                                    'pendiente' => 'warning',
                                    'anulado' => 'danger'
                                ];
                                ?>
                                <span class="badge bg-<?= $badges[$factura['estado']] ?? 'secondary' ?>">
                                    <?= ucfirst($factura['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="ver.php?id=<?= $factura['id'] ?>" 
                                       class="btn btn-outline-primary" 
                                       title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <?php if ($factura['estado'] !== 'anulado'): ?>
                                        <a href="pdf.php?id=<?= $factura['id'] ?>" 
                                           class="btn btn-outline-danger" 
                                           target="_blank"
                                           title="Generar PDF">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                        
                                        <?php if (puede_eliminar()): ?>
                                            <a href="anular.php?id=<?= $factura['id'] ?>" 
                                               class="btn btn-outline-danger"
                                               onclick="return confirm('¿Anular este recibo?')"
                                               title="Anular">
                                                <i class="bi bi-x-circle"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Paginación de facturas">
                <ul class="pagination justify-content-center">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= !empty($filtros) ? '&' . http_build_query($filtros) : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include INCLUDES_PATH . 'footer.php'; ?>