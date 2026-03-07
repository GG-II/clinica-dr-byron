<?php
/**
 * API - BUSCAR PACIENTES
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Endpoint para búsqueda de pacientes (autocompletado)
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

session_start();
define('ACCESS_GRANTED', true);

require_once '../../config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

// Verificar autenticación
verificar_sesion();

// Headers JSON
header('Content-Type: application/json');

// Obtener término de búsqueda
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// Validar término
if (strlen($q) < 2) {
    echo json_encode([
        'success' => false,
        'message' => 'El término de búsqueda debe tener al menos 2 caracteres',
        'pacientes' => []
    ]);
    exit;
}

try {
    // Buscar pacientes activos por nombre o código
    $sql = "
        SELECT 
            id,
            codigo,
            nombre,
            telefono,
            fecha_nacimiento,
            TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) as edad
        FROM pacientes 
        WHERE activo = 1
        AND (
            nombre LIKE ?
            OR codigo LIKE ?
        )
        ORDER BY nombre ASC
        LIMIT 20
    ";
    
    $termino = "%{$q}%";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$termino, $termino]);
    
    $pacientes = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'pacientes' => $pacientes,
        'total' => count($pacientes)
    ]);
    
} catch (PDOException $e) {
    error_log("Error en búsqueda de pacientes: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al buscar pacientes',
        'pacientes' => []
    ]);
}