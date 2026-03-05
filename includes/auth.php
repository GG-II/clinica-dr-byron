<?php
/**
 * SISTEMA DE AUTENTICACIÓN
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Funciones para:
 * - Verificar sesión activa
 * - Validar credenciales de login
 * - Gestionar roles y permisos
 * - Cerrar sesión
 * - Regenerar ID de sesión (seguridad)
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');

// ============================================================================
// CONFIGURACIÓN DE SESIÓN
// ============================================================================

/**
 * Inicializa la sesión de forma segura
 * Configura parámetros de seguridad y regenera ID periódicamente
 */
function iniciar_sesion_segura() {
    // Si la sesión ya está iniciada, no hacer nada
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    
    // Configuración de sesión segura
    ini_set('session.cookie_httponly', 1);  // Cookie solo HTTP (no JavaScript)
    ini_set('session.use_only_cookies', 1); // Solo usar cookies
    ini_set('session.cookie_secure', 0);    // 0 en desarrollo, 1 en producción con HTTPS
    ini_set('session.use_strict_mode', 1);  // Rechazar IDs no inicializados
    
    // Nombre personalizado de sesión
    session_name(SESSION_NAME);
    
    // Iniciar sesión
    session_start();
    
    // Regenerar ID de sesión periódicamente (seguridad contra session fixation)
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > SESSION_REGENERATE_TIME) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// ============================================================================
// FUNCIONES DE AUTENTICACIÓN
// ============================================================================

/**
 * Valida las credenciales de un usuario
 * 
 * @param PDO $pdo Conexión a base de datos
 * @param string $email Email del usuario
 * @param string $password Password en texto plano
 * @return array|false Datos del usuario si es válido, false si no
 */
function validar_credenciales($pdo, $email, $password) {
    try {
        // Buscar usuario por email
        $stmt = $pdo->prepare("
            SELECT 
                id, nombre, email, password, rol, activo 
            FROM usuarios 
            WHERE email = ? 
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        // Si no existe el usuario
        if (!$usuario) {
            return false;
        }
        
        // Si el usuario está inactivo
        if ($usuario['activo'] != ESTADO_ACTIVO) {
            return false;
        }
        
        // Verificar password
        if (!password_verify($password, $usuario['password'])) {
            return false;
        }
        
        // Credenciales válidas
        return $usuario;
        
    } catch (PDOException $e) {
        log_mensaje("Error al validar credenciales: " . $e->getMessage(), 'error');
        return false;
    }
}

/**
 * Inicia sesión para un usuario
 * Guarda datos del usuario en sesión y registra en audit_log
 * 
 * @param array $usuario Datos del usuario
 * @param PDO $pdo Conexión a BD (para audit log)
 */
function iniciar_sesion_usuario($usuario, $pdo = null) {
    // Regenerar ID de sesión (prevenir session fixation)
    session_regenerate_id(true);
    
    // Guardar datos en sesión
    $_SESSION['usuario_id']     = $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_email']  = $usuario['email'];
    $_SESSION['usuario_rol']    = $usuario['rol'];
    $_SESSION['login_time']     = time();
    $_SESSION['last_activity']  = time();
    $_SESSION['ip_address']     = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Registrar en audit log
    if ($pdo) {
        registrar_auditoria($pdo, $usuario['id'], 'LOGIN', null, null);
    }
    
    // Log del sistema
    log_mensaje("Login exitoso: {$usuario['nombre']} ({$usuario['email']}) - Rol: {$usuario['rol']}", 'info');
}

/**
 * Verifica que exista una sesión activa
 * Si no existe o expiró, redirige a login
 */
function verificar_sesion() {
    // Verificar que la sesión esté iniciada
    if (session_status() !== PHP_SESSION_ACTIVE) {
        iniciar_sesion_segura();
    }
    
    // Verificar que el usuario esté logueado
    if (!isset($_SESSION['usuario_id'])) {
        // Guardar la URL que intentaba acceder (para redirigir después del login)
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        redireccionar('index.php');
    }
    
    // Verificar timeout de sesión (tiempo total desde login)
    if (isset($_SESSION['login_time'])) {
        $session_age = time() - $_SESSION['login_time'];
        if ($session_age > SESSION_LIFETIME) {
            cerrar_sesion();
            mensaje_advertencia('Su sesión ha expirado por tiempo de inactividad.');
            redireccionar('index.php');
        }
    }
    
    // Verificar timeout de inactividad
    if (isset($_SESSION['last_activity'])) {
        $inactivity_time = time() - $_SESSION['last_activity'];
        if ($inactivity_time > SESSION_TIMEOUT) {
            cerrar_sesion();
            mensaje_advertencia('Su sesión ha expirado por inactividad.');
            redireccionar('index.php');
        }
    }
    
    // Actualizar última actividad
    $_SESSION['last_activity'] = time();
    
    // Verificar que la IP no haya cambiado (seguridad básica)
    $current_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $current_ip) {
        log_mensaje("Posible session hijacking detectado. IP original: {$_SESSION['ip_address']}, IP actual: {$current_ip}", 'warning');
        // Puedes decidir cerrar la sesión o solo registrar
        // cerrar_sesion();
        // redireccionar('index.php');
    }
}

/**
 * Cierra la sesión del usuario actual
 * Limpia todas las variables de sesión y destruye la sesión
 * 
 * @param PDO|null $pdo Conexión opcional para registrar en audit log
 */
