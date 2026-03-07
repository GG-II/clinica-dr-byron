<?php
/**
 * INVENTARIO - Editar Producto
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para editar producto existente
 */

define('ACCESS_GRANTED', true);
session_start();

require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once MODELS_PATH . 'Inventario.php';

// Verificar autenticación y permisos
verificar_sesion();

if (es_asistente()) {
    $_SESSION['mensaje_error'] = 'No tienes permisos para editar productos';
    header('Location: ' . BASE_URL . 'modules/inventario/index.php');
    exit;
}

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

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        // Validar datos obligatorios
        if (empty($_POST['nombre'])) {
            throw new Exception('Debe ingresar el nombre del producto');
        }
        
        if (empty($_POST['unidad'])) {
            throw new Exception('Debe especificar la unidad de medida');
        }
        
        // Preparar datos
        $datos = [
            'nombre' => trim($_POST['nombre']),
            'unidad' => trim($_POST['unidad']),
            'stock_minimo' => !empty($_POST['stock_minimo']) ? (int)$_POST['stock_minimo'] : 0
        ];
        
        // Actualizar producto
        $resultado = actualizar_producto($pdo, $id, $datos);
        
        if ($resultado) {
            $_SESSION['mensaje_exito'] = 'Producto actualizado correctamente';
            header('Location: ' . BASE_URL . 'modules/inventario/ver.php?id=' . $id);
            exit;
        } else {
            throw new Exception('Error al actualizar el producto');
        }
        
    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = $e->getMessage();
    }
}

// Variables para el header
$titulo = 'Editar Producto';
$breadcrumb = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard.php'],
    ['titulo' => 'Inventario', 'url' => BASE_URL . 'modules/inventario/index.php'],
    ['titulo' => 'Editar', 'url' => '']
];

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Contenido principal -->
<div class="container-fluid mt-4">
    
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="<?= BASE_URL ?>modules/inventario/ver.php?id=<?= $id ?>" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <h2><?= $titulo ?></h2>
            <p class="text-muted">Inventario / Editar</p>
        </div>
    </div>
    
    <!-- Mensajes flash -->
    <?php mostrar_mensajes(); ?>
    
    <!-- Formulario -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam"></i>
                        Datos del Producto
                    </h5>
                </div>
                <div class="card-body">
                    
                    <form method="POST" id="form-producto">
                        
                        <div class="row g-3">
                            
                            <!-- Nombre -->
                            <div class="col-md-12">
                                <label for="nombre" class="form-label text-muted small mb-1">
                                    NOMBRE DEL PRODUCTO <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre" id="nombre" 
                                       class="form-control" required maxlength="150"
                                       value="<?= e($producto['nombre']) ?>">
                            </div>
                            
                            <!-- Unidad -->
                            <div class="col-md-6">
                                <label for="unidad" class="form-label text-muted small mb-1">
                                    UNIDAD DE MEDIDA <span class="text-danger">*</span>
                                </label>
                                <select name="unidad" id="unidad" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="cajas" <?= $producto['unidad'] === 'cajas' ? 'selected' : '' ?>>Cajas</option>
                                    <option value="frascos" <?= $producto['unidad'] === 'frascos' ? 'selected' : '' ?>>Frascos</option>
                                    <option value="unidades" <?= $producto['unidad'] === 'unidades' ? 'selected' : '' ?>>Unidades</option>
                                    <option value="paquetes" <?= $producto['unidad'] === 'paquetes' ? 'selected' : '' ?>>Paquetes</option>
                                    <option value="rollos" <?= $producto['unidad'] === 'rollos' ? 'selected' : '' ?>>Rollos</option>
                                    <option value="litros" <?= $producto['unidad'] === 'litros' ? 'selected' : '' ?>>Litros</option>
                                    <option value="mililitros" <?= $producto['unidad'] === 'mililitros' ? 'selected' : '' ?>>Mililitros</option>
                                    <option value="gramos" <?= $producto['unidad'] === 'gramos' ? 'selected' : '' ?>>Gramos</option>
                                    <option value="kilogramos" <?= $producto['unidad'] === 'kilogramos' ? 'selected' : '' ?>>Kilogramos</option>
                                    <?php 
                                    // Si la unidad actual no está en la lista, agregar "Otro" y un input
                                    $unidades_predefinidas = ['cajas', 'frascos', 'unidades', 'paquetes', 'rollos', 'litros', 'mililitros', 'gramos', 'kilogramos'];
                                    $es_personalizada = !in_array($producto['unidad'], $unidades_predefinidas);
                                    ?>
                                    <option value="otro" <?= $es_personalizada ? 'selected' : '' ?>>Otro...</option>
                                </select>
                            </div>
                            
                            <!-- Unidad personalizada (si aplica) -->
                            <div class="col-md-6" id="container-unidad-otro" style="display: <?= $es_personalizada ? 'block' : 'none' ?>;">
                                <label for="unidad_otro" class="form-label text-muted small mb-1">
                                    ESPECIFICAR UNIDAD <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="unidad_otro" class="form-control" 
                                       maxlength="50" 
                                       value="<?= $es_personalizada ? e($producto['unidad']) : '' ?>">
                            </div>
                            
                            <!-- Stock actual (solo lectura) -->
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    STOCK ACTUAL (SOLO LECTURA)
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" 
                                           value="<?= $producto['cantidad_actual'] ?>" 
                                           readonly disabled>
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    Use "Registrar Movimiento" para cambiar el stock
                                </small>
                            </div>
                            
                            <!-- Stock mínimo -->
                            <div class="col-md-6">
                                <label for="stock_minimo" class="form-label text-muted small mb-1">
                                    STOCK MÍNIMO (ALERTA)
                                </label>
                                <input type="number" name="stock_minimo" id="stock_minimo" 
                                       class="form-control" min="0" 
                                       value="<?= $producto['stock_minimo'] ?>">
                                <small class="text-muted">
                                    Te avisaremos cuando esté por debajo
                                </small>
                            </div>
                            
                        </div>
                        
                        <!-- Botones -->
                        <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                            <a href="<?= BASE_URL ?>modules/inventario/ver.php?id=<?= $id ?>" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Cambios
                            </button>
                        </div>
                        
                    </form>
                    
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            
            <!-- Info del producto -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle"></i>
                        Información
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        <strong>Nota:</strong> Solo se puede editar el nombre, unidad y stock mínimo del producto.
                    </p>
                    <p class="small text-muted mb-0">
                        Para modificar el stock actual, utiliza la opción 
                        <strong>"Registrar Movimiento"</strong> desde la vista del producto.
                    </p>
                </div>
            </div>
            
            <!-- Estadísticas -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up"></i>
                        Datos Actuales
                    </h6>
                </div>
                <div class="card-body">
                    
                    <div class="mb-3">
                        <small class="text-muted">STOCK ACTUAL</small>
                        <p class="mb-0">
                            <span class="badge <?= $producto['cantidad_actual'] <= $producto['stock_minimo'] ? 'bg-danger' : 'bg-success' ?>" 
                                  style="font-size: 1.25rem;">
                                <?= $producto['cantidad_actual'] ?>
                            </span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">FECHA CREACIÓN</small>
                        <p class="mb-0">
                            <?= formatear_fecha($producto['created_at']) ?>
                        </p>
                    </div>
                    
                    <div class="mb-0">
                        <small class="text-muted">ÚLTIMA ACTUALIZACIÓN</small>
                        <p class="mb-0">
                            <?= formatear_fecha($producto['updated_at']) ?>
                        </p>
                    </div>
                    
                </div>
            </div>
            
        </div>
        
    </div>
    
