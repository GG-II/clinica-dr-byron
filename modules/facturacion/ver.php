<?php
/**
 * VER DETALLE DE FACTURA
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Visualización completa del recibo con opción de imprimir
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

// Obtener ID de la factura
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    mensaje_error('ID de factura no válido.');
    header('Location: index.php');
    exit;
}

// Obtener datos completos de la factura
$factura = obtener_factura_completa($pdo, $id);

if (!$factura) {
    mensaje_error('La factura no existe.');
    header('Location: index.php');
    exit;
}

// Variables para la página
$page_title = 'Recibo #' . str_pad($factura['numero_correlativo'], 4, '0', STR_PAD_LEFT);
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Facturación', 'url' => BASE_URL . 'modules/facturacion/'],
    ['titulo' => 'Recibo #' . str_pad($factura['numero_correlativo'], 4, '0', STR_PAD_LEFT), 'url' => null]
];

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<div class="row">
    <!-- Contenido principal -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-<?= $factura['estado'] === 'anulado' ? 'danger' : 'primary' ?> text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i> Recibo #<?= str_pad($factura['numero_correlativo'], 4, '0', STR_PAD_LEFT) ?>
                    </h5>
                    <div>
                        <?php if ($factura['estado'] !== 'anulado'): ?>
                            <a href="pdf.php?id=<?= $id ?>" class="btn btn-light btn-sm me-2" target="_blank">
                                <i class="bi bi-file-pdf"></i> Generar PDF
                            </a>
                        <?php endif; ?>
                        
                        <a href="index.php" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <?php if ($factura['estado'] === 'anulado'): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle"></i> 
                        <strong>RECIBO ANULADO</strong>
                        <?php if ($factura['notas']): ?>
                            <br>Motivo: <?= e($factura['notas']) ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Datos del paciente -->
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase mb-3">Datos del Paciente</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <small class="text-muted d-block">Nombre</small>
                            <strong><?= e($factura['paciente_nombre']) ?></strong>
                        </div>
                        
                        <div class="col-md-3 mb-2">
                            <small class="text-muted d-block">Código</small>
                            <strong><?= e($factura['paciente_codigo']) ?></strong>
                        </div>
                        
                        <div class="col-md-3 mb-2">
                            <small class="text-muted d-block">Teléfono</small>
                            <strong><?= e($factura['paciente_telefono']) ?></strong>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Datos del cobro -->
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase mb-3">Detalles del Cobro</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Fecha</small>
                            <strong><?= formatear_fecha($factura['fecha']) ?></strong>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Atendió</small>
                            <strong><?= e($factura['usuario_nombre']) ?></strong>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <small class="text-muted d-block">Concepto</small>
                            <strong><?= e($factura['concepto']) ?></strong>
                            
                            <?php if ($factura['tipo_consulta']): ?>
                                <br>
                                <span class="badge bg-<?= obtener_color_tipo_consulta($factura['tipo_consulta']) ?>">
                                    <?= obtener_nombre_tipo_consulta($factura['tipo_consulta']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Montos -->
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase mb-3">Detalles del Pago</h6>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">Monto Total</small>
                                    <h3 class="mb-0 text-primary">Q <?= number_format($factura['monto'], 2) ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card bg-success bg-opacity-10">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">Monto Pagado</small>
                                    <h3 class="mb-0 text-success">Q <?= number_format($factura['monto_pagado'], 2) ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card <?= $factura['cambio'] > 0 ? 'bg-info bg-opacity-10' : 'bg-warning bg-opacity-10' ?>">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block">
                                        <?= $factura['cambio'] > 0 ? 'Cambio' : 'Pendiente' ?>
                                    </small>
                                    <h3 class="mb-0 <?= $factura['cambio'] > 0 ? 'text-info' : 'text-warning' ?>">
                                        Q <?= number_format(abs($factura['cambio']), 2) ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Forma de Pago</small>
                            <?php
                            $iconos = [
                                'efectivo' => 'bi-cash',
                                'tarjeta' => 'bi-credit-card',
                                'transferencia' => 'bi-arrow-left-right'
                            ];
                            ?>
                            <strong>
                                <i class="bi <?= $iconos[$factura['forma_pago']] ?? 'bi-cash' ?>"></i>
                                <?= ucfirst($factura['forma_pago']) ?>
                            </strong>
                        </div>
                        
                        <div class="col-md-6">
                            <small class="text-muted d-block">Estado</small>
                            <?php
                            $badges = [
                                'pagado' => 'success',
                                'pendiente' => 'warning',
                                'anulado' => 'danger'
                            ];
                            ?>
                            <span class="badge bg-<?= $badges[$factura['estado']] ?? 'secondary' ?> fs-6">
                                <?= ucfirst($factura['estado']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <?php if ($factura['notas'] && $factura['estado'] !== 'anulado'): ?>
                <hr>
                <div class="mb-3">
                    <small class="text-muted d-block">Notas</small>
                    <p class="mb-0" style="white-space: pre-line;"><?= e(trim($factura['notas'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Creado: <?= formatear_fecha_hora($factura['created_at']) ?>
                    </small>
                    
                    <div>
                        <?php if ($factura['estado'] !== 'anulado' && puede_eliminar()): ?>
                            <a href="anular.php?id=<?= $id ?>" 
                               class="btn btn-outline-danger btn-sm"
                               onclick="return confirm('¿Está seguro de anular este recibo? Esta acción no se puede deshacer.')">
                                <i class="bi bi-x-circle"></i> Anular Recibo
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar: Información adicional -->
    <div class="col-lg-4">
        <?php if (!empty($factura['paciente_direccion'])): ?>
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Información Adicional</h6>
            </div>
            <div class="card-body">
                <small class="text-muted d-block">Dirección</small>
                <p class="mb-0" style="white-space: pre-line;"><?= e(trim($factura['paciente_direccion'])) ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Acciones Disponibles</h6>
            </div>
            <div class="card-body">
                <?php if ($factura['estado'] !== 'anulado'): ?>
                    <a href="pdf.php?id=<?= $id ?>" class="btn btn-danger w-100 mb-2" target="_blank">
                        <i class="bi bi-file-pdf"></i> Generar PDF
                    </a>
                <?php endif; ?>
                
                <a href="index.php" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-left"></i> Volver al Listado
                </a>
            </div>
        </div>
    </div>
</div>

<?php include INCLUDES_PATH . 'footer.php'; ?>