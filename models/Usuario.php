<?php
/**
 * MODELO: Usuario
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Funciones para gestionar usuarios del sistema
 */

if (!defined('ACCESS_GRANTED')) {
    die('Acceso denegado');
}

/**
 * Obtener lista de usuarios con filtros
 */
function obtener_usuarios($pdo, $filtros = [], $limit = 100, $offset = 0) {
    try {
        $sql = "SELECT id, nombre, email, rol, activo, created_at, updated_at 
                FROM usuarios 
                WHERE 1=1";
        $params = [];
        
        // Filtro por búsqueda (nombre o email)
        if (!empty($filtros['buscar'])) {
            $sql .= " AND (nombre LIKE ? OR email LIKE ?)";
            $buscar = '%' . $filtros['buscar'] . '%';
            $params[] = $buscar;
            $params[] = $buscar;
        }
        
        // Filtro por rol
        if (!empty($filtros['rol'])) {
            $sql .= " AND rol = ?";
            $params[] = $filtros['rol'];
        }
        
        // Filtro por estado
        if (isset($filtros['activo'])) {
            $sql .= " AND activo = ?";
            $params[] = $filtros['activo'];
        }
        
        $sql .= " ORDER BY nombre ASC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error en obtener_usuarios: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener usuario por ID
 */
function obtener_usuario($pdo, $id) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, nombre, email, rol, activo, created_at, updated_at 
            FROM usuarios 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error en obtener_usuario: " . $e->getMessage());
        return null;
    }
}

/**
 * Crear nuevo usuario
 */
function crear_usuario($pdo, $datos) {
    try {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$datos['email']]);
        
        if ($stmt->fetch()) {
            throw new Exception('Este email ya está registrado');
        }
        
        // Encriptar password
        $password_hash = password_hash($datos['password'], PASSWORD_BCRYPT);
        
        // Insertar usuario
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nombre, email, password, rol, activo) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $datos['nombre'],
            $datos['email'],
            $password_hash,
            $datos['rol'],
            isset($datos['activo']) ? $datos['activo'] : 1
        ]);
        
        $usuario_id = $pdo->lastInsertId();
        
        error_log("Usuario creado exitosamente: ID $usuario_id");
        
        return $usuario_id;
        
    } catch (Exception $e) {
        error_log("Error en crear_usuario: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Actualizar usuario existente
 */
function actualizar_usuario($pdo, $id, $datos) {
    try {
        // Verificar si el email ya existe en otro usuario
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->execute([$datos['email'], $id]);
        
        if ($stmt->fetch()) {
            throw new Exception('Este email ya está registrado en otro usuario');
        }
        
        // Si se envió nueva contraseña, actualizar también
        if (!empty($datos['password'])) {
            $password_hash = password_hash($datos['password'], PASSWORD_BCRYPT);
            
            $stmt = $pdo->prepare("
                UPDATE usuarios 
                SET nombre = ?, email = ?, password = ?, rol = ?, activo = ? 
                WHERE id = ?
            ");
            
            $stmt->execute([
                $datos['nombre'],
                $datos['email'],
                $password_hash,
                $datos['rol'],
                $datos['activo'],
                $id
            ]);
        } else {
            // Actualizar sin cambiar contraseña
            $stmt = $pdo->prepare("
                UPDATE usuarios 
                SET nombre = ?, email = ?, rol = ?, activo = ? 
                WHERE id = ?
            ");
            
            $stmt->execute([
                $datos['nombre'],
                $datos['email'],
                $datos['rol'],
                $datos['activo'],
                $id
            ]);
        }
        
        error_log("Usuario actualizado exitosamente: ID $id");
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error en actualizar_usuario: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Activar/Desactivar usuario (toggle)
 */
function cambiar_estado_usuario($pdo, $id, $activo) {
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
        $stmt->execute([$activo ? 1 : 0, $id]);
        
        error_log("Estado de usuario cambiado: ID $id - Activo: " . ($activo ? 'SI' : 'NO'));
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Error en cambiar_estado_usuario: " . $e->getMessage());
        return false;
    }
}

/**
 * Contar usuarios con filtros
 */
function contar_usuarios($pdo, $filtros = []) {
    try {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE 1=1";
        $params = [];
        
        if (!empty($filtros['buscar'])) {
            $sql .= " AND (nombre LIKE ? OR email LIKE ?)";
            $buscar = '%' . $filtros['buscar'] . '%';
            $params[] = $buscar;
            $params[] = $buscar;
        }
        
        if (!empty($filtros['rol'])) {
            $sql .= " AND rol = ?";
            $params[] = $filtros['rol'];
        }
        
        if (isset($filtros['activo'])) {
            $sql .= " AND activo = ?";
            $params[] = $filtros['activo'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
        
    } catch (PDOException $e) {
        error_log("Error en contar_usuarios: " . $e->getMessage());
        return 0;
    }
}


/**
 * Verificar si se puede eliminar un usuario
 * (No eliminar si tiene registros asociados)
 */
function puede_eliminar_usuario($pdo, $id) {
    try {
        // Verificar si tiene consultas
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM consultas WHERE usuario_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return false;
        }
        
        // Verificar si tiene movimientos de inventario
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM inventario_movimientos WHERE usuario_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return false;
        }
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Error en puede_eliminar_usuario: " . $e->getMessage());
        return false;
    }
}