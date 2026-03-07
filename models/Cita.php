<?php
/**
 * MODELO DE CITAS
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Funciones para gestionar citas médicas
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');

// ============================================================================
// FUNCIONES DE LECTURA (READ)
// ============================================================================

/**
 * Obtener citas con filtros
 * 
 * @param PDO $pdo Conexión a BD
 * @param array $filtros Filtros opcionales (fecha_inicio, fecha_fin, estado, paciente_id)
 * @param int $limit Límite de resultados
 * @param int $offset Offset para paginación
 * @return array Lista de citas
 */
function obtener_citas($pdo, $filtros = [], $limit = 100, $offset = 0) {
    $sql = "
        SELECT 
            c.*,
            p.nombre as paciente_nombre,
            p.codigo as paciente_codigo,
            p.telefono as paciente_telefono
        FROM citas c
        INNER JOIN pacientes p ON c.paciente_id = p.id
        WHERE 1=1
    ";
    
    $params = [];
    
    // Filtro por fecha inicio
    if (!empty($filtros['fecha_inicio'])) {
        $sql .= " AND c.fecha >= ?";
        $params[] = $filtros['fecha_inicio'];
    }
    
    // Filtro por fecha fin
    if (!empty($filtros['fecha_fin'])) {
        $sql .= " AND c.fecha <= ?";
        $params[] = $filtros['fecha_fin'];
    }
    
    // Filtro por estado
    if (!empty($filtros['estado'])) {
        $sql .= " AND c.estado = ?";
        $params[] = $filtros['estado'];
    }
    
    // Filtro por paciente
    if (!empty($filtros['paciente_id'])) {
        $sql .= " AND c.paciente_id = ?";
        $params[] = $filtros['paciente_id'];
    }
    
    $sql .= " ORDER BY c.fecha ASC, c.hora_inicio ASC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

/**
 * Obtener cita por ID
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID de la cita
 * @return array|null Datos de la cita o null
 */
function obtener_cita_por_id($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            p.nombre as paciente_nombre,
            p.codigo as paciente_codigo,
            p.telefono as paciente_telefono,
            p.email as paciente_email
        FROM citas c
        INNER JOIN pacientes p ON c.paciente_id = p.id
        WHERE c.id = ?
    ");
    
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Obtener citas del día
 * 
 * @param PDO $pdo Conexión a BD
 * @param string $fecha Fecha en formato Y-m-d (default: hoy)
 * @return array Lista de citas del día
 */
function obtener_citas_del_dia($pdo, $fecha = null) {
    if ($fecha === null) {
        $fecha = date('Y-m-d');
    }
    
    return obtener_citas($pdo, ['fecha_inicio' => $fecha, 'fecha_fin' => $fecha]);
}

/**
 * Obtener citas de una semana
 * 
 * @param PDO $pdo Conexión a BD
 * @param string $fecha_inicio Fecha inicio de semana (Y-m-d)
 * @return array Lista de citas de la semana
 */
function obtener_citas_semana($pdo, $fecha_inicio) {
    $fecha_fin = date('Y-m-d', strtotime($fecha_inicio . ' +6 days'));
    
    return obtener_citas($pdo, [
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin
    ]);
}

/**
 * Obtener citas próximas (para recordatorios)
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $dias_adelante Días hacia adelante (default: 2)
 * @return array Lista de citas próximas
 */
function obtener_citas_proximas($pdo, $dias_adelante = 2) {
    $fecha_inicio = date('Y-m-d');
    $fecha_fin = date('Y-m-d', strtotime("+{$dias_adelante} days"));
    
    return obtener_citas($pdo, [
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin,
        'estado' => 'programada'
    ]);
}

/**
 * Verificar si hay conflicto de horario
 * 
 * @param PDO $pdo Conexión a BD
 * @param string $fecha Fecha de la cita
 * @param string $hora_inicio Hora inicio
 * @param string $hora_fin Hora fin
 * @param int|null $excluir_id ID de cita a excluir (para edición)
 * @return bool True si hay conflicto
 */
function verificar_conflicto_horario($pdo, $fecha, $hora_inicio, $hora_fin, $excluir_id = null) {
    $sql = "
        SELECT COUNT(*) 
        FROM citas 
        WHERE fecha = ? 
        AND estado != 'cancelada'
        AND (
            (hora_inicio < ? AND hora_fin > ?)
            OR (hora_inicio < ? AND hora_fin > ?)
            OR (hora_inicio >= ? AND hora_fin <= ?)
        )
    ";
    
    $params = [$fecha, $hora_fin, $hora_inicio, $hora_fin, $hora_inicio, $hora_inicio, $hora_fin];
    
    if ($excluir_id !== null) {
        $sql .= " AND id != ?";
        $params[] = $excluir_id;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchColumn() > 0;
}

// ============================================================================
// FUNCIONES DE CREACIÓN (CREATE)
// ============================================================================

/**
 * Crear nueva cita
 * 
 * @param PDO $pdo Conexión a BD
 * @param array $datos Datos de la cita
 * @return array ['success' => bool, 'message' => string, 'id' => int]
 */
function crear_cita($pdo, $datos) {
    try {
        // Validar datos
        $validacion = validar_datos_cita($datos, true);
        if (!$validacion['success']) {
            return $validacion;
        }
        
        // Verificar conflicto de horario
        if (verificar_conflicto_horario($pdo, $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin'])) {
            return [
                'success' => false,
                'message' => 'Ya existe una cita en ese horario. Por favor elija otro horario.'
            ];
        }
        
        // Insertar cita
        $stmt = $pdo->prepare("
            INSERT INTO citas (
                paciente_id, usuario_id, fecha, hora_inicio, hora_fin,
                motivo, estado, notas
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $datos['paciente_id'],
            $_SESSION['usuario_id'], // ✅ AGREGADO
            $datos['fecha'],
            $datos['hora_inicio'],
            $datos['hora_fin'],
            $datos['motivo'] ?? null,
            $datos['estado'] ?? 'programada',
            $datos['notas'] ?? null
        ]);
        
        $cita_id = $pdo->lastInsertId();
        
        // Registrar auditoría
        registrar_auditoria($pdo, $_SESSION['usuario_id'], 'CREAR', 'citas', $cita_id, 
            "Creó cita para paciente ID: {$datos['paciente_id']}");
        
        log_mensaje("Cita creada exitosamente - ID: {$cita_id}", 'info');
        
        return [
            'success' => true,
            'message' => 'Cita creada exitosamente',
            'id' => $cita_id
        ];
        
    } catch (PDOException $e) {
        log_mensaje("Error al crear cita: " . $e->getMessage(), 'error');
        return [
            'success' => false,
            'message' => 'Error al crear la cita. Por favor intente nuevamente.'
        ];
    }
}

// ============================================================================
// FUNCIONES DE ACTUALIZACIÓN (UPDATE)
// ============================================================================

/**
 * Actualizar cita
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID de la cita
 * @param array $datos Datos a actualizar
 * @return array ['success' => bool, 'message' => string]
 */
function actualizar_cita($pdo, $id, $datos) {
    try {
        // Validar datos
        $validacion = validar_datos_cita($datos, false);
        if (!$validacion['success']) {
            return $validacion;
        }
        
        // Verificar que la cita existe
        $cita = obtener_cita_por_id($pdo, $id);
        if (!$cita) {
            return [
                'success' => false,
                'message' => 'La cita no existe'
            ];
        }
        
        // Verificar conflicto de horario (excluyendo la cita actual)
        if (isset($datos['fecha']) && isset($datos['hora_inicio']) && isset($datos['hora_fin'])) {
            if (verificar_conflicto_horario($pdo, $datos['fecha'], $datos['hora_inicio'], $datos['hora_fin'], $id)) {
                return [
                    'success' => false,
                    'message' => 'Ya existe una cita en ese horario. Por favor elija otro horario.'
                ];
            }
        }
        
        // Construir query dinámico
        $campos = [];
        $valores = [];
        
        $campos_permitidos = ['paciente_id', 'usuario_id', 'fecha', 'hora_inicio', 'hora_fin', 'motivo', 'estado', 'notas'];
        
        foreach ($campos_permitidos as $campo) {
            if (array_key_exists($campo, $datos)) {
                $campos[] = "{$campo} = ?";
                $valores[] = $datos[$campo];
            }
        }
        
        if (empty($campos)) {
            return [
                'success' => false,
                'message' => 'No hay datos para actualizar'
            ];
        }
        
        $valores[] = $id;
        
        $sql = "UPDATE citas SET " . implode(', ', $campos) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);
        
        // Registrar auditoría
        registrar_auditoria($pdo, $_SESSION['usuario_id'], 'ACTUALIZAR', 'citas', $id, 
            "Actualizó cita");
        
        log_mensaje("Cita actualizada - ID: {$id}", 'info');
        
        return [
            'success' => true,
            'message' => 'Cita actualizada exitosamente'
        ];
        
    } catch (PDOException $e) {
        log_mensaje("Error al actualizar cita: " . $e->getMessage(), 'error');
        return [
            'success' => false,
            'message' => 'Error al actualizar la cita. Por favor intente nuevamente.'
        ];
    }
}

/**
 * Cambiar estado de cita
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID de la cita
 * @param string $nuevo_estado Estado nuevo
 * @return array ['success' => bool, 'message' => string]
 */
function cambiar_estado_cita($pdo, $id, $nuevo_estado) {
    $estados_validos = ['programada', 'confirmada', 'atendida', 'cancelada'];
    
    if (!in_array($nuevo_estado, $estados_validos)) {
        return [
            'success' => false,
            'message' => 'Estado no válido'
        ];
    }
    
    return actualizar_cita($pdo, $id, ['estado' => $nuevo_estado]);
}

/**
 * Cancelar cita
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID de la cita
 * @return array ['success' => bool, 'message' => string]
 */
function cancelar_cita($pdo, $id) {
    return cambiar_estado_cita($pdo, $id, 'cancelada');
}

/**
 * Confirmar cita
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID de la cita
 * @return array ['success' => bool, 'message' => string]
 */
function confirmar_cita($pdo, $id) {
    return cambiar_estado_cita($pdo, $id, 'confirmada');
}

/**
 * Marcar cita como atendida
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID de la cita
 * @return array ['success' => bool, 'message' => string]
 */
function marcar_cita_atendida($pdo, $id) {
    return cambiar_estado_cita($pdo, $id, 'atendida');
}

// ============================================================================
// FUNCIONES DE ELIMINACIÓN (DELETE)
// ============================================================================

/**
 * Eliminar cita (hard delete)
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID de la cita
 * @return array ['success' => bool, 'message' => string]
 */
function eliminar_cita($pdo, $id) {
    try {
        // Verificar que la cita existe
        $cita = obtener_cita_por_id($pdo, $id);
        if (!$cita) {
            return [
                'success' => false,
                'message' => 'La cita no existe'
            ];
        }
        
        // Verificar si tiene consulta asociada
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM consultas WHERE cita_id = ?");
        $stmt->execute([$id]);
        $tiene_consulta = $stmt->fetchColumn() > 0;
        
        if ($tiene_consulta) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar una cita que tiene consulta asociada. Cámbiela a estado "cancelada" en su lugar.'
            ];
        }
        
        // Eliminar cita
        $stmt = $pdo->prepare("DELETE FROM citas WHERE id = ?");
        $stmt->execute([$id]);
        
        // Registrar auditoría
        registrar_auditoria($pdo, $_SESSION['usuario_id'], 'ELIMINAR', 'citas', $id, 
            "Eliminó cita de paciente: {$cita['paciente_nombre']}");
        
        log_mensaje("Cita eliminada - ID: {$id}", 'info');
        
        return [
            'success' => true,
            'message' => 'Cita eliminada exitosamente'
        ];
        
    } catch (PDOException $e) {
        log_mensaje("Error al eliminar cita: " . $e->getMessage(), 'error');
        return [
            'success' => false,
            'message' => 'Error al eliminar la cita. Por favor intente nuevamente.'
        ];
    }
}

// ============================================================================
// FUNCIONES DE VALIDACIÓN
// ============================================================================

/**
 * Validar datos de cita
 * 
 * @param array $datos Datos a validar
 * @param bool $es_nueva True si es creación, false si es actualización
 * @return array ['success' => bool, 'message' => string]
 */
function validar_datos_cita($datos, $es_nueva = true) {
    $errores = [];
    
    // Validar paciente_id (requerido en creación)
    if ($es_nueva && empty($datos['paciente_id'])) {
        $errores[] = 'El paciente es requerido';
    }
    
    // Validar fecha (requerido en creación)
    if ($es_nueva && empty($datos['fecha'])) {
        $errores[] = 'La fecha es requerida';
    } elseif (!empty($datos['fecha'])) {
        $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha']);
        if (!$fecha) {
            $errores[] = 'Fecha no válida';
        }
    }
    
    // Validar hora_inicio (requerido en creación)
    if ($es_nueva && empty($datos['hora_inicio'])) {
        $errores[] = 'La hora de inicio es requerida';
    } elseif (!empty($datos['hora_inicio'])) {
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $datos['hora_inicio'])) {
            $errores[] = 'Hora de inicio no válida';
        }
    }
    
    // Validar hora_fin (requerido en creación)
    if ($es_nueva && empty($datos['hora_fin'])) {
        $errores[] = 'La hora de fin es requerida';
    } elseif (!empty($datos['hora_fin'])) {
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $datos['hora_fin'])) {
            $errores[] = 'Hora de fin no válida';
        }
        
        // Validar que hora_fin > hora_inicio
        if (!empty($datos['hora_inicio']) && $datos['hora_fin'] <= $datos['hora_inicio']) {
            $errores[] = 'La hora de fin debe ser posterior a la hora de inicio';
        }
    }
    
    // Validar estado
    if (!empty($datos['estado'])) {
        $estados_validos = ['programada', 'confirmada', 'atendida', 'cancelada'];
        if (!in_array($datos['estado'], $estados_validos)) {
            $errores[] = 'Estado no válido';
        }
    }
    
    if (!empty($errores)) {
        return [
            'success' => false,
            'message' => implode('. ', $errores)
        ];
    }
    
    return ['success' => true];
}

// ============================================================================
// FUNCIONES AUXILIARES
// ============================================================================

/**
 * Obtener color según estado de cita
 * 
 * @param string $estado Estado de la cita
 * @return string Código de color hexadecimal
 */
function obtener_color_estado_cita($estado) {
    $colores = [
        'programada' => '#0ea5e9',  // Azul cyan
        'confirmada' => '#22c55e',  // Verde
        'atendida' => '#64748b',    // Gris
        'cancelada' => '#ef4444'    // Rojo
    ];
    
    return $colores[$estado] ?? '#6b7280';
}

/**
 * Obtener nombre de estado en español
 * 
 * @param string $estado Estado de la cita
 * @return string Nombre en español
 */
function obtener_nombre_estado_cita($estado) {
    $nombres = [
        'programada' => 'Programada',
        'confirmada' => 'Confirmada',
        'atendida' => 'Atendida',
        'cancelada' => 'Cancelada'
    ];
    
    return $nombres[$estado] ?? $estado;
}