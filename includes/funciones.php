<?php
/**
 * FUNCIONES HELPER GENERALES
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Este archivo contiene funciones auxiliares que se usan en todo el sistema.
 * Incluye: validaciones, formateo de datos, seguridad, utilidades comunes.
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');

// ============================================================================
// FUNCIONES DE SEGURIDAD
// ============================================================================

/**
 * Limpia y escapa texto para mostrar en HTML
 * Previene ataques XSS
 * 
 * @param string $texto Texto a limpiar
 * @return string Texto limpio y seguro
 */
function limpiar_texto($texto) {
    if (is_null($texto)) {
        return '';
    }
    return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
}

/**
 * Alias corto para limpiar_texto (más cómodo de escribir)
 */
function e($texto) {
    return limpiar_texto($texto);
}

/**
 * Genera un token CSRF único para formularios
 * 
 * @return string Token CSRF de 64 caracteres
 */
function generar_csrf_token() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        $_SESSION[CSRF_TOKEN_NAME . '_time'] = time();
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Valida que el token CSRF sea correcto
 * 
 * @param string $token Token a validar
 * @return bool True si es válido, False si no
 */
function validar_csrf_token($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME]) || empty($token)) {
        return false;
    }
    
    // Verificar que el token no haya expirado
    if (isset($_SESSION[CSRF_TOKEN_NAME . '_time'])) {
        $token_age = time() - $_SESSION[CSRF_TOKEN_NAME . '_time'];
        if ($token_age > CSRF_TOKEN_TIME) {
            return false;
        }
    }
    
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Genera campo hidden con token CSRF para formularios
 * 
 * @return string HTML del campo hidden
 */
function campo_csrf() {
    $token = generar_csrf_token();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
}

// ============================================================================
// FUNCIONES DE VALIDACIÓN
// ============================================================================

/**
 * Valida que un email sea válido
 * 
 * @param string $email Email a validar
 * @return bool True si es válido
 */
function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida que un teléfono guatemalteco sea válido
 * Formato: 8 dígitos o con guión 1234-5678
 * 
 * @param string $telefono Teléfono a validar
 * @return bool True si es válido
 */
function validar_telefono($telefono) {
    // Quitar espacios y guiones
    $telefono_limpio = str_replace([' ', '-'], '', $telefono);
    // Debe tener 8 dígitos
    return preg_match('/^[0-9]{8}$/', $telefono_limpio);
}

/**
 * Valida que un DPI guatemalteco sea válido
 * Formato: 13 dígitos
 * 
 * @param string $dpi DPI a validar
 * @return bool True si es válido
 */
function validar_dpi($dpi) {
    // Quitar espacios
    $dpi_limpio = str_replace(' ', '', $dpi);
    // Debe tener 13 dígitos
    return preg_match('/^[0-9]{13}$/', $dpi_limpio);
}

/**
 * Valida que una fecha sea válida
 * 
 * @param string $fecha Fecha a validar (formato: YYYY-MM-DD o DD/MM/YYYY)
 * @return bool True si es válida
 */
function validar_fecha($fecha) {
    // Intentar parsear la fecha
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    if ($d && $d->format('Y-m-d') === $fecha) {
        return true;
    }
    
    $d = DateTime::createFromFormat('d/m/Y', $fecha);
    return $d && $d->format('d/m/Y') === $fecha;
}

/**
 * Valida que un número sea positivo
 * 
 * @param mixed $numero Número a validar
 * @return bool True si es positivo
 */
function validar_numero_positivo($numero) {
    return is_numeric($numero) && $numero > 0;
}

/**
 * Valida que una cadena no esté vacía
 * 
 * @param string $texto Texto a validar
 * @return bool True si NO está vacía
 */
function validar_no_vacio($texto) {
    return !empty(trim($texto));
}

// ============================================================================
// FUNCIONES DE FORMATEO DE DATOS
// ============================================================================

/**
 * Formatea una fecha de BD (Y-m-d) a formato guatemalteco (d/m/Y)
 * 
 * @param string $fecha_bd Fecha en formato BD (2025-03-05)
 * @return string Fecha formateada (05/03/2025) o cadena vacía si es NULL
 */
function formatear_fecha($fecha_bd) {
    if (empty($fecha_bd) || $fecha_bd === '0000-00-00') {
        return '';
    }
    
    $fecha = DateTime::createFromFormat('Y-m-d', $fecha_bd);
    if (!$fecha) {
        return $fecha_bd; // Si no se puede parsear, devolver original
    }
    
    return $fecha->format(FORMATO_FECHA);
}

