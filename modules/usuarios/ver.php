<?php
/**
 * USUARIOS - Ver Usuario
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Vista detallada de usuario del sistema
 */

define('ACCESS_GRANTED', true);
session_start();

require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once MODELS_PATH . 'Usuario.php';

// Verificar autenticación y permisos (SOLO ADMIN)
verificar_sesion();

if (!es_admin()) {
    $_SESSION['mensaje_error'] = 'No tienes permisos para ver usuarios';
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
}

// Obtener ID del usuario
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['mensaje_error'] = 'ID de usuario inválido';
    header('Location: ' . BASE_URL . 'modules/usuarios/index.php');
    exit;
}

// Obtener datos del usuario
$usuario = obtener_usuario($pdo, $id);

if (!$usuario) {
    $_SESSION['mensaje_error'] = 'Usuario no encontrado';
    header('Location: ' . BASE_URL . 'modules/usuarios/index.php');
    exit;
}

// Generar iniciales para avatar
$iniciales = '';
$palabras = explode(' ', $usuario['nombre']);
$iniciales = strtoupper(substr($palabras[0], 0, 1));
if (isset($palabras[1])) {
    $iniciales .= strtoupper(substr($palabras[1], 0, 1));
}

// Color según rol
$color_rol = 'primary';
$nombre_rol = 'Usuario';
if ($usuario['rol'] === 'admin') {
    $color_rol = 'danger';
    $nombre_rol = 'Administrador';
} elseif ($usuario['rol'] === 'medico') {
    $color_rol = 'primary';
    $nombre_rol = 'Médico';
} elseif ($usuario['rol'] === 'asistente') {
    $color_rol = 'success';
    $nombre_rol = 'Asistente';
}

// Variables para el header
$titulo = 'Detalle de Usuario';
$breadcrumb = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard.php'],
    ['titulo' => 'Usuarios', 'url' => BASE_URL . 'modules/usuarios/index.php'],
    ['titulo' => 'Ver', 'url' => '']
];

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Contenido principal -->
<div class="container-fluid mt-4">
    
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="<?= BASE_URL ?>modules/usuarios/index.php" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <h2><?= $titulo ?></h2>
            <p class="text-muted">Usuarios / Ver</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= BASE_URL ?>modules/usuarios/editar.php?id=<?= $id ?>" 
               class="btn btn-warning me-2">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="<?= BASE_URL ?>modules/usuarios/cambiar-password.php?id=<?= $id ?>" 
               class="btn btn-secondary">
                <i class="bi bi-key"></i> Cambiar Contraseña
            </a>
        </div>
    </div>
    
    <!-- Mensajes flash -->
    <?php mostrar_mensajes(); ?>
    
    <div class="row">
        
        <!-- Columna principal -->
        <div class="col-lg-8">
            
            <!-- Información del Usuario -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle"></i>
                        Información del Usuario
                    </h5>
                </div>
                <div class="card-body">
                    
                    <!-- Avatar y nombre -->
                    <div class="text-center mb-4 pb-4 border-bottom">
                        <div style="width: 120px; height: 120px; border-radius: 50%; background-color: var(--bs-<?= $color_rol ?>); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 3rem; margin: 0 auto 1rem;">
                            <?= $iniciales ?>
                        </div>
                        <h3 class="mb-1"><?= e($usuario['nombre']) ?></h3>
                        <p class="text-muted mb-2"><?= e($usuario['email']) ?></p>
                        <span class="badge bg-<?= $color_rol ?> fs-6">
                            <?= $nombre_rol ?>
                        </span>
                    </div>
                    
                    <!-- Detalles -->
                    <div class="row g-3">
                        
                        <div class="col-md-6">
                            <label class="text-muted small">NOMBRE COMPLETO</label>
                            <p class="mb-0 fw-bold"><?= e($usuario['nombre']) ?></p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="text-muted small">EMAIL</label>
                            <p class="mb-0 fw-bold"><?= e($usuario['email']) ?></p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="text-muted small">ROL</label>
                            <p class="mb-0">
                                <span class="badge bg-<?= $color_rol ?> fs-6">
                                    <?= $nombre_rol ?>
                                </span>
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="text-muted small">ESTADO</label>
                            <p class="mb-0">
                                <?php if ($usuario['activo']): ?>
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-check-circle-fill"></i> Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger fs-6">
                                        <i class="bi bi-x-circle-fill"></i> Inactivo
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="text-muted small">FECHA DE CREACIÓN</label>
                            <p class="mb-0 fw-bold">
                                <?= formatear_fecha($usuario['created_at']) ?>
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="text-muted small">ÚLTIMA ACTUALIZACIÓN</label>
                            <p class="mb-0 fw-bold">
                                <?= formatear_fecha($usuario['updated_at']) ?>
                            </p>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
            
            <!-- Permisos según rol -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-shield-lock"></i>
            Permisos y Accesos
        </h5>
    </div>
    <div class="card-body">
        
        <?php if ($usuario['rol'] === 'admin'): ?>
            
            <div class="p-3 mb-0" style="background-color: rgba(220, 53, 69, 0.1); border-left: 4px solid #dc3545;">
                <h6 class="mb-2">
                    <i class="bi bi-shield-fill-exclamation text-danger"></i>
                    <strong>Administrador - Acceso Total</strong>
                </h6>
                <p class="mb-2">Este usuario tiene acceso completo a todas las funciones del sistema:</p>
                <ul class="mb-0">
                    <li>Gestión completa de usuarios</li>
                    <li>Acceso a todos los módulos</li>
                    <li>Visualización y generación de reportes</li>
                    <li>Configuración del sistema</li>
                    <li>Gestión de pacientes, citas, consultas</li>
                    <li>Facturación y reportes financieros</li>
                </ul>
            </div>
            
        <?php elseif ($usuario['rol'] === 'medico'): ?>
            
            <div class="p-3 mb-0" style="background-color: rgba(13, 110, 253, 0.1); border-left: 4px solid #0d6efd;">
                <h6 class="mb-2">
                    <i class="bi bi-person-badge text-primary"></i>
                    <strong>Médico - Acceso Clínico</strong>
                </h6>
                <p class="mb-2">Este usuario tiene acceso a funciones clínicas:</p>
                <ul class="mb-0">
                    <li>Gestión de pacientes y expedientes</li>
                    <li>Creación y edición de citas</li>
                    <li>Registro de consultas médicas</li>
                    <li>Emisión de recetas médicas</li>
                    <li>Informes de ultrasonido</li>
                    <li>Facturación de consultas</li>
                    <li>Laboratorios e inventario</li>
                    <li>Reportes clínicos</li>
                </ul>
            </div>
            
        <?php elseif ($usuario['rol'] === 'asistente'): ?>
            
            <div class="p-3 mb-0" style="background-color: rgba(25, 135, 84, 0.1); border-left: 4px solid #198754;">
                <h6 class="mb-2">
                    <i class="bi bi-person-check text-success"></i>
                    <strong>Asistente - Acceso Limitado</strong>
                </h6>
                <p class="mb-2">Este usuario tiene acceso limitado a:</p>
                <ul class="mb-0">
                    <li>Gestión de citas (agendar, modificar, cancelar)</li>
                    <li>Datos básicos de pacientes (solo lectura)</li>
                    <li>Facturación y cobros</li>
                    <li>Envío de recordatorios</li>
                    <li>Visualización de inventario</li>
                </ul>
                <hr>
                <p class="mb-0 small text-muted">
                    <i class="bi bi-info-circle"></i>
                    <strong>Sin acceso a:</strong> Consultas médicas, recetas, ultrasonidos, reportes, gestión de usuarios.
                </p>
            </div>
            
        <?php endif; ?>
        
    </div>
