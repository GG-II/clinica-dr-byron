<?php
/**
 * PÁGINA DE LOGIN
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Pantalla de inicio de sesión del sistema
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

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    redireccionar('dashboard.php');
}

// Variables para el formulario
$email = '';
$error = '';
$mensaje_info = '';

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validar campos
    if (empty($email) || empty($password)) {
        $error = 'Por favor ingrese su correo y contraseña.';
    } else {
        // Validar credenciales
        $usuario = validar_credenciales($pdo, $email, $password);
        
        if ($usuario) {
            // Login exitoso
            iniciar_sesion_usuario($usuario, $pdo);
            
            // Redirigir al dashboard o a la página que intentaba acceder
            $redirect = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
            unset($_SESSION['redirect_after_login']);
            redireccionar($redirect);
        } else {
            $error = 'Correo electrónico o contraseña incorrectos.';
            log_mensaje("Intento de login fallido: {$email}", 'warning');
        }
    }
}

// Verificar si hay mensaje de sesión (ej: sesión expirada)
if (isset($_SESSION['mensaje_advertencia'])) {
    $mensaje_info = $_SESSION['mensaje_advertencia'];
    unset($_SESSION['mensaje_advertencia']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión Clínica - Clínica Médica de la Mujer">
    <meta name="author" content="GG-Systems">
    <title>Iniciar Sesión - <?= APP_NAME ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #0077B6 0%, #00B4D8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
        }
        
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 40px 35px;
            text-align: center;
        }
        
        .logo-container {
            margin-bottom: 25px;
        }
        
        .logo {
            width: 100px;
            height: 100px;
            background: #0077B6;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 119, 182, 0.3);
        }
        
        .logo i {
            font-size: 50px;
            color: white;
        }
        
        .login-title {
            font-size: 24px;
            font-weight: 600;
            color: #0077B6;
            margin-bottom: 8px;
        }
        
        .login-subtitle {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .form-label {
            text-align: left;
            font-weight: 500;
            font-size: 14px;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            height: 48px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #0077B6;
            box-shadow: 0 0 0 3px rgba(0, 119, 182, 0.1);
        }
        
        .password-wrapper {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 8px;
            font-size: 18px;
            transition: color 0.2s;
        }
        
        .password-toggle:hover {
            color: #0077B6;
        }
        
        .btn-login {
            width: 100%;
            height: 48px;
            background: #0077B6;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 15px;
            margin-top: 25px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-login:hover {
            background: #005f94;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 119, 182, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .forgot-password {
            display: block;
            text-align: center;
            color: #0077B6;
            font-size: 14px;
            margin-top: 20px;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .forgot-password:hover {
            color: #005f94;
            text-decoration: underline;
        }
        
        .footer-text {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .alert {
            border-radius: 8px;
            font-size: 14px;
            padding: 12px 16px;
            margin-bottom: 20px;
            text-align: left;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
            color: #842029;
        }
        
        .alert-info {
            background-color: #cff4fc;
            border: 1px solid #b6effb;
            color: #055160;
        }
        
        .mb-3 {
            margin-bottom: 20px;
        }
        
        /* Animación de entrada */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Logo -->
            <div class="logo-container">
                <div class="logo">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <h1 class="login-title">Clínica Médica de la Mujer</h1>
                <p class="login-subtitle">Sistema de Gestión Clínica</p>
            </div>
            
            <!-- Mensajes -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($mensaje_info)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <?= e($mensaje_info) ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulario -->
            <form method="POST" action="" id="loginForm">
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        placeholder="admin@clinica.com"
                        value="<?= e($email) ?>"
                        required
                        autocomplete="email"
                        autofocus
                    >
                </div>
                
                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="password-wrapper">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Botón -->
                <button type="submit" class="btn-login">
                    Iniciar Sesión
                </button>
                
                <!-- Link recuperar contraseña -->
                <a href="#" class="forgot-password" onclick="alert('Contacte al administrador para recuperar su contraseña.'); return false;">
                    ¿Olvidaste tu contraseña?
                </a>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="footer-text">
            <?= APP_AUTHOR ?> © <?= date('Y') ?>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script>
        // Toggle mostrar/ocultar contraseña
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Cambiar icono
            if (type === 'password') {
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            } else {
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            }
        });
        
        // Validación básica del formulario
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Por favor complete todos los campos.');
                return false;
            }
            
            if (!email.includes('@')) {
                e.preventDefault();
                alert('Por favor ingrese un correo electrónico válido.');
                return false;
            }
        });
    </script>
</body>
</html>