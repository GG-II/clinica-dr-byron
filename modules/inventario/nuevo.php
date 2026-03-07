<?php
/**
 * INVENTARIO - Nuevo Producto
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para crear nuevo producto de inventario
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
    $_SESSION['mensaje_error'] = 'No tienes permisos para crear productos';
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
            'cantidad_actual' => !empty($_POST['cantidad_actual']) ? (int)$_POST['cantidad_actual'] : 0,
            'stock_minimo' => !empty($_POST['stock_minimo']) ? (int)$_POST['stock_minimo'] : 0
        ];
        
        // Crear producto
        $producto_id = crear_producto($pdo, $datos);
        
        if ($producto_id) {
            $_SESSION['mensaje_exito'] = 'Producto creado correctamente';
            header('Location: ' . BASE_URL . 'modules/inventario/ver.php?id=' . $producto_id);
            exit;
        } else {
            throw new Exception('Error al crear el producto');
        }
        
    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = $e->getMessage();
    }
}

// Variables para el header
$titulo = 'Nuevo Producto';
$breadcrumb = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard.php'],
    ['titulo' => 'Inventario', 'url' => BASE_URL . 'modules/inventario/index.php'],
    ['titulo' => 'Nuevo', 'url' => '']
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
            <p class="text-muted">Inventario / <span class="text-primary">Nuevo</span></p>
        </div>
    </div>
    
    <!-- Mensajes flash -->
    <?php mostrar_mensajes(); ?>
    
    <!-- Formulario -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    
                    <form method="POST" id="form-producto">
                        
                        <!-- DATOS DEL PRODUCTO -->
                        <h5 class="border-bottom pb-2 mb-4">
                            <i class="bi bi-box-seam text-primary"></i>
                            Datos del Producto
                        </h5>
                        
                        <div class="row g-3">
                            
                            <!-- Nombre -->
                            <div class="col-md-12">
                                <label for="nombre" class="form-label text-muted small mb-1">
                                    NOMBRE DEL PRODUCTO <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre" id="nombre" 
                                       class="form-control" required maxlength="150"
                                       placeholder="Ej: Guantes de látex, Solución antiséptica...">
                            </div>
                            
                            <!-- Unidad -->
                            <div class="col-md-6">
                                <label for="unidad" class="form-label text-muted small mb-1">
                                    UNIDAD DE MEDIDA <span class="text-danger">*</span>
                                </label>
                                <select name="unidad" id="unidad" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="cajas">Cajas</option>
                                    <option value="frascos">Frascos</option>
                                    <option value="unidades">Unidades</option>
                                    <option value="paquetes">Paquetes</option>
                                    <option value="rollos">Rollos</option>
                                    <option value="litros">Litros</option>
                                    <option value="mililitros">Mililitros</option>
                                    <option value="gramos">Gramos</option>
                                    <option value="kilogramos">Kilogramos</option>
                                    <option value="otro">Otro...</option>
                                </select>
                            </div>
                            
                            <!-- Unidad otro (texto libre) -->
                            <div class="col-md-6" id="container-unidad-otro" style="display: none;">
                                <label for="unidad_otro" class="form-label text-muted small mb-1">
                                    ESPECIFICAR UNIDAD <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="unidad_otro" class="form-control" 
                                       maxlength="50" placeholder="Ej: sets, kits...">
                            </div>
                            
                            <!-- Cantidad inicial -->
                            <div class="col-md-6">
                                <label for="cantidad_actual" class="form-label text-muted small mb-1">
                                    CANTIDAD INICIAL
                                </label>
                                <input type="number" name="cantidad_actual" id="cantidad_actual" 
                                       class="form-control" min="0" value="0">
                                <small class="text-muted">
                                    Cantidad con la que inicia el producto
                                </small>
                            </div>
                            
                            <!-- Stock mínimo -->
                            <div class="col-md-6">
                                <label for="stock_minimo" class="form-label text-muted small mb-1">
                                    STOCK MÍNIMO (ALERTA)
                                </label>
                                <input type="number" name="stock_minimo" id="stock_minimo" 
                                       class="form-control" min="0" value="5">
                                <small class="text-muted">
                                    Te avisaremos cuando esté por debajo
                                </small>
                            </div>
                            
                        </div>
                        
                        <!-- Botones -->
                        <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                            <a href="<?= BASE_URL ?>modules/inventario/index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Guardar Producto
                            </button>
                        </div>
                        
                    </form>
                    
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
                
                // Deshabilitar el select para que no envíe "otro"
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