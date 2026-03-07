<?php
/**
 * NAVBAR (SIDEBAR)
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Menú lateral del sistema con opciones según rol
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');

// Obtener datos del usuario
$usuario_nombre = obtener_usuario_nombre();
$usuario_rol = obtener_usuario_rol();
$usuario_rol_nombre = obtener_nombre_rol($usuario_rol);

// Obtener iniciales para avatar
$iniciales = '';
if ($usuario_nombre) {
    $palabras = explode(' ', $usuario_nombre);
    $iniciales = strtoupper(substr($palabras[0], 0, 1));
    if (isset($palabras[1])) {
        $iniciales .= strtoupper(substr($palabras[1], 0, 1));
    }
}

// URL actual para marcar menú activo
$current_page = basename($_SERVER['PHP_SELF']);
$current_module = '';
if (strpos($_SERVER['PHP_SELF'], '/modules/') !== false) {
    $parts = explode('/modules/', $_SERVER['PHP_SELF']);
    $current_module = explode('/', $parts[1])[0];
}

/**
 * Helper para marcar item activo
 */
function menu_active($page_or_module) {
    global $current_page, $current_module;
    if ($current_page === $page_or_module || $current_module === $page_or_module) {
        return 'active';
    }
    return '';
}
?>

<aside class="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="bi bi-heart-pulse-fill"></i>
        </div>
        <div class="sidebar-logo-text">
            Clínica Médica de la Mujer
        </div>
    </div>
    
    <!-- Menú -->
    <nav class="sidebar-menu">
        <ul>
            <!-- Dashboard -->
            <li>
                <a href="<?= BASE_URL ?>dashboard.php" class="<?= menu_active('dashboard.php') ?>">
                    <i class="bi bi-house-door"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Pacientes -->
            <?php if (es_admin() || es_medico() || es_asistente()): ?>
            <li>
                <a href="<?= BASE_URL ?>modules/pacientes/" class="<?= menu_active('pacientes') ?>">
                    <i class="bi bi-people"></i>
                    <span>Pacientes</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Citas -->
            <?php if (es_admin() || es_medico() || es_asistente()): ?>
            <li>
                <a href="<?= BASE_URL ?>modules/citas/" class="<?= menu_active('citas') ?>">
                    <i class="bi bi-calendar3"></i>
                    <span>Citas</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Consultas -->
            <?php if (es_admin() || es_medico()): ?>
            <li>
                <a href="<?= BASE_URL ?>modules/consultas/" class="<?= menu_active('consultas') ?>">
                    <i class="bi bi-clipboard2-pulse"></i>
                    <span>Consultas</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Recetas -->
            <?php if (es_admin() || es_medico()): ?>
            <li>
                <a href="<?= BASE_URL ?>modules/recetas/" class="<?= menu_active('recetas') ?>">
                    <i class="bi bi-prescription2"></i>
                    <span>Recetas</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Ultrasonidos -->
            <?php if (es_admin() || es_medico()): ?>
            <li>
                <a href="<?= BASE_URL ?>modules/ultrasonido/" class="<?= menu_active('ultrasonido') ?>">
                    <i class="bi bi-activity"></i>
                    <span>Ultrasonidos</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Facturación -->
            <?php if (es_admin() || es_medico() || es_asistente()): ?>
            <li>
                <a href="<?= BASE_URL ?>modules/facturacion/" class="<?= menu_active('facturacion') ?>">
                    <i class="bi bi-cash-coin"></i>
                    <span>Facturación</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Recordatorios -->
            <?php if (es_admin() || es_medico() || es_asistente()): ?>
            <li>
                <a href="<?= BASE_URL ?>modules/recordatorios/" class="<?= menu_active('recordatorios') ?>">
                    <i class="bi bi-bell"></i>
                    <span>Recordatorios</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Reportes -->
            <?php if (es_admin() || es_medico()): ?>
            <li>
                <a href="<?= BASE_URL ?>modules/reportes/" class="<?= menu_active('reportes') ?>">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Reportes</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Laboratorios -->
            <?php if (es_admin() || es_medico()): ?>
            <li>
                <a href="<?= BASE_URL ?>modules/laboratorios/" class="<?= menu_active('laboratorios') ?>">
                    <i class="bi bi-eyedropper"></i>
                    <span>Laboratorios</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Inventario -->
            <?php if (es_admin() || es_medico()): ?>
            <li>
                <a href="<?= BASE_URL ?>modules/inventario/" class="<?= menu_active('inventario') ?>">
                    <i class="bi bi-box-seam"></i>
                    <span>Inventario</span>
                </a>
            </li>
            <?php endif; ?>     
            
            <!-- Usuarios (solo Admin) -->
            <?php if (es_admin()): ?>
            <li class="nav-item">
                <a class="nav-link <?= strpos($current_page, 'usuarios') !== false ? 'active' : '' ?>" 
                href="<?= BASE_URL ?>modules/usuarios/index.php">
                    <i class="bi bi-people"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <!-- Usuario -->
    <div class="sidebar-user">
        <div class="sidebar-user-info">
            <div class="sidebar-user-avatar">
                <?= e($iniciales) ?>
            </div>
            <div class="sidebar-user-details">
                <div class="sidebar-user-name"><?= e($usuario_nombre) ?></div>
                <div class="sidebar-user-role"><?= e($usuario_rol_nombre) ?></div>
            </div>
        </div>
        <a href="<?= BASE_URL ?>logout.php" class="btn-logout">
            <i class="bi bi-box-arrow-right"></i>
            <span>CERRAR SESIÓN</span>
        </a>
    </div>
</aside>