<?php
/**
 * USUARIOS - Cambiar Contraseña
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para cambiar contraseña de usuario
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
    $_SESSION['mensaje_error'] = 'No tienes permisos para cambiar contraseñas';
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

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        // Validar contraseña
        if (empty($_POST['password'])) {
            throw new Exception('Debe ingresar la nueva contraseña');
        }
        
        if (strlen($_POST['password']) < 6) {
            throw new Exception('La contraseña debe tener al menos 6 caracteres');
        }
        
        if ($_POST['password'] !== $_POST['password_confirm']) {
            throw new Exception('Las contraseñas no coinciden');
        }
        
        // Actualizar solo la contraseña
        $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $resultado = $stmt->execute([$password_hash, $id]);
        
        if ($resultado) {
            error_log("Contraseña cambiada para usuario ID: $id");
            $_SESSION['mensaje_exito'] = 'Contraseña actualizada correctamente';
            header('Location: ' . BASE_URL . 'modules/usuarios/ver.php?id=' . $id);
            exit;
        } else {
            throw new Exception('Error al actualizar la contraseña');
        }
        
    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = $e->getMessage();
    }
}

// Variables para el header
$titulo = 'Cambiar Contraseña';
$breadcrumb = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard.php'],
    ['titulo' => 'Usuarios', 'url' => BASE_URL . 'modules/usuarios/index.php'],
    ['titulo' => 'Cambiar Contraseña', 'url' => '']
];

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Contenido principal -->
<div class="container-fluid mt-4">
    
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="<?= BASE_URL ?>modules/usuarios/ver.php?id=<?= $id ?>" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <h2><?= $titulo ?></h2>
            <p class="text-muted">Usuarios / Cambiar Contraseña</p>
        </div>
    </div>
    
    <!-- Mensajes flash -->
    <?php mostrar_mensajes(); ?>
    
    <!-- Formulario -->
    <div class="row justify-content-center">
        <div class="col-lg-6">
            
            <!-- Info del usuario -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php
                    // Generar iniciales
                    $iniciales = '';
                    $palabras = explode(' ', $usuario['nombre']);
                    $iniciales = strtoupper(substr($palabras[0], 0, 1));
                    if (isset($palabras[1])) {
                        $iniciales .= strtoupper(substr($palabras[1], 0, 1));
                    }
                    
                    // Color según rol
                    $color_rol = 'primary';
                    if ($usuario['rol'] === 'admin') {
                        $color_rol = 'danger';
                    } elseif ($usuario['rol'] === 'medico') {
                        $color_rol = 'primary';
                    } elseif ($usuario['rol'] === 'asistente') {
                        $color_rol = 'success';
                    }
                    ?>
                    <div style="width: 80px; height: 80px; border-radius: 50%; background-color: var(--bs-<?= $color_rol ?>); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 2rem; margin: 0 auto 1rem;">
                        <?= $iniciales ?>
                    </div>
                    <h5 class="mb-1"><?= e($usuario['nombre']) ?></h5>
                    <p class="text-muted mb-0"><?= e($usuario['email']) ?></p>
                </div>
            </div>
            
            <!-- Formulario de contraseña -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-key"></i>
                        Nueva Contraseña
                    </h5>
                </div>
                <div class="card-body">
                    
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        Ingresa la nueva contraseña para <strong><?= e($usuario['nombre']) ?></strong>. 
                        El usuario deberá iniciar sesión nuevamente con esta contraseña.
                    </div>
                    
                    <form method="POST" id="form-password">
                        
                        <!-- Nueva contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label text-muted small mb-1">
                                NUEVA CONTRASEÑA <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" 
                                       class="form-control form-control-lg" 
                                       required minlength="6" maxlength="50"
                                       placeholder="Mínimo 6 caracteres">
                                <button class="btn btn-outline-secondary" type="button" 
                                        id="toggle-password" title="Mostrar/Ocultar">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Confirmar contraseña -->
                        <div class="mb-4">
                            <label for="password_confirm" class="form-label text-muted small mb-1">
                                CONFIRMAR CONTRASEÑA <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" name="password_confirm" id="password_confirm" 
                                       class="form-control form-control-lg" 
                                       required minlength="6" maxlength="50"
                                       placeholder="Repite la contraseña">
                                <button class="btn btn-outline-secondary" type="button" 
                                        id="toggle-confirm" title="Mostrar/Ocultar">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Indicador de fortaleza -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">FORTALEZA DE LA CONTRASEÑA</small>
                                <small class="text-muted" id="strength-text">-</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" id="strength-bar" 
                                     role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= BASE_URL ?>modules/usuarios/ver.php?id=<?= $id ?>" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-key"></i> Cambiar Contraseña
                            </button>
                        </div>
                        
                    </form>
                    
                </div>
            </div>
            
            <!-- Consejos de seguridad -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-shield-check"></i>
                        Consejos de Seguridad
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Usa al menos 6 caracteres
                    </p>
                    <p class="small text-muted mb-2">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Combina letras y números
                    </p>
                    <p class="small text-muted mb-2">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Evita información personal obvia
                    </p>
                    <p class="small text-muted mb-0">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        No reutilices contraseñas de otros sistemas
                    </p>
                </div>
            </div>
            
        </div>
    </div>
    
</div>

<script>
// Toggle mostrar/ocultar contraseña
document.getElementById('toggle-password').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        passwordInput.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

document.getElementById('toggle-confirm').addEventListener('click', function() {
    const passwordInput = document.getElementById('password_confirm');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        passwordInput.type = 'password';
        icon.className = 'bi bi-eye';
    }
});

// Indicador de fortaleza de contraseña
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    
    let strength = 0;
    let text = '';
    let color = '';
    
    if (password.length >= 6) strength++;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    switch(strength) {
        case 0:
        case 1:
            text = 'Muy débil';
            color = 'bg-danger';
            break;
        case 2:
            text = 'Débil';
            color = 'bg-warning';
            break;
        case 3:
            text = 'Aceptable';
            color = 'bg-info';
            break;
        case 4:
            text = 'Fuerte';
            color = 'bg-primary';
            break;
        case 5:
            text = 'Muy fuerte';
            color = 'bg-success';
            break;
    }
    
    strengthBar.style.width = (strength * 20) + '%';
    strengthBar.className = 'progress-bar ' + color;
    strengthText.textContent = text;
});

// Validación del formulario
document.getElementById('form-password').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    if (!password) {
        e.preventDefault();
        alert('Debe ingresar la nueva contraseña');
        document.getElementById('password').focus();
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 6 caracteres');
        document.getElementById('password').focus();
        return false;
    }
    
    if (password !== passwordConfirm) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
        document.getElementById('password_confirm').focus();
        return false;
    }
    
    // Confirmación final
    if (!confirm('¿Está seguro que desea cambiar la contraseña de este usuario?')) {
        e.preventDefault();
        return false;
    }
});
</script>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>