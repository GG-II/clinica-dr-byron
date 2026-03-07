<?php
/**
 * USUARIOS - Nuevo Usuario
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Formulario para crear nuevo usuario del sistema
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
    $_SESSION['mensaje_error'] = 'No tienes permisos para crear usuarios';
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        // Validar datos obligatorios
        if (empty($_POST['nombre'])) {
            throw new Exception('Debe ingresar el nombre completo');
        }
        
        if (empty($_POST['email'])) {
            throw new Exception('Debe ingresar el email');
        }
        
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('El email no es válido');
        }
        
        if (empty($_POST['password'])) {
            throw new Exception('Debe ingresar una contraseña');
        }
        
        if (strlen($_POST['password']) < 6) {
            throw new Exception('La contraseña debe tener al menos 6 caracteres');
        }
        
        if ($_POST['password'] !== $_POST['password_confirm']) {
            throw new Exception('Las contraseñas no coinciden');
        }
        
        if (empty($_POST['rol'])) {
            throw new Exception('Debe seleccionar un rol');
        }
        
        // Preparar datos
        $datos = [
            'nombre' => trim($_POST['nombre']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'rol' => $_POST['rol'],
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];
        
        // Crear usuario
        $usuario_id = crear_usuario($pdo, $datos);
        
        if ($usuario_id) {
            $_SESSION['mensaje_exito'] = 'Usuario creado correctamente';
            header('Location: ' . BASE_URL . 'modules/usuarios/ver.php?id=' . $usuario_id);
            exit;
        } else {
            throw new Exception('Error al crear el usuario');
        }
        
    } catch (Exception $e) {
        $_SESSION['mensaje_error'] = $e->getMessage();
    }
}

// Variables para el header
$titulo = 'Nuevo Usuario';
$breadcrumb = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard.php'],
    ['titulo' => 'Usuarios', 'url' => BASE_URL . 'modules/usuarios/index.php'],
    ['titulo' => 'Nuevo', 'url' => '']
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
            <p class="text-muted">Usuarios / Nuevo</p>
        </div>
    </div>
    
    <!-- Mensajes flash -->
    <?php mostrar_mensajes(); ?>
    
    <!-- Formulario -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-plus"></i>
                        Datos del Usuario
                    </h5>
                </div>
                <div class="card-body">
                    
                    <form method="POST" id="form-usuario">
                        
                        <div class="row g-3">
                            
                            <!-- Nombre -->
                            <div class="col-md-12">
                                <label for="nombre" class="form-label text-muted small mb-1">
                                    NOMBRE COMPLETO <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre" id="nombre" 
                                       class="form-control" required maxlength="100"
                                       value="<?= isset($_POST['nombre']) ? e($_POST['nombre']) : '' ?>">
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-12">
                                <label for="email" class="form-label text-muted small mb-1">
                                    EMAIL <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" id="email" 
                                       class="form-control" required maxlength="100"
                                       value="<?= isset($_POST['email']) ? e($_POST['email']) : '' ?>">
                                <small class="text-muted">Este será el usuario para iniciar sesión</small>
                            </div>
                            
                            <!-- Contraseña -->
                            <div class="col-md-6">
                                <label for="password" class="form-label text-muted small mb-1">
                                    CONTRASEÑA <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password" id="password" 
                                       class="form-control" required minlength="6" maxlength="50">
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>
                            
                            <!-- Confirmar contraseña -->
                            <div class="col-md-6">
                                <label for="password_confirm" class="form-label text-muted small mb-1">
                                    CONFIRMAR CONTRASEÑA <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password_confirm" id="password_confirm" 
                                       class="form-control" required minlength="6" maxlength="50">
                            </div>
                            
                            <!-- Rol -->
                            <div class="col-md-6">
                                <label for="rol" class="form-label text-muted small mb-1">
                                    ROL <span class="text-danger">*</span>
                                </label>
                                <select name="rol" id="rol" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="admin" <?= isset($_POST['rol']) && $_POST['rol'] === 'admin' ? 'selected' : '' ?>>
                                        Administrador
                                    </option>
                                    <option value="medico" <?= isset($_POST['rol']) && $_POST['rol'] === 'medico' ? 'selected' : '' ?>>
                                        Médico
                                    </option>
                                    <option value="asistente" <?= isset($_POST['rol']) && $_POST['rol'] === 'asistente' ? 'selected' : '' ?>>
                                        Asistente
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Estado -->
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">ESTADO</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="activo" 
                                           id="activo" checked>
                                    <label class="form-check-label" for="activo">
                                        Usuario activo
                                    </label>
                                </div>
                                <small class="text-muted">Usuarios inactivos no pueden iniciar sesión</small>
                            </div>
                            
                        </div>
                        
                        <!-- Botones -->
                        <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                            <a href="<?= BASE_URL ?>modules/usuarios/index.php" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Crear Usuario
                            </button>
                        </div>
                        
                    </form>
                    
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            
            <!-- Información -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle"></i>
                        Información
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-muted small mb-2">ROLES DISPONIBLES:</h6>
                    
                    <div class="mb-3">
                        <span class="badge bg-danger me-2">Administrador</span>
                        <p class="small text-muted mb-0 mt-1">
                            Acceso completo al sistema. Gestiona usuarios, reportes y configuración.
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <span class="badge bg-primary me-2">Médico</span>
                        <p class="small text-muted mb-0 mt-1">
                            Gestiona pacientes, citas, consultas, recetas y ultrasonidos.
                        </p>
                    </div>
                    
                    <div class="mb-0">
                        <span class="badge bg-success me-2">Asistente</span>
                        <p class="small text-muted mb-0 mt-1">
                            Gestiona citas, pacientes básicos y facturación. Acceso limitado.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Seguridad -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-shield-check"></i>
                        Seguridad
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Las contraseñas se encriptan automáticamente
                    </p>
                    <p class="small text-muted mb-2">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Se requiere mínimo 6 caracteres
                    </p>
                    <p class="small text-muted mb-0">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        El email debe ser único en el sistema
                    </p>
                </div>
            </div>
            
        </div>
        
    </div>
    
</div>

<script>
// Validación del formulario
document.getElementById('form-usuario').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    const rol = document.getElementById('rol').value;
    
    if (!nombre) {
        e.preventDefault();
        alert('Debe ingresar el nombre completo');
        document.getElementById('nombre').focus();
        return false;
    }
    
    if (!email) {
        e.preventDefault();
        alert('Debe ingresar el email');
        document.getElementById('email').focus();
        return false;
    }
    
    // Validar formato de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('El email no tiene un formato válido');
        document.getElementById('email').focus();
        return false;
    }
    
    if (!password) {
        e.preventDefault();
        alert('Debe ingresar una contraseña');
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
    
    if (!rol) {
        e.preventDefault();
        alert('Debe seleccionar un rol');
        document.getElementById('rol').focus();
        return false;
    }
});

// Mostrar/ocultar contraseñas
document.querySelectorAll('input[type="password"]').forEach(input => {
    const parent = input.parentElement;
    const toggleBtn = document.createElement('button');
    toggleBtn.type = 'button';
    toggleBtn.className = 'btn btn-outline-secondary btn-sm position-absolute end-0 top-50 translate-middle-y me-2';
    toggleBtn.innerHTML = '<i class="bi bi-eye"></i>';
    toggleBtn.style.zIndex = '10';
    
    input.style.paddingRight = '3rem';
    parent.style.position = 'relative';
    parent.appendChild(toggleBtn);
    
    toggleBtn.addEventListener('click', function() {
        if (input.type === 'password') {
            input.type = 'text';
            this.innerHTML = '<i class="bi bi-eye-slash"></i>';
        } else {
            input.type = 'password';
            this.innerHTML = '<i class="bi bi-eye"></i>';
        }
    });
});
</script>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>