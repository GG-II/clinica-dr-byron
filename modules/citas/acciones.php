<?php
/**
 * MÓDULO DE CITAS - ACCIONES
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Procesa acciones sobre citas (confirmar, cancelar, atender, eliminar)
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

session_start();
define('ACCESS_GRANTED', true);

require_once '../../config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/funciones.php';
require_once '../../models/Cita.php';

// Verificar autenticación
verificar_sesion();

// Verificar método POST o petición AJAX
$es_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Obtener acción
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$cita_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

// Validar ID
if ($cita_id === 0) {
    if ($es_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ID de cita no válido']);
        exit;
    }
    mensaje_error('ID de cita no válido');
    header('Location: index.php');
    exit;
}

// Procesar según acción
$resultado = ['success' => false, 'message' => 'Acción no válida'];

switch ($accion) {
    case 'confirmar':
        $resultado = confirmar_cita($pdo, $cita_id);
        break;
        
    case 'cancelar':
        $resultado = cancelar_cita($pdo, $cita_id);
        break;
        
    case 'atender':
        $resultado = marcar_cita_atendida($pdo, $cita_id);
        break;
        
    case 'eliminar':
        $resultado = eliminar_cita($pdo, $cita_id);
        break;
        
    default:
        $resultado = ['success' => false, 'message' => 'Acción no reconocida'];
}

// Responder según tipo de petición
if ($es_ajax) {
    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit;
}

// Respuesta normal (POST form)
if ($resultado['success']) {
    mensaje_exito($resultado['message']);
    
    // Si eliminó, redirigir a index
    if ($accion === 'eliminar') {
        header('Location: index.php');
    } else {
        // Si no eliminó, regresar a ver.php
        header('Location: ver.php?id=' . $cita_id);
    }
} else {
    mensaje_error($resultado['message']);
    header('Location: ver.php?id=' . $cita_id);
}
exit;