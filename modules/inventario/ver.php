<?php
/**
 * INVENTARIO - Ver Producto
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Vista detallada de producto con historial de movimientos
 */

define('ACCESS_GRANTED', true);
session_start();

require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once MODELS_PATH . 'Inventario.php';

// Verificar autenticación
verificar_sesion();

// Obtener ID del producto
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['mensaje_error'] = 'ID de producto inválido';
    header('Location: ' . BASE_URL . 'modules/inventario/index.php');
    exit;
}

// Obtener datos del producto
$producto = obtener_producto_completo($pdo, $id);

if (!$producto) {
    $_SESSION['mensaje_error'] = 'Producto no encontrado';
    header('Location: ' . BASE_URL . 'modules/inventario/index.php');
    exit;
}

// Obtener historial de movimientos
$movimientos = obtener_movimientos_producto($pdo, $id, 100);

// Calcular stock bajo
$stock_bajo = $producto['cantidad_actual'] <= $producto['stock_minimo'];

// Variables para el header
$titulo = 'Detalle de Producto';
$breadcrumb = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard.php'],
    ['titulo' => 'Inventario', 'url' => BASE_URL . 'modules/inventario/index.php'],
    ['titulo' => 'Ver', 'url' => '']
];

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Contenido principal -->
<div class="container-fluid mt-4">
    
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="<?= BASE_URL ?>modules/inventario/index.php" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <h2><?= $titulo ?></h2>
            <p class="text-muted">Inventario / Ver</p>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-success me-2" 
                    onclick="abrirModalMovimiento(<?= $id ?>, '<?= e($producto['nombre']) ?>', <?= $producto['cantidad_actual'] ?>, '<?= e($producto['unidad']) ?>')">
                <i class="bi bi-arrow-left-right"></i> Registrar Movimiento
            </button>
            
            <?php if (!es_asistente()): ?>
                <a href="<?= BASE_URL ?>modules/inventario/editar.php?id=<?= $id ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Mensajes flash -->
    <?php mostrar_mensajes(); ?>
    
    <div class="row">
        
        <!-- Columna principal -->
        <div class="col-lg-8">
            
            <!-- Información del Producto -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam"></i>
                        Información del Producto
                    </h5>
                </div>
                <div class="card-body">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="text-muted small">NOMBRE DEL PRODUCTO</label>
                            <h4 class="mb-0">
                                <?= e($producto['nombre']) ?>
                            </h4>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">UNIDAD</label>
                            <p class="mb-0 fs-5">
                                <?= e($producto['unidad']) ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted small">STOCK ACTUAL</label>
                            <p class="mb-0">
                                <span class="badge <?= $stock_bajo ? 'bg-danger' : 'bg-success' ?>" 
                                      style="font-size: 1.5rem; padding: 0.5rem 1rem;">
                                    <?= $producto['cantidad_actual'] ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">STOCK MÍNIMO</label>
                            <p class="mb-0 fs-5">
                                <?= $producto['stock_minimo'] ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">ESTADO</label>
                            <p class="mb-0">
                                <?php if ($stock_bajo): ?>
                                    <span class="badge bg-danger fs-6">STOCK BAJO</span>
                                <?php else: ?>
                                    <span class="badge bg-success fs-6">OK</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($stock_bajo): ?>
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Advertencia:</strong> El stock actual está por debajo del mínimo establecido. 
                            Se recomienda reabastecer este producto.
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
            
            <!-- Historial de Movimientos -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history"></i>
                        Historial de Movimientos
                    </h5>
                </div>
                <div class="card-body">
                    
                    <?php if (empty($movimientos)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No hay movimientos registrados para este producto</p>
                        </div>
                    <?php else: ?>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>FECHA</th>
                                        <th>TIPO</th>
                                        <th>CANTIDAD</th>
                                        <th>MOTIVO</th>
                                        <th>USUARIO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($movimientos as $mov): ?>
                                        <tr>
                                            <!-- Fecha -->
                                            <td>
                                                <div class="text-dark">
                                                    <?= date('d/m/Y', strtotime($mov['fecha'])) ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?= date('H:i', strtotime($mov['fecha'])) ?>
                                                </small>
                                            </td>
                                            
                                            <!-- Tipo -->
                                            <td>
                                                <span class="badge bg-<?= obtener_color_tipo_movimiento($mov['tipo']) ?>">
                                                    <?= obtener_nombre_tipo_movimiento($mov['tipo']) ?>
                                                </span>
                                            </td>
                                            
                                            <!-- Cantidad -->
                                            <td>
                                                <span class="fw-bold <?= $mov['tipo'] === 'entrada' ? 'text-success' : 'text-danger' ?>">
                                                    <?= $mov['tipo'] === 'entrada' ? '+' : '-' ?><?= $mov['cantidad'] ?>
                                                </span>
                                            </td>
                                            
                                            <!-- Motivo -->
                                            <td>
                                                <?php if ($mov['motivo']): ?>
                                                    <span class="text-muted">
                                                        <?= e($mov['motivo']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted fst-italic">Sin motivo</span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <!-- Usuario -->
                                            <td>
                                                <small class="text-muted">
                                                    <?= e($mov['usuario_nombre']) ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <p class="text-muted small text-center mb-0 mt-3">
                            Mostrando últimos <?= count($movimientos) ?> movimientos
                        </p>
                        
                    <?php endif; ?>
                    
                </div>
            </div>
            
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            
            <!-- Estadísticas rápidas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up"></i>
                        Estadísticas
                    </h6>
                </div>
                <div class="card-body">
                    
                    <div class="mb-3">
                        <small class="text-muted">TOTAL MOVIMIENTOS</small>
                        <p class="mb-0 h4">
                            <?= count($movimientos) ?>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">FECHA CREACIÓN</small>
                        <p class="mb-0">
                            <?= formatear_fecha($producto['created_at']) ?>
                        </p>
                    </div>
                    
                    <?php if ($producto['updated_at'] !== $producto['created_at']): ?>
                        <div class="mb-0">
                            <small class="text-muted">ÚLTIMA ACTUALIZACIÓN</small>
                            <p class="mb-0">
                                <?= formatear_fecha($producto['updated_at']) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
            
            <!-- Acciones Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning"></i>
                        Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    
                    <button type="button" class="btn btn-success w-100 mb-2" 
                            onclick="abrirModalMovimiento(<?= $id ?>, '<?= e($producto['nombre']) ?>', <?= $producto['cantidad_actual'] ?>, '<?= e($producto['unidad']) ?>')">
                        <i class="bi bi-arrow-left-right"></i> Registrar Movimiento
                    </button>
                    
                    <?php if (!es_asistente()): ?>
                        <a href="<?= BASE_URL ?>modules/inventario/editar.php?id=<?= $id ?>" 
                           class="btn btn-warning w-100 mb-2">
                            <i class="bi bi-pencil"></i> Editar Producto
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?= BASE_URL ?>modules/inventario/nuevo.php" 
                       class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-plus-circle"></i> Nuevo Producto
                    </a>
                    
                    <a href="<?= BASE_URL ?>modules/inventario/index.php" 
                       class="btn btn-outline-secondary w-100">
                        <i class="bi bi-list-ul"></i> Ver Todos los Productos
                    </a>
                    
                </div>
            </div>
            
        </div>
        
    </div>
    
</div>

<!-- MODAL: Registrar Movimiento (mismo que en index.php) -->
<div class="modal fade" id="modalMovimiento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Registrar Movimiento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                <form id="form-movimiento">
                    <input type="hidden" id="movimiento-producto-id" name="producto_id">
                    
                    <!-- Info del producto -->
                    <div class="text-center mb-3 p-3 bg-light rounded">
                        <p class="mb-1 text-muted small">Producto</p>
                        <h5 class="mb-1" id="movimiento-producto-nombre">-</h5>
                        <p class="mb-0">
                            <i class="bi bi-info-circle"></i>
                            Stock actual: <strong id="movimiento-stock-actual">0</strong> 
                            <span id="movimiento-unidad">unidades</span>
                        </p>
                    </div>
                    
                    <!-- Tipo de movimiento -->
                    <div class="mb-3">
                        <label class="form-label text-muted small">TIPO DE MOVIMIENTO</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="tipo" id="tipo-entrada" value="entrada" checked>
                            <label class="btn btn-outline-success" for="tipo-entrada">
                                <i class="bi bi-arrow-down-circle"></i> Entrada
                            </label>
                            
                            <input type="radio" class="btn-check" name="tipo" id="tipo-salida" value="salida">
                            <label class="btn btn-outline-warning" for="tipo-salida">
                                <i class="bi bi-arrow-up-circle"></i> Salida
                            </label>
                            
                            <input type="radio" class="btn-check" name="tipo" id="tipo-venta" value="venta">
                            <label class="btn btn-outline-info" for="tipo-venta">
                                <i class="bi bi-cart"></i> Venta
                            </label>
                        </div>
                    </div>
                    
                    <!-- Cantidad -->
                    <div class="mb-3">
                        <label for="movimiento-cantidad" class="form-label text-muted small">
                            CANTIDAD
                        </label>
                        <input type="number" class="form-control form-control-lg text-center" 
                               id="movimiento-cantidad" name="cantidad" 
                               min="1" value="0" required
                               oninput="calcularStockResultante()">
                    </div>
                    
                    <!-- Motivo -->
                    <div class="mb-3">
                        <label for="movimiento-motivo" class="form-label text-muted small">
                            MOTIVO
                        </label>
                        <textarea class="form-control" id="movimiento-motivo" name="motivo" 
                                  rows="2" maxlength="255"
                                  placeholder="Ej: Compra a proveedor, uso en consulta..."></textarea>
                    </div>
                    
                    <!-- Stock resultante -->
                    <div class="alert alert-info mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Stock resultante:</span>
                            <strong class="h5 mb-0" id="stock-resultante">0</strong>
                        </div>
                    </div>
                    
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="registrarMovimiento()">
                    <i class="bi bi-check-circle"></i> Registrar Movimiento
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales del modal
let stockActual = 0;

// Abrir modal de movimiento
function abrirModalMovimiento(productoId, productoNombre, stockActualParam, unidad) {
    stockActual = stockActualParam;
    
    document.getElementById('movimiento-producto-id').value = productoId;
    document.getElementById('movimiento-producto-nombre').textContent = productoNombre;
    document.getElementById('movimiento-stock-actual').textContent = stockActual;
    document.getElementById('movimiento-unidad').textContent = unidad;
    
    document.getElementById('form-movimiento').reset();
    document.getElementById('tipo-entrada').checked = true;
    document.getElementById('movimiento-cantidad').value = 0;
    
    calcularStockResultante();
    
    const modal = new bootstrap.Modal(document.getElementById('modalMovimiento'));
    modal.show();
}

// Calcular stock resultante
function calcularStockResultante() {
    const tipo = document.querySelector('input[name="tipo"]:checked').value;
    const cantidad = parseInt(document.getElementById('movimiento-cantidad').value) || 0;
    
    let stockResultante = stockActual;
    
    if (tipo === 'entrada') {
        stockResultante += cantidad;
    } else if (tipo === 'salida' || tipo === 'venta') {
        stockResultante -= cantidad;
    }
    
    document.getElementById('stock-resultante').textContent = stockResultante;
    
    const alertElement = document.querySelector('.alert-info');
    if (stockResultante < 0) {
        alertElement.classList.remove('alert-info');
        alertElement.classList.add('alert-danger');
    } else {
        alertElement.classList.remove('alert-danger');
        alertElement.classList.add('alert-info');
    }
}

// Listeners
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[name="tipo"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', calcularStockResultante);
    });
});

// Registrar movimiento
function registrarMovimiento() {
    const form = document.getElementById('form-movimiento');
    const formData = new FormData(form);
    
    const cantidad = parseInt(formData.get('cantidad'));
    if (!cantidad || cantidad <= 0) {
        alert('Debe ingresar una cantidad válida');
        return;
    }
    
    fetch('<?= BASE_URL ?>api/inventario/registrar_movimiento.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Movimiento registrado correctamente');
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'No se pudo registrar el movimiento'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}
</script>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>