/**
 * Formatea una fecha-hora de BD a formato legible
 * 
 * @param string $fecha_hora_bd Fecha-hora en formato BD (2025-03-05 14:30:00)
 * @return string Fecha-hora formateada (05/03/2025 14:30)
 */
function formatear_fecha_hora($fecha_hora_bd) {
    if (empty($fecha_hora_bd) || $fecha_hora_bd === '0000-00-00 00:00:00') {
        return '';
    }
    
    $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $fecha_hora_bd);
    if (!$fecha) {
        return $fecha_hora_bd;
    }
    
    return $fecha->format(FORMATO_FECHA_HORA);
}

/**
 * Convierte fecha guatemalteca (d/m/Y) a formato BD (Y-m-d)
 * 
 * @param string $fecha_gt Fecha en formato guatemalteco (05/03/2025)
 * @return string Fecha en formato BD (2025-03-05) o NULL si inválida
 */
function fecha_a_bd($fecha_gt) {
    $fecha = DateTime::createFromFormat('d/m/Y', $fecha_gt);
    if (!$fecha) {
        return null;
    }
    
    return $fecha->format(FORMATO_FECHA_BD);
}

/**
 * Formatea un número como moneda guatemalteca
 * 
 * @param float $monto Monto a formatear
 * @param bool $incluir_simbolo Incluir símbolo Q (default: true)
 * @return string Monto formateado (Q 1,234.56)
 */
function formatear_moneda($monto, $incluir_simbolo = true) {
    $formateado = number_format($monto, 2, '.', ',');
    return $incluir_simbolo ? 'Q ' . $formateado : $formateado;
}

/**
 * Formatea un número de teléfono guatemalteco
 * De 12345678 a 1234-5678
 * 
 * @param string $telefono Teléfono sin formato
 * @return string Teléfono formateado
 */
function formatear_telefono($telefono) {
    $telefono_limpio = str_replace([' ', '-'], '', $telefono);
    
    if (strlen($telefono_limpio) === 8) {
        return substr($telefono_limpio, 0, 4) . '-' . substr($telefono_limpio, 4);
    }
    
    return $telefono;
}

/**
 * Formatea un DPI guatemalteco
 * De 1234567890123 a 1234 56789 0123
 * 
 * @param string $dpi DPI sin formato
 * @return string DPI formateado
 */
function formatear_dpi($dpi) {
    $dpi_limpio = str_replace(' ', '', $dpi);
    
    if (strlen($dpi_limpio) === 13) {
        return substr($dpi_limpio, 0, 4) . ' ' . 
               substr($dpi_limpio, 4, 5) . ' ' . 
               substr($dpi_limpio, 9, 4);
    }
    
    return $dpi;
}

// ============================================================================
// FUNCIONES DE UTILIDAD
// ============================================================================

/**
 * Calcula la edad a partir de una fecha de nacimiento
 * 
 * @param string $fecha_nacimiento Fecha de nacimiento (Y-m-d)
 * @return int Edad en años
 */
function calcular_edad($fecha_nacimiento) {
    if (empty($fecha_nacimiento) || $fecha_nacimiento === '0000-00-00') {
        return 0;
    }
    
    $nacimiento = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento);
    
    return $edad->y;
}

/**
 * Genera un código único alfanumérico
 * Útil para códigos de pacientes, facturas, etc.
 * 
 * @param string $prefijo Prefijo opcional (ej: 'PAC', 'FAC')
 * @param int $longitud Longitud del código sin prefijo (default: 6)
 * @return string Código generado (ej: PAC-A1B2C3)
 */
function generar_codigo($prefijo = '', $longitud = 6) {
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $codigo = '';
    
    for ($i = 0; $i < $longitud; $i++) {
        $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    
    return $prefijo ? $prefijo . '-' . $codigo : $codigo;
}

/**
 * Genera un número correlativo
 * 
 * @param int $numero Número actual
 * @param int $digitos Cantidad de dígitos (default: 6)
 * @return string Número con ceros a la izquierda (ej: 000042)
 */
function generar_correlativo($numero, $digitos = 6) {
    return str_pad($numero, $digitos, '0', STR_PAD_LEFT);
}

/**
 * Redirecciona a otra página
 * 
 * @param string $url URL a redireccionar (relativa a BASE_URL)
 */
function redireccionar($url) {
    // Si la URL no empieza con http, agregar BASE_URL
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = BASE_URL . ltrim($url, '/');
    }
    
    header('Location: ' . $url);
    exit;
}

/**
 * Registra un mensaje en el log del sistema
 * 
 * @param string $mensaje Mensaje a registrar
 * @param string $nivel Nivel: info, warning, error, debug
 * @return bool True si se escribió correctamente
 */
