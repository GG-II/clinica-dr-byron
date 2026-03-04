<?php
/**
 * Conexión a base de datos
 * Usa PDO con prepared statements
 * 
 * Uso en cualquier archivo:
 * define('ACCESS_GRANTED', true);
 * require_once 'ruta/a/config.php';
 * require_once 'ruta/a/includes/db.php';
 * // Ya tienes $pdo disponible
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);

} catch (PDOException $e) {
    if (ENVIRONMENT === 'development') {
        die('Error de conexión: ' . $e->getMessage());
    } else {
        error_log('DB Error: ' . $e->getMessage());
        die('Error de conexión. Contacte al administrador.');
    }
}