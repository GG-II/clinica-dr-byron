<?php
/**
 * MODELO: Usuario
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Gestión completa de usuarios del sistema:
 * - CRUD de usuarios
 * - Validación de credenciales
 * - Cambio de contraseñas
 * - Listados y búsquedas
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');

// ============================================================================
// FUNCIONES DE LECTURA (READ)
// ============================================================================

/**
 * Obtiene todos los usuarios del sistema
 * 
 * @param PDO $pdo Conexión a BD
 * @param array $filtros Filtros opcionales ['activo' => 1, 'rol' => 'medico']
 * @return array Lista de usuarios
 */
function obtener_usuarios($pdo, $filtros = []) {
    try {
        $sql = "SELECT id, nombre, email, rol, activo, created_at, updated_at 
                FROM usuarios 
                WHERE 1=1";
        
        $params = [];
        
        // Filtro por activo
        if (isset($filtros['activo'])) {
            $sql .= " AND activo = ?";
            $params[] = $filtros['activo'];
        }
        
        // Filtro por rol
        if (isset($filtros['rol'])) {
            $sql .= " AND rol = ?";
            $params[] = $filtros['rol'];
        }
        
        // Ordenar por nombre
        $sql .= " ORDER BY nombre ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        log_mensaje("Error al obtener usuarios: " . $e->getMessage(), 'error');
        return [];
    }
}

/**
 * Obtiene un usuario por su ID
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID del usuario
 * @return array|false Datos del usuario o false si no existe
 */
function obtener_usuario_por_id($pdo, $id) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, nombre, email, rol, activo, created_at, updated_at 
            FROM usuarios 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        log_mensaje("Error al obtener usuario por ID: " . $e->getMessage(), 'error');
        return false;
    }
}

/**
 * Obtiene un usuario por su email
 * 
 * @param PDO $pdo Conexión a BD
 * @param string $email Email del usuario
 * @return array|false Datos del usuario o false si no existe
 */
function obtener_usuario_por_email($pdo, $email) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, nombre, email, password, rol, activo, created_at, updated_at 
            FROM usuarios 
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        log_mensaje("Error al obtener usuario por email: " . $e->getMessage(), 'error');
        return false;
    }
}

/**
 * Busca usuarios por nombre o email
 * 
 * @param PDO $pdo Conexión a BD
 * @param string $termino Término de búsqueda
 * @return array Lista de usuarios que coinciden
 */
function buscar_usuarios($pdo, $termino) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, nombre, email, rol, activo 
            FROM usuarios 
            WHERE nombre LIKE ? OR email LIKE ?
            ORDER BY nombre ASC
        ");
        
        $termino_busqueda = '%' . $termino . '%';
        $stmt->execute([$termino_busqueda, $termino_busqueda]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        log_mensaje("Error al buscar usuarios: " . $e->getMessage(), 'error');
        return [];
    }
}

/**
 * Cuenta el total de usuarios
 * 
 * @param PDO $pdo Conexión a BD
 * @param array $filtros Filtros opcionales
 * @return int Total de usuarios
 */
function contar_usuarios($pdo, $filtros = []) {
    try {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE 1=1";
        $params = [];
        
        if (isset($filtros['activo'])) {
            $sql .= " AND activo = ?";
            $params[] = $filtros['activo'];
        }
        
        if (isset($filtros['rol'])) {
            $sql .= " AND rol = ?";
            $params[] = $filtros['rol'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int) $stmt->fetchColumn();
        
    } catch (PDOException $e) {
        log_mensaje("Error al contar usuarios: " . $e->getMessage(), 'error');
        return 0;
    }
}

// ============================================================================
// FUNCIONES DE CREACIÓN (CREATE)
// ============================================================================

/**
 * Crea un nuevo usuario
 * 
 * @param PDO $pdo Conexión a BD
 * @param array $datos Datos del usuario ['nombre', 'email', 'password', 'rol']
 * @return array Resultado ['success' => bool, 'message' => string, 'id' => int|null]
 */
function crear_usuario($pdo, $datos) {
    try {
        // Validaciones
        $validacion = validar_datos_usuario($datos, true);
        if (!$validacion['success']) {
            return $validacion;
        }
        
        // Verificar que el email no exista
        if (email_usuario_existe($pdo, $datos['email'])) {
            return [
                'success' => false,
                'message' => 'El email ya está registrado en el sistema.'
            ];
        }
        
        // Hashear password
        $password_hash = password_hash($datos['password'], PASSWORD_BCRYPT);
        
        // Insertar usuario
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nombre, email, password, rol, activo) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $activo = isset($datos['activo']) ? $datos['activo'] : ESTADO_ACTIVO;
        
        $stmt->execute([
            $datos['nombre'],
            $datos['email'],
            $password_hash,
            $datos['rol'],
            $activo
        ]);
        
        $nuevo_id = $pdo->lastInsertId();
        
        // Registrar en auditoría
        if (isset($_SESSION['usuario_id'])) {
            registrar_auditoria($pdo, $_SESSION['usuario_id'], 'CREAR', 'usuarios', $nuevo_id);
        }
        
        log_mensaje("Usuario creado: {$datos['nombre']} ({$datos['email']}) - Rol: {$datos['rol']}", 'info');
        
        return [
            'success' => true,
            'message' => 'Usuario creado correctamente.',
            'id' => $nuevo_id
        ];
        
    } catch (PDOException $e) {
        log_mensaje("Error al crear usuario: " . $e->getMessage(), 'error');
        return [
            'success' => false,
            'message' => 'Error al crear el usuario. Por favor intente nuevamente.'
        ];
    }
}

