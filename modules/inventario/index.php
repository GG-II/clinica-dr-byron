<?php
/**
 * INVENTARIO - Listado
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Lista de productos de inventario con modal de movimientos
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

// Paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 20;
$offset = ($pagina - 1) * $por_pagina;

// Filtros
$filtros = [];

if (!empty($_GET['busqueda'])) {
    $filtros['busqueda'] = $_GET['busqueda'];
}

if (!empty($_GET['stock_bajo'])) {
    $filtros['stock_bajo'] = true;
}

// Obtener productos
$productos = obtener_productos($pdo, $filtros, $por_pagina, $offset);
$total_productos = contar_productos($pdo, $filtros);
$total_paginas = ceil($total_productos / $por_pagina);
$productos_stock_bajo = contar_productos_stock_bajo($pdo);

// Variables para el header
$titulo = 'Inventario';
$breadcrumb = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard.php'],
    ['titulo' => 'Inventario', 'url' => '']
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
                <a href="<?= BASE_URL ?>modules/inventario/nuevo.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Producto
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Alerta de stock bajo -->
    <?php if ($productos_stock_bajo > 0): ?>
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 1.5rem;"></i>
            <div class="flex-grow-1">
                <strong>Atención: Stock Bajo</strong>
                <p class="mb-0">
                    Hay <?= $productos_stock_bajo ?> producto<?= $productos_stock_bajo > 1 ? 's' : '' ?> con existencias por debajo del mínimo establecido.
                </p>
            </div>
            <a href="?stock_bajo=1" class="btn btn-warning">
                Ver productos
            </a>
        </div>
    <?php endif; ?>
    
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                
                <!-- Búsqueda -->
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="busqueda" class="form-control" 
                               placeholder="Buscar producto por nombre o código..."
                               value="<?= isset($_GET['busqueda']) ? e($_GET['busqueda']) : '' ?>">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                </div>
                
                <!-- Stock bajo -->
                <div class="col-md-4 d-flex align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="stock_bajo" 
                               id="check-stock-bajo" value="1"
                               <?= !empty($_GET['stock_bajo']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="check-stock-bajo">
                            Solo productos con stock bajo
                        </label>
                    </div>
                </div>
                
                <?php if (!empty($_GET['busqueda']) || !empty($_GET['stock_bajo'])): ?>
                    <div class="col-12">
                        <a href="<?= BASE_URL ?>modules/inventario/index.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Limpiar filtros
                        </a>
                    </div>
                <?php endif; ?>
                
            </form>
        </div>
    </div>
    
    <!-- Tabla de productos -->
    <div class="card">
        <div class="card-body">
            
            <?php if (empty($productos)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No hay productos registrados</p>
                    <?php if (!es_asistente()): ?>
                        <a href="<?= BASE_URL ?>modules/inventario/nuevo.php" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle"></i> Agregar Primer Producto
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>PRODUCTO</th>
                                <th>UNIDAD</th>
                                <th>STOCK ACTUAL</th>
                                <th>STOCK MÍNIMO</th>
                                <th>ESTADO</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $producto): 
                                $stock_bajo = $producto['cantidad_actual'] <= $producto['stock_minimo'];
                            ?>
                                <tr>
                                    <!-- Producto -->
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <?= e($producto['nombre']) ?>
                                        </div>
                                        <small class="text-muted">ID: <?= $producto['id'] ?></small>
                                    </td>
                                    
                                    <!-- Unidad -->
                                    <td>
                                        <span class="text-muted">
                                            <?= e($producto['unidad']) ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Stock actual -->
                                    <td>
                                        <span class="badge <?= $stock_bajo ? 'bg-danger' : 'bg-success' ?>" 
                                              style="font-size: 1rem;">
                                            <?= $producto['cantidad_actual'] ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Stock mínimo -->
                                    <td>
                                        <span class="text-muted">
                                            <?= $producto['stock_minimo'] ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Estado -->
                                    <td>
                                        <?php if ($stock_bajo): ?>
                                            <span class="badge bg-danger">STOCK BAJO</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">OK</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Acciones -->
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-success me-1" 
                                                onclick="abrirModalMovimiento(<?= $producto['id'] ?>, '<?= e($producto['nombre']) ?>', <?= $producto['cantidad_actual'] ?>, '<?= e($producto['unidad']) ?>')">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </button>
                                        
                                        <a href="<?= BASE_URL ?>modules/inventario/ver.php?id=<?= $producto['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        <?php if (!es_asistente()): ?>
                                            <a href="<?= BASE_URL ?>modules/inventario/editar.php?id=<?= $producto['id'] ?>" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
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
                                    <a class="page-link" href="?pagina=<?= $i ?><?= !empty($_GET['busqueda']) ? '&busqueda=' . urlencode($_GET['busqueda']) : '' ?><?= !empty($_GET['stock_bajo']) ? '&stock_bajo=1' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    
                    <p class="text-center text-muted small">
                        Mostrando <?= count($productos) ?> de <?= $total_productos ?> productos
                    </p>
                <?php endif; ?>
                
            <?php endif; ?>
            
        </div>
    </div>
    
</div>

<!-- MODAL: Registrar Movimiento -->
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
    // Guardar stock actual
    stockActual = stockActualParam;
    
    // Llenar datos del modal
    document.getElementById('movimiento-producto-id').value = productoId;
    document.getElementById('movimiento-producto-nombre').textContent = productoNombre;
    document.getElementById('movimiento-stock-actual').textContent = stockActual;
    document.getElementById('movimiento-unidad').textContent = unidad;
    
    // Reset formulario
    document.getElementById('form-movimiento').reset();
    document.getElementById('tipo-entrada').checked = true;
    document.getElementById('movimiento-cantidad').value = 0;
    
    // Calcular stock resultante
    calcularStockResultante();
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalMovimiento'));
    modal.show();
}

// Calcular stock resultante en tiempo real
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
    
    // Advertencia si queda negativo
    const alertElement = document.querySelector('.alert-info');
    if (stockResultante < 0) {
        alertElement.classList.remove('alert-info');
        alertElement.classList.add('alert-danger');
    } else {
        alertElement.classList.remove('alert-danger');
        alertElement.classList.add('alert-info');
    }
}

// Escuchar cambios en tipo y cantidad
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[name="tipo"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', calcularStockResultante);
    });
});

// Registrar movimiento via AJAX
function registrarMovimiento() {
    const form = document.getElementById('form-movimiento');
    const formData = new FormData(form);
    
    // Validar cantidad
    const cantidad = parseInt(formData.get('cantidad'));
    if (!cantidad || cantidad <= 0) {
        alert('Debe ingresar una cantidad válida');
        return;
    }
    
    // Enviar via AJAX
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