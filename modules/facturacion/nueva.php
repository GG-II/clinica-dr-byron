<?php
/**
 * CREAR NUEVA FACTURA/RECIBO
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para registrar cobro y generar recibo
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
require_once MODELS_PATH . 'Paciente.php';

// Proteger la ruta
proteger_ruta();

if (!puede_crear()) {
    mensaje_error('No tiene permisos para crear facturas.');
    header('Location: ' . BASE_URL . 'modules/facturacion/');
    exit;
}

// VALIDAR que venga consulta_id (obligatorio)
if (empty($_GET['consulta_id'])) {
    mensaje_error('Las facturas deben crearse desde una consulta. Seleccione una consulta primero.');
    header('Location: ' . BASE_URL . 'modules/consultas/');
    exit;
}

$consulta_id = (int)$_GET['consulta_id'];

// Obtener datos de la consulta
require_once MODELS_PATH . 'Consulta.php';
$consulta = obtener_consulta_completa($pdo, $consulta_id);

if (!$consulta) {
    mensaje_error('La consulta no existe.');
    header('Location: ' . BASE_URL . 'modules/consultas/');
    exit;
}

// Verificar que no tenga ya factura
$stmt = $pdo->prepare("SELECT id FROM facturas WHERE consulta_id = ? AND estado != 'anulado'");
$stmt->execute([$consulta_id]);
if ($stmt->fetch()) {
    mensaje_error('Esta consulta ya tiene una factura asociada.');
    header('Location: ' . BASE_URL . 'modules/consultas/ver.php?id=' . $consulta_id);
    exit;
}

// Obtener siguiente número de recibo
$siguiente_numero = obtener_siguiente_numero_factura($pdo);

// Obtener datos del paciente
$paciente_id = $consulta['paciente_id'];
$paciente = obtener_paciente_por_id($pdo, $paciente_id);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validaciones
        if (empty($_POST['paciente_id'])) {
            throw new Exception('Debe seleccionar un paciente.');
        }
        
        if (empty($_POST['concepto'])) {
            throw new Exception('El concepto es obligatorio.');
        }
        
        $monto = (float)($_POST['monto'] ?? 0);
        if ($monto <= 0) {
            throw new Exception('El monto debe ser mayor a cero.');
        }
        
        $monto_pagado = (float)($_POST['monto_pagado'] ?? 0);
        if ($monto_pagado < $monto && $_POST['forma_pago'] !== 'pendiente') {
            throw new Exception('El monto pagado no puede ser menor al monto total.');
        }
        
        // Preparar datos
        $datos = [
            'paciente_id' => (int)$_POST['paciente_id'],
            'consulta_id' => !empty($_POST['consulta_id']) ? (int)$_POST['consulta_id'] : null,
            'usuario_id' => $_SESSION['usuario_id'],
            'fecha' => !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d'),
            'concepto' => trim($_POST['concepto']),
            'monto' => $monto,
            'forma_pago' => $_POST['forma_pago'],
            'monto_pagado' => $monto_pagado,
            'estado' => $monto_pagado >= $monto ? 'pagado' : 'pendiente',
            'notas' => !empty($_POST['notas']) ? trim($_POST['notas']) : null
        ];
        
        // Crear factura
        $factura_id = crear_factura($pdo, $datos);
        
        if (!$factura_id) {
            throw new Exception('Error al crear la factura. Intente nuevamente.');
        }
        
        // Registrar auditoría
        registrar_auditoria($pdo, $_SESSION['usuario_id'], 'CREAR', 'facturas', $factura_id, 
            "Creó factura #{$siguiente_numero} por Q" . number_format($monto, 2));
        
        mensaje_exito('Recibo #' . $siguiente_numero . ' creado exitosamente.');
        header('Location: ver.php?id=' . $factura_id);
        exit;
        
    } catch (Exception $e) {
        mensaje_error($e->getMessage());
    }
}

// Variables para la página
$page_title = 'Nuevo Recibo';
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Facturación', 'url' => BASE_URL . 'modules/facturacion/'],
    ['titulo' => 'Nuevo Recibo', 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<div class="row">
    <!-- Formulario principal -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-receipt"></i> Nuevo Recibo
                    <span class="badge bg-white text-primary ms-2">RECIBO #<?= str_pad($siguiente_numero, 4, '0', STR_PAD_LEFT) ?></span>
                </h5>
            </div>
            
            <div class="card-body">
                <form method="POST" id="formFactura">
                    
                    <!-- DATOS DEL COBRO -->
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-cash-coin text-success fs-1 me-3"></i>
                        <div>
                            <h6 class="mb-0 text-muted">Datos del Cobro</h6>
                            <small class="text-muted"><?= date('d/m/Y') ?></small>
                        </div>
                    </div>
                    
                    <!-- Datos de la consulta -->
                    <input type="hidden" name="paciente_id" value="<?= $paciente['id'] ?>">
                    <input type="hidden" name="consulta_id" value="<?= $consulta_id ?>">
                    
                    <div class="alert alert-info mb-4">
                        <h6 class="mb-2">
                            <i class="bi bi-person-circle"></i> 
                            <strong><?= e($paciente['nombre']) ?></strong> 
                            (<?= e($paciente['codigo']) ?>)
                        </h6>
                        <small class="text-muted">
                            <i class="bi bi-clipboard2-pulse"></i>
                            Consulta: <?= formatear_fecha($consulta['fecha']) ?> - 
                            <span class="badge bg-<?= obtener_color_tipo_consulta($consulta['tipo_consulta']) ?>">
                                <?= obtener_nombre_tipo_consulta($consulta['tipo_consulta']) ?>
                            </span>
                        </small>
                    </div>
                    
                    <!-- Fecha de pago -->
                    <div class="mb-4">
                        <label for="fecha" class="form-label">Fecha de Pago</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <!-- Concepto -->
                    <div class="mb-4">
                        <label for="concepto" class="form-label">
                            Concepto <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="concepto" name="concepto" 
                               placeholder="Ej: Honorarios médicos, Ultrasonido, Consulta..." 
                               required>
                        <div class="form-text">
                            Ejemplos: Honorarios médicos, Ultrasonido obstétrico, Consulta ginecológica
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- DETALLES DEL PAGO -->
                    <h6 class="text-muted text-uppercase mb-3">Detalles del Pago</h6>
                    
                    <!-- Monto Total -->
                    <div class="mb-4">
                        <label for="monto" class="form-label">
                            Monto Total (Q) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.01" class="form-control form-control-lg" 
                               id="monto" name="monto" 
                               placeholder="0.00" 
                               required
                               style="font-size: 1.5rem; font-weight: bold; color: #0d6efd;">
                    </div>
                    
                    <!-- Monto Pagado -->
                    <div class="mb-4" style="background: #f0f9ff; padding: 1rem; border-radius: 0.5rem;">
                        <label for="monto_pagado" class="form-label">
                            Monto Pagado (Q) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.01" class="form-control form-control-lg" 
                               id="monto_pagado" name="monto_pagado" 
                               placeholder="0.00" 
                               required
                               style="font-size: 1.5rem; font-weight: bold; color: #28a745;">
                    </div>
                    
                    <!-- Cambio/Saldo pendiente (calculado automático) -->
                    <div class="mb-4" style="background: #e7f5e7; padding: 1rem; border-radius: 0.5rem;">
                        <label class="form-label">Cambio / Saldo Pendiente</label>
                        <div id="cambioDisplay" style="font-size: 1.5rem; font-weight: bold; color: #28a745;">
                            Q 0.00
                        </div>
                    </div>
                    
                    <!-- Forma de pago -->
                    <div class="mb-4">
                        <label class="form-label">Forma de Pago <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="forma_pago" id="efectivo" value="efectivo" checked>
                            <label class="btn btn-outline-primary" for="efectivo">
                                <i class="bi bi-cash"></i> Efectivo
                            </label>
                            
                            <input type="radio" class="btn-check" name="forma_pago" id="tarjeta" value="tarjeta">
                            <label class="btn btn-outline-primary" for="tarjeta">
                                <i class="bi bi-credit-card"></i> Tarjeta
                            </label>
                            
                            <input type="radio" class="btn-check" name="forma_pago" id="transferencia" value="transferencia">
                            <label class="btn btn-outline-primary" for="transferencia">
                                <i class="bi bi-arrow-left-right"></i> Transferencia
                            </label>
                        </div>
                    </div>
                    
                    <!-- Notas adicionales -->
                    <div class="mb-4">
                        <label for="notas" class="form-label">Notas Adicionales</label>
                        <textarea class="form-control" id="notas" name="notas" rows="2" 
                                  placeholder="Observaciones opcionales..."></textarea>
                    </div>
                    
                    <!-- Botones -->
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        
                        <div>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Guardar Recibo
                            </button>
                            <button type="button" class="btn btn-primary btn-lg" id="btnGuardarImprimir">
                                <i class="bi bi-printer"></i> Guardar e Imprimir
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar: Ayuda -->
    <div class="col-lg-4">
        <div class="card mb-3 border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Información</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Número de recibo:</strong></p>
                <p class="text-muted">#<?= str_pad($siguiente_numero, 4, '0', STR_PAD_LEFT) ?> (correlativo automático)</p>
                
                <hr>
                
                <p class="mb-2"><strong>Conceptos comunes:</strong></p>
                <ul class="list-unstyled small">
                    <li>• Consulta ginecológica</li>
                    <li>• Ultrasonido obstétrico</li>
                    <li>• Control prenatal</li>
                    <li>• Honorarios médicos</li>
                    <li>• Procedimiento</li>
                </ul>
                
                <hr>
                
                <p class="mb-2"><strong>Cálculo de cambio:</strong></p>
                <p class="small text-muted">
                    El cambio se calcula automáticamente. Si el monto pagado es menor al total, 
                    quedará como saldo pendiente.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- TomSelect CSS -->
<link href="<?= ASSETS_URL ?>tomselect/tom-select.bootstrap5.min.css" rel="stylesheet">

<script src="<?= ASSETS_URL ?>tomselect/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Cálculo automático de cambio
    const inputMonto = document.getElementById('monto');
    const inputMontoPagado = document.getElementById('monto_pagado');
    const cambioDisplay = document.getElementById('cambioDisplay');
    
    function calcularCambio() {
        const monto = parseFloat(inputMonto.value) || 0;
        const pagado = parseFloat(inputMontoPagado.value) || 0;
        const cambio = pagado - monto;
        
        if (cambio >= 0) {
            cambioDisplay.textContent = 'Q ' + cambio.toFixed(2);
            cambioDisplay.style.color = '#28a745';
        } else {
            cambioDisplay.textContent = 'Saldo pendiente: Q ' + Math.abs(cambio).toFixed(2);
            cambioDisplay.style.color = '#dc3545';
        }
    }
    
    inputMonto.addEventListener('input', calcularCambio);
    inputMontoPagado.addEventListener('input', calcularCambio);
    
    // Auto-copiar monto a monto_pagado cuando se escribe monto
    inputMonto.addEventListener('blur', function() {
        if (inputMontoPagado.value === '' || parseFloat(inputMontoPagado.value) === 0) {
            inputMontoPagado.value = this.value;
            calcularCambio();
        }
    });
    
    // Botón "Guardar e Imprimir"
    document.getElementById('btnGuardarImprimir').addEventListener('click', function() {
        // Agregar campo hidden para indicar que se debe abrir el PDF
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'imprimir';
        input.value = '1';
        document.getElementById('formFactura').appendChild(input);
        
        // Submit del formulario
        document.getElementById('formFactura').submit();
    });
});
</script>

<?php include INCLUDES_PATH . 'footer.php'; ?>