</div>

<script>
// Mostrar/ocultar campo "Unidad Otro"
document.getElementById('unidad').addEventListener('change', function() {
    const containerOtro = document.getElementById('container-unidad-otro');
    const inputOtro = document.getElementById('unidad_otro');
    
    if (this.value === 'otro') {
        containerOtro.style.display = 'block';
        inputOtro.required = true;
        
        // Al enviar, usar el valor del input otro
        document.getElementById('form-producto').addEventListener('submit', function(e) {
            const selectUnidad = document.getElementById('unidad');
            const inputOtro = document.getElementById('unidad_otro');
            
            if (selectUnidad.value === 'otro' && inputOtro.value.trim()) {
                // Crear input hidden con el valor personalizado
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'unidad';
                hiddenInput.value = inputOtro.value.trim();
                this.appendChild(hiddenInput);
                
                // Deshabilitar el select
                selectUnidad.disabled = true;
            }
        });
    } else {
        containerOtro.style.display = 'none';
        inputOtro.required = false;
        inputOtro.value = '';
    }
});

// Validación del formulario
document.getElementById('form-producto').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const unidad = document.getElementById('unidad').value;
    const unidadOtro = document.getElementById('unidad_otro').value.trim();
    
    if (!nombre) {
        e.preventDefault();
        alert('Debe ingresar el nombre del producto');
        document.getElementById('nombre').focus();
        return false;
    }
    
    if (unidad === 'otro' && !unidadOtro) {
        e.preventDefault();
        alert('Debe especificar la unidad de medida');
        document.getElementById('unidad_otro').focus();
        return false;
    }
});
</script>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>