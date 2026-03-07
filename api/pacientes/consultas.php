<?php
/**
 * API: OBTENER CONSULTAS DE UN PACIENTE
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Endpoint para cargar consultas de un paciente (para dropdown en facturación)
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
require_once MODELS_PATH . 'Factura.php';
require_once MODELS_PATH . 'Consulta.php';

// Verificar autenticación
if (!verificar_sesion()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Obtener paciente_id
$paciente_id = isset($_GET['paciente_id']) ? (int)$_GET['paciente_id'] : 0;

if ($paciente_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de paciente inválido']);
    exit;
}

// Obtener consultas del paciente
$consultas = obtener_consultas_paciente_dropdown($pdo, $paciente_id);

// Formatear respuesta
$resultado = [];
foreach ($consultas as $consulta) {
    $resultado[] = [
        'id' => $consulta['id'],
        'fecha' => $consulta['fecha'],
        'fecha_format' => formatear_fecha($consulta['fecha']),
        'tipo_consulta' => $consulta['tipo_consulta'],
        'tipo_nombre' => obtener_nombre_tipo_consulta($consulta['tipo_consulta']),
        'motivo_consulta' => $consulta['motivo_consulta']
    ];
}

header('Content-Type: application/json');
echo json_encode($resultado);