function cerrar_sesion($pdo = null) {
    // Registrar en audit log antes de destruir sesión
    if ($pdo && isset($_SESSION['usuario_id'])) {
        registrar_auditoria($pdo, $_SESSION['usuario_id'], 'LOGOUT', null, null);
        log_mensaje("Logout: {$_SESSION['usuario_nombre']} ({$_SESSION['usuario_email']})", 'info');
    }
    
    // Limpiar todas las variables de sesión
    $_SESSION = [];
    
    // Destruir la cookie de sesión
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destruir la sesión
    session_destroy();
}

/**
 * Verifica que el usuario tenga uno de los roles permitidos
 * Si no tiene permiso, muestra error 403
 * 
 * @param array $roles_permitidos Array de roles (ej: ['admin', 'medico'])
 */
function verificar_rol($roles_permitidos) {
    if (!isset($_SESSION['usuario_rol'])) {
        http_response_code(403);
        die('Acceso denegado: No hay rol de usuario en sesión');
    }
    
    // Convertir a array si es un string
    if (!is_array($roles_permitidos)) {
        $roles_permitidos = [$roles_permitidos];
    }
    
    // Verificar si el rol del usuario está en los permitidos
    if (!in_array($_SESSION['usuario_rol'], $roles_permitidos)) {
        http_response_code(403);
        mostrar_error_403();
        exit;
    }
}

/**
 * Verifica si el usuario actual tiene un rol específico
 * 
 * @param string $rol Rol a verificar (ej: 'admin')
 * @return bool True si tiene el rol
 */
function tiene_rol($rol) {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === $rol;
}

/**
 * Verifica si el usuario es administrador
 * 
 * @return bool True si es admin
 */
function es_admin() {
    return tiene_rol(ROL_ADMIN);
}

/**
 * Verifica si el usuario es médico
 * 
 * @return bool True si es médico
 */
function es_medico() {
    return tiene_rol(ROL_MEDICO);
}

/**
 * Verifica si el usuario es asistente
 * 
 * @return bool True si es asistente
 */
function es_asistente() {
    return tiene_rol(ROL_ASISTENTE);
}

/**
 * Obtiene el ID del usuario actual
 * 
 * @return int|null ID del usuario o null si no hay sesión
 */
function obtener_usuario_id() {
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Obtiene el nombre del usuario actual
 * 
 * @return string|null Nombre del usuario o null
 */
function obtener_usuario_nombre() {
    return $_SESSION['usuario_nombre'] ?? null;
}

/**
 * Obtiene el rol del usuario actual
 * 
 * @return string|null Rol del usuario o null
 */
function obtener_usuario_rol() {
    return $_SESSION['usuario_rol'] ?? null;
}

// ============================================================================
// FUNCIONES DE AUDITORÍA
// ============================================================================

/**
 * Registra una acción en el audit log
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $usuario_id ID del usuario que realiza la acción
 * @param string $accion Acción realizada (VER, CREAR, EDITAR, ELIMINAR, LOGIN, LOGOUT)
 * @param string|null $tabla_afectada Tabla afectada (opcional)
 * @param int|null $registro_id ID del registro afectado (opcional)
 */
function registrar_auditoria($pdo, $usuario_id, $accion, $tabla_afectada = null, $registro_id = null) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO audit_log 
            (usuario_id, accion, tabla_afectada, registro_id, ip_address, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $stmt->execute([
            $usuario_id,
            $accion,
            $tabla_afectada,
            $registro_id,
            $ip
        ]);
        
    } catch (PDOException $e) {
        // No detener el flujo si falla el audit log, solo registrar
        log_mensaje("Error al registrar auditoría: " . $e->getMessage(), 'error');
    }
}

// ============================================================================
// FUNCIONES DE INTERFAZ
// ============================================================================

/**
 * Muestra página de error 403 (Acceso Denegado)
 */
function mostrar_error_403() {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acceso Denegado - <?= APP_NAME ?></title>
        <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .error-card {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                padding: 40px;
                max-width: 500px;
                text-align: center;
            }
            .error-icon {
                font-size: 80px;
                color: #dc3545;
            }
        </style>
    </head>
    <body>
        <div class="error-card">
            <div class="error-icon">🚫</div>
            <h2 class="mb-3">Acceso Denegado</h2>
            <p class="text-muted mb-4">
                No tiene permisos para acceder a esta sección del sistema.
            </p>
            <div class="alert alert-danger">
                <strong>Error 403:</strong> Permisos insuficientes
            </div>
            <p class="mb-2">Rol actual: <strong><?= e(obtener_usuario_rol()) ?></strong></p>
            <hr class="my-4">
            <a href="<?= BASE_URL ?>dashboard.php" class="btn btn-primary">
                Volver al Dashboard
            </a>
            <a href="<?= BASE_URL ?>logout.php" class="btn btn-outline-secondary">
                Cerrar Sesión
            </a>
        </div>
    </body>
    </html>
    <?php
}

/**
 * Obtiene el nombre legible de un rol
 * 
 * @param string $rol Código del rol
 * @return string Nombre legible
 */
function obtener_nombre_rol($rol) {
    $roles = [
        ROL_ADMIN     => 'Administrador',
        ROL_MEDICO    => 'Médico',
        ROL_ASISTENTE => 'Asistente/Recepcionista'
    ];
    
    return $roles[$rol] ?? $rol;
}

/**
 * Obtiene un badge HTML para un rol (Bootstrap)
 * 
 * @param string $rol Código del rol
 * @return string HTML del badge
 */
function badge_rol($rol) {
    $badges = [
        ROL_ADMIN     => '<span class="badge bg-danger">Administrador</span>',
        ROL_MEDICO    => '<span class="badge bg-primary">Médico</span>',
        ROL_ASISTENTE => '<span class="badge bg-info">Asistente</span>'
    ];
    
    return $badges[$rol] ?? '<span class="badge bg-secondary">' . e($rol) . '</span>';
}