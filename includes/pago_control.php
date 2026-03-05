<?php
/**
 * Sistema de Control de Pagos - GG-Systems
 * Verifica el estado de pago del cliente y muestra recordatorios o bloquea el sistema
 * 
 * USO: Incluir después de verificar sesión en cada página protegida
 * require_once INCLUDES_PATH . 'pago_control.php';
 * verificar_estado_pago();
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');

/**
 * Verifica el estado de pago y actúa según corresponda
 * - Muestra recordatorio si está cerca la fecha límite
 * - Bloquea el sistema si pasó la fecha límite
 */
function verificar_estado_pago() {
    // Si el control de pagos está desactivado, no hacer nada
    if (!PAGO_CONTROL_ACTIVO) {
        return;
    }

    // Si el archivo de configuración no existe, no hacer nada (evitar errores)
    if (!file_exists(PAGO_CONFIG_FILE)) {
        return;
    }

    // Leer configuración de pagos
    $config_json = file_get_contents(PAGO_CONFIG_FILE);
    $config = json_decode($config_json, true);

    if (!$config || !isset($config['fecha_limite'])) {
        return;
    }

    // Convertir fechas
    $hoy = new DateTime();
    $fecha_limite = new DateTime($config['fecha_limite']);
    $diferencia_dias = $hoy->diff($fecha_limite)->days;
    $fecha_limite_pasada = $hoy > $fecha_limite;

    // CASO 1: Fecha límite PASADA → BLOQUEAR
    if ($fecha_limite_pasada) {
        mostrar_pantalla_bloqueo($config);
        exit;
    }

    // CASO 2: Falta poco para la fecha límite → RECORDATORIO
    if ($diferencia_dias <= PAGO_DIAS_RECORDATORIO) {
        mostrar_recordatorio_pago($config, $fecha_limite);
    }
}

/**
 * Muestra pantalla de bloqueo total del sistema
 */
function mostrar_pantalla_bloqueo($config) {
    $fecha_limite_formateada = date('d/m/Y', strtotime($config['fecha_limite']));
    $whatsapp_link = 'https://wa.me/' . PAGO_WHATSAPP . '?text=' . urlencode(
        "Hola, soy {$config['cliente']}. Quisiera regularizar el pago de {$config['proyecto']}."
    );
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Servicio Suspendido - <?= APP_NAME ?></title>
        <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .bloqueo-card {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                padding: 40px;
                max-width: 500px;
                text-align: center;
            }
            .icono-bloqueo {
                font-size: 80px;
                color: #dc3545;
                margin-bottom: 20px;
            }
            .btn-whatsapp {
                background: #25D366;
                color: white;
                padding: 12px 30px;
                border-radius: 25px;
                text-decoration: none;
                display: inline-block;
                margin-top: 20px;
                font-weight: bold;
            }
            .btn-whatsapp:hover {
                background: #128C7E;
                color: white;
            }
        </style>
    </head>
    <body>
        <div class="bloqueo-card">
            <div class="icono-bloqueo">🔒</div>
            <h2 class="mb-3">Servicio Suspendido</h2>
            <p class="text-muted mb-4">
                <?= PAGO_MSG_BLOQUEO ?>
            </p>
            <div class="alert alert-warning">
                <strong>Fecha límite de pago:</strong> <?= $fecha_limite_formateada ?>
            </div>
            <p class="mb-2">Para reactivar el servicio, contacte a:</p>
            <p class="fw-bold"><?= APP_AUTHOR ?></p>
            <a href="<?= $whatsapp_link ?>" target="_blank" class="btn-whatsapp">
                💬 Contactar por WhatsApp
            </a>
            <hr class="my-4">
            <small class="text-muted">
                Cliente: <?= $config['cliente'] ?><br>
                Proyecto: <?= $config['proyecto'] ?>
            </small>
        </div>
    </body>
    </html>
    <?php
}

/**
 * Muestra banner de recordatorio (no bloquea, solo avisa)
 */
function mostrar_recordatorio_pago($config, $fecha_limite) {
    $fecha_limite_formateada = $fecha_limite->format('d/m/Y');
    $mensaje = str_replace('{fecha}', $fecha_limite_formateada, PAGO_MSG_RECORDATORIO);
    
    // Guardar en sesión para mostrar solo una vez por sesión
    if (!isset($_SESSION['recordatorio_pago_mostrado'])) {
        $_SESSION['recordatorio_pago_mostrado'] = true;
        $_SESSION['mensaje_recordatorio'] = $mensaje;
    }
}

/**
 * Muestra el recordatorio en la página (llamar en header.php)
 */
function mostrar_banner_recordatorio() {
    if (isset($_SESSION['mensaje_recordatorio'])) {
        $whatsapp_link = 'https://wa.me/' . PAGO_WHATSAPP . '?text=' . urlencode(
            "Hola, quisiera realizar el pago mensual del sistema."
        );
        ?>
        <div class="alert alert-warning alert-dismissible fade show m-3" role="alert">
            <strong>⚠️ Recordatorio de Pago:</strong> <?= $_SESSION['mensaje_recordatorio'] ?>
            <a href="<?= $whatsapp_link ?>" target="_blank" class="alert-link ms-2">
                Contactar para pago
            </a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php
        // Limpiar mensaje después de mostrarlo
        unset($_SESSION['mensaje_recordatorio']);
    }
}