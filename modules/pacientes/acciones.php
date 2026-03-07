<?php
/**
 * ACCIONES DE PACIENTES
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Procesa acciones: activar, desactivar, eliminar
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
require_once MODELS_PATH . 'Paciente.php';

// Proteger la ruta
proteger_ruta();

// Obtener parámetros
$accion = $_GET['accion'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validar ID
if ($id <= 0) {
    mensaje_error('ID de paciente no válido.');
    header('Location: index.php');
    exit;
}

// Procesar según la acción
switch ($accion) {
    case 'activar':
        // Solo admin puede activar/desactivar
        if (!puede_eliminar()) {
            mensaje_error('No tiene permisos para realizar esta acción.');
            header('Location: index.php');
            exit;
        }
        
        $resultado = activar_paciente($pdo, $id);
        
        if ($resultado['success']) {
            mensaje_exito('Paciente activado correctamente.');
        } else {
            mensaje_error($resultado['message']);
        }
        
        header('Location: index.php');
        exit;
        
    case 'desactivar':
        // Solo admin puede activar/desactivar
        if (!puede_eliminar()) {
            mensaje_error('No tiene permisos para realizar esta acción.');
            header('Location: index.php');
            exit;
        }
        
        $resultado = desactivar_paciente($pdo, $id);
        
        if ($resultado['success']) {
            mensaje_exito('Paciente desactivado correctamente.');
        } else {
            mensaje_error($resultado['message']);
        }
        
        header('Location: index.php');
        exit;
        
    case 'eliminar':
        // Solo admin puede eliminar (PELIGROSO - no recomendado)
        if (!es_admin()) {
            mensaje_error('No tiene permisos para eliminar pacientes.');
            header('Location: index.php');
            exit;
        }
        
        $resultado = eliminar_paciente($pdo, $id);
        
        if ($resultado['success']) {
            mensaje_exito('Paciente eliminado correctamente.');
        } else {
            mensaje_error($resultado['message']);
        }
        
        header('Location: index.php');
        exit;
        
    default:
        mensaje_error('Acción no válida.');
        header('Location: index.php');
        exit;
}
?>