function log_mensaje($mensaje, $nivel = 'info') {
    // Ruta absoluta del archivo de log
    $logs_dir = __DIR__ . '/../logs/';
    $log_file = $logs_dir . 'sistema.log';
    
    // Crear directorio si no existe
    if (!is_dir($logs_dir)) {
        if (!mkdir($logs_dir, 0755, true)) {
            // Si falla crear directorio, escribir en error_log de PHP
            error_log("ERROR: No se pudo crear directorio de logs: {$logs_dir}");
            return false;
        }
    }
    
    // Crear archivo si no existe
    if (!file_exists($log_file)) {
        if (!touch($log_file)) {
            error_log("ERROR: No se pudo crear archivo de log: {$log_file}");
            return false;
        }
        chmod($log_file, 0666);
    }
    
    // Verificar que sea escribible
    if (!is_writable($log_file)) {
        error_log("ERROR: Archivo de log no es escribible: {$log_file}");
        chmod($log_file, 0666); // Intentar dar permisos
    }
    
    // Formato del mensaje
    $fecha = date('Y-m-d H:i:s');
    $nivel_upper = strtoupper($nivel);
    $usuario = $_SESSION['usuario_nombre'] ?? 'SISTEMA';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    $linea = "[{$fecha}] [{$nivel_upper}] [{$usuario}] [{$ip}] {$mensaje}" . PHP_EOL;
    
    // Intentar escribir
    $resultado = file_put_contents($log_file, $linea, FILE_APPEND | LOCK_EX);
    
    // Si falla, escribir en error_log de PHP también
    if ($resultado === false) {
        error_log("FALLO AL ESCRIBIR LOG: {$mensaje}");
        error_log("Ruta intentada: {$log_file}");
        error_log("Directorio existe: " . (is_dir($logs_dir) ? 'SÍ' : 'NO'));
        error_log("Archivo existe: " . (file_exists($log_file) ? 'SÍ' : 'NO'));
        error_log("Es escribible: " . (is_writable($log_file) ? 'SÍ' : 'NO'));
        return false;
    }
    
    // En desarrollo, mostrar errores también en error_log de PHP
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development' && $nivel === 'error') {
        error_log("APP ERROR: {$mensaje}");
    }
    
    return true;
}

/**
 * Debug: imprime variable de forma legible (solo en desarrollo)
 * 
 * @param mixed $variable Variable a imprimir
 * @param bool $die Detener ejecución después de imprimir (default: false)
 */
function dd($variable, $die = true) {
    if (ENVIRONMENT !== 'development') {
        return;
    }
    
    echo '<pre style="background: #f4f4f4; padding: 15px; border: 1px solid #ddd; border-radius: 5px; overflow: auto;">';
    var_dump($variable);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}

/**
 * Sanitiza un string para usarlo en nombres de archivos
 * 
 * @param string $filename Nombre de archivo
 * @return string Nombre sanitizado
 */
function sanitizar_filename($filename) {
    // Quitar acentos
    $filename = iconv('UTF-8', 'ASCII//TRANSLIT', $filename);
    // Reemplazar espacios y caracteres especiales
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    // Quitar múltiples guiones bajos
    $filename = preg_replace('/_+/', '_', $filename);
    
    return strtolower($filename);
}

/**
 * Obtiene la extensión de un archivo
 * 
 * @param string $filename Nombre del archivo
 * @return string Extensión en minúsculas
 */
function obtener_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Convierte bytes a formato legible (KB, MB, GB)
 * 
 * @param int $bytes Cantidad de bytes
 * @param int $precision Decimales (default: 2)
 * @return string Tamaño formateado
 */
function formatear_bytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

// ============================================================================
// FUNCIONES DE MENSAJES (para mostrar en vistas)
// ============================================================================

/**
 * Guarda un mensaje de éxito en la sesión
 * 
 * @param string $texto Mensaje a mostrar
 */
function mensaje_exito($texto) {
    $_SESSION['mensaje'] = [
        'tipo' => 'exito',
        'texto' => $texto
    ];
}

/**
 * Guarda un mensaje de error en la sesión
 * 
 * @param string $texto Mensaje a mostrar
 */
function mensaje_error($texto) {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => $texto
    ];
}

/**
 * Guarda un mensaje de advertencia en la sesión
 * 
 * @param string $texto Mensaje a mostrar
 */
function mensaje_advertencia($texto) {
    $_SESSION['mensaje'] = [
        'tipo' => 'warning',
        'texto' => $texto
    ];
}

/**
 * Guarda un mensaje informativo en la sesión
 * 
 * @param string $texto Mensaje a mostrar
 */