</div>
            
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            
            <!-- Estado del usuario -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-toggle-on"></i>
                        Estado
                    </h6>
                </div>
                <div class="card-body">
                    
                    <?php if ($usuario['activo']): ?>
                        <div class="alert alert-success mb-3">
                            <h6 class="mb-1">
                                <i class="bi bi-check-circle-fill"></i>
                                Usuario Activo
                            </h6>
                            <p class="small mb-0">
                                Este usuario puede iniciar sesión y usar el sistema normalmente.
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-3">
                            <h6 class="mb-1">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                Usuario Inactivo
                            </h6>
                            <p class="small mb-0">
                                Este usuario NO puede iniciar sesión en el sistema.
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($usuario['id'] != obtener_usuario_id()): ?>
                        <button type="button" 
                                class="btn btn-<?= $usuario['activo'] ? 'danger' : 'success' ?> w-100"
                                onclick="cambiarEstado(<?= $usuario['id'] ?>, <?= $usuario['activo'] ? 0 : 1 ?>)">
                            <i class="bi bi-<?= $usuario['activo'] ? 'toggle-off' : 'toggle-on' ?>"></i>
                            <?= $usuario['activo'] ? 'Desactivar Usuario' : 'Activar Usuario' ?>
                        </button>
                    <?php else: ?>
                        <div class="alert alert-info mb-0 small">
                            <i class="bi bi-info-circle"></i>
                            No puedes cambiar tu propio estado
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
            
            <!-- Acciones rápidas -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning"></i>
                        Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    
                    <a href="<?= BASE_URL ?>modules/usuarios/editar.php?id=<?= $id ?>" 
                       class="btn btn-warning w-100 mb-2">
                        <i class="bi bi-pencil"></i> Editar Usuario
                    </a>
                    
                    <a href="<?= BASE_URL ?>modules/usuarios/cambiar-password.php?id=<?= $id ?>" 
                       class="btn btn-secondary w-100 mb-2">
                        <i class="bi bi-key"></i> Cambiar Contraseña
                    </a>
                    
                    <a href="<?= BASE_URL ?>modules/usuarios/nuevo.php" 
                       class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-person-plus"></i> Nuevo Usuario
                    </a>
                    
                    <a href="<?= BASE_URL ?>modules/usuarios/index.php" 
                       class="btn btn-outline-secondary w-100">
                        <i class="bi bi-list-ul"></i> Ver Todos los Usuarios
                    </a>
                    
                </div>
            </div>
            
        </div>
        
    </div>
    
</div>

<script>
// Cambiar estado de usuario
function cambiarEstado(userId, nuevoEstado) {
    const accion = nuevoEstado ? 'activar' : 'desactivar';
    
    if (!confirm(`¿Está seguro que desea ${accion} este usuario?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('activo', nuevoEstado);
    
    fetch('<?= BASE_URL ?>api/usuarios/cambiar_estado.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'No se pudo cambiar el estado'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}
</script>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>