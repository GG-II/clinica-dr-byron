<?php
/**
 * MODELO: Examen
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Manejo de exámenes de laboratorio (SIMPLE)
 * 
 * TABLA: examenes_laboratorio
 * - id, paciente_id, consulta_id (NULL), tipo_examen, fecha_solicitud
 * - descripcion, estado (solicitado/recibido), resultado, fecha_resultado
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

// Seguridad: Evitar acceso directo
if (!defined('ACCESS_GRANTED')) {
    http_response_code(403);
    die('Acceso denegado');
}

/**
 * Tipos de exámenes comunes (para dropdown)
 */
function obtener_tipos_examenes_comunes() {
    return [
        'Hemograma completo',
        'Química sanguínea',
        'Perfil hormonal',
        'Papanicolaou',
        'Colposcopia',
        'Examen general de orina',
        'Ultrasonido',
        'Prueba de embarazo',
        'Perfil tiroideo',
        'Glucosa',
        'Otro' // Opción para texto libre
    ];
}

/**
 * Obtener lista de exámenes con filtros
 * 
 * @param PDO $pdo Conexión a la BD
 * @param array $filtros Filtros opcionales (paciente_id, estado, fecha_desde, fecha_hasta)
 * @param int $limit Límite de resultados
 * @param int $offset Offset para paginación
 * @return array Array de exámenes
 */
function obtener_examenes($pdo, $filtros = [], $limit = 50, $offset = 0) {
    try {
        $sql = "SELECT 
                    e.*,
                    p.nombre AS paciente_nombre,
                    p.codigo AS paciente_codigo,
                    c.tipo_consulta
                FROM examenes_laboratorio e
                INNER JOIN pacientes p ON e.paciente_id = p.id
                LEFT JOIN consultas c ON e.consulta_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        // Filtro por paciente
        if (!empty($filtros['paciente_id'])) {
            $sql .= " AND e.paciente_id = ?";
            $params[] = $filtros['paciente_id'];
        }
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $sql .= " AND e.estado = ?";
            $params[] = $filtros['estado'];
        }
        
        // Filtro por rango de fechas (fecha solicitud)
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND e.fecha_solicitud >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND e.fecha_solicitud <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        $sql .= " ORDER BY e.fecha_solicitud DESC, e.id DESC
                  LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error al obtener exámenes: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener examen completo por ID
 * 
 * @param PDO $pdo Conexión a la BD
 * @param int $id ID del examen
 * @return array|null Array con datos completos o null si no existe
 */
function obtener_examen_completo($pdo, $id) {
    try {
        $sql = "SELECT 
                    e.*,
                    p.nombre AS paciente_nombre,
                    p.codigo AS paciente_codigo,
                    p.fecha_nacimiento AS paciente_fecha_nac,
                    p.telefono AS paciente_telefono,
                    c.fecha AS consulta_fecha,
                    c.tipo_consulta,
                    c.motivo_consulta
                FROM examenes_laboratorio e
                INNER JOIN pacientes p ON e.paciente_id = p.id
                LEFT JOIN consultas c ON e.consulta_id = c.id
                WHERE e.id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error al obtener examen completo: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtener exámenes de un paciente específico
 * 
 * @param PDO $pdo Conexión a la BD
 * @param int $paciente_id ID del paciente
 * @param int $limit Límite de resultados
 * @return array Array de exámenes del paciente
 */
function obtener_examenes_paciente($pdo, $paciente_id, $limit = 10) {
    try {
        $sql = "SELECT 
                    e.*,
                    c.tipo_consulta
                FROM examenes_laboratorio e
                LEFT JOIN consultas c ON e.consulta_id = c.id
                WHERE e.paciente_id = ?
                ORDER BY e.fecha_solicitud DESC
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$paciente_id, $limit]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error al obtener exámenes del paciente: " . $e->getMessage());
        return [];
    }
}

/**
 * Crear nuevo examen de laboratorio
 * 
 * @param PDO $pdo Conexión a la BD
 * @param array $datos Datos del examen
 * @return int|false ID del examen creado o false si falla
 */
function crear_examen($pdo, $datos) {
    try {
        $sql = "INSERT INTO examenes_laboratorio 
                (paciente_id, consulta_id, tipo_examen, fecha_solicitud, descripcion, estado) 
                VALUES (?, ?, ?, ?, ?, 'solicitado')";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $datos['paciente_id'],
            $datos['consulta_id'] ?? null,
            $datos['tipo_examen'],
            $datos['fecha_solicitud'] ?? date('Y-m-d'),
            $datos['descripcion'] ?? null
        ]);
        
        if ($result) {
            $id = $pdo->lastInsertId();
            error_log("Examen creado: ID=$id, Paciente={$datos['paciente_id']}, Tipo={$datos['tipo_examen']}");
            return $id;
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Error al crear examen: " . $e->getMessage());
        return false;
    }
}

/**
 * Actualizar examen (agregar resultado)
 * 
 * @param PDO $pdo Conexión a la BD
 * @param int $id ID del examen
 * @param array $datos Datos actualizados
 * @return bool True si se actualizó correctamente
 */
function actualizar_examen($pdo, $id, $datos) {
    try {
        $sql = "UPDATE examenes_laboratorio 
                SET resultado = ?,
                    fecha_resultado = ?,
                    estado = ?
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $datos['resultado'],
            $datos['fecha_resultado'] ?? date('Y-m-d'),
            $datos['estado'] ?? 'recibido',
            $id
        ]);
        
        if ($result) {
            error_log("Examen actualizado: ID=$id, Estado={$datos['estado']}");
        }
        
        return $result;
        
    } catch (PDOException $e) {
        error_log("Error al actualizar examen: " . $e->getMessage());
        return false;
    }
}

/**
 * Contar total de exámenes (para paginación)
 * 
 * @param PDO $pdo Conexión a la BD
 * @param array $filtros Filtros opcionales
 * @return int Total de exámenes
 */
function contar_examenes($pdo, $filtros = []) {
    try {
        $sql = "SELECT COUNT(*) AS total FROM examenes_laboratorio e WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['paciente_id'])) {
            $sql .= " AND e.paciente_id = ?";
            $params[] = $filtros['paciente_id'];
        }
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND e.estado = ?";
            $params[] = $filtros['estado'];
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND e.fecha_solicitud >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND e.fecha_solicitud <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
    } catch (PDOException $e) {
        error_log("Error al contar exámenes: " . $e->getMessage());
        return 0;
    }
}

/**
 * Obtener consultas del paciente (para dropdown)
 * 
 * @param PDO $pdo Conexión a la BD
 * @param int $paciente_id ID del paciente
 * @return array Array de consultas
 */
function obtener_consultas_paciente_para_examen($pdo, $paciente_id) {
    try {
        $sql = "SELECT 
                    c.id,
                    c.fecha,
                    c.tipo_consulta,
                    c.motivo_consulta
                FROM consultas c
                WHERE c.paciente_id = ?
                ORDER BY c.fecha DESC
                LIMIT 20";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$paciente_id]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error al obtener consultas para examen: " . $e->getMessage());
        return [];
    }
}