<?php
/**
 * MIDDLEWARE DE PROTECCIÓN DE RUTAS
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Funciones para proteger páginas según roles de usuario.
 * Simplifica la verificación de permisos en cada página.
 * 
 * USO EN PÁGINAS:
 * require_once 'includes/middleware.php';
 * proteger_ruta(['admin', 'medico']); // Solo admin y médico
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');

// ============================================================================
// FUNCIONES PRINCIPALES DE MIDDLEWARE
// ============================================================================

/**
 * Protege una ruta verificando sesión y roles
 * Combina verificar_sesion() y verificar_rol() en una sola función
 * 
 * @param array|string|null $roles_permitidos Roles que pueden acceder (null = cualquier rol logueado)
 */
function proteger_ruta($roles_permitidos = null) {
    // Verificar que haya sesión activa
    verificar_sesion();
    
    // Si se especificaron roles, verificar
    if ($roles_permitidos !== null) {
        verificar_rol($roles_permitidos);
    }
}

/**
 * Protege una ruta solo para administradores
 * Atajo para proteger_ruta(['admin'])
 */
function solo_admin() {
    proteger_ruta([ROL_ADMIN]);
}

/**
 * Protege una ruta para médicos y administradores
 * Atajo para proteger_ruta(['admin', 'medico'])
 */
function solo_medico() {
    proteger_ruta([ROL_ADMIN, ROL_MEDICO]);
}

/**
 * Protege una ruta para asistentes, médicos y administradores
 * Atajo para proteger_ruta(['admin', 'medico', 'asistente'])
 */
function solo_personal() {
    proteger_ruta([ROL_ADMIN, ROL_MEDICO, ROL_ASISTENTE]);
}

// ============================================================================
// MIDDLEWARE PARA OPERACIONES ESPECÍFICAS
// ============================================================================

/**
 * Verifica si el usuario puede crear registros
 * Admin y médico pueden crear, asistente solo puede ver
 * 
 * @return bool True si puede crear
 */
function puede_crear() {
    return es_admin() || es_medico();
}

/**
 * Verifica si el usuario puede editar registros
 * Admin y médico pueden editar, asistente no
 * 
 * @return bool True si puede editar
 */
function puede_editar() {
    return es_admin() || es_medico();
}

/**
 * Verifica si el usuario puede eliminar registros
 * Solo admin puede eliminar
 * 
 * @return bool True si puede eliminar
 */
function puede_eliminar() {
    return es_admin();
}

/**
 * Verifica si el usuario puede ver información sensible
 * Admin y médico pueden ver datos completos
 * 
 * @return bool True si puede ver datos sensibles
 */
function puede_ver_sensible() {
    return es_admin() || es_medico();
}

/**
 * Verifica si el usuario puede gestionar usuarios
 * Solo admin puede gestionar usuarios
 * 
 * @return bool True si puede gestionar usuarios
 */
function puede_gestionar_usuarios() {
    return es_admin();
}

/**
 * Verifica si el usuario puede acceder a reportes
 * Admin y médico pueden ver reportes
 * 
 * @return bool True si puede ver reportes
 */
function puede_ver_reportes() {
    return es_admin() || es_medico();
}

/**
 * Verifica si el usuario puede gestionar inventario
 * Admin y médico pueden gestionar inventario
 * 
 * @return bool True si puede gestionar inventario
 */
function puede_gestionar_inventario() {
    return es_admin() || es_medico();
}

// ============================================================================
// HELPERS DE INTERFAZ
// ============================================================================

/**
 * Muestra un elemento HTML solo si el usuario tiene permiso
 * Útil para ocultar botones según rol
 * 
 * @param callable $condicion Función que retorna true/false
 * @param string $html HTML a mostrar si la condición es true
 */
function mostrar_si($condicion, $html) {
    if (is_callable($condicion) && $condicion()) {
        echo $html;
    } elseif ($condicion === true) {
        echo $html;
    }
}

/**
 * Genera un botón HTML solo si el usuario tiene permiso
 * 
 * @param callable $condicion Función que retorna true/false (ej: puede_crear())
 * @param string $url URL del botón
 * @param string $texto Texto del botón
 * @param string $clase Clases CSS adicionales (default: btn-primary)
 * @param string $icono Icono opcional (HTML)
 */
