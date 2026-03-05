<?php
/**
 * CERRAR SESIÓN
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Cierra la sesión del usuario y redirige al login
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

session_start();
define('ACCESS_GRANTED', true);
require_once 'config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once INCLUDES_PATH . 'auth.php';

// Cerrar sesión
cerrar_sesion($pdo);

// Mensaje de despedida
mensaje_info('Sesión cerrada correctamente. ¡Hasta pronto!');

// Redirigir al login
redireccionar('index');