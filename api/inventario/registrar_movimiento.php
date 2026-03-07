<?php
/**
 * API: Registrar Movimiento de Inventario
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Endpoint para registrar entradas, salidas y ventas de productos
 */

define('ACCESS_GRANTED', true);
session_start();

require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'auth.php';
require_once MODELS_PATH . 'Inventario.php';

// Headers
header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'No autenticado'
    ]);
    exit;
}

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

try {
    // Validar datos requeridos
    if (empty($_POST['producto_id'])) {
        throw new Exception('Producto no especificado');
    }
    
    if (empty($_POST['tipo']) || !in_array($_POST['tipo'], ['entrada', 'salida', 'venta'])) {
        throw new Exception('Tipo de movimiento inválido');
    }
    
    if (empty($_POST['cantidad']) || (int)$_POST['cantidad'] <= 0) {
        throw new Exception('Cantidad inválida');
    }
    
    $producto_id = (int)$_POST['producto_id'];
    $tipo = $_POST['tipo'];
    $cantidad = (int)$_POST['cantidad'];
    $motivo = !empty($_POST['motivo']) ? trim($_POST['motivo']) : null;
    
    // Verificar que el producto exista
    $producto = obtener_producto_completo($pdo, $producto_id);
    
    if (!$producto) {
        throw new Exception('Producto no encontrado');
    }
    
    // Validar stock suficiente para salidas/ventas
    if (($tipo === 'salida' || $tipo === 'venta') && $producto['cantidad_actual'] < $cantidad) {
        throw new Exception('Stock insuficiente. Disponible: ' . $producto['cantidad_actual']);
    }
    
    // Preparar datos del movimiento
    $datos = [
        'producto_id' => $producto_id,
        'usuario_id' => $_SESSION['usuario_id'],
        'tipo' => $tipo,
        'cantidad' => $cantidad,
        'motivo' => $motivo
    ];
    
    // Registrar movimiento
    $movimiento_id = registrar_movimiento($pdo, $datos);
    
    if ($movimiento_id) {
        // Obtener stock actualizado
        $producto_actualizado = obtener_producto_completo($pdo, $producto_id);
        
        echo json_encode([
            'success' => true,
            'message' => 'Movimiento registrado correctamente',
            'movimiento_id' => $movimiento_id,
            'stock_nuevo' => $producto_actualizado['cantidad_actual']
        ]);
    } else {
        throw new Exception('Error al registrar el movimiento');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}