function mensaje_info($texto) {
    $_SESSION['mensaje'] = [
        'tipo' => 'info',
        'texto' => $texto
    ];
}

/**
 * Muestra los mensajes flash del sistema
 * 
 * @return string HTML con los mensajes
 */
function mostrar_mensajes() {
    if (!isset($_SESSION['mensaje'])) {
        return '';
    }
    
    $tipo = $_SESSION['mensaje']['tipo'];
    $texto = $_SESSION['mensaje']['texto'];
    
    // Determinar clase Bootstrap según tipo
    $clase_bootstrap = 'alert-info';
    $icono = 'bi-info-circle-fill';
    
    switch ($tipo) {
        case 'exito':
        case 'success':
            $clase_bootstrap = 'alert-success';
            $icono = 'bi-check-circle-fill';
            break;
        case 'error':
        case 'danger':
            $clase_bootstrap = 'alert-danger';
            $icono = 'bi-exclamation-triangle-fill';
            break;
        case 'warning':
        case 'advertencia':
            $clase_bootstrap = 'alert-warning';
            $icono = 'bi-exclamation-circle-fill';
            break;
        case 'info':
            $clase_bootstrap = 'alert-info';
            $icono = 'bi-info-circle-fill';
            break;
    }
    
    $html = "<div class='alert {$clase_bootstrap} alert-dismissible fade show' role='alert'>";
    $html .= "  <i class='bi {$icono} me-2'></i>";
    $html .= "  <strong>" . e($texto) . "</strong>";
    $html .= "  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
    $html .= "</div>";
    
    // Limpiar mensaje después de mostrarlo
    unset($_SESSION['mensaje']);
    
    return $html;
}

// ============================================================================
// FUNCIONES DE PAGINACIÓN
// ============================================================================

/**
 * Calcula el offset para paginación
 * 
 * @param int $pagina Número de página actual
 * @param int $registros_por_pagina Registros por página
 * @return int Offset para la consulta SQL
 */
function calcular_offset($pagina, $registros_por_pagina = REGISTROS_POR_PAGINA) {
    $pagina = max(1, intval($pagina)); // Mínimo página 1
    return ($pagina - 1) * $registros_por_pagina;
}

/**
 * Genera HTML de paginación estilo Bootstrap 5
 * 
 * @param int $total_registros Total de registros
 * @param int $pagina_actual Página actual
 * @param int $registros_por_pagina Registros por página
 * @param string $url_base URL base para los links (sin parámetro page)
 * @return string HTML de la paginación
 */
function generar_paginacion($total_registros, $pagina_actual, $registros_por_pagina = REGISTROS_POR_PAGINA, $url_base = '') {
    $total_paginas = ceil($total_registros / $registros_por_pagina);
    
    if ($total_paginas <= 1) {
        return ''; // No mostrar paginación si solo hay 1 página
    }
    
    $pagina_actual = max(1, min($pagina_actual, $total_paginas));
    
    // Agregar ? o & según corresponda
    $separador = strpos($url_base, '?') !== false ? '&' : '?';
    
    $html = '<nav><ul class="pagination justify-content-center">';
    
    // Botón anterior
    if ($pagina_actual > 1) {
        $html .= sprintf(
            '<li class="page-item"><a class="page-link" href="%s%spage=%d">Anterior</a></li>',
            $url_base,
            $separador,
            $pagina_actual - 1
        );
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
    }
    
    // Números de página
    $rango = 2; // Mostrar 2 páginas antes y después
    $inicio = max(1, $pagina_actual - $rango);
    $fin = min($total_paginas, $pagina_actual + $rango);
    
    if ($inicio > 1) {
        $html .= sprintf('<li class="page-item"><a class="page-link" href="%s%spage=1">1</a></li>', $url_base, $separador);
        if ($inicio > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $inicio; $i <= $fin; $i++) {
        if ($i == $pagina_actual) {
            $html .= sprintf('<li class="page-item active"><span class="page-link">%d</span></li>', $i);
        } else {
            $html .= sprintf('<li class="page-item"><a class="page-link" href="%s%spage=%d">%d</a></li>', $url_base, $separador, $i, $i);
        }
    }
    
    if ($fin < $total_paginas) {
        if ($fin < $total_paginas - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= sprintf('<li class="page-item"><a class="page-link" href="%s%spage=%d">%d</a></li>', $url_base, $separador, $total_paginas, $total_paginas);
    }
    
    // Botón siguiente
    if ($pagina_actual < $total_paginas) {
        $html .= sprintf(
            '<li class="page-item"><a class="page-link" href="%s%spage=%d">Siguiente</a></li>',
            $url_base,
            $separador,
            $pagina_actual + 1
        );
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Siguiente</span></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}