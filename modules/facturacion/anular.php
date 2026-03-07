<?php
/**
 * ANULAR FACTURA
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Anula una factura (no se elimina, se marca como anulada)
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

// Proteger la ruta
proteger_ruta();

if (!puede_eliminar()) {
    mensaje_error('No tiene permisos para anular facturas.');
    header('Location: ' . BASE_URL . 'modules/facturacion/');
    exit;
}

// Obtener ID de la factura
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    mensaje_error('ID de factura no válido.');
    header('Location: index.php');
    exit;
}

// Obtener datos de la factura
$factura = obtener_factura_completa($pdo, $id);

if (!$factura) {
    mensaje_error('La factura no existe.');
    header('Location: index.php');
    exit;
}

// Verificar que no esté ya anulada
if ($factura['estado'] === 'anulado') {
    mensaje_error('Esta factura ya está anulada.');
    header('Location: ver.php?id=' . $id);
    exit;
}

// Procesar anulación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $motivo = !empty($_POST['motivo']) ? trim($_POST['motivo']) : 'Sin motivo especificado';
        
        // Anular factura
        $resultado = anular_factura($pdo, $id, $motivo);
        
        if (!$resultado) {
            throw new Exception('Error al anular la factura. Intente nuevamente.');
        }
        
        // Registrar auditoría
        registrar_auditoria($pdo, $_SESSION['usuario_id'], 'ANULAR', 'facturas', $id, 
            "Anuló recibo #{$factura['numero_correlativo']} - Motivo: $motivo");
        
        mensaje_exito('Recibo anulado exitosamente.');
        header('Location: ver.php?id=' . $id);
        exit;
        
    } catch (Exception $e) {
        mensaje_error($e->getMessage());
    }
}

// Variables para la página
$page_title = 'Anular Recibo #' . str_pad($factura['numero_correlativo'], 4, '0', STR_PAD_LEFT);
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Facturación', 'url' => BASE_URL . 'modules/facturacion/'],
    ['titulo' => 'Anular Recibo', 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Anular Recibo
                </h5>
            </div>
            
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Atención:</strong> Esta acción no se puede deshacer. El recibo quedará marcado como anulado 
                    y no podrá ser modificado.
                </div>
                
                <!-- Datos de la factura a anular -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">Datos del Recibo</h6>
                    
                    <table class="table table-sm">
                        <tr>
                            <td class="text-muted">Número:</td>
                            <td><strong>#<?= str_pad($factura['numero_correlativo'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fecha:</td>
                            <td><?= formatear_fecha($factura['fecha']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Paciente:</td>
                            <td><strong><?= e($factura['paciente_nombre']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Concepto:</td>
                            <td><?= e($factura['concepto']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Monto:</td>
                            <td><strong class="text-danger">Q <?= number_format($factura['monto'], 2) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Estado:</td>
                            <td>
                                <span class="badge bg-<?= $factura['estado'] === 'pagado' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($factura['estado']) ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Formulario de anulación -->
                <form method="POST">
                    <div class="mb-4">
                        <label for="motivo" class="form-label">
                            Motivo de Anulación <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3" 
                                  placeholder="Explique brevemente el motivo de la anulación..." 
                                  required></textarea>
                        <div class="form-text">
                            Ejemplos: Error en el monto, Cobro duplicado, Cancelación de servicio, etc.
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="ver.php?id=<?= $id ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Confirmar Anulación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . 'footer.php'; ?>