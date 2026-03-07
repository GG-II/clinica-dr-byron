<?php
/**
 * MODELO: FACTURA
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Manejo de recibos y cobros del consultorio
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

if (!defined('ACCESS_GRANTED')) {
    die('Acceso denegado');
}

/**
 * Obtener lista de facturas con filtros
 */
function obtener_facturas($pdo, $filtros = [], $limit = 50, $offset = 0) {
    try {
        $where = ["1=1"];
        $params = [];
        
        // Filtro por paciente
        if (!empty($filtros['paciente_id'])) {
            $where[] = "f.paciente_id = ?";
            $params[] = $filtros['paciente_id'];
        }
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $where[] = "f.estado = ?";
            $params[] = $filtros['estado'];
        }
        
        // Filtro por rango de fechas
        if (!empty($filtros['fecha_inicio'])) {
            $where[] = "f.fecha >= ?";
            $params[] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $where[] = "f.fecha <= ?";
            $params[] = $filtros['fecha_fin'];
        }
        
        // Búsqueda por nombre de paciente o número
        if (!empty($filtros['busqueda'])) {
            $where[] = "(p.nombre LIKE ? OR f.numero_correlativo LIKE ? OR f.concepto LIKE ?)";
            $busqueda = '%' . $filtros['busqueda'] . '%';
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $sql = "SELECT 
                    f.*,
                    p.nombre as paciente_nombre,
                    p.codigo as paciente_codigo,
                    p.telefono as paciente_telefono,
                    u.nombre as usuario_nombre,
                    c.tipo_consulta
                FROM facturas f
                INNER JOIN pacientes p ON f.paciente_id = p.id
                INNER JOIN usuarios u ON f.usuario_id = u.id
                LEFT JOIN consultas c ON f.consulta_id = c.id
                WHERE $where_clause
                ORDER BY f.fecha DESC, f.numero_correlativo DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error al obtener facturas: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener factura completa por ID
 */
function obtener_factura_completa($pdo, $id) {
    try {
        $sql = "SELECT 
                    f.*,
                    p.nombre as paciente_nombre,
                    p.codigo as paciente_codigo,
                    p.telefono as paciente_telefono,
                    p.direccion as paciente_direccion,
                    p.fecha_nacimiento as paciente_fecha_nac,
                    u.nombre as usuario_nombre,
                    c.tipo_consulta,
                    c.motivo_consulta
                FROM facturas f
                INNER JOIN pacientes p ON f.paciente_id = p.id
                INNER JOIN usuarios u ON f.usuario_id = u.id
                LEFT JOIN consultas c ON f.consulta_id = c.id
                WHERE f.id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        error_log("Error al obtener factura: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtener facturas de un paciente
 */
function obtener_facturas_paciente($pdo, $paciente_id, $limit = 10) {
    try {
        $sql = "SELECT 
                    f.*,
                    u.nombre as usuario_nombre,
                    c.tipo_consulta
                FROM facturas f
                INNER JOIN usuarios u ON f.usuario_id = u.id
                LEFT JOIN consultas c ON f.consulta_id = c.id
                WHERE f.paciente_id = ?
                ORDER BY f.fecha DESC, f.numero_correlativo DESC
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$paciente_id, $limit]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error al obtener facturas del paciente: " . $e->getMessage());
        return [];
    }
}

/**
 * Crear nueva factura/recibo
 */
function crear_factura($pdo, $datos) {
    try {
        // Obtener siguiente número correlativo
        $numero = obtener_siguiente_numero_factura($pdo);
        
        // Calcular cambio
        $cambio = max(0, $datos['monto_pagado'] - $datos['monto']);
        
        $sql = "INSERT INTO facturas 
                (numero_correlativo, paciente_id, consulta_id, usuario_id, fecha, 
                 concepto, monto, forma_pago, monto_pagado, cambio, estado, notas) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $numero,
            $datos['paciente_id'],
            $datos['consulta_id'] ?? null,
            $datos['usuario_id'],
            $datos['fecha'] ?? date('Y-m-d'),
            $datos['concepto'],
            $datos['monto'],
            $datos['forma_pago'],
            $datos['monto_pagado'],
            $cambio,
            $datos['estado'] ?? 'pagado',
            $datos['notas'] ?? null
        ]);
        
        if ($result) {
            $id = $pdo->lastInsertId();
            error_log("Factura creada: ID=$id, Número=$numero, Paciente={$datos['paciente_id']}, Monto={$datos['monto']}");
            return $id;
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Error al crear factura: " . $e->getMessage());
        return false;
    }
}

/**
 * Anular factura (no se elimina, se marca como anulada)
 */
function anular_factura($pdo, $id, $motivo = null) {
    try {
        $sql = "UPDATE facturas 
                SET estado = 'anulado', notas = ? 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$motivo, $id]);
        
        if ($result) {
            error_log("Factura anulada: ID=$id, Motivo=$motivo");
        }
        
        return $result;
        
    } catch (PDOException $e) {
        error_log("Error al anular factura: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtener siguiente número correlativo
 */
function obtener_siguiente_numero_factura($pdo) {
    try {
        $sql = "SELECT COALESCE(MAX(numero_correlativo), 0) + 1 as siguiente 
                FROM facturas";
        $stmt = $pdo->query($sql);
        $row = $stmt->fetch();
        
        return (int)$row['siguiente'];
        
    } catch (PDOException $e) {
        error_log("Error al obtener número de factura: " . $e->getMessage());
        return 1;
    }
}

/**
 * Contar total de facturas (para paginación)
 */
function contar_facturas($pdo, $filtros = []) {
    try {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filtros['paciente_id'])) {
            $where[] = "f.paciente_id = ?";
            $params[] = $filtros['paciente_id'];
        }
        
        if (!empty($filtros['estado'])) {
            $where[] = "f.estado = ?";
            $params[] = $filtros['estado'];
        }
        
        if (!empty($filtros['fecha_inicio'])) {
            $where[] = "f.fecha >= ?";
            $params[] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $where[] = "f.fecha <= ?";
            $params[] = $filtros['fecha_fin'];
        }
        
        if (!empty($filtros['busqueda'])) {
            $where[] = "(p.nombre LIKE ? OR f.numero_correlativo LIKE ? OR f.concepto LIKE ?)";
            $busqueda = '%' . $filtros['busqueda'] . '%';
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
        }
        
        $where_clause = implode(' AND ', $where);
        
        $sql = "SELECT COUNT(*) as total 
                FROM facturas f
                INNER JOIN pacientes p ON f.paciente_id = p.id
                WHERE $where_clause";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $row = $stmt->fetch();
        return (int)$row['total'];
        
    } catch (PDOException $e) {
        error_log("Error al contar facturas: " . $e->getMessage());
        return 0;
    }
}

/**
 * Obtener total de ingresos por período
 */
function obtener_ingresos_periodo($pdo, $fecha_inicio, $fecha_fin = null) {
    try {
        $fecha_fin = $fecha_fin ?? date('Y-m-d');
        
        $sql = "SELECT 
                    COUNT(*) as total_facturas,
                    SUM(CASE WHEN estado = 'pagado' THEN monto ELSE 0 END) as total_ingresado,
                    SUM(CASE WHEN estado = 'pendiente' THEN monto ELSE 0 END) as total_pendiente,
                    SUM(CASE WHEN estado = 'anulado' THEN monto ELSE 0 END) as total_anulado
                FROM facturas
                WHERE fecha BETWEEN ? AND ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        error_log("Error al obtener ingresos: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtener consultas del paciente (para dropdown en formulario)
 */
function obtener_consultas_paciente_dropdown($pdo, $paciente_id) {
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
        error_log("Error al obtener consultas: " . $e->getMessage());
        return [];
    }
}