<?php
/**
 * MODELO DE CONSULTAS
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Funciones para gestionar consultas médicas
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');

// ============================================================================
// FUNCIONES DE LECTURA (READ)
// ============================================================================

/**
 * Obtener consultas con filtros
 * 
 * @param PDO $pdo Conexión a BD
 * @param array $filtros Filtros opcionales
 * @param int $limit Límite de resultados
 * @param int $offset Offset para paginación
 * @return array Lista de consultas
 */
function obtener_consultas($pdo, $filtros = [], $limit = 50, $offset = 0) {
    $sql = "
        SELECT 
            c.*,
            p.nombre as paciente_nombre,
            p.codigo as paciente_codigo,
            u.nombre as medico_nombre,
            cit.motivo as motivo_cita
        FROM consultas c
        INNER JOIN pacientes p ON c.paciente_id = p.id
        INNER JOIN usuarios u ON c.usuario_id = u.id
        LEFT JOIN citas cit ON c.cita_id = cit.id
        WHERE 1=1
    ";
    
    $params = [];
    
    // Filtro por paciente
    if (!empty($filtros['paciente_id'])) {
        $sql .= " AND c.paciente_id = ?";
        $params[] = $filtros['paciente_id'];
    }
    
    // Filtro por tipo
    if (!empty($filtros['tipo_consulta'])) {
        $sql .= " AND c.tipo_consulta = ?";
        $params[] = $filtros['tipo_consulta'];
    }
    
    // Filtro por fecha
    if (!empty($filtros['fecha_desde'])) {
        $sql .= " AND DATE(c.fecha) >= ?";
        $params[] = $filtros['fecha_desde'];
    }
    
    if (!empty($filtros['fecha_hasta'])) {
        $sql .= " AND DATE(c.fecha) <= ?";
        $params[] = $filtros['fecha_hasta'];
    }
    
    // Filtro por médico
    if (!empty($filtros['usuario_id'])) {
        $sql .= " AND c.usuario_id = ?";
        $params[] = $filtros['usuario_id'];
    }
    
    $sql .= " ORDER BY c.fecha DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

/**
 * Obtener consulta por ID con todos los datos relacionados
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID de la consulta
 * @return array|null Datos completos de la consulta
 */
function obtener_consulta_completa($pdo, $id) {
    // Datos principales de la consulta
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            p.nombre as paciente_nombre,
            p.codigo as paciente_codigo,
            p.fecha_nacimiento as paciente_fecha_nacimiento,
            p.telefono as paciente_telefono,
            u.nombre as medico_nombre,
            cit.motivo as motivo_cita,
            cit.fecha as fecha_cita
        FROM consultas c
        INNER JOIN pacientes p ON c.paciente_id = p.id
        INNER JOIN usuarios u ON c.usuario_id = u.id
        LEFT JOIN citas cit ON c.cita_id = cit.id
        WHERE c.id = ?
    ");
    
    $stmt->execute([$id]);
    $consulta = $stmt->fetch();
    
    if (!$consulta) {
        return null;
    }
    
    // Signos vitales
    $stmt = $pdo->prepare("
        SELECT * FROM signos_vitales 
        WHERE consulta_id = ?
    ");
    $stmt->execute([$id]);
    $consulta['signos_vitales'] = $stmt->fetch();
    
    // Receta (si existe)
    $stmt = $pdo->prepare("
        SELECT r.*, rd.medicamento, rd.dosis, rd.via, rd.frecuencia, rd.duracion
        FROM recetas r
        LEFT JOIN recetas_detalle rd ON r.id = rd.receta_id
        WHERE r.consulta_id = ?
        ORDER BY rd.orden ASC
    ");
    $stmt->execute([$id]);
    $consulta['receta'] = $stmt->fetchAll();
    
    // Ultrasonido (si existe)
    $stmt = $pdo->prepare("
        SELECT * FROM ultrasonidos 
        WHERE consulta_id = ?
    ");
    $stmt->execute([$id]);
    $consulta['ultrasonido'] = $stmt->fetch();
    
    // Exámenes (si hay)
    $stmt = $pdo->prepare("
        SELECT * FROM examenes_laboratorio 
        WHERE consulta_id = ?
        ORDER BY fecha_solicitud DESC
    ");
    $stmt->execute([$id]);
    $consulta['examenes'] = $stmt->fetchAll();
    
    return $consulta;
}

/**
 * Obtener consultas de un paciente
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $paciente_id ID del paciente
 * @param int $limit Límite de consultas
 * @return array Lista de consultas
 */
function obtener_consultas_paciente($pdo, $paciente_id, $limit = 10) {
    return obtener_consultas($pdo, ['paciente_id' => $paciente_id], $limit);
}

// ============================================================================
// FUNCIONES DE CREACIÓN (CREATE)
// ============================================================================

/**
 * Crear nueva consulta
 * 
 * @param PDO $pdo Conexión a BD
 * @param array $datos Datos de la consulta
 * @return array ['success' => bool, 'message' => string, 'id' => int]
 */
function crear_consulta($pdo, $datos) {
    try {
        $pdo->beginTransaction();
        
        // Validar datos principales
        if (empty($datos['paciente_id']) || empty($datos['usuario_id'])) {
            throw new Exception('Faltan datos requeridos');
        }
        
        // Insertar consulta
        $stmt = $pdo->prepare("
            INSERT INTO consultas (
                paciente_id, cita_id, usuario_id, fecha,
                tipo_consulta, motivo_consulta, notas, diagnostico
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $datos['paciente_id'],
            $datos['cita_id'] ?? null,
            $datos['usuario_id'],
            $datos['fecha'] ?? date('Y-m-d H:i:s'),
            $datos['tipo_consulta'] ?? 'control',
            $datos['motivo_consulta'] ?? null,
            $datos['notas'] ?? null,
            $datos['diagnostico'] ?? null
        ]);
        
        $consulta_id = $pdo->lastInsertId();
        
        // Insertar signos vitales (si existen)
        if (!empty($datos['signos_vitales'])) {
            $sv = $datos['signos_vitales'];
            $stmt = $pdo->prepare("
                INSERT INTO signos_vitales (
                    consulta_id, peso, talla, presion_arterial, temperatura
                ) VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $consulta_id,
                !empty($sv['peso']) ? $sv['peso'] : null,
                !empty($sv['talla']) ? $sv['talla'] : null,
                !empty($sv['presion_arterial']) ? $sv['presion_arterial'] : null,
                !empty($sv['temperatura']) ? $sv['temperatura'] : null
            ]);
        }
        
        // Registrar auditoría
        registrar_auditoria($pdo, $datos['usuario_id'], 'CREAR', 'consultas', $consulta_id,
            "Creó consulta para paciente ID: {$datos['paciente_id']}");
        
        log_mensaje("Consulta creada - ID: {$consulta_id}", 'info');
        
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => 'Consulta registrada exitosamente',
            'id' => $consulta_id
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        log_mensaje("Error al crear consulta: " . $e->getMessage(), 'error');
        return [
            'success' => false,
            'message' => 'Error al registrar la consulta'
        ];
    }
}

// ============================================================================
// FUNCIONES DE ACTUALIZACIÓN (UPDATE)
// ============================================================================

/**
 * Actualizar consulta
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID de la consulta
 * @param array $datos Datos a actualizar
 * @return array ['success' => bool, 'message' => string]
 */
function actualizar_consulta($pdo, $id, $datos) {
    try {
        $pdo->beginTransaction();
        
        // Construir query dinámico
        $campos = [];
        $valores = [];
        
        $campos_permitidos = [
            'tipo_consulta', 'motivo_consulta', 'notas', 'diagnostico'
        ];
        
        foreach ($campos_permitidos as $campo) {
            if (array_key_exists($campo, $datos)) {
                $campos[] = "{$campo} = ?";
                $valores[] = $datos[$campo];
            }
        }
        
        if (empty($campos)) {
            throw new Exception('No hay datos para actualizar');
        }
        
        $valores[] = $id;
        
        $sql = "UPDATE consultas SET " . implode(', ', $campos) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);
        
        // Actualizar signos vitales si existen
        if (!empty($datos['signos_vitales'])) {
            $sv = $datos['signos_vitales'];
            
            // Verificar si ya existen signos vitales
            $stmt = $pdo->prepare("SELECT id FROM signos_vitales WHERE consulta_id = ?");
            $stmt->execute([$id]);
            $existe = $stmt->fetch();
            
            if ($existe) {
                // Actualizar
                $stmt = $pdo->prepare("
                    UPDATE signos_vitales 
                    SET peso = ?, talla = ?, presion_arterial = ?, temperatura = ?
                    WHERE consulta_id = ?
                ");
                
                $stmt->execute([
                    !empty($sv['peso']) ? $sv['peso'] : null,
                    !empty($sv['talla']) ? $sv['talla'] : null,
                    !empty($sv['presion_arterial']) ? $sv['presion_arterial'] : null,
                    !empty($sv['temperatura']) ? $sv['temperatura'] : null,
                    $id
                ]);
            } else {
                // Insertar
                $stmt = $pdo->prepare("
                    INSERT INTO signos_vitales (
                        consulta_id, peso, talla, presion_arterial, temperatura
                    ) VALUES (?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $id,
                    !empty($sv['peso']) ? $sv['peso'] : null,
                    !empty($sv['talla']) ? $sv['talla'] : null,
                    !empty($sv['presion_arterial']) ? $sv['presion_arterial'] : null,
                    !empty($sv['temperatura']) ? $sv['temperatura'] : null
                ]);
            }
        }
        
        // Registrar auditoría
        registrar_auditoria($pdo, $_SESSION['usuario_id'], 'ACTUALIZAR', 'consultas', $id,
            "Actualizó consulta");
        
        log_mensaje("Consulta actualizada - ID: {$id}", 'info');
        
        $pdo->commit();
        
        return [
            'success' => true,
            'message' => 'Consulta actualizada exitosamente'
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        log_mensaje("Error al actualizar consulta: " . $e->getMessage(), 'error');
        return [
            'success' => false,
            'message' => 'Error al actualizar la consulta'
        ];
    }
}

// ============================================================================
// FUNCIONES AUXILIARES
// ============================================================================

/**
 * Obtener nombre de tipo de consulta en español
 * 
 * @param string $tipo Tipo de consulta
 * @return string Nombre en español
 */
function obtener_nombre_tipo_consulta($tipo) {
    $tipos = [
        'primera_vez' => 'Primera Vez',
        'control' => 'Control',
        'urgencia' => 'Urgencia',
        'procedimiento' => 'Procedimiento'
    ];
    
    return $tipos[$tipo] ?? $tipo;
}

/**
 * Obtener color según tipo de consulta
 * 
 * @param string $tipo Tipo de consulta
 * @return string Clase de color Bootstrap
 */
function obtener_color_tipo_consulta($tipo) {
    $colores = [
        'primera_vez' => 'primary',
        'control' => 'info',
        'urgencia' => 'danger',
        'procedimiento' => 'warning'
    ];
    
    return $colores[$tipo] ?? 'secondary';
}