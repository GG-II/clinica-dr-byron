<?php
/**
 * API: REGISTRAR RECORDATORIO ENVIADO
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Endpoint para registrar en auditoría que se envió un recordatorio
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

// Verificar autenticación
if (!verificar_sesion()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Obtener datos del POST
$json = file_get_contents('php://input');
$datos = json_decode($json, true);

if (empty($datos['cita_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de cita requerido']);
    exit;
}

$cita_id = (int)$datos['cita_id'];

// Obtener datos de la cita para el mensaje de auditoría
try {
    $stmt = $pdo->prepare("
        SELECT c.fecha, c.hora_inicio, p.nombre as paciente_nombre 
        FROM citas c
        INNER JOIN pacientes p ON c.paciente_id = p.id
        WHERE c.id = ?
    ");
    $stmt->execute([$cita_id]);
    $cita = $stmt->fetch();
    
    if ($cita) {
        // Registrar en auditoría
        registrar_auditoria(
            $pdo, 
            $_SESSION['usuario_id'], 
            'RECORDATORIO', 
            'citas', 
            $cita_id, 
            "Envió recordatorio WhatsApp a {$cita['paciente_nombre']} para cita del " . formatear_fecha($cita['fecha'])
        );
        
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Cita no encontrada']);
    }
    
} catch (PDOException $e) {
    error_log("Error al registrar recordatorio: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor']);
}