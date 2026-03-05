<?php
/**
 * HEADER DEL SISTEMA
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Header superior con breadcrumb y usuario
 * Incluir en todas las páginas después de verificar sesión
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');

// Obtener datos del usuario actual
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

// Breadcrumb (se puede personalizar por página)
$breadcrumb_items = $breadcrumb_items ?? [
    ['titulo' => 'Clínica', 'url' => null],
    ['titulo' => 'Dashboard', 'url' => null]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión Clínica - <?= APP_NAME ?>">
    <meta name="author" content="<?= APP_AUTHOR ?>">
    <title><?= $page_title ?? 'Dashboard' ?> - <?= APP_NAME ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="<?= BASE_URL ?>assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="<?= BASE_URL ?>assets/css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>assets/css/estilos.css" rel="stylesheet">
    
    <!-- CSS adicional por página -->
    <?php if (isset($additional_css)): ?>
        <?= $additional_css ?>
    <?php endif; ?>
</head>
<body>
    <div class="main-wrapper">
        <!-- Sidebar -->
        <?php include INCLUDES_PATH . 'navbar.php'; ?>
        
        <!-- Contenido Principal -->
        <div class="main-content">
            <!-- Header Superior -->
            <div class="top-header">
                <!-- Breadcrumb -->
                <div class="breadcrumb mb-0">
                    <?php foreach ($breadcrumb_items as $index => $item): ?>
                        <?php if ($index > 0): ?>
                            <span class="mx-2 text-muted">›</span>
                        <?php endif; ?>
                        
                        <?php if ($index === count($breadcrumb_items) - 1): ?>
                            <span><?= e($item['titulo']) ?></span>
                        <?php else: ?>
                            <?php if ($item['url']): ?>
                                <a href="<?= e($item['url']) ?>" class="text-muted text-decoration-none">
                                    <?= e($item['titulo']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted"><?= e($item['titulo']) ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <!-- Usuario e iconos -->
                <div class="top-header-user">
                    <!-- Icono de notificaciones (futuro) -->
                    <div class="notification-icon" title="Notificaciones">
                        <i class="bi bi-bell"></i>
                    </div>
                    
                    <!-- Separador -->
                    <div style="width: 1px; height: 30px; background: #dee2e6;"></div>
                    
                    <!-- Info del usuario -->
                    <div class="user-info-header">
                        <div class="user-info-header-name"><?= e($usuario_nombre) ?></div>
                        <div class="user-info-header-role"><?= e($usuario_rol_nombre) ?></div>
                    </div>
                    
                    <!-- Avatar -->
                    <div class="user-avatar-header">
                        <?= e($iniciales) ?>
                    </div>
                </div>
            </div>
            
            <!-- Mensajes del sistema -->
            <div class="page-content">
                <?= mostrar_mensajes() ?>
                
                <!-- Banner de recordatorio de pago (si aplica) -->
                <?php 
                if (function_exists('mostrar_banner_recordatorio')) {
                    mostrar_banner_recordatorio();
                }
                ?>
                
                <!-- Aquí va el contenido de cada página -->