function boton_si_puede($condicion, $url, $texto, $clase = 'btn-primary', $icono = '') {
    if (is_callable($condicion) && $condicion()) {
        $icono_html = $icono ? $icono . ' ' : '';
        echo sprintf(
            '<a href="%s" class="btn %s">%s%s</a>',
            $url,
            $clase,
            $icono_html,
            $texto
        );
    }
}

/**
 * Deshabilita un campo de formulario si el usuario no puede editar
 * 
 * @return string Atributo 'disabled' o cadena vacía
 */
function deshabilitar_si_no_puede_editar() {
    return puede_editar() ? '' : 'disabled';
}

/**
 * Oculta un elemento si el usuario no tiene permiso
 * Retorna 'd-none' (clase de Bootstrap) si no tiene permiso
 * 
 * @param callable $condicion Función que retorna true/false
 * @return string Clase CSS 'd-none' o cadena vacía
 */
function ocultar_si_no_puede($condicion) {
    if (is_callable($condicion) && !$condicion()) {
        return 'd-none';
    }
    return '';
}

// ============================================================================
// REDIRECCIONES CONDICIONALES
// ============================================================================

/**
 * Redirige al usuario a una página según su rol
 * Útil después del login para enviar a cada usuario a su sección
 * 
 * @param array $rutas_por_rol Array asociativo [rol => url]
 * @param string $default URL por defecto si no hay coincidencia
 */
function redirigir_por_rol($rutas_por_rol, $default = 'dashboard.php') {
    $rol_actual = obtener_usuario_rol();
    
    if (isset($rutas_por_rol[$rol_actual])) {
        redireccionar($rutas_por_rol[$rol_actual]);
    } else {
        redireccionar($default);
    }
}

// ============================================================================
// LOGGING DE ACCESOS
// ============================================================================

/**
 * Registra el acceso a una página protegida
 * Útil para auditoría y estadísticas
 * 
 * @param PDO $pdo Conexión a BD
 * @param string $pagina Nombre de la página (ej: 'Lista de Pacientes')
 */
function registrar_acceso_pagina($pdo, $pagina) {
    if (!isset($_SESSION['usuario_id'])) {
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO audit_log 
            (usuario_id, accion, tabla_afectada, ip_address, created_at) 
            VALUES (?, 'ACCESO_PAGINA', ?, ?, NOW())
        ");
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $stmt->execute([
            $_SESSION['usuario_id'],
            $pagina,
            $ip
        ]);
        
    } catch (PDOException $e) {
        // No detener flujo si falla el registro
        log_mensaje("Error al registrar acceso a página: " . $e->getMessage(), 'error');
    }
}

// ============================================================================
// MATRIZ DE PERMISOS (DOCUMENTACIÓN)
// ============================================================================

/*
MATRIZ DE PERMISOS DEL SISTEMA
===============================

ROL: ADMIN
- Acceso total al sistema
- Puede crear, editar, eliminar usuarios
- Puede ver todos los reportes
- Puede gestionar inventario
- Puede ver datos sensibles
- Puede eliminar registros

ROL: MEDICO
- Puede crear, editar, ver pacientes
- Puede crear, editar, ver consultas
- Puede generar recetas
- Puede generar informes de ultrasonido
- Puede registrar pagos
- Puede ver reportes médicos
- Puede gestionar inventario
- NO puede eliminar registros
- NO puede gestionar usuarios

ROL: ASISTENTE
- Puede ver pacientes (solo datos básicos)
- Puede crear, editar, ver citas
- Puede registrar pagos
- Puede ver agenda
- NO puede ver datos sensibles de pacientes
- NO puede editar consultas médicas
- NO puede generar recetas
- NO puede ver reportes completos
- NO puede gestionar inventario
- NO puede gestionar usuarios

IMPLEMENTACIÓN EN PÁGINAS:
==========================

// Página de usuarios (solo admin)
solo_admin();

// Página de consultas (admin y médico)
solo_medico();

// Página de citas (todos los roles logueados)
solo_personal();

// Botón de eliminar (solo si puede)
<?php if (puede_eliminar()): ?>
    <button class="btn btn-danger">Eliminar</button>
<?php endif; ?>

// Campo de edición (deshabilitar si no puede)
<input type="text" <?= deshabilitar_si_no_puede_editar() ?>>

// Mostrar sección según permiso
<?php mostrar_si('puede_ver_sensible', '<div>Datos sensibles...</div>'); ?>
*/