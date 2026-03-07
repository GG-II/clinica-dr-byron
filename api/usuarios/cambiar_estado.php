<?php
/**
 * API: Cambiar Estado de Usuario
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Activar o desactivar usuario
 */

define('ACCESS_GRANTED', true);
session_start();

require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once MODELS_PATH . 'Usuario.php';

header('Content-Type: application/json');

// Verificar autenticación y permisos
verificar_sesion();

if (!es_admin()) {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos']);
    exit;
}

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    // Validar datos
    if (!isset($_POST['user_id']) || !isset($_POST['activo'])) {
        throw new Exception('Datos incompletos');
    }
    
    $user_id = (int)$_POST['user_id'];
    $activo = (int)$_POST['activo'];
    
    if ($user_id <= 0) {
        throw new Exception('ID de usuario inválido');
    }
    
    // No permitir que el admin se desactive a sí mismo
    if ($user_id == obtener_usuario_id() && $activo == 0) {
        throw new Exception('No puedes desactivarte a ti mismo');
    }
    
    // Cambiar estado
    $resultado = cambiar_estado_usuario($pdo, $user_id, $activo);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => $activo ? 'Usuario activado correctamente' : 'Usuario desactivado correctamente'
        ]);
    } else {
        throw new Exception('Error al cambiar el estado del usuario');
    }
    
} catch (Exception $e) {
    error_log("Error en cambiar_estado.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}