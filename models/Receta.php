<?php
/**
 * MODELO: Receta
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Manejo de recetas médicas y sus medicamentos
 * 
 * TABLAS:
 * - recetas (id, numero_receta, consulta_id, paciente_id, indicaciones_generales, fecha_emision, created_at)
 * - recetas_detalle (id, receta_id, medicamento, dosis, via, frecuencia, duracion, orden)
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
 * Obtener lista de recetas con filtros
 * 
 * @param PDO $pdo Conexión a la BD
 * @param array $filtros Filtros opcionales (paciente_id, consulta_id, fecha_desde, fecha_hasta, numero_receta)
 * @param int $limit Límite de resultados
 * @param int $offset Offset para paginación
 * @return array Array de recetas con datos del paciente y médico
 */
function obtener_recetas($pdo, $filtros = [], $limit = 50, $offset = 0) {
    try {
        $sql = "SELECT 
                    r.id,
                    r.numero_receta,
                    r.consulta_id,
                    r.paciente_id,
                    r.indicaciones_generales,
                    r.fecha_emision,
                    r.created_at,
                    p.nombre AS paciente_nombre,
                    p.codigo AS paciente_codigo,
                    c.usuario_id,
                    u.nombre AS medico_nombre,
                    COUNT(rd.id) AS total_medicamentos
                FROM recetas r
                INNER JOIN pacientes p ON r.paciente_id = p.id
                LEFT JOIN consultas c ON r.consulta_id = c.id
                LEFT JOIN usuarios u ON c.usuario_id = u.id
                LEFT JOIN recetas_detalle rd ON r.id = rd.receta_id
                WHERE 1=1";
        
        $params = [];
        
        // Filtro por paciente
        if (!empty($filtros['paciente_id'])) {
            $sql .= " AND r.paciente_id = ?";
            $params[] = $filtros['paciente_id'];
        }
        
        // Filtro por consulta
        if (!empty($filtros['consulta_id'])) {
            $sql .= " AND r.consulta_id = ?";
            $params[] = $filtros['consulta_id'];
        }
        
        // Filtro por número de receta
        if (!empty($filtros['numero_receta'])) {
            $sql .= " AND r.numero_receta = ?";
            $params[] = $filtros['numero_receta'];
        }
        
        // Filtro por rango de fechas
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND r.fecha_emision >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND r.fecha_emision <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        $sql .= " GROUP BY r.id
                  ORDER BY r.fecha_emision DESC, r.numero_receta DESC
                  LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error al obtener recetas: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener receta completa por ID (con medicamentos)
 * 
 * @param PDO $pdo Conexión a la BD
 * @param int $id ID de la receta
 * @return array|null Array con datos completos o null si no existe
 */
function obtener_receta_completa($pdo, $id) {
    try {
        // Obtener datos de la receta
        $sql = "SELECT 
                    r.*,
                    p.nombre AS paciente_nombre,
                    p.codigo AS paciente_codigo,
                    p.fecha_nacimiento AS paciente_fecha_nac,
                    p.telefono AS paciente_telefono,
                    c.usuario_id,
                    c.fecha AS consulta_fecha,
                    c.tipo_consulta,
                    c.motivo_consulta,
                    u.nombre AS medico_nombre,
                    u.email AS medico_email
                FROM recetas r
                INNER JOIN pacientes p ON r.paciente_id = p.id
                LEFT JOIN consultas c ON r.consulta_id = c.id
                LEFT JOIN usuarios u ON c.usuario_id = u.id
                WHERE r.id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $receta = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$receta) {
            return null;
        }
        
        // Obtener medicamentos de la receta
        $sql_detalle = "SELECT * FROM recetas_detalle 
                        WHERE receta_id = ? 
                        ORDER BY orden ASC, id ASC";
        $stmt_detalle = $pdo->prepare($sql_detalle);
        $stmt_detalle->execute([$id]);
        $receta['medicamentos'] = $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);
        
        return $receta;
        
    } catch (PDOException $e) {
        error_log("Error al obtener receta completa: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtener recetas de un paciente específico
 */
function obtener_recetas_paciente($pdo, $paciente_id, $limit = 10) {
    try {
        $sql = "SELECT 
                    r.*,
                    u.nombre as medico_nombre,
                    GROUP_CONCAT(
                        CONCAT('{\"medicamento\":\"', rd.medicamento, 
                               '\",\"dosis\":\"', COALESCE(rd.dosis, ''), 
                               '\",\"via\":\"', COALESCE(rd.via, ''), 
                               '\",\"frecuencia\":\"', COALESCE(rd.frecuencia, ''), 
                               '\",\"duracion\":\"', COALESCE(rd.duracion, ''), 
                               '\"}')
                        ORDER BY rd.orden
                        SEPARATOR ','
                    ) as medicamentos_json
                FROM recetas r
                INNER JOIN usuarios u ON r.consulta_id IN (SELECT id FROM consultas WHERE usuario_id = u.id)
                LEFT JOIN recetas_detalle rd ON r.id = rd.receta_id
                WHERE r.paciente_id = ?
                GROUP BY r.id
                ORDER BY r.fecha_emision DESC, r.numero_receta DESC
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$paciente_id, $limit]);
        $recetas = $stmt->fetchAll();
        
        // Decodificar medicamentos JSON
        foreach ($recetas as &$receta) {
            if (!empty($receta['medicamentos_json'])) {
                $receta['medicamentos'] = json_decode('[' . $receta['medicamentos_json'] . ']', true);
            } else {
                $receta['medicamentos'] = [];
            }
        }
        
        return $recetas;
        
    } catch (PDOException $e) {
        error_log("Error al obtener recetas del paciente: " . $e->getMessage());
        return [];
    }
}

/**
 * Crear nueva receta con medicamentos (TRANSACCIÓN)
 * 
 * @param PDO $pdo Conexión a la BD
 * @param array $datos_receta Datos de la receta (consulta_id, paciente_id, indicaciones_generales, fecha_emision)
 * @param array $medicamentos Array de medicamentos [{medicamento, dosis, via, frecuencia, duracion, orden}, ...]
 * @return int|false ID de la receta creada o false si falla
 */
function crear_receta($pdo, $datos_receta, $medicamentos) {
    try {
        $pdo->beginTransaction();
        
        // Generar número correlativo de receta
        $stmt = $pdo->query("SELECT COALESCE(MAX(numero_receta), 0) + 1 AS siguiente FROM recetas");
        $numero_receta = $stmt->fetch(PDO::FETCH_ASSOC)['siguiente'];
        
        // Insertar receta
        $sql = "INSERT INTO recetas 
                (numero_receta, consulta_id, paciente_id, indicaciones_generales, fecha_emision) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $numero_receta,
            $datos_receta['consulta_id'],
            $datos_receta['paciente_id'],
            $datos_receta['indicaciones_generales'] ?? null,
            $datos_receta['fecha_emision'] ?? date('Y-m-d')
        ]);
        
        $receta_id = $pdo->lastInsertId();
        
        // Insertar medicamentos
        if (!empty($medicamentos)) {
            $sql_detalle = "INSERT INTO recetas_detalle 
                            (receta_id, medicamento, dosis, via, frecuencia, duracion, orden) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_detalle = $pdo->prepare($sql_detalle);
            
            foreach ($medicamentos as $index => $med) {
                $orden = $med['orden'] ?? ($index + 1);
                
                $stmt_detalle->execute([
                    $receta_id,
                    $med['medicamento'],
                    $med['dosis'] ?? null,
                    $med['via'] ?? null,
                    $med['frecuencia'] ?? null,
                    $med['duracion'] ?? null,
                    $orden
                ]);
            }
        }
        
        $pdo->commit();
        
        error_log("Receta creada: ID=$receta_id, Número=$numero_receta, Paciente={$datos_receta['paciente_id']}");
        
        return $receta_id;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error al crear receta: " . $e->getMessage());
        return false;
    }
}

/**
 * Contar total de recetas (para paginación)
 * 
 * @param PDO $pdo Conexión a la BD
 * @param array $filtros Filtros opcionales
 * @return int Total de recetas
 */
function contar_recetas($pdo, $filtros = []) {
    try {
        $sql = "SELECT COUNT(DISTINCT r.id) AS total
                FROM recetas r
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['paciente_id'])) {
            $sql .= " AND r.paciente_id = ?";
            $params[] = $filtros['paciente_id'];
        }
        
        if (!empty($filtros['consulta_id'])) {
            $sql .= " AND r.consulta_id = ?";
            $params[] = $filtros['consulta_id'];
        }
        
        if (!empty($filtros['numero_receta'])) {
            $sql .= " AND r.numero_receta = ?";
            $params[] = $filtros['numero_receta'];
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND r.fecha_emision >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND r.fecha_emision <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
    } catch (PDOException $e) {
        error_log("Error al contar recetas: " . $e->getMessage());
        return 0;
    }
}

/**
 * Verificar si una consulta ya tiene receta
 * 
 * @param PDO $pdo Conexión a la BD
 * @param int $consulta_id ID de la consulta
 * @return bool True si ya tiene receta
 */
function consulta_tiene_receta($pdo, $consulta_id) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM recetas WHERE consulta_id = ?");
        $stmt->execute([$consulta_id]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Error al verificar receta de consulta: " . $e->getMessage());
        return false;
    }
}