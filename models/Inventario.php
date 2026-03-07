<?php
/**
 * MODELO: Inventario
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Manejo de inventario REDUCIDO de medicamentos e insumos
 * 
 * TABLAS:
 * - inventario_productos (id, nombre, unidad, cantidad_actual, stock_minimo, activo)
 * - inventario_movimientos (id, producto_id, usuario_id, tipo, cantidad, motivo, fecha)
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

if (!defined('ACCESS_GRANTED')) {
    die('Acceso denegado');
}

// ============================================================================
// FUNCIONES DE PRODUCTOS
// ============================================================================

/**
 * Obtener lista de productos con filtros
 */
function obtener_productos($pdo, $filtros = [], $limit = 50, $offset = 0) {
    try {
        $sql = "SELECT * FROM inventario_productos WHERE activo = 1";
        
        $params = [];
        
        // Filtro por búsqueda
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND nombre LIKE ?";
            $params[] = '%' . $filtros['busqueda'] . '%';
        }
        
        // Filtro por stock bajo
        if (!empty($filtros['stock_bajo'])) {
            $sql .= " AND cantidad_actual <= stock_minimo";
        }
        
        $sql .= " ORDER BY nombre ASC LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener producto por ID con datos completos
 */
function obtener_producto_completo($pdo, $id) {
    try {
        $sql = "SELECT * FROM inventario_productos WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        error_log("Error al obtener producto: " . $e->getMessage());
        return null;
    }
}

/**
 * Crear nuevo producto
 */
function crear_producto($pdo, $datos) {
    try {
        $sql = "INSERT INTO inventario_productos 
                (nombre, unidad, cantidad_actual, stock_minimo) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $datos['nombre'],
            $datos['unidad'],
            $datos['cantidad_actual'] ?? 0,
            $datos['stock_minimo'] ?? 0
        ]);
        
        if ($result) {
            $id = $pdo->lastInsertId();
            error_log("Producto creado: ID=$id, Nombre={$datos['nombre']}, Cantidad={$datos['cantidad_actual']}");
            return $id;
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Error al crear producto: " . $e->getMessage());
        return false;
    }
}

/**
 * Actualizar producto existente
 */
function actualizar_producto($pdo, $id, $datos) {
    try {
        $sql = "UPDATE inventario_productos 
                SET nombre = ?, unidad = ?, stock_minimo = ? 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $datos['nombre'],
            $datos['unidad'],
            $datos['stock_minimo'],
            $id
        ]);
        
        if ($result) {
            error_log("Producto actualizado: ID=$id");
        }
        
        return $result;
        
    } catch (PDOException $e) {
        error_log("Error al actualizar producto: " . $e->getMessage());
        return false;
    }
}

/**
 * Desactivar producto (soft delete)
 */
function desactivar_producto($pdo, $id) {
    try {
        $sql = "UPDATE inventario_productos SET activo = 0 WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$id]);
        
        if ($result) {
            error_log("Producto desactivado: ID=$id");
        }
        
        return $result;
        
    } catch (PDOException $e) {
        error_log("Error al desactivar producto: " . $e->getMessage());
        return false;
    }
}

/**
 * Contar productos con stock bajo
 */
function contar_productos_stock_bajo($pdo) {
    try {
        $sql = "SELECT COUNT(*) FROM inventario_productos 
                WHERE activo = 1 AND cantidad_actual <= stock_minimo";
        
        $stmt = $pdo->query($sql);
        return (int)$stmt->fetchColumn();
        
    } catch (PDOException $e) {
        error_log("Error al contar productos con stock bajo: " . $e->getMessage());
        return 0;
    }
}

/**
 * Contar total de productos (para paginación)
 */
function contar_productos($pdo, $filtros = []) {
    try {
        $sql = "SELECT COUNT(*) FROM inventario_productos WHERE activo = 1";
        
        $params = [];
        
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND nombre LIKE ?";
            $params[] = '%' . $filtros['busqueda'] . '%';
        }
        
        if (!empty($filtros['stock_bajo'])) {
            $sql .= " AND cantidad_actual <= stock_minimo";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int)$stmt->fetchColumn();
        
    } catch (PDOException $e) {
        error_log("Error al contar productos: " . $e->getMessage());
        return 0;
    }
}

// ============================================================================
// FUNCIONES DE MOVIMIENTOS
// ============================================================================

/**
 * Registrar movimiento de inventario (con transacción)
 */
function registrar_movimiento($pdo, $datos) {
    try {
        $pdo->beginTransaction();
        
        // 1. Insertar movimiento
        $sql = "INSERT INTO inventario_movimientos 
                (producto_id, usuario_id, tipo, cantidad, motivo) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $datos['producto_id'],
            $datos['usuario_id'],
            $datos['tipo'],
            $datos['cantidad'],
            $datos['motivo'] ?? null
        ]);
        
        $movimiento_id = $pdo->lastInsertId();
        
        // 2. Actualizar stock del producto
        $cantidad_cambio = $datos['cantidad'];
        
        // Si es salida o venta, restar
        if ($datos['tipo'] === 'salida' || $datos['tipo'] === 'venta') {
            $cantidad_cambio = -$cantidad_cambio;
        }
        
        $sql_update = "UPDATE inventario_productos 
                       SET cantidad_actual = cantidad_actual + ? 
                       WHERE id = ?";
        
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$cantidad_cambio, $datos['producto_id']]);
        
        $pdo->commit();
        
        error_log("Movimiento registrado: ID=$movimiento_id, Producto={$datos['producto_id']}, Tipo={$datos['tipo']}, Cantidad={$datos['cantidad']}");
        
        return $movimiento_id;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error al registrar movimiento: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtener movimientos de un producto
 */
function obtener_movimientos_producto($pdo, $producto_id, $limit = 50) {
    try {
        $sql = "SELECT 
                    m.*,
                    u.nombre as usuario_nombre
                FROM inventario_movimientos m
                INNER JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.producto_id = ?
                ORDER BY m.fecha DESC
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$producto_id, $limit]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error al obtener movimientos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener todos los movimientos recientes
 */
function obtener_movimientos_recientes($pdo, $limit = 50) {
    try {
        $sql = "SELECT 
                    m.*,
                    p.nombre as producto_nombre,
                    p.unidad as producto_unidad,
                    u.nombre as usuario_nombre
                FROM inventario_movimientos m
                INNER JOIN inventario_productos p ON m.producto_id = p.id
                INNER JOIN usuarios u ON m.usuario_id = u.id
                ORDER BY m.fecha DESC
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error al obtener movimientos recientes: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener nombre de tipo de movimiento
 */
function obtener_nombre_tipo_movimiento($tipo) {
    $tipos = [
        'entrada' => 'Entrada',
        'salida' => 'Salida',
        'venta' => 'Venta'
    ];
    
    return $tipos[$tipo] ?? $tipo;
}

/**
 * Obtener color de badge para tipo de movimiento
 */
function obtener_color_tipo_movimiento($tipo) {
    $colores = [
        'entrada' => 'success',
        'salida' => 'warning',
        'venta' => 'info'
    ];
    
    return $colores[$tipo] ?? 'secondary';
}