// ============================================================================
// FUNCIONES DE ACTUALIZACIÓN (UPDATE)
// ============================================================================

/**
 * Actualiza los datos de un usuario
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID del usuario
 * @param array $datos Datos a actualizar ['nombre', 'email', 'rol', 'activo']
 * @return array Resultado ['success' => bool, 'message' => string]
 */
function actualizar_usuario($pdo, $id, $datos) {
    try {
        // Verificar que el usuario existe
        $usuario_actual = obtener_usuario_por_id($pdo, $id);
        if (!$usuario_actual) {
            return [
                'success' => false,
                'message' => 'El usuario no existe.'
            ];
        }
        
        // Validaciones
        $validacion = validar_datos_usuario($datos, false);
        if (!$validacion['success']) {
            return $validacion;
        }
        
        // Si cambió el email, verificar que no exista
        if (isset($datos['email']) && $datos['email'] !== $usuario_actual['email']) {
            if (email_usuario_existe($pdo, $datos['email'], $id)) {
                return [
                    'success' => false,
                    'message' => 'El email ya está registrado por otro usuario.'
                ];
            }
        }
        
        // Construir query dinámicamente según campos recibidos
        $campos_actualizar = [];
        $params = [];
        
        if (isset($datos['nombre'])) {
            $campos_actualizar[] = "nombre = ?";
            $params[] = $datos['nombre'];
        }
        
        if (isset($datos['email'])) {
            $campos_actualizar[] = "email = ?";
            $params[] = $datos['email'];
        }
        
        if (isset($datos['rol'])) {
            $campos_actualizar[] = "rol = ?";
            $params[] = $datos['rol'];
        }
        
        if (isset($datos['activo'])) {
            $campos_actualizar[] = "activo = ?";
            $params[] = $datos['activo'];
        }
        
        // Si no hay campos para actualizar
        if (empty($campos_actualizar)) {
            return [
                'success' => false,
                'message' => 'No hay datos para actualizar.'
            ];
        }
        
        // Agregar ID al final de params
        $params[] = $id;
        
        // Ejecutar actualización
        $sql = "UPDATE usuarios SET " . implode(', ', $campos_actualizar) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Registrar en auditoría
        if (isset($_SESSION['usuario_id'])) {
            registrar_auditoria($pdo, $_SESSION['usuario_id'], 'EDITAR', 'usuarios', $id);
        }
        
        log_mensaje("Usuario actualizado ID: {$id}", 'info');
        
        return [
            'success' => true,
            'message' => 'Usuario actualizado correctamente.'
        ];
        
    } catch (PDOException $e) {
        log_mensaje("Error al actualizar usuario: " . $e->getMessage(), 'error');
        return [
            'success' => false,
            'message' => 'Error al actualizar el usuario. Por favor intente nuevamente.'
        ];
    }
}

/**
 * Cambia la contraseña de un usuario
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID del usuario
 * @param string $password_nueva Nueva contraseña en texto plano
 * @param string|null $password_actual Contraseña actual (para validar si el usuario cambia su propia contraseña)
 * @return array Resultado ['success' => bool, 'message' => string]
 */
function cambiar_password($pdo, $id, $password_nueva, $password_actual = null) {
    try {
        // Obtener usuario
        $usuario = obtener_usuario_por_id($pdo, $id);
        if (!$usuario) {
            return [
                'success' => false,
                'message' => 'El usuario no existe.'
            ];
        }
        
        // Si se proporcionó contraseña actual, validarla
        if ($password_actual !== null) {
            $usuario_completo = obtener_usuario_por_email($pdo, $usuario['email']);
            if (!password_verify($password_actual, $usuario_completo['password'])) {
                return [
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta.'
                ];
            }
        }
        
        // Validar nueva contraseña
        if (strlen($password_nueva) < 6) {
            return [
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres.'
            ];
        }
        
        // Hashear nueva contraseña
        $password_hash = password_hash($password_nueva, PASSWORD_BCRYPT);
        
        // Actualizar contraseña
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->execute([$password_hash, $id]);
        
        // Registrar en auditoría
        if (isset($_SESSION['usuario_id'])) {
            registrar_auditoria($pdo, $_SESSION['usuario_id'], 'CAMBIO_PASSWORD', 'usuarios', $id);
        }
        
        log_mensaje("Contraseña cambiada para usuario ID: {$id}", 'info');
        
        return [
            'success' => true,
            'message' => 'Contraseña actualizada correctamente.'
        ];
        
    } catch (PDOException $e) {
        log_mensaje("Error al cambiar contraseña: " . $e->getMessage(), 'error');
        return [
            'success' => false,
            'message' => 'Error al cambiar la contraseña. Por favor intente nuevamente.'
        ];
    }
}

/**
 * Activa un usuario
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID del usuario
 * @return array Resultado ['success' => bool, 'message' => string]
 */
function activar_usuario($pdo, $id) {
    return actualizar_usuario($pdo, $id, ['activo' => ESTADO_ACTIVO]);
}

/**
 * Desactiva un usuario (no lo elimina, solo lo marca como inactivo)
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID del usuario
 * @return array Resultado ['success' => bool, 'message' => string]
 */
function desactivar_usuario($pdo, $id) {
    return actualizar_usuario($pdo, $id, ['activo' => ESTADO_INACTIVO]);
}

// ============================================================================
// FUNCIONES DE ELIMINACIÓN (DELETE)
// ============================================================================

/**
 * Elimina un usuario de forma permanente
 * ADVERTENCIA: Esto es peligroso. Mejor usar desactivar_usuario()
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID del usuario
 * @return array Resultado ['success' => bool, 'message' => string]
 */
function eliminar_usuario($pdo, $id) {
    try {
        // Verificar que no sea el usuario actual
        if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id) {
            return [
                'success' => false,
                'message' => 'No puede eliminar su propio usuario.'
            ];
        }
        
        // Verificar que el usuario existe
        $usuario = obtener_usuario_por_id($pdo, $id);
        if (!$usuario) {
            return [
                'success' => false,
                'message' => 'El usuario no existe.'
            ];
        }
        
        // Eliminar usuario
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        
        // Registrar en auditoría
        if (isset($_SESSION['usuario_id'])) {
            registrar_auditoria($pdo, $_SESSION['usuario_id'], 'ELIMINAR', 'usuarios', $id);
        }
        
        log_mensaje("Usuario eliminado: {$usuario['nombre']} (ID: {$id})", 'warning');
        
        return [
            'success' => true,
            'message' => 'Usuario eliminado correctamente.'
        ];
        
    } catch (PDOException $e) {
        log_mensaje("Error al eliminar usuario: " . $e->getMessage(), 'error');
        return [
            'success' => false,
            'message' => 'Error al eliminar el usuario. Puede tener registros relacionados.'
        ];
    }
}

// ============================================================================
// FUNCIONES DE VALIDACIÓN
// ============================================================================

/**
 * Valida los datos de un usuario
 * 
 * @param array $datos Datos a validar
 * @param bool $es_nuevo Si es true, valida password (requerido solo en creación)
 * @return array Resultado ['success' => bool, 'message' => string]
 */
function validar_datos_usuario($datos, $es_nuevo = false) {
    // Validar nombre
    if ($es_nuevo || isset($datos['nombre'])) {
        if (empty($datos['nombre']) || strlen($datos['nombre']) < 3) {
            return [
                'success' => false,
                'message' => 'El nombre debe tener al menos 3 caracteres.'
            ];
        }
    }
    
    // Validar email
    if ($es_nuevo || isset($datos['email'])) {
        if (!validar_email($datos['email'])) {
            return [
                'success' => false,
                'message' => 'El email no es válido.'
            ];
        }
    }
    
    // Validar password (solo en creación)
    if ($es_nuevo) {
        if (empty($datos['password']) || strlen($datos['password']) < 6) {
            return [
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres.'
            ];
        }
    }
    
    // Validar rol
    if ($es_nuevo || isset($datos['rol'])) {
        $roles_validos = [ROL_ADMIN, ROL_MEDICO, ROL_ASISTENTE];
        if (!in_array($datos['rol'], $roles_validos)) {
            return [
                'success' => false,
                'message' => 'El rol seleccionado no es válido.'
            ];
        }
    }
    
    return ['success' => true];
}

/**
 * Verifica si un email ya está registrado
 * 
 * @param PDO $pdo Conexión a BD
 * @param string $email Email a verificar
 * @param int|null $excluir_id ID de usuario a excluir (para actualizaciones)
 * @return bool True si el email existe
 */
function email_usuario_existe($pdo, $email, $excluir_id = null) {
    try {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = ?";
        $params = [$email];
        
        if ($excluir_id !== null) {
            $sql .= " AND id != ?";
            $params[] = $excluir_id;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
        
    } catch (PDOException $e) {
        log_mensaje("Error al verificar email: " . $e->getMessage(), 'error');
        return false;
    }
}