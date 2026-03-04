<?php
/**
 * Configuración principal del sistema
 * 
 * INSTRUCCIONES:
 * 1. Copiar este archivo y renombrarlo a config.php
 * 2. Ajustar los valores según tu entorno
 * 3. NUNCA subir config.php a Git (ya está en .gitignore)
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');

// ==========================================
// BASE DE DATOS
// ==========================================
define('DB_HOST',    'localhost');
define('DB_PORT',    '3306');       // Cambiar a 3307 si usas puerto alternativo
define('DB_NAME',    'clinica_dr_byron');
define('DB_USER',    'tu_usuario'); // En local: root
define('DB_PASS',    'tu_password');// En local: dejar vacío ''
define('DB_CHARSET', 'utf8mb4');

// ==========================================
// APLICACIÓN
// ==========================================
define('APP_NAME',    'Clínica Médica de la Mujer');
define('APP_VERSION', '1.0.0');
define('BASE_URL',    'http://localhost/clinica-dr-byron/');

// ==========================================
// ENTORNO
// ==========================================
define('ENVIRONMENT', 'development'); // development | production

// ==========================================
// RUTAS
// ==========================================
define('ROOT_PATH',    __DIR__ . '/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('LOGS_PATH',    ROOT_PATH . 'logs/');
define('PDF_PATH',     ROOT_PATH . 'pdf/');

// ==========================================
// SESIÓN
// ==========================================
define('SESSION_LIFETIME', 7200); // 2 horas en segundos

// ==========================================
// ZONA HORARIA
// ==========================================
date_default_timezone_set('America/Guatemala');

// ==========================================
// ERRORES (se ajusta automáticamente según ENVIRONMENT)
// ==========================================
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOGS_PATH . 'php_errors.log');
}

// ==========================================
// PAGINACIÓN
// ==========================================
define('REGISTROS_POR_PAGINA', 25);