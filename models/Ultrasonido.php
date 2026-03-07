<?php
/**
 * MODELO: Ultrasonido
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Manejo de informes de ultrasonido obstétrico
 * 
 * TABLA: ultrasonidos
 * - id, consulta_id, paciente_id, fecha, edad_gestacional
 * - dbp (Diámetro biparietal), cc (Circunferencia cefálica)
 * - ca (Circunferencia abdominal), lf (Longitud del fémur)
 * - peso_estimado, observaciones, conclusion, created_at
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
 * Obtener lista de ultrasonidos con filtros
 * 
 * @param PDO $pdo Conexión a la BD
 * @param array $filtros Filtros opcionales (paciente_id, consulta_id, fecha_desde, fecha_hasta)
 * @param int $limit Límite de resultados
 * @param int $offset Offset para paginación
 * @return array Array de ultrasonidos
 */
function obtener_ultrasonidos($pdo, $filtros = [], $limit = 50, $offset = 0) {
    try {
        $sql = "SELECT 
                    u.*,
                    p.nombre AS paciente_nombre,
                    p.codigo AS paciente_codigo,
                    c.usuario_id,
                    us.nombre AS medico_nombre
                FROM ultrasonidos u
                INNER JOIN pacientes p ON u.paciente_id = p.id
                LEFT JOIN consultas c ON u.consulta_id = c.id
                LEFT JOIN usuarios us ON c.usuario_id = us.id
                WHERE 1=1";
        
        $params = [];
        
        // Filtro por paciente
        if (!empty($filtros['paciente_id'])) {
            $sql .= " AND u.paciente_id = ?";
            $params[] = $filtros['paciente_id'];
        }
        
        // Filtro por consulta
        if (!empty($filtros['consulta_id'])) {
            $sql .= " AND u.consulta_id = ?";
            $params[] = $filtros['consulta_id'];
        }
        
        // Filtro por rango de fechas
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND u.fecha >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND u.fecha <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        $sql .= " ORDER BY u.fecha DESC, u.id DESC
                  LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error al obtener ultrasonidos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener ultrasonido completo por ID
 * 
 * @param PDO $pdo Conexión a la BD
 * @param int $id ID del ultrasonido
 * @return array|null Array con datos completos o null si no existe
 */
function obtener_ultrasonido_completo($pdo, $id) {
    try {
        $sql = "SELECT 
                    u.*,
                    p.nombre AS paciente_nombre,
                    p.codigo AS paciente_codigo,
                    p.fecha_nacimiento AS paciente_fecha_nac,
                    p.telefono AS paciente_telefono,
                    c.usuario_id,
                    c.fecha AS consulta_fecha,
                    c.tipo_consulta,
                    us.nombre AS medico_nombre,
                    us.email AS medico_email
                FROM ultrasonidos u
                INNER JOIN pacientes p ON u.paciente_id = p.id
                LEFT JOIN consultas c ON u.consulta_id = c.id
                LEFT JOIN usuarios us ON c.usuario_id = us.id
                WHERE u.id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error al obtener ultrasonido completo: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtener ultrasonidos de un paciente específico
 */
function obtener_ultrasonidos_paciente($pdo, $paciente_id, $limit = 10) {
    try {
        $sql = "SELECT 
                    u.*,
                    c.fecha as consulta_fecha,
                    c.tipo_consulta,
                    us.nombre as doctor_nombre
                FROM ultrasonidos u
                INNER JOIN consultas c ON u.consulta_id = c.id
                INNER JOIN usuarios us ON c.usuario_id = us.id
                WHERE u.paciente_id = ?
                ORDER BY u.fecha DESC
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$paciente_id, $limit]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error al obtener ultrasonidos del paciente: " . $e->getMessage());
        return [];
    }
}

/**
 * Crear nuevo ultrasonido
 * 
 * @param PDO $pdo Conexión a la BD
 * @param array $datos Datos del ultrasonido (consulta_id es obligatorio)
 * @return int|false ID del ultrasonido creado o false si falla
 */
function crear_ultrasonido($pdo, $datos) {
    try {
        // Validar que venga consulta_id
        if (empty($datos['consulta_id'])) {
            error_log("Error: Intento de crear ultrasonido sin consulta_id");
            return false;
        }
        
        $sql = "INSERT INTO ultrasonidos 
                (consulta_id, paciente_id, fecha, edad_gestacional, 
                 dbp, cc, ca, lf, peso_estimado, observaciones, conclusion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $datos['consulta_id'],
            $datos['paciente_id'],
            $datos['fecha'] ?? date('Y-m-d'),
            $datos['edad_gestacional'] ?? null,
            $datos['dbp'] ?? null,
            $datos['cc'] ?? null,
            $datos['ca'] ?? null,
            $datos['lf'] ?? null,
            $datos['peso_estimado'] ?? null,
            $datos['observaciones'] ?? null,
            $datos['conclusion'] ?? null
        ]);
        
        if ($result) {
            $id = $pdo->lastInsertId();
            error_log("Ultrasonido creado: ID=$id, Paciente={$datos['paciente_id']}, Consulta={$datos['consulta_id']}");
            return $id;
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Error al crear ultrasonido: " . $e->getMessage());
        return false;
    }
}

/**
 * Actualizar ultrasonido existente
 * 
 * @param PDO $pdo Conexión a la BD
 * @param int $id ID del ultrasonido
 * @param array $datos Datos actualizados
 * @return bool True si se actualizó correctamente
 */
function actualizar_ultrasonido($pdo, $id, $datos) {
    try {
        $sql = "UPDATE ultrasonidos 
                SET fecha = ?,
                    edad_gestacional = ?,
                    dbp = ?,
                    cc = ?,
                    ca = ?,
                    lf = ?,
                    peso_estimado = ?,
                    observaciones = ?,
                    conclusion = ?
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $datos['fecha'] ?? date('Y-m-d'),
            $datos['edad_gestacional'] ?? null,
            $datos['dbp'] ?? null,
            $datos['cc'] ?? null,
            $datos['ca'] ?? null,
            $datos['lf'] ?? null,
            $datos['peso_estimado'] ?? null,
            $datos['observaciones'] ?? null,
            $datos['conclusion'] ?? null,
            $id
        ]);
        
        if ($result) {
            error_log("Ultrasonido actualizado: ID=$id");
        }
        
        return $result;
        
    } catch (PDOException $e) {
        error_log("Error al actualizar ultrasonido: " . $e->getMessage());
        return false;
    }
}

/**
 * Contar total de ultrasonidos (para paginación)
 * 
 * @param PDO $pdo Conexión a la BD
 * @param array $filtros Filtros opcionales
 * @return int Total de ultrasonidos
 */
function contar_ultrasonidos($pdo, $filtros = []) {
    try {
        $sql = "SELECT COUNT(*) AS total FROM ultrasonidos u WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['paciente_id'])) {
            $sql .= " AND u.paciente_id = ?";
            $params[] = $filtros['paciente_id'];
        }
        
        if (!empty($filtros['consulta_id'])) {
            $sql .= " AND u.consulta_id = ?";
            $params[] = $filtros['consulta_id'];
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND u.fecha >= ?";
            $params[] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND u.fecha <= ?";
            $params[] = $filtros['fecha_hasta'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
    } catch (PDOException $e) {
        error_log("Error al contar ultrasonidos: " . $e->getMessage());
        return 0;
    }
}

/**
 * Verificar si una consulta ya tiene ultrasonido
 * 
 * @param PDO $pdo Conexión a la BD
 * @param int $consulta_id ID de la consulta
 * @return bool True si ya tiene ultrasonido
 */
function consulta_tiene_ultrasonido($pdo, $consulta_id) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ultrasonidos WHERE consulta_id = ?");
        $stmt->execute([$consulta_id]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Error al verificar ultrasonido de consulta: " . $e->getMessage());
        return false;
    }
}