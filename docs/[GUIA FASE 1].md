# 🏗️ METODOLOGÍA PROFESIONAL DE DESARROLLO - FASE 1
## Arquitectura Base y Sistema de Autenticación

**Versión:** 1.0  
**Fecha de creación:** Enero 2026  
**Autor:** Basado en proyectos reales (Joyería Torre Fuerte, Clínica)  
**Stack:** PHP 8+ | MySQL/MariaDB | Vanilla JavaScript | Bootstrap 5

---

## 📋 ÍNDICE COMPLETO

### PARTE 1: FUNDAMENTOS
1. [Introducción a la Fase 1](#1-introducción-a-la-fase-1)
2. [Arquitectura General](#2-arquitectura-general)
3. [Planificación de la Fase](#3-planificación-de-la-fase)

### PARTE 2: IMPLEMENTACIÓN DÍA A DÍA
4. [Día 1: Configuración y Estructura](#4-día-1-configuración-y-estructura)
5. [Día 2: Base de Datos y Funciones Helper](#5-día-2-base-de-datos-y-funciones-helper)
6. [Día 3: Sistema de Autenticación](#6-día-3-sistema-de-autenticación)
7. [Día 4: Roles, Permisos y Refinamiento](#7-día-4-roles-permisos-y-refinamiento)

### PARTE 3: COMPONENTES TÉCNICOS
8. [Sistema de Configuración](#8-sistema-de-configuración)
9. [Conexión a Base de Datos](#9-conexión-a-base-de-datos)
10. [Funciones Helper](#10-funciones-helper)
11. [Sistema de Autenticación](#11-sistema-de-autenticación)
12. [Sistema de Roles y Permisos](#12-sistema-de-roles-y-permisos)
13. [Middleware de Protección](#13-middleware-de-protección)
14. [Datos de Prueba](#14-datos-de-prueba)

### PARTE 4: ESTÁNDARES Y CALIDAD
15. [Seguridad](#15-seguridad)
16. [Manejo de Errores](#16-manejo-de-errores)
17. [Testing Básico](#17-testing-básico)
18. [Documentación](#18-documentación)

### PARTE 5: CIERRE
19. [Checklist de Completitud](#19-checklist-de-completitud)
20. [Errores Comunes](#20-errores-comunes)
21. [Preparación para Fase 2](#21-preparación-para-fase-2)

---

## 1. INTRODUCCIÓN A LA FASE 1

### 1.1 ¿Qué es la Fase 1?

La **Fase 1** es la etapa donde transformamos el diseño de la Fase 0 en **código funcional**. Construimos la arquitectura base del sistema: configuración, conexión a base de datos, autenticación, y todos los componentes fundamentales que se reutilizarán en el resto del proyecto.

**Metáfora:** Si la Fase 0 es el plano arquitectónico de un edificio, la Fase 1 son los cimientos, estructura y servicios básicos (electricidad, agua, alcantarillado). Sin una Fase 1 sólida, todo el edificio será inestable.

---

### 1.2 Objetivos de la Fase 1

Al completar esta fase, tendremos:

✅ **Arquitectura backend sólida y escalable**
- Patrón de diseño definido
- Estructura de carpetas profesional
- Código base reutilizable

✅ **Sistema de autenticación completo**
- Login/Logout funcional
- Sesiones seguras
- Protección contra ataques comunes

✅ **Sistema de autorización robusto**
- Roles de usuario implementados
- Permisos granulares
- Middleware de protección de rutas

✅ **Fundamentos técnicos**
- Configuración centralizada
- Conexión a BD con manejo de errores
- Funciones helper útiles
- Sistema de logging/auditoría

✅ **Datos de prueba realistas**
- Usuarios de diferentes roles
- Información para testing

✅ **Base sólida para Fase 2**
- Todo listo para desarrollar módulos específicos
- Patrones establecidos
- Seguridad implementada desde el inicio

---

### 1.3 ¿Por qué es importante?

#### 🔐 Seguridad desde el Inicio
- Implementar seguridad DESPUÉS es 10x más difícil
- Los ataques explotan vulnerabilidades en la arquitectura base
- Prevenir > Remediar

#### 🏗️ Arquitectura Escalable
- Decisiones de Fase 1 afectan TODO el proyecto
- Difícil cambiar estructura después
- Buenos fundamentos = desarrollo más rápido después

#### ♻️ Reutilización de Código
- Funciones helper se usan en TODOS los módulos
- Sistema de auth se usa en TODAS las páginas
- DRY: Don't Repeat Yourself

#### 🐛 Menos Bugs a Futuro
- Manejo centralizado de errores
- Validación desde el inicio
- Menos código duplicado = menos bugs

---

### 1.4 Duración Esperada

**Tiempo total:** 3-5 días

| Día | Actividades | Horas | % Completitud |
|-----|-------------|-------|---------------|
| **Día 1** | Configuración, estructura, archivos base | 4-6h | 25% |
| **Día 2** | BD, funciones helper, datos de prueba | 4-6h | 50% |
| **Día 3** | Autenticación, login, dashboard | 6-8h | 75% |
| **Día 4** | Roles, permisos, refinamiento, testing | 4-6h | 100% |

**Variables que afectan la duración:**
- Experiencia del desarrollador
- Complejidad del sistema de roles
- Cantidad de datos de prueba necesarios
- Nivel de testing requerido

---

### 1.5 Prerequisitos (de Fase 0)

Antes de iniciar Fase 1, DEBES tener:

- [x] Base de datos diseñada y documentada (schema.sql)
- [x] Tablas de usuarios y roles definidas
- [x] Requerimientos funcionales claros
- [x] Mockups o wireframes de login/dashboard
- [x] Paleta de colores definida
- [x] Stack tecnológico decidido

**Si no tienes esto, REGRESA A FASE 0.**

---

## 2. ARQUITECTURA GENERAL

### 2.1 Patrón de Diseño

Para proyectos medianos sin framework, usamos **MVC modificado**:

```
┌─────────────────────────────────────────┐
│              USUARIO (Navegador)        │
└────────────────┬────────────────────────┘
                 │
                 ↓
┌────────────────────────────────────────┐
│         VISTA (login.php, etc.)        │ ← HTML + PHP mínimo
└────────────────┬───────────────────────┘
                 │
                 ↓
┌────────────────────────────────────────┐
│    CONTROLADOR (lógica en misma       │ ← Validación, procesar
│     vista o archivos separados)        │    formularios
└────────────────┬───────────────────────┘
                 │
                 ↓
┌────────────────────────────────────────┐
│      MODELO (models/Usuario.php)       │ ← Lógica de negocio
└────────────────┬───────────────────────┘
                 │
                 ↓
┌────────────────────────────────────────┐
│    CAPA DE DATOS (includes/db.php)     │ ← Conexión BD
└────────────────┬───────────────────────┘
                 │
                 ↓
┌────────────────────────────────────────┐
│         BASE DE DATOS (MySQL)          │
└────────────────────────────────────────┘
```

**Características:**
- **Simple:** No sobre-ingeniería
- **Mantenible:** Separación clara de responsabilidades
- **Escalable:** Fácil agregar módulos
- **Sin framework:** Fácil deployment

---

### 2.2 Estructura de Carpetas Definitiva

```
proyecto/
│
├── 📁 assets/                    # Recursos estáticos
│   ├── css/
│   │   └── estilos.css
│   ├── js/
│   │   └── main.js
│   └── img/
│       └── logo.png
│
├── 📁 includes/                  # Archivos PHP reutilizables
│   ├── config.php               # ⚙️ Configuración
│   ├── db.php                   # 🗄️ Conexión BD
│   ├── funciones.php            # 🔧 Helpers generales
│   ├── auth.php                 # 🔐 Autenticación
│   └── middleware.php           # 🛡️ Protección de rutas
│
├── 📁 models/                    # Modelos (clases)
│   ├── Model.php                # Clase base
│   └── Usuario.php              # Ejemplo
│
├── 📁 modules/                   # Módulos funcionales
│   ├── inventario/
│   ├── ventas/
│   └── clientes/
│
├── 📁 api/                       # Endpoints API (Fase 3+)
│   └── usuarios/
│
├── 📁 database/                  # Scripts de BD
│   ├── schema.sql               # (de Fase 0)
│   └── seed.sql                 # Datos de prueba
│
├── 📁 uploads/                   # Archivos subidos
│   └── .gitkeep
│
├── 📁 logs/                      # Logs del sistema
│   └── .gitkeep
│
├── 📁 tests/                     # Tests
│   └── test-auth.php
│
├── 📁 docs/                      # Documentación
│   ├── FASE-0-COMPLETADA.md
│   └── FASE-1-COMPLETADA.md
│
├── 📄 .gitignore                # Archivos a ignorar
├── 📄 .htaccess                 # Configuración Apache
├── 📄 index.php                 # Punto de entrada
├── 📄 login.php                 # Vista de login
├── 📄 logout.php                # Cerrar sesión
└── 📄 dashboard.php             # Panel principal
```

**Principios de organización:**
- **Separación por responsabilidad:** Cada carpeta tiene un propósito claro
- **Escalabilidad:** Fácil agregar nuevos módulos
- **Seguridad:** includes/ no debe ser accesible directamente
- **Mantenibilidad:** Ubicación predecible de archivos

---

### 2.3 Flujo de Datos

**Flujo de Login:**

```
1. Usuario visita login.php
2. login.php incluye auth.php
3. Usuario envía formulario (POST)
4. auth.php valida credenciales
   ├─ funciones.php → limpiar inputs
   ├─ db.php → consultar usuario
   └─ funciones.php → verificar password
5. Si válido: crear sesión
6. Redirigir a dashboard.php
7. dashboard.php verifica sesión con middleware
8. Si sesión válida: mostrar contenido
   Si no: redirigir a login.php
```

---

### 2.4 Convenciones de Nombres

#### Archivos PHP:
```
- Vistas: nombre-descriptivo.php (kebab-case)
  Ejemplos: login.php, nuevo-producto.php
  
- Modelos: NombreModelo.php (PascalCase)
  Ejemplos: Usuario.php, Producto.php
  
- Includes: nombre.php (lowercase)
  Ejemplos: auth.php, db.php
```

#### Variables PHP:
```php
$nombre_variable = 'valor';         // snake_case
$usuario_nombre = 'Juan';
$total_productos = 100;
```

#### Funciones:
```php
function nombre_funcion() {}        // snake_case
function validar_email() {}
function obtener_productos() {}
```

#### Clases:
```php
class NombreClase {}                // PascalCase
class Usuario {}
class ProductoModel {}
```

#### Constantes:
```php
define('NOMBRE_CONSTANTE', 'valor'); // SCREAMING_SNAKE_CASE
define('BASE_URL', 'http://localhost/');
define('DB_HOST', 'localhost');
```

#### CSS:
```css
.nombre-clase {}                    /* kebab-case */
.btn-primary {}
#nombre-id {}
```

#### JavaScript:
```javascript
let nombreVariable = 'valor';       // camelCase
function nombreFuncion() {}
```

---

## 3. PLANIFICACIÓN DE LA FASE

### 3.1 Cronograma Detallado

#### **DÍA 1: Configuración y Estructura Base** (4-6 horas)

**Mañana (2-3 horas):**
- [ ] Crear estructura de carpetas completa
- [ ] Configurar .gitignore
- [ ] Crear config.php
- [ ] Crear includes/db.php básico
- [ ] Verificar conexión a BD

**Tarde (2-3 horas):**
- [ ] Crear includes/funciones.php
- [ ] Implementar funciones de validación básicas
- [ ] Implementar funciones de formato
- [ ] Crear test-conexion.php
- [ ] Verificar que todo funciona

**Entregables:**
- ✅ Estructura de carpetas completa
- ✅ Conexión a BD funcional
- ✅ 10+ funciones helper implementadas
- ✅ Test de conexión exitoso

---

#### **DÍA 2: Base de Datos y Datos de Prueba** (4-6 horas)

**Mañana (2-3 horas):**
- [ ] Revisar schema.sql de Fase 0
- [ ] Ejecutar schema.sql en BD
- [ ] Crear database/seed.sql
- [ ] Insertar datos de usuarios de prueba
- [ ] Insertar datos complementarios (categorías, etc.)

**Tarde (2-3 horas):**
- [ ] Mejorar includes/db.php con funciones helper
- [ ] Implementar db_query(), db_execute(), etc.
- [ ] Agregar manejo de errores robusto
- [ ] Probar queries con datos de prueba
- [ ] Documentar funciones de BD

**Entregables:**
- ✅ Base de datos poblada con datos de prueba
- ✅ Funciones helper de BD implementadas
- ✅ Manejo de errores en queries
- ✅ Datos realistas para testing

---

#### **DÍA 3: Sistema de Autenticación** (6-8 horas)

**Mañana (3-4 horas):**
- [ ] Crear includes/auth.php
- [ ] Implementar intentar_login()
- [ ] Implementar iniciar_sesion()
- [ ] Implementar cerrar_sesion()
- [ ] Implementar verificar_sesion()

**Tarde (3-4 horas):**
- [ ] Crear login.php (vista)
- [ ] Crear logout.php
- [ ] Crear dashboard.php básico
- [ ] Implementar protección de rutas
- [ ] Probar flujo completo de login/logout

**Entregables:**
- ✅ Sistema de login funcional
- ✅ Sesiones seguras implementadas
- ✅ Dashboard básico protegido
- ✅ Flujo de autenticación completo

---

#### **DÍA 4: Roles, Permisos y Refinamiento** (4-6 horas)

**Mañana (2-3 horas):**
- [ ] Implementar sistema de roles
- [ ] Crear función tiene_rol()
- [ ] Crear función tiene_permiso()
- [ ] Implementar middleware de autorización
- [ ] Dashboard dinámico según rol

**Tarde (2-3 horas):**
- [ ] Crear tests/test-auth.php
- [ ] Probar todos los roles
- [ ] Refinar diseño visual
- [ ] Documentar FASE-1-COMPLETADA.md
- [ ] Commit final

**Entregables:**
- ✅ Sistema de roles funcional
- ✅ Permisos granulares implementados
- ✅ Tests básicos pasando
- ✅ Documentación completa
- ✅ **Fase 1 100% completa**

---

### 3.2 Herramientas Necesarias

**Obligatorias:**
- [x] XAMPP/WAMP/MAMP (Apache + MySQL/MariaDB)
- [x] PHP 8.0+ (recomendado 8.2)
- [x] Editor de código (VS Code recomendado)
- [x] Navegador moderno (Chrome/Firefox)
- [x] Git (control de versiones)

**Opcionales pero recomendadas:**
- [ ] HeidiSQL / phpMyAdmin (gestión de BD)
- [ ] Postman (testing de APIs en Fase 3)
- [ ] Chrome DevTools (debugging)
- [ ] Extension PHP Intelephense (VS Code)

---

## 4. DÍA 1: CONFIGURACIÓN Y ESTRUCTURA

### 4.1 Crear Estructura de Carpetas

**Objetivo:** Tener la estructura completa del proyecto lista.

#### Método Manual:

Crear todas estas carpetas en la raíz del proyecto:

```
assets/css/
assets/js/
assets/img/
includes/
models/
modules/inventario/
modules/ventas/
modules/clientes/
api/usuarios/
database/
uploads/
logs/
tests/
docs/
```

#### Método con Script (Windows):

Crear archivo `setup.bat`:

```batch
@echo off
mkdir assets\css assets\js assets\img
mkdir includes models modules\inventario modules\ventas modules\clientes
mkdir api\usuarios database uploads logs tests docs
echo. > uploads\.gitkeep
echo. > logs\.gitkeep
echo Estructura creada exitosamente!
```

Ejecutar: `setup.bat`

#### Método con Script (Linux/Mac):

```bash
#!/bin/bash
mkdir -p assets/{css,js,img}
mkdir -p includes models modules/{inventario,ventas,clientes}
mkdir -p api/usuarios database uploads logs tests docs
touch uploads/.gitkeep logs/.gitkeep
echo "Estructura creada exitosamente!"
```

---

### 4.2 Crear .gitignore

**Archivo:** `.gitignore` (raíz del proyecto)

**Propósito:** Evitar que archivos sensibles o innecesarios se suban a Git.

```gitignore
# ================================================
# .gitignore para Proyectos PHP
# ================================================

# === ARCHIVOS DE CONFIGURACIÓN SENSIBLES ===
config.php
includes/config.php
.env

# === ARCHIVOS SUBIDOS POR USUARIOS ===
uploads/*
!uploads/.gitkeep

# === LOGS ===
logs/*.log
logs/*.txt
!logs/.gitkeep

# === BACKUPS DE BASE DE DATOS ===
*.sql.backup
database/*.backup
database/backups/

# === SISTEMA OPERATIVO ===
# Windows
Thumbs.db
ehthumbs.db
Desktop.ini

# macOS
.DS_Store
.AppleDouble
.LSOverride

# Linux
*~

# === EDITORES E IDEs ===
# VS Code
.vscode/
*.code-workspace

# PhpStorm
.idea/
*.iml

# Sublime Text
*.sublime-project
*.sublime-workspace

# === DEPENDENCIAS ===
vendor/
node_modules/
composer.lock
package-lock.json

# === TEMPORALES ===
*.tmp
*.temp
*.cache
*.bak
*.swp
*.swo
*~

# === OTROS ===
error_log
php_errorlog
```

**⚠️ IMPORTANTE:** Crear este archivo ANTES del primer commit.

---

### 4.3 Crear config.php (Versión Profesional)

**Archivo:** `config.php` (raíz del proyecto)

**Propósito:** Configuración centralizada del sistema.

```php
<?php
/**
 * ================================================
 * ARCHIVO DE CONFIGURACIÓN PRINCIPAL
 * ================================================
 * 
 * Este archivo contiene toda la configuración del sistema.
 * 
 * SEGURIDAD: Este archivo NO debe estar en el repositorio Git.
 * Usar config.example.php como plantilla.
 * 
 * @package   Sistema
 * @author    Tu Nombre
 * @version   1.0
 */

// ================================================
// MODO DE DESARROLLO
// ================================================
// true = Mostrar errores | false = Ocultar errores
define('DEVELOPMENT_MODE', true);

// ================================================
// CONFIGURACIÓN DE BASE DE DATOS
// ================================================
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');              // 3306 MySQL, 3307 si tienes conflicto
define('DB_NAME', 'nombre_base_datos');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ================================================
// CONFIGURACIÓN DE RUTAS
// ================================================
// URL base del proyecto (ajustar según tu configuración)
define('BASE_URL', 'http://localhost/nombre-proyecto/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('UPLOADS_URL', BASE_URL . 'uploads/');

// Rutas del servidor
define('ROOT_PATH', __DIR__);
define('INCLUDES_PATH', ROOT_PATH . '/includes/');
define('MODELS_PATH', ROOT_PATH . '/models/');
define('UPLOADS_PATH', ROOT_PATH . '/uploads/');
define('LOGS_PATH', ROOT_PATH . '/logs/');

// ================================================
// CONFIGURACIÓN DE SESIONES
// ================================================
define('SESSION_NAME', 'app_session');
define('SESSION_LIFETIME', 3600);      // 1 hora en segundos
define('SESSION_TIMEOUT', 3600);       // Timeout de inactividad

// ================================================
// CONFIGURACIÓN DE SEGURIDAD
// ================================================
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900);     // 15 minutos

// ================================================
// CONFIGURACIÓN DE ZONA HORARIA
// ================================================
date_default_timezone_set('America/Guatemala'); // Ajustar según ubicación

// ================================================
// CONFIGURACIÓN DE ERRORES
// ================================================
if (DEVELOPMENT_MODE) {
    // Modo desarrollo: Mostrar todos los errores
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    // Modo producción: Ocultar errores, solo loguear
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOGS_PATH . 'php-errors.log');
}

// ================================================
// CONFIGURACIÓN DE UPLOADS
// ================================================
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);  // 5 MB en bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// ================================================
// CONSTANTES DEL SISTEMA
// ================================================
define('SISTEMA_NOMBRE', 'Nombre del Sistema');
define('SISTEMA_VERSION', '1.0.0');
define('ITEMS_POR_PAGINA', 20);

// ================================================
// CONFIGURACIÓN DE EMAIL (para futuro)
// ================================================
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu-email@gmail.com');
define('SMTP_PASS', 'tu-password');
define('SMTP_FROM', 'noreply@tusitio.com');
define('SMTP_FROM_NAME', SISTEMA_NOMBRE);

// ================================================
// INICIAR SESIÓN
// ================================================
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => '',
        'secure' => false,  // true si usas HTTPS
        'httponly' => true,  // Previene acceso JavaScript
        'samesite' => 'Lax'  // Protección CSRF
    ]);
    session_start();
}

// ================================================
// AUTO-REGENERAR SESSION ID (seguridad)
// ================================================
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    // Regenerar cada 30 minutos
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
?>
```

---

### 4.4 Crear config.example.php

**Archivo:** `config.example.php` (raíz del proyecto)

**Propósito:** Plantilla de configuración para Git (sin datos sensibles).

```php
<?php
/**
 * ================================================
 * PLANTILLA DE CONFIGURACIÓN
 * ================================================
 * 
 * Copiar este archivo como config.php y ajustar valores.
 * 
 * NO editar este archivo directamente.
 */

define('DEVELOPMENT_MODE', true);

// Base de datos
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'nombre_base_datos');
define('DB_USER', 'root');
define('DB_PASS', '');

// Rutas (ajustar según tu configuración)
define('BASE_URL', 'http://localhost/tu-proyecto/');
define('ASSETS_URL', BASE_URL . 'assets/');

// Sesiones
define('SESSION_NAME', 'app_session');
define('SESSION_TIMEOUT', 3600);

// Seguridad
define('PASSWORD_MIN_LENGTH', 6);

// Zona horaria
date_default_timezone_set('America/Guatemala');

// Sistema
define('SISTEMA_NOMBRE', 'Tu Sistema');
define('SISTEMA_VERSION', '1.0.0');

// Errores
if (DEVELOPMENT_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
```

---

### 4.5 Crear includes/db.php (Conexión Básica)

**Archivo:** `includes/db.php`

**Propósito:** Conexión a base de datos con PDO.

```php
<?php
/**
 * ================================================
 * CONEXIÓN A BASE DE DATOS
 * ================================================
 * 
 * Maneja la conexión a MySQL usando PDO.
 * Incluye funciones helper para queries.
 * 
 * @package   Sistema
 * @version   1.0
 */

// Incluir configuración si no está cargada
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/../config.php';
}

// ================================================
// CONEXIÓN PDO
// ================================================
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    // Manejo de errores según entorno
    if (DEVELOPMENT_MODE) {
        die("Error de conexión: " . $e->getMessage());
    } else {
        // En producción, loguear y mostrar mensaje genérico
        error_log("DB Connection Error: " . $e->getMessage());
        die("Error al conectar con la base de datos. Por favor, contacte al administrador.");
    }
}

// ================================================
// FUNCIONES HELPER PARA QUERIES
// ================================================

/**
 * Ejecuta un SELECT y retorna múltiples resultados
 * 
 * @param string $sql Query SQL con placeholders (?)
 * @param array $params Parámetros para prepared statement
 * @return array Resultados de la consulta
 */
function db_query($sql, $params = []) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        if (DEVELOPMENT_MODE) {
            die("Error en query: " . $e->getMessage() . "<br>SQL: " . $sql);
        } else {
            error_log("Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            return [];
        }
    }
}

/**
 * Ejecuta un SELECT y retorna un solo resultado
 * 
 * @param string $sql Query SQL con placeholders (?)
 * @param array $params Parámetros para prepared statement
 * @return array|false Un registro o false si no existe
 */
function db_query_one($sql, $params = []) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (PDOException $e) {
        if (DEVELOPMENT_MODE) {
            die("Error en query: " . $e->getMessage());
        } else {
            error_log("Query Error: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Ejecuta INSERT, UPDATE o DELETE
 * 
 * @param string $sql Query SQL con placeholders (?)
 * @param array $params Parámetros para prepared statement
 * @return int|false ID del último registro insertado o false en error
 */
function db_execute($sql, $params = []) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Si es INSERT, retornar ID insertado
        if (stripos(trim($sql), 'INSERT') === 0) {
            return $pdo->lastInsertId();
        }
        
        // Para UPDATE/DELETE, retornar filas afectadas
        return $stmt->rowCount();
        
    } catch (PDOException $e) {
        if (DEVELOPMENT_MODE) {
            die("Error al ejecutar: " . $e->getMessage() . "<br>SQL: " . $sql);
        } else {
            error_log("Execute Error: " . $e->getMessage() . " | SQL: " . $sql);
            return false;
        }
    }
}

/**
 * Cuenta registros en una tabla con condición opcional
 * 
 * @param string $table Nombre de la tabla
 * @param string $where Condición WHERE (sin la palabra WHERE)
 * @param array $params Parámetros para la condición
 * @return int Número de registros
 */
function db_count($table, $where = '1=1', $params = []) {
    global $pdo;
    
    try {
        $sql = "SELECT COUNT(*) as total FROM $table WHERE $where";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int)$result['total'];
    } catch (PDOException $e) {
        if (DEVELOPMENT_MODE) {
            die("Error al contar: " . $e->getMessage());
        } else {
            error_log("Count Error: " . $e->getMessage());
            return 0;
        }
    }
}

/**
 * Verifica si existe al menos un registro que cumpla la condición
 * 
 * @param string $table Nombre de la tabla
 * @param string $where Condición WHERE (sin la palabra WHERE)
 * @param array $params Parámetros para la condición
 * @return bool true si existe, false si no
 */
function db_exists($table, $where, $params = []) {
    return db_count($table, $where, $params) > 0;
}

?>
```

---

### 4.6 Verificar Conexión

**Archivo:** `test-conexion.php` (raíz del proyecto)

**Propósito:** Verificar que la conexión a BD funciona.

```php
<?php
require_once 'config.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test de Conexión</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #4CAF50; color: white; }
    </style>
</head>
<body>
    <h1>Test de Conexión a Base de Datos</h1>
    
    <?php
    try {
        // Verificar conexión
        echo "<p class='success'>✓ Conexión exitosa a la base de datos</p>";
        echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
        echo "<p><strong>Puerto:</strong> " . DB_PORT . "</p>";
        echo "<p><strong>Base de datos:</strong> " . DB_NAME . "</p>";
        
        // Listar tablas
        $sql = "SHOW TABLES";
        $tablas = db_query($sql);
        
        echo "<h2>Tablas en la base de datos (" . count($tablas) . "):</h2>";
        echo "<table>";
        echo "<tr><th>#</th><th>Nombre de Tabla</th><th>Registros</th></tr>";
        
        foreach ($tablas as $index => $tabla) {
            $nombre_tabla = array_values($tabla)[0];
            $total = db_count($nombre_tabla);
            echo "<tr>";
            echo "<td>" . ($index + 1) . "</td>";
            echo "<td>" . $nombre_tabla . "</td>";
            echo "<td>" . $total . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Información del sistema
        echo "<h2>Información del Sistema:</h2>";
        echo "<p><strong>PHP:</strong> " . PHP_VERSION . "</p>";
        echo "<p><strong>Modo:</strong> " . (DEVELOPMENT_MODE ? 'Desarrollo' : 'Producción') . "</p>";
        echo "<p><strong>Zona horaria:</strong> " . date_default_timezone_get() . "</p>";
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    }
    ?>
</body>
</html>
```

**Ejecutar:** Ir a `http://localhost/tu-proyecto/test-conexion.php`

**Debe mostrar:**
- ✓ Conexión exitosa
- Lista de tablas
- Información del sistema

---

### 4.7 Checklist Día 1

Al finalizar el Día 1, debes tener:

- [ ] Estructura de carpetas completa creada
- [ ] `.gitignore` configurado
- [ ] `config.php` creado y configurado
- [ ] `config.example.php` creado
- [ ] `includes/db.php` implementado
- [ ] `test-conexion.php` funcional
- [ ] Conexión a BD verificada y exitosa
- [ ] Git inicializado con primer commit

**Comando Git:**
```bash
git init
git add .
git commit -m "Fase 1 Día 1: Estructura base y configuración"
```

---

## 5. DÍA 2: BASE DE DATOS Y FUNCIONES HELPER

### 5.1 Ejecutar schema.sql

**Prerequisito:** Tener `database/schema.sql` de la Fase 0.

**Métodos para ejecutar:**

#### Método 1: phpMyAdmin
1. Abrir phpMyAdmin (`http://localhost/phpmyadmin`)
2. Crear base de datos (si no existe)
3. Seleccionar la base de datos
4. Pestaña "SQL"
5. Copiar contenido de `schema.sql`
6. Ejecutar

#### Método 2: MySQL Command Line
```bash
mysql -u root -p nombre_base_datos < database/schema.sql
```

#### Método 3: HeidiSQL
1. Abrir HeidiSQL
2. Conectar al servidor
3. Archivo → Ejecutar archivo SQL
4. Seleccionar `schema.sql`

**Verificar:** Todas las tablas creadas correctamente.

---

### 5.2 Crear includes/funciones.php

**Archivo:** `includes/funciones.php`

**Propósito:** Funciones helper reutilizables en todo el proyecto.

```php
<?php
/**
 * ================================================
 * FUNCIONES HELPER GENERALES
 * ================================================
 * 
 * Colección de funciones útiles para todo el sistema.
 * 
 * Categorías:
 * - Sanitización y validación
 * - Formato de datos
 * - Seguridad
 * - Navegación
 * - Mensajes flash
 * - Utilidades generales
 * 
 * @package   Sistema
 * @version   1.0
 */

// ================================================
// SANITIZACIÓN Y VALIDACIÓN
// ================================================

/**
 * Limpia y sanitiza texto de entrada
 * Previene XSS removiendo HTML/scripts
 * 
 * @param string $data Texto a limpiar
 * @return string Texto sanitizado
 */
function limpiar_texto($data) {
    if (is_null($data)) {
        return '';
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

/**
 * Valida formato de email
 * 
 * @param string $email Email a validar
 * @return bool true si es válido, false si no
 */
function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida número de teléfono (8 dígitos - Guatemala)
 * Ajustar según país
 * 
 * @param string $telefono Teléfono a validar
 * @return bool true si es válido, false si no
 */
function validar_telefono($telefono) {
    // Remover espacios, guiones, paréntesis
    $telefono = preg_replace('/[\s\-\(\)]/', '', $telefono);
    
    // Verificar que sean 8 dígitos
    return preg_match('/^[0-9]{8}$/', $telefono);
}

/**
 * Valida NIT guatemalteco (formato: 12345678-9)
 * Ajustar según país
 * 
 * @param string $nit NIT a validar
 * @return bool true si es válido, false si no
 */
function validar_nit($nit) {
    // Formato: 12345678-9 o 12345678
    return preg_match('/^[0-9]{7,8}(-?[0-9kK])?$/', $nit);
}

/**
 * Valida que un número esté en un rango
 * 
 * @param mixed $numero Número a validar
 * @param int $min Valor mínimo
 * @param int $max Valor máximo
 * @return bool true si está en rango, false si no
 */
function validar_rango($numero, $min, $max) {
    return is_numeric($numero) && $numero >= $min && $numero <= $max;
}

/**
 * Valida que un string no esté vacío
 * 
 * @param string $valor Valor a validar
 * @return bool true si no está vacío, false si sí
 */
function validar_no_vacio($valor) {
    return !empty(trim($valor));
}

// ================================================
// SEGURIDAD - PASSWORDS
// ================================================

/**
 * Hashea un password usando bcrypt
 * 
 * @param string $password Password en texto plano
 * @return string Password hasheado
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verifica que un password coincida con su hash
 * 
 * @param string $password Password en texto plano
 * @param string $hash Hash almacenado en BD
 * @return bool true si coincide, false si no
 */
function verificar_password($password, $hash) {
    return password_verify($password, $hash);
}

// ================================================
// FORMATO DE DATOS
// ================================================

/**
 * Formatea número como dinero
 * 
 * @param float $numero Número a formatear
 * @param string $simbolo Símbolo de moneda (default: Q)
 * @return string Número formateado (Ej: Q 1,234.56)
 */
function formato_dinero($numero, $simbolo = 'Q') {
    return $simbolo . ' ' . number_format($numero, 2, '.', ',');
}

/**
 * Formatea fecha de MySQL a formato legible
 * 
 * @param string $fecha Fecha en formato MySQL (YYYY-MM-DD)
 * @param bool $incluir_hora Incluir hora en el formato
 * @return string Fecha formateada (dd/mm/yyyy)
 */
function formato_fecha($fecha, $incluir_hora = false) {
    if (empty($fecha) || $fecha == '0000-00-00' || $fecha == '0000-00-00 00:00:00') {
        return 'N/A';
    }
    
    $formato = 'd/m/Y';
    if ($incluir_hora) {
        $formato .= ' H:i';
    }
    
    try {
        $dt = new DateTime($fecha);
        return $dt->format($formato);
    } catch (Exception $e) {
        return 'Fecha inválida';
    }
}

/**
 * Convierte fecha de formato dd/mm/yyyy a YYYY-MM-DD (MySQL)
 * 
 * @param string $fecha Fecha en formato dd/mm/yyyy
 * @return string Fecha en formato MySQL
 */
function fecha_a_mysql($fecha) {
    if (empty($fecha)) {
        return null;
    }
    
    // Si ya está en formato MySQL, retornar tal cual
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        return $fecha;
    }
    
    // Convertir dd/mm/yyyy a YYYY-MM-DD
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fecha, $matches)) {
        return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
    }
    
    return null;
}

/**
 * Formatea número de teléfono (Ej: 12345678 → 1234-5678)
 * 
 * @param string $telefono Teléfono sin formato
 * @return string Teléfono formateado
 */
function formato_telefono($telefono) {
    $telefono = preg_replace('/[^0-9]/', '', $telefono);
    
    if (strlen($telefono) == 8) {
        return substr($telefono, 0, 4) . '-' . substr($telefono, 4);
    }
    
    return $telefono;
}

// ================================================
// UTILIDADES GENERALES
// ================================================

/**
 * Genera un código alfanumérico aleatorio
 * 
 * @param int $longitud Longitud del código
 * @return string Código generado
 */
function generar_codigo($longitud = 8) {
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $codigo = '';
    
    for ($i = 0; $i < $longitud; $i++) {
        $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
    }
    
    return $codigo;
}

/**
 * Redirecciona a una URL y termina la ejecución
 * 
 * @param string $url URL de destino
 */
function redirigir($url) {
    // Si la URL no empieza con http, asumir que es relativa
    if (strpos($url, 'http') !== 0) {
        $url = BASE_URL . $url;
    }
    
    header("Location: " . $url);
    exit();
}

/**
 * Obtiene la URL actual
 * 
 * @return string URL actual completa
 */
function url_actual() {
    $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocolo . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Trunca un texto a cierta longitud
 * 
 * @param string $texto Texto a truncar
 * @param int $longitud Longitud máxima
 * @param string $sufijo Sufijo a agregar (default: ...)
 * @return string Texto truncado
 */
function truncar_texto($texto, $longitud, $sufijo = '...') {
    if (strlen($texto) <= $longitud) {
        return $texto;
    }
    
    return substr($texto, 0, $longitud) . $sufijo;
}

// ================================================
// MENSAJES FLASH (sesión)
// ================================================

/**
 * Guarda un mensaje de éxito en la sesión
 * 
 * @param string $mensaje Mensaje a mostrar
 */
function mensaje_exito($mensaje) {
    $_SESSION['mensaje_exito'] = $mensaje;
}

/**
 * Guarda un mensaje de error en la sesión
 * 
 * @param string $mensaje Mensaje a mostrar
 */
function mensaje_error($mensaje) {
    $_SESSION['mensaje_error'] = $mensaje;
}

/**
 * Guarda un mensaje de advertencia en la sesión
 * 
 * @param string $mensaje Mensaje a mostrar
 */
function mensaje_advertencia($mensaje) {
    $_SESSION['mensaje_advertencia'] = $mensaje;
}

/**
 * Obtiene y limpia el mensaje de éxito
 * 
 * @return string|null Mensaje o null si no existe
 */
function obtener_mensaje_exito() {
    if (isset($_SESSION['mensaje_exito'])) {
        $mensaje = $_SESSION['mensaje_exito'];
        unset($_SESSION['mensaje_exito']);
        return $mensaje;
    }
    return null;
}

/**
 * Obtiene y limpia el mensaje de error
 * 
 * @return string|null Mensaje o null si no existe
 */
function obtener_mensaje_error() {
    if (isset($_SESSION['mensaje_error'])) {
        $mensaje = $_SESSION['mensaje_error'];
        unset($_SESSION['mensaje_error']);
        return $mensaje;
    }
    return null;
}

/**
 * Obtiene y limpia el mensaje de advertencia
 * 
 * @return string|null Mensaje o null si no existe
 */
function obtener_mensaje_advertencia() {
    if (isset($_SESSION['mensaje_advertencia'])) {
        $mensaje = $_SESSION['mensaje_advertencia'];
        unset($_SESSION['mensaje_advertencia']);
        return $mensaje;
    }
    return null;
}

// ================================================
// LOGGING Y AUDITORÍA
// ================================================

/**
 * Registra un evento en la tabla de auditoría
 * 
 * @param string $tabla Tabla afectada
 * @param string $accion Acción realizada (INSERT, UPDATE, DELETE, etc.)
 * @param int $registro_id ID del registro afectado
 * @param string $descripcion Descripción del evento
 */
function registrar_auditoria($tabla, $accion, $registro_id, $descripcion) {
    // Verificar que la tabla audit_log existe
    if (!db_exists('INFORMATION_SCHEMA.TABLES', 'TABLE_NAME = ?', ['audit_log'])) {
        return; // No hacer nada si no existe la tabla
    }
    
    $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $sql = "INSERT INTO audit_log (usuario_id, tabla, accion, registro_id, descripcion, ip_address) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    db_execute($sql, [$usuario_id, $tabla, $accion, $registro_id, $descripcion, $ip]);
}

/**
 * Escribe un mensaje en el archivo de log
 * 
 * @param string $mensaje Mensaje a loguear
 * @param string $nivel Nivel del log (INFO, WARNING, ERROR)
 */
function escribir_log($mensaje, $nivel = 'INFO') {
    $archivo_log = LOGS_PATH . 'app.log';
    $fecha = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $usuario = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : 'guest';
    
    $linea = "[$fecha] [$nivel] [IP:$ip] [User:$usuario] $mensaje" . PHP_EOL;
    
    file_put_contents($archivo_log, $linea, FILE_APPEND);
}

?>
```

---

### 5.3 Crear database/seed.sql

**Archivo:** `database/seed.sql`

**Propósito:** Poblar la BD con datos de prueba realistas.

```sql
-- ================================================
-- DATOS DE PRUEBA
-- ================================================
-- Este script inserta datos realistas para desarrollo.
-- IMPORTANTE: NO ejecutar en producción.
-- ================================================

USE nombre_base_datos;

-- Deshabilitar checks temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- ================================================
-- LIMPIAR DATOS EXISTENTES (opcional)
-- ================================================
DELETE FROM usuarios;
DELETE FROM sucursales;
-- ... otras tablas según necesites

-- Resetear AUTO_INCREMENT
ALTER TABLE usuarios AUTO_INCREMENT = 1;
ALTER TABLE sucursales AUTO_INCREMENT = 1;

-- ================================================
-- 1. SUCURSALES (si aplica)
-- ================================================
INSERT INTO sucursales (id, nombre, direccion, telefono, email, activo) VALUES
(1, 'Sucursal Principal', 'Calle Principal 123, Zona 1', '1234-5678', 'principal@empresa.com', 1),
(2, 'Sucursal Norte', 'Avenida Norte 456, Zona 2', '8765-4321', 'norte@empresa.com', 1);

-- ================================================
-- 2. USUARIOS (6 usuarios, 6 roles diferentes)
-- ================================================
-- Password para todos: "123456" (hash bcrypt)
INSERT INTO usuarios (id, nombre, email, password, rol, sucursal_id, activo) VALUES
(1, 'Carlos Admin', 'admin@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', NULL, 1),
(2, 'María Dueño', 'dueno@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dueño', NULL, 1),
(3, 'Juan Vendedor', 'vendedor@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendedor', 1, 1),
(4, 'Ana Cajera', 'cajera@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cajero', 1, 1),
(5, 'Roberto Técnico', 'tecnico@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tecnico', 1, 1),
(6, 'Laura Marketing', 'marketing@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'marketing', NULL, 1);

-- ================================================
-- 3. DATOS COMPLEMENTARIOS
-- ================================================
-- Insertar categorías, productos, clientes, etc.
-- según el proyecto específico

-- ================================================
-- Restaurar checks
-- ================================================
SET FOREIGN_KEY_CHECKS = 1;

-- ================================================
-- VERIFICACIÓN
-- ================================================
SELECT 'Datos de prueba cargados exitosamente!' AS mensaje;
SELECT 'Usuarios insertados' AS tabla, COUNT(*) AS registros FROM usuarios
UNION ALL SELECT 'Sucursales insertadas', COUNT(*) FROM sucursales;
```

**⚠️ IMPORTANTE:**
- El hash `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi` corresponde a la contraseña "123456"
- Todos los usuarios de prueba usan el mismo password
- Ajustar según las tablas de tu proyecto

---

### 5.4 Ejecutar seed.sql

**Métodos:**

#### Método 1: phpMyAdmin
1. Seleccionar tu base de datos
2. Pestaña "SQL"
3. Copiar contenido de `seed.sql`
4. Ejecutar

#### Método 2: MySQL Command Line
```bash
mysql -u root -p nombre_base_datos < database/seed.sql
```

**Verificar:** Datos insertados correctamente.

---

### 5.5 Mejorar includes/db.php

Agregar funciones adicionales que se necesitarán:

```php
<?php
// ... código anterior de db.php ...

/**
 * Inicia una transacción
 */
function db_begin_transaction() {
    global $pdo;
    return $pdo->beginTransaction();
}

/**
 * Confirma una transacción
 */
function db_commit() {
    global $pdo;
    return $pdo->commit();
}

/**
 * Revierte una transacción
 */
function db_rollback() {
    global $pdo;
    return $pdo->rollBack();
}

/**
 * Obtiene el último ID insertado
 * 
 * @return int ID del último registro insertado
 */
function db_last_id() {
    global $pdo;
    return $pdo->lastInsertId();
}

/**
 * Escapa un valor para uso seguro en queries (USAR SOLO CUANDO NO SE PUEDAN USAR PREPARED STATEMENTS)
 * 
 * @param mixed $valor Valor a escapar
 * @return string Valor escapado
 */
function db_escape($valor) {
    global $pdo;
    return $pdo->quote($valor);
}

?>
```

---

### 5.6 Checklist Día 2

Al finalizar el Día 2, debes tener:

- [ ] `schema.sql` ejecutado (todas las tablas creadas)
- [ ] `database/seed.sql` creado y ejecutado
- [ ] Base de datos con datos de prueba (usuarios, etc.)
- [ ] `includes/funciones.php` implementado con 25+ funciones
- [ ] `includes/db.php` mejorado con funciones adicionales
- [ ] Todas las funciones probadas y funcionales

**Comando Git:**
```bash
git add .
git commit -m "Fase 1 Día 2: Funciones helper y datos de prueba"
```

---

## 6. DÍA 3: SISTEMA DE AUTENTICACIÓN

### 6.1 Crear includes/auth.php

**Archivo:** `includes/auth.php`

**Propósito:** Sistema completo de autenticación y autorización.

```php
<?php
/**
 * ================================================
 * SISTEMA DE AUTENTICACIÓN
 * ================================================
 * 
 * Maneja login, logout, verificación de sesiones y permisos.
 * 
 * Funciones principales:
 * - intentar_login()
 * - iniciar_sesion()
 * - cerrar_sesion()
 * - verificar_sesion()
 * - requiere_autenticacion()
 * - requiere_rol()
 * - tiene_permiso()
 * 
 * @package   Sistema
 * @version   1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/funciones.php';

// ================================================
// AUTENTICACIÓN
// ================================================

/**
 * Intenta autenticar un usuario con email y password
 * 
 * @param string $email Email del usuario
 * @param string $password Password en texto plano
 * @return array|false Datos del usuario o false si falla
 */
function intentar_login($email, $password) {
    global $pdo;
    
    // Buscar usuario por email
    $sql = "SELECT u.*, s.nombre as sucursal_nombre 
            FROM usuarios u 
            LEFT JOIN sucursales s ON u.sucursal_id = s.id 
            WHERE u.email = ? AND u.activo = 1";
    
    $usuario = db_query_one($sql, [$email]);
    
    // Si no existe el usuario
    if (!$usuario) {
        escribir_log("Intento de login fallido: usuario no existe ($email)", 'WARNING');
        return false;
    }
    
    // Verificar password
    if (!verificar_password($password, $usuario['password'])) {
        escribir_log("Intento de login fallido: password incorrecto ($email)", 'WARNING');
        return false;
    }
    
    // Login exitoso
    escribir_log("Login exitoso: {$usuario['nombre']} ($email)", 'INFO');
    return $usuario;
}

/**
 * Inicia sesión para un usuario
 * 
 * @param array $usuario Datos del usuario desde la BD
 */
function iniciar_sesion($usuario) {
    // Regenerar ID de sesión por seguridad (previene session fixation)
    session_regenerate_id(true);
    
    // Guardar datos en sesión
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_rol'] = $usuario['rol'];
    $_SESSION['usuario_sucursal_id'] = $usuario['sucursal_id'] ?? null;
    $_SESSION['usuario_sucursal_nombre'] = $usuario['sucursal_nombre'] ?? null;
    $_SESSION['sesion_inicio'] = time();
    $_SESSION['ultima_actividad'] = time();
    
    // Actualizar último acceso en BD
    $sql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
    db_execute($sql, [$usuario['id']]);
    
    // Registrar en auditoría
    registrar_auditoria('usuarios', 'LOGIN', $usuario['id'], 'Inicio de sesión exitoso');
}

/**
 * Cierra la sesión del usuario actual
 */
function cerrar_sesion() {
    // Registrar en auditoría antes de cerrar sesión
    if (esta_autenticado()) {
        registrar_auditoria('usuarios', 'LOGOUT', usuario_actual_id(), 'Cierre de sesión');
        escribir_log("Logout: " . usuario_actual_nombre(), 'INFO');
    }
    
    // Destruir todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la cookie de sesión
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    
    // Destruir la sesión
    session_destroy();
}

// ================================================
// VERIFICACIÓN DE SESIÓN
// ================================================

/**
 * Verifica si el usuario está autenticado
 * 
 * @return bool true si está autenticado, false si no
 */
function esta_autenticado() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Verifica si la sesión es válida (no expirada)
 * 
 * @return bool true si es válida, false si expiró
 */
function verificar_sesion() {
    if (!esta_autenticado()) {
        return false;
    }
    
    // Verificar timeout de inactividad
    if (isset($_SESSION['ultima_actividad'])) {
        $tiempo_inactivo = time() - $_SESSION['ultima_actividad'];
        
        if ($tiempo_inactivo > SESSION_TIMEOUT) {
            escribir_log("Sesión expirada por inactividad: " . usuario_actual_nombre(), 'INFO');
            cerrar_sesion();
            return false;
        }
    }
    
    // Actualizar última actividad
    $_SESSION['ultima_actividad'] = time();
    
    return true;
}

/**
 * Requiere que el usuario esté autenticado
 * Redirige a login si no lo está
 */
function requiere_autenticacion() {
    if (!verificar_sesion()) {
        mensaje_error('Debes iniciar sesión para acceder a esta página');
        redirigir('login.php');
    }
}

// ================================================
// AUTORIZACIÓN (Roles y Permisos)
// ================================================

/**
 * Verifica si el usuario actual tiene un rol específico
 * 
 * @param string|array $roles Rol(es) permitido(s)
 * @return bool true si tiene el rol, false si no
 */
function tiene_rol($roles) {
    if (!esta_autenticado()) {
        return false;
    }
    
    $rol_usuario = usuario_actual_rol();
    
    // Si se pasa un string, convertir a array
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    
    return in_array($rol_usuario, $roles);
}

/**
 * Requiere que el usuario tenga un rol específico
 * Redirige al dashboard si no tiene el rol
 * 
 * @param string|array $roles Rol(es) permitido(s)
 */
function requiere_rol($roles) {
    requiere_autenticacion();
    
    if (!tiene_rol($roles)) {
        mensaje_error('No tienes permisos para acceder a esta página');
        escribir_log("Acceso denegado por falta de rol: " . usuario_actual_nombre() . " intentó acceder", 'WARNING');
        redirigir('dashboard.php');
    }
}

/**
 * Verifica si el usuario tiene permiso para una acción específica
 * 
 * @param string $modulo Nombre del módulo (ej: 'ventas', 'inventario')
 * @param string $accion Acción a realizar (ver, crear, editar, eliminar)
 * @return bool true si tiene permiso, false si no
 */
function tiene_permiso($modulo, $accion = 'ver') {
    if (!esta_autenticado()) {
        return false;
    }
    
    $rol = usuario_actual_rol();
    
    // Administrador y Dueño tienen TODOS los permisos
    if (in_array($rol, ['administrador', 'dueño', 'admin'])) {
        return true;
    }
    
    // Matriz de permisos por rol
    // Ajustar según los roles y módulos de tu proyecto
    $permisos = [
        'vendedor' => [
            'ventas' => ['ver', 'crear'],
            'clientes' => ['ver', 'crear', 'editar'],
            'productos' => ['ver'],
            'inventario' => ['ver'],
            'reportes' => ['ver']
        ],
        'cajero' => [
            'ventas' => ['ver', 'crear'],
            'caja' => ['ver', 'crear', 'editar'],
            'clientes' => ['ver'],
            'creditos' => ['ver', 'crear'],
            'reportes' => ['ver']
        ],
        'tecnico' => [
            'taller' => ['ver', 'crear', 'editar'],
            'inventario' => ['ver'],
            'materias_primas' => ['ver', 'crear'],
            'reportes' => ['ver']
        ],
        'marketing' => [
            'productos' => ['ver'],
            'clientes' => ['ver'],
            'reportes' => ['ver']
        ]
    ];
    
    // Verificar si el rol tiene permiso para el módulo y acción
    if (isset($permisos[$rol][$modulo])) {
        return in_array($accion, $permisos[$rol][$modulo]);
    }
    
    return false;
}

// ================================================
// OBTENER DATOS DEL USUARIO ACTUAL
// ================================================

/**
 * Obtiene el ID del usuario actual
 * 
 * @return int|null ID del usuario o null
 */
function usuario_actual_id() {
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Obtiene el nombre del usuario actual
 * 
 * @return string|null Nombre del usuario o null
 */
function usuario_actual_nombre() {
    return $_SESSION['usuario_nombre'] ?? null;
}

/**
 * Obtiene el email del usuario actual
 * 
 * @return string|null Email del usuario o null
 */
function usuario_actual_email() {
    return $_SESSION['usuario_email'] ?? null;
}

/**
 * Obtiene el rol del usuario actual
 * 
 * @return string|null Rol del usuario o null
 */
function usuario_actual_rol() {
    return $_SESSION['usuario_rol'] ?? null;
}

/**
 * Obtiene el ID de sucursal del usuario actual
 * 
 * @return int|null ID de sucursal o null
 */
function usuario_actual_sucursal() {
    return $_SESSION['usuario_sucursal_id'] ?? null;
}

// ================================================
// MENÚ DINÁMICO
// ================================================

/**
 * Obtiene el menú de navegación según el rol del usuario
 * 
 * @return array Items del menú
 */
function obtener_menu_usuario() {
    $rol = usuario_actual_rol();
    
    $menu = [];
    
    // Dashboard visible para todos
    $menu[] = [
        'texto' => 'Dashboard',
        'url' => 'dashboard.php',
        'icono' => 'bi-speedometer2'
    ];
    
    // Menú según rol (ajustar según tu proyecto)
    switch ($rol) {
        case 'administrador':
        case 'dueño':
        case 'admin':
            $menu[] = ['texto' => 'Inventario', 'url' => 'modules/inventario/', 'icono' => 'bi-box-seam'];
            $menu[] = ['texto' => 'Ventas', 'url' => 'modules/ventas/', 'icono' => 'bi-cart'];
            $menu[] = ['texto' => 'Clientes', 'url' => 'modules/clientes/', 'icono' => 'bi-people'];
            $menu[] = ['texto' => 'Caja', 'url' => 'modules/caja/', 'icono' => 'bi-cash-coin'];
            $menu[] = ['texto' => 'Reportes', 'url' => 'modules/reportes/', 'icono' => 'bi-graph-up'];
            $menu[] = ['texto' => 'Configuración', 'url' => 'modules/configuracion/', 'icono' => 'bi-gear'];
            break;
            
        case 'vendedor':
            $menu[] = ['texto' => 'Ventas', 'url' => 'modules/ventas/', 'icono' => 'bi-cart'];
            $menu[] = ['texto' => 'Clientes', 'url' => 'modules/clientes/', 'icono' => 'bi-people'];
            $menu[] = ['texto' => 'Inventario', 'url' => 'modules/inventario/', 'icono' => 'bi-box-seam'];
            $menu[] = ['texto' => 'Reportes', 'url' => 'modules/reportes/', 'icono' => 'bi-graph-up'];
            break;
            
        case 'cajero':
            $menu[] = ['texto' => 'Ventas', 'url' => 'modules/ventas/', 'icono' => 'bi-cart'];
            $menu[] = ['texto' => 'Caja', 'url' => 'modules/caja/', 'icono' => 'bi-cash-coin'];
            $menu[] = ['texto' => 'Clientes', 'url' => 'modules/clientes/', 'icono' => 'bi-people'];
            break;
            
        case 'tecnico':
            $menu[] = ['texto' => 'Taller', 'url' => 'modules/taller/', 'icono' => 'bi-tools'];
            $menu[] = ['texto' => 'Inventario', 'url' => 'modules/inventario/', 'icono' => 'bi-box-seam'];
            break;
    }
    
    return $menu;
}

?>
```

---

### 6.2 Crear login.php

**Archivo:** `login.php` (raíz del proyecto)

**Propósito:** Página de inicio de sesión.

```php
<?php
// ================================================
// PÁGINA DE LOGIN
// ================================================

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/funciones.php';
require_once 'includes/auth.php';

// Si ya está autenticado, redirigir al dashboard
if (esta_autenticado()) {
    redirigir('dashboard.php');
}

// Procesar el formulario de login
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = limpiar_texto($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validar campos
    if (empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos';
    } else {
        // Intentar login
        $usuario = intentar_login($email, $password);
        
        if ($usuario) {
            // Login exitoso
            iniciar_sesion($usuario);
            mensaje_exito('Bienvenido, ' . $usuario['nombre']);
            redirigir('dashboard.php');
        } else {
            // Login fallido
            $error = 'Email o contraseña incorrectos';
        }
    }
}

// Título de página
$titulo_pagina = 'Iniciar Sesión';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?> - <?php echo SISTEMA_NOMBRE; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .login-header i {
            font-size: 64px;
            margin-bottom: 15px;
        }
        
        .login-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 15px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="bi bi-shield-lock"></i>
            <h2><?php echo SISTEMA_NOMBRE; ?></h2>
            <p>Sistema de Gestión</p>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php
            $mensaje_error = obtener_mensaje_error();
            if ($mensaje_error):
            ?>
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <?php echo $mensaje_error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-1"></i> Email
                    </label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        placeholder="tu@email.com"
                        required
                        autofocus
                        value="<?php echo htmlspecialchars($email ?? ''); ?>"
                    >
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i> Contraseña
                    </label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••"
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Iniciar Sesión
                </button>
            </form>
            
            <?php if (DEVELOPMENT_MODE): ?>
            <!-- Credenciales de prueba (solo en desarrollo) -->
            <div class="mt-4 p-3 bg-light rounded">
                <small class="text-muted">
                    <strong>Usuarios de prueba:</strong><br>
                    admin@empresa.com / 123456<br>
                    vendedor@empresa.com / 123456
                </small>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

### 6.3 Crear logout.php

**Archivo:** `logout.php` (raíz del proyecto)

**Propósito:** Cerrar sesión y redirigir al login.

```php
<?php
// ================================================
// CERRAR SESIÓN
// ================================================

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/funciones.php';
require_once 'includes/auth.php';

// Cerrar sesión
cerrar_sesion();

// Redirigir al login con mensaje
mensaje_exito('Has cerrado sesión exitosamente');
redirigir('login.php');
?>
```

---

### 6.4 Crear dashboard.php

**Archivo:** `dashboard.php` (raíz del proyecto)

**Propósito:** Página principal después del login.

```php
<?php
// ================================================
// DASHBOARD PRINCIPAL
// ================================================

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/funciones.php';
require_once 'includes/auth.php';

// Requiere autenticación
requiere_autenticacion();

// Obtener estadísticas básicas (ajustar según tu proyecto)
$total_usuarios = db_count('usuarios', 'activo = 1');

// Título de página
$titulo_pagina = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?> - <?php echo SISTEMA_NOMBRE; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background: #f8f9fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
        }
        
        .main-content {
            padding: 30px 0;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-shield-lock me-2"></i>
                <?php echo SISTEMA_NOMBRE; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo usuario_actual_nombre(); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <span class="dropdown-item-text">
                                    <small class="text-muted">
                                        <i class="bi bi-tag me-1"></i>
                                        <?php echo ucfirst(usuario_actual_rol()); ?>
                                    </small>
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Contenido Principal -->
    <div class="container-fluid main-content">
        <!-- Mensaje de éxito -->
        <?php
        $mensaje_exito = obtener_mensaje_exito();
        if ($mensaje_exito):
        ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo $mensaje_exito; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <h1 class="mb-4">
            <i class="bi bi-emoji-smile me-2"></i>
            ¡Bienvenido, <?php echo explode(' ', usuario_actual_nombre())[0]; ?>!
        </h1>
        
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                    <div class="stat-value"><?php echo $total_usuarios; ?></div>
                    <div class="stat-label">Usuarios Activos</div>
                </div>
            </div>
            
            <!-- Agregar más tarjetas de estadísticas según el proyecto -->
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

### 6.5 Crear index.php

**Archivo:** `index.php` (raíz del proyecto)

**Propósito:** Punto de entrada del sistema.

```php
<?php
// ================================================
// PÁGINA DE INICIO
// ================================================

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/funciones.php';
require_once 'includes/auth.php';

// Si está autenticado, ir al dashboard
if (esta_autenticado()) {
    redirigir('dashboard.php');
}

// Si no está autenticado, ir al login
redirigir('login.php');
?>
```

---

### 6.6 Probar el Sistema

**Pasos:**

1. Ir a `http://localhost/tu-proyecto/`
2. Debería redirigir automáticamente a `login.php`
3. Probar login con:
   - Email: `admin@empresa.com`
   - Password: `123456`
4. Debería redirigir a `dashboard.php`
5. Ver mensaje de bienvenida
6. Cerrar sesión
7. Verificar que redirige a login

**Verificar:**
- ✅ Redireccionamiento funcional
- ✅ Login exitoso
- ✅ Dashboard protegido
- ✅ Logout funcional

---

### 6.7 Checklist Día 3

Al finalizar el Día 3, debes tener:

- [ ] `includes/auth.php` implementado completamente
- [ ] `login.php` funcional y con diseño profesional
- [ ] `logout.php` funcional
- [ ] `dashboard.php` básico protegido
- [ ] `index.php` con redirecciones
- [ ] Flujo completo de login/logout probado
- [ ] Sesiones funcionando correctamente

**Comando Git:**
```bash
git add .
git commit -m "Fase 1 Día 3: Sistema de autenticación completo"
```

---

## 7. DÍA 4: ROLES, PERMISOS Y REFINAMIENTO

### 7.1 Mejorar Sistema de Roles

**Objetivo:** Dashboard dinámico según rol del usuario.

**Archivo:** `dashboard.php` (mejorar)

Agregar estadísticas según permisos:

```php
<?php
// ... código anterior ...

// Obtener estadísticas según el rol
$estadisticas = [];

// Estadísticas generales (todos pueden ver)
$estadisticas['total_usuarios'] = db_count('usuarios', 'activo = 1');

// Si tiene acceso a inventario
if (tiene_permiso('inventario', 'ver')) {
    $estadisticas['total_productos'] = db_count('productos', 'activo = 1');
    $estadisticas['productos_bajo_stock'] = db_count('productos', 'stock <= stock_minimo');
}

// Si tiene acceso a ventas
if (tiene_permiso('ventas', 'ver')) {
    $sql = "SELECT COUNT(*) as total FROM ventas WHERE DATE(fecha) = CURDATE()";
    $result = db_query_one($sql);
    $estadisticas['ventas_hoy'] = $result['total'];
    
    $sql = "SELECT COALESCE(SUM(total), 0) as monto FROM ventas WHERE DATE(fecha) = CURDATE()";
    $result = db_query_one($sql);
    $estadisticas['monto_hoy'] = $result['monto'];
}

// Si tiene acceso a clientes
if (tiene_permiso('clientes', 'ver')) {
    $estadisticas['total_clientes'] = db_count('clientes', 'activo = 1');
}
?>
```

---

### 7.2 Crear includes/middleware.php

**Archivo:** `includes/middleware.php`

**Propósito:** Funciones adicionales de protección y validación.

```php
<?php
/**
 * ================================================
 * MIDDLEWARE DE PROTECCIÓN
 * ================================================
 * 
 * Funciones adicionales para proteger rutas y validar acciones.
 * 
 * @package   Sistema
 * @version   1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth.php';

/**
 * Verifica que el usuario tenga permiso para ver un recurso específico
 * 
 * @param string $tabla Tabla del recurso
 * @param int $id ID del recurso
 * @param string $campo_usuario Campo que relaciona con el usuario (default: usuario_id)
 * @return bool true si puede ver, false si no
 */
function puede_ver_recurso($tabla, $id, $campo_usuario = 'usuario_id') {
    // Admin y dueño pueden ver todo
    if (tiene_rol(['administrador', 'dueño'])) {
        return true;
    }
    
    // Verificar si el recurso pertenece al usuario actual
    $sql = "SELECT COUNT(*) as total FROM $tabla WHERE id = ? AND $campo_usuario = ?";
    $result = db_query_one($sql, [$id, usuario_actual_id()]);
    
    return $result['total'] > 0;
}

/**
 * Verifica que el usuario pertenezca a la misma sucursal del recurso
 * 
 * @param string $tabla Tabla del recurso
 * @param int $id ID del recurso
 * @return bool true si pertenece a la misma sucursal, false si no
 */
function misma_sucursal($tabla, $id) {
    $sucursal_usuario = usuario_actual_sucursal();
    
    // Si el usuario no tiene sucursal asignada, puede ver todo
    if (is_null($sucursal_usuario)) {
        return true;
    }
    
    $sql = "SELECT sucursal_id FROM $tabla WHERE id = ?";
    $result = db_query_one($sql, [$id]);
    
    if (!$result) {
        return false;
    }
    
    return $result['sucursal_id'] == $sucursal_usuario;
}

/**
 * Protección contra CSRF
 * Genera un token CSRF y lo guarda en sesión
 * 
 * @return string Token CSRF
 */
function generar_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica que el token CSRF sea válido
 * 
 * @param string $token Token a verificar
 * @return bool true si es válido, false si no
 */
function verificar_csrf_token($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Campo oculto HTML con token CSRF
 * Usar en formularios
 * 
 * @return string HTML del campo oculto
 */
function csrf_field() {
    $token = generar_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Requiere token CSRF válido o aborta
 * Usar al procesar formularios POST
 */
function requiere_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        
        if (!verificar_csrf_token($token)) {
            escribir_log("Intento de CSRF detectado", 'WARNING');
            http_response_code(403);
            die('Token CSRF inválido. Por favor, recarga la página e intenta de nuevo.');
        }
    }
}

/**
 * Rate limiting simple
 * Limita el número de intentos de una acción
 * 
 * @param string $clave Clave única de la acción (ej: 'login_attempts')
 * @param int $max_intentos Máximo de intentos permitidos
 * @param int $tiempo_ventana Tiempo en segundos para el rate limit
 * @return bool true si está dentro del límite, false si excedió
 */
function rate_limit($clave, $max_intentos = 5, $tiempo_ventana = 300) {
    $clave_session = 'rate_limit_' . $clave;
    $ahora = time();
    
    // Inicializar si no existe
    if (!isset($_SESSION[$clave_session])) {
        $_SESSION[$clave_session] = [
            'intentos' => 0,
            'primer_intento' => $ahora
        ];
    }
    
    $datos = $_SESSION[$clave_session];
    
    // Si ya pasó el tiempo de ventana, resetear
    if ($ahora - $datos['primer_intento'] > $tiempo_ventana) {
        $_SESSION[$clave_session] = [
            'intentos' => 1,
            'primer_intento' => $ahora
        ];
        return true;
    }
    
    // Incrementar intentos
    $_SESSION[$clave_session]['intentos']++;
    
    // Verificar si excedió el límite
    if ($_SESSION[$clave_session]['intentos'] > $max_intentos) {
        escribir_log("Rate limit excedido para: $clave", 'WARNING');
        return false;
    }
    
    return true;
}

/**
 * Verifica que la petición sea AJAX
 * 
 * @return bool true si es AJAX, false si no
 */
function es_ajax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Responde con JSON y termina la ejecución
 * 
 * @param mixed $data Datos a enviar
 * @param int $status_code Código de estado HTTP
 */
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

?>
```

---

### 7.3 Implementar Rate Limiting en Login

**Archivo:** `login.php` (mejorar)

Agregar protección contra fuerza bruta:

```php
<?php
// ... código anterior ...

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar rate limiting
    if (!rate_limit('login_attempts', MAX_LOGIN_ATTEMPTS, LOGIN_LOCKOUT_TIME)) {
        $tiempo_espera = ceil(LOGIN_LOCKOUT_TIME / 60);
        $error = "Demasiados intentos fallidos. Por favor, espera $tiempo_espera minutos e intenta de nuevo.";
    } else {
        $email = limpiar_texto($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // ... resto del código de login ...
    }
}
?>
```

---

### 7.4 Crear tests/test-auth.php

**Archivo:** `tests/test-auth.php`

**Propósito:** Probar todas las funciones de autenticación.

```php
<?php
/**
 * ================================================
 * TEST DE AUTENTICACIÓN
 * ================================================
 * 
 * Prueba todas las funciones del sistema de auth.
 */

require_once '../config.php';
require_once '../includes/db.php';
require_once '../includes/funciones.php';
require_once '../includes/auth.php';

// Función helper para tests
function test($nombre, $resultado, $esperado) {
    $pass = $resultado === $esperado;
    $icono = $pass ? '✅' : '❌';
    $estado = $pass ? 'PASS' : 'FAIL';
    
    echo "$icono [$estado] $nombre";
    
    if (!$pass) {
        echo " (Esperado: " . var_export($esperado, true) . ", Obtenido: " . var_export($resultado, true) . ")";
    }
    
    echo "<br>";
    
    return $pass;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test de Autenticación</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; }
        .test-section { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .summary { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196F3; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>🧪 Test de Autenticación</h1>
    
    <div class="test-section">
        <h2>1. Tests de Validación</h2>
        <?php
        $tests_validacion = 0;
        $tests_validacion_pass = 0;
        
        // Test validar_email
        if (test('validar_email() - email válido', validar_email('test@ejemplo.com'), true)) $tests_validacion_pass++;
        $tests_validacion++;
        
        if (test('validar_email() - email inválido', validar_email('no-es-email'), false)) $tests_validacion_pass++;
        $tests_validacion++;
        
        // Test validar_telefono
        if (test('validar_telefono() - teléfono válido', validar_telefono('12345678'), true)) $tests_validacion_pass++;
        $tests_validacion++;
        
        if (test('validar_telefono() - teléfono inválido', validar_telefono('123'), false)) $tests_validacion_pass++;
        $tests_validacion++;
        
        echo "<br><strong>Validación: $tests_validacion_pass / $tests_validacion tests pasaron</strong>";
        ?>
    </div>
    
    <div class="test-section">
        <h2>2. Tests de Passwords</h2>
        <?php
        $tests_password = 0;
        $tests_password_pass = 0;
        
        // Test hash_password y verificar_password
        $password = 'test123';
        $hash = hash_password($password);
        
        if (test('hash_password() genera hash', !empty($hash) && strlen($hash) > 50, true)) $tests_password_pass++;
        $tests_password++;
        
        if (test('verificar_password() - password correcto', verificar_password($password, $hash), true)) $tests_password_pass++;
        $tests_password++;
        
        if (test('verificar_password() - password incorrecto', verificar_password('incorrecto', $hash), false)) $tests_password_pass++;
        $tests_password++;
        
        echo "<br><strong>Passwords: $tests_password_pass / $tests_password tests pasaron</strong>";
        ?>
    </div>
    
    <div class="test-section">
        <h2>3. Tests de Base de Datos</h2>
        <?php
        $tests_bd = 0;
        $tests_bd_pass = 0;
        
        // Test db_count
        $total_usuarios = db_count('usuarios');
        if (test('db_count() retorna número', is_numeric($total_usuarios), true)) $tests_bd_pass++;
        $tests_bd++;
        
        // Test db_exists
        $existe = db_exists('usuarios', 'id = 1');
        if (test('db_exists() - registro existente', $existe, true)) $tests_bd_pass++;
        $tests_bd++;
        
        $no_existe = db_exists('usuarios', 'id = 999999');
        if (test('db_exists() - registro no existente', $no_existe, false)) $tests_bd_pass++;
        $tests_bd++;
        
        // Test db_query_one
        $usuario = db_query_one("SELECT * FROM usuarios WHERE id = 1");
        if (test('db_query_one() retorna resultado', !empty($usuario) && isset($usuario['email']), true)) $tests_bd_pass++;
        $tests_bd++;
        
        echo "<br><strong>Base de Datos: $tests_bd_pass / $tests_bd tests pasaron</strong>";
        ?>
    </div>
    
    <div class="test-section">
        <h2>4. Tests de Autenticación (sin iniciar sesión real)</h2>
        <?php
        $tests_auth = 0;
        $tests_auth_pass = 0;
        
        // Test intentar_login con credenciales incorrectas
        $resultado_login_fail = intentar_login('noexiste@test.com', 'password');
        if (test('intentar_login() - credenciales incorrectas', $resultado_login_fail, false)) $tests_auth_pass++;
        $tests_auth++;
        
        // Test esta_autenticado sin sesión
        if (test('esta_autenticado() - sin sesión', esta_autenticado(), false)) $tests_auth_pass++;
        $tests_auth++;
        
        // Test tiene_rol sin sesión
        if (test('tiene_rol() - sin sesión', tiene_rol('administrador'), false)) $tests_auth_pass++;
        $tests_auth++;
        
        echo "<br><strong>Autenticación: $tests_auth_pass / $tests_auth tests pasaron</strong>";
        ?>
    </div>
    
    <div class="test-section">
        <h2>5. Tests de Formato</h2>
        <?php
        $tests_formato = 0;
        $tests_formato_pass = 0;
        
        // Test formato_dinero
        if (test('formato_dinero()', formato_dinero(1234.56), 'Q 1,234.56')) $tests_formato_pass++;
        $tests_formato++;
        
        // Test formato_fecha
        $fecha_formateada = formato_fecha('2026-01-20');
        if (test('formato_fecha()', $fecha_formateada, '20/01/2026')) $tests_formato_pass++;
        $tests_formato++;
        
        // Test limpiar_texto
        $texto_limpio = limpiar_texto('<script>alert("xss")</script>Hola');
        $texto_esperado = htmlspecialchars('<script>alert("xss")</script>Hola', ENT_QUOTES, 'UTF-8');
        if (test('limpiar_texto() - previene XSS', $texto_limpio === $texto_esperado, true)) $tests_formato_pass++;
        $tests_formato++;
        
        echo "<br><strong>Formato: $tests_formato_pass / $tests_formato tests pasaron</strong>";
        ?>
    </div>
    
    <div class="summary">
        <h2>📊 Resumen de Tests</h2>
        <?php
        $total_tests = $tests_validacion + $tests_password + $tests_bd + $tests_auth + $tests_formato;
        $total_pass = $tests_validacion_pass + $tests_password_pass + $tests_bd_pass + $tests_auth_pass + $tests_formato_pass;
        $porcentaje = round(($total_pass / $total_tests) * 100, 2);
        
        echo "<p><strong>Total de tests ejecutados:</strong> $total_tests</p>";
        echo "<p><strong>Tests pasados:</strong> $total_pass</p>";
        echo "<p><strong>Tests fallados:</strong> " . ($total_tests - $total_pass) . "</p>";
        echo "<p><strong>Porcentaje de éxito:</strong> $porcentaje%</p>";
        
        if ($porcentaje == 100) {
            echo "<p style='color: green; font-weight: bold;'>✅ ¡Todos los tests pasaron exitosamente!</p>";
        } else {
            echo "<p style='color: orange; font-weight: bold;'>⚠️ Algunos tests fallaron. Revisar código.</p>";
        }
        ?>
    </div>
    
    <p style="margin-top: 20px;"><a href="../dashboard.php">← Volver al Dashboard</a></p>
</body>
</html>
```

**Ejecutar:** `http://localhost/tu-proyecto/tests/test-auth.php`

---

### 7.5 Refinar Diseño Visual

Asegurarse de que login y dashboard tengan diseño profesional según las guías del cliente.

Ver: `GUIA-DISENO-ESTILO.md` para paleta de colores y componentes.

---

### 7.6 Crear .htaccess

**Archivo:** `.htaccess` (raíz del proyecto)

**Propósito:** Configuración de Apache para seguridad y URLs amigables.

```apache
# ================================================
# CONFIGURACIÓN DE APACHE
# ================================================

# Habilitar RewriteEngine
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Si el archivo o directorio existe, servir directamente
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Redirigir index.php si existe
    # RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>

# ================================================
# SEGURIDAD
# ================================================

# Proteger config.php
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>

# Proteger archivos en includes/
<FilesMatch "\.(php)$">
    <If "%{REQUEST_URI} =~ m#^/includes/#">
        Order Allow,Deny
        Deny from all
    </If>
</FilesMatch>

# Proteger archivos .git
<DirectoryMatch "\.git">
    Order Allow,Deny
    Deny from all
</DirectoryMatch>

# Deshabilitar listado de directorios
Options -Indexes

# Proteger contra clickjacking
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# ================================================
# COMPRESIÓN (Optimización)
# ================================================
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# ================================================
# CACHÉ (Optimización)
# ================================================
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>

# ================================================
# LÍMITES DE UPLOAD
# ================================================
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300
php_value max_input_time 300
```

---

### 7.7 Documentar FASE-1-COMPLETADA.md

**Archivo:** `docs/FASE-1-COMPLETADA.md`

Crear documentación completa de lo realizado en Fase 1.

Ver ejemplo en: `docs/FASE-0-COMPLETADA.md`

**Incluir:**
- Resumen ejecutivo
- Archivos creados
- Funciones implementadas
- Problemas encontrados y soluciones
- Lecciones aprendidas
- Preparación para Fase 2

---

### 7.8 Checklist Día 4

Al finalizar el Día 4, debes tener:

- [ ] Sistema de roles implementado correctamente
- [ ] Dashboard dinámico según permisos
- [ ] `includes/middleware.php` creado con protecciones CSRF
- [ ] Rate limiting en login
- [ ] `tests/test-auth.php` ejecutado (100% de tests pasando)
- [ ] `.htaccess` configurado
- [ ] Diseño visual refinado
- [ ] `FASE-1-COMPLETADA.md` documentado
- [ ] **Fase 1 100% completa y funcional**

**Comando Git:**
```bash
git add .
git commit -m "Fase 1 Completada: Roles, permisos, tests y documentación"
git tag v1.0-fase1
```

---

## 8. SISTEMA DE CONFIGURACIÓN

### 8.1 Mejores Prácticas

#### ✅ Separar Configuración por Entorno

Crear múltiples archivos de configuración:

```
config/
├── config.development.php
├── config.production.php
└── config.testing.php
```

**Cargar según entorno:**

```php
<?php
// config.php
$entorno = getenv('APP_ENV') ?: 'development';
require_once __DIR__ . "/config/config.$entorno.php";
?>
```

#### ✅ Usar Variables de Entorno

Para datos sensibles, usar `.env`:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=mi_password_secreto
DB_NAME=mi_base_datos
```

Cargar con librería como `vlucas/phpdotenv` (opcional en proyectos sin Composer).

#### ✅ Validar Configuración al Iniciar

```php
<?php
// Verificar que todas las constantes necesarias estén definidas
$constantes_requeridas = ['DB_HOST', 'DB_NAME', 'DB_USER', 'BASE_URL'];

foreach ($constantes_requeridas as $constante) {
    if (!defined($constante)) {
        die("Error: Configuración incompleta. Falta definir: $constante");
    }
}
?>
```

---

## 9. CONEXIÓN A BASE DE DATOS

### 9.1 Mejores Prácticas

#### ✅ Usar Prepared Statements SIEMPRE

**MAL:**
```php
// NUNCA hacer esto (vulnerable a SQL Injection)
$sql = "SELECT * FROM usuarios WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
```

**BIEN:**
```php
// Usar prepared statements
$sql = "SELECT * FROM usuarios WHERE email = ?";
$result = db_query_one($sql, [$email]);
```

#### ✅ Usar Transacciones para Operaciones Críticas

```php
<?php
try {
    db_begin_transaction();
    
    // Insertar venta
    $venta_id = db_execute("INSERT INTO ventas (cliente_id, total) VALUES (?, ?)", [$cliente_id, $total]);
    
    // Insertar detalles
    foreach ($productos as $producto) {
        db_execute("INSERT INTO detalle_ventas (venta_id, producto_id, cantidad) VALUES (?, ?, ?)", 
                   [$venta_id, $producto['id'], $producto['cantidad']]);
    }
    
    // Actualizar inventario
    foreach ($productos as $producto) {
        db_execute("UPDATE inventario SET cantidad = cantidad - ? WHERE producto_id = ?", 
                   [$producto['cantidad'], $producto['id']]);
    }
    
    db_commit();
    mensaje_exito('Venta registrada exitosamente');
    
} catch (Exception $e) {
    db_rollback();
    mensaje_error('Error al registrar venta: ' . $e->getMessage());
    escribir_log("Error en transacción de venta: " . $e->getMessage(), 'ERROR');
}
?>
```

#### ✅ Manejar Reconexión Automática

```php
<?php
/**
 * Verifica conexión y reconecta si es necesario
 */
function verificar_conexion_bd() {
    global $pdo;
    
    try {
        $pdo->query('SELECT 1');
    } catch (PDOException $e) {
        // Reconectar
        escribir_log("Reconectando a BD...", 'WARNING');
        require __DIR__ . '/db.php';
    }
}
?>
```

#### ✅ Pool de Conexiones (para alta concurrencia)

En proyectos grandes, considerar usar PgBouncer (PostgreSQL) o ProxySQL (MySQL).

---

## 10. FUNCIONES HELPER

### 10.1 Categorías Recomendadas

**Ya implementadas en `includes/funciones.php`:**
- ✅ Sanitización y validación
- ✅ Seguridad (passwords)
- ✅ Formato de datos
- ✅ Utilidades generales
- ✅ Mensajes flash
- ✅ Logging y auditoría

**Adicionales útiles según proyecto:**

```php
<?php
/**
 * Genera un slug URL-friendly desde un texto
 * 
 * @param string $texto Texto a convertir
 * @return string Slug
 */
function generar_slug($texto) {
    $texto = strtolower(trim($texto));
    $texto = preg_replace('/[^a-z0-9-]/', '-', $texto);
    $texto = preg_replace('/-+/', '-', $texto);
    return trim($texto, '-');
}

/**
 * Calcula el porcentaje de un valor
 * 
 * @param float $valor Valor parcial
 * @param float $total Valor total
 * @return float Porcentaje
 */
function calcular_porcentaje($valor, $total) {
    if ($total == 0) return 0;
    return round(($valor / $total) * 100, 2);
}

/**
 * Calcula la diferencia en días entre dos fechas
 * 
 * @param string $fecha1 Primera fecha
 * @param string $fecha2 Segunda fecha
 * @return int Días de diferencia
 */
function diferencia_dias($fecha1, $fecha2) {
    $dt1 = new DateTime($fecha1);
    $dt2 = new DateTime($fecha2);
    $diff = $dt1->diff($dt2);
    return $diff->days;
}

/**
 * Verifica si una fecha ya pasó
 * 
 * @param string $fecha Fecha a verificar
 * @return bool true si ya pasó, false si no
 */
function fecha_pasada($fecha) {
    return strtotime($fecha) < time();
}

/**
 * Convierte bytes a formato legible
 * 
 * @param int $bytes Tamaño en bytes
 * @return string Tamaño formateado (ej: 1.5 MB)
 */
function formato_bytes($bytes) {
    $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
    $potencia = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $potencia), 2) . ' ' . $unidades[$potencia];
}
?>
```

---

## 11. SISTEMA DE AUTENTICACIÓN

### 11.1 Flujo Completo de Autenticación

```
┌─────────────────────────────────────────┐
│   Usuario visita cualquier URL         │
└──────────────┬──────────────────────────┘
               │
               ↓
┌──────────────────────────────────────────┐
│   ¿Es página pública (login)?            │
├──────────────┬──────────────────┬────────┤
│     SÍ       │                  │   NO   │
└──────────────┼──────────────────┴────────┘
               │                       │
               ↓                       ↓
        Mostrar página      ┌──────────────────────┐
                           │ requiere_autenticacion()│
                           └──────────┬──────────────┘
                                     │
                                     ↓
                           ┌──────────────────────┐
                           │ ¿Está autenticado?   │
                           ├──────────┬───────────┤
                           │   SÍ     │    NO     │
                           └──────────┼───────────┘
                                     │         │
                                     ↓         ↓
                          ┌─────────────┐  Redirigir
                          │ ¿Tiene rol? │  a login.php
                          ├──────┬──────┤
                          │  SÍ  │  NO  │
                          └──────┼──────┘
                                │    │
                                ↓    ↓
                       Mostrar    Redirigir
                       página     a dashboard
```

### 11.2 Seguridad en Autenticación

#### ✅ Password Hashing

Usar `password_hash()` y `password_verify()` de PHP (bcrypt):

```php
// Al registrar usuario
$hash = password_hash($password, PASSWORD_DEFAULT);

// Al verificar login
if (password_verify($password_ingresado, $hash_guardado)) {
    // Correcto
}
```

**NUNCA:**
- ❌ Guardar passwords en texto plano
- ❌ Usar MD5 o SHA1 sin salt
- ❌ Usar encriptación reversible

#### ✅ Protección contra Session Fixation

```php
// Regenerar ID de sesión al hacer login
session_regenerate_id(true);
```

#### ✅ Timeout de Sesión

```php
// Verificar inactividad
if (time() - $_SESSION['ultima_actividad'] > SESSION_TIMEOUT) {
    cerrar_sesion();
}

$_SESSION['ultima_actividad'] = time();
```

#### ✅ Logout Completo

```php
function cerrar_sesion() {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    
    session_destroy();
}
```

---

## 12. SISTEMA DE ROLES Y PERMISOS

### 12.1 Dos Enfoques

#### Enfoque A: Permisos en Código (Recomendado para < 10 roles)

**Ventajas:**
- Más rápido (no consulta BD)
- Más simple de implementar
- Menos overhead

**Desventajas:**
- Menos flexible
- Requiere cambio de código para modificar permisos

**Implementación:**

Ver función `tiene_permiso()` en `includes/auth.php`.

---

#### Enfoque B: Permisos en Base de Datos (Para > 10 roles)

**Ventajas:**
- Muy flexible
- Permisos modificables sin código
- Granularidad extrema

**Desventajas:**
- Más complejo
- Más queries a BD

**Implementación:**

**Tablas necesarias:**

```sql
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    activo BOOLEAN DEFAULT 1
);

CREATE TABLE permisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    modulo VARCHAR(50) NOT NULL,
    accion VARCHAR(50) NOT NULL,
    descripcion TEXT
);

CREATE TABLE roles_permisos (
    rol_id INT NOT NULL,
    permiso_id INT NOT NULL,
    PRIMARY KEY (rol_id, permiso_id),
    FOREIGN KEY (rol_id) REFERENCES roles(id),
    FOREIGN KEY (permiso_id) REFERENCES permisos(id)
);
```

**Función de verificación:**

```php
<?php
function tiene_permiso_bd($modulo, $accion) {
    if (!esta_autenticado()) {
        return false;
    }
    
    $rol = usuario_actual_rol();
    
    // Admin siempre tiene todos los permisos
    if ($rol === 'administrador') {
        return true;
    }
    
    // Buscar en BD
    $sql = "SELECT COUNT(*) as total 
            FROM roles r
            INNER JOIN roles_permisos rp ON r.id = rp.rol_id
            INNER JOIN permisos p ON rp.permiso_id = p.id
            WHERE r.nombre = ? AND p.modulo = ? AND p.accion = ?";
    
    $result = db_query_one($sql, [$rol, $modulo, $accion]);
    
    return $result['total'] > 0;
}
?>
```

---

### 12.2 Matriz de Permisos Recomendada

| Rol | Inventario | Ventas | Clientes | Caja | Reportes | Config |
|-----|-----------|--------|----------|------|----------|--------|
| **Admin** | CRUD | CRUD | CRUD | CRUD | CRUD | CRUD |
| **Dueño** | CRUD | CRUD | CRUD | CRUD | CRUD | CRUD |
| **Vendedor** | R | CR | CRU | - | R | - |
| **Cajero** | - | CR | R | CRU | R | - |
| **Técnico** | R | - | - | - | R | - |

**Leyenda:**
- C = Create (crear)
- R = Read (ver/listar)
- U = Update (editar)
- D = Delete (eliminar)

---

## 13. MIDDLEWARE DE PROTECCIÓN

### 13.1 Uso de Middleware

**En cada página protegida:**

```php
<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/funciones.php';
require_once 'includes/auth.php';

// Proteger página
requiere_autenticacion();

// Opcionalmente, verificar rol
requiere_rol(['administrador', 'vendedor']);

// O verificar permiso específico
if (!tiene_permiso('ventas', 'crear')) {
    mensaje_error('No tienes permiso para crear ventas');
    redirigir('dashboard.php');
}

// Si llegó aquí, el usuario tiene acceso
?>
```

### 13.2 Protección CSRF

**En formularios:**

```php
<form method="POST" action="">
    <?php echo csrf_field(); ?>
    
    <!-- Campos del formulario -->
    
    <button type="submit">Guardar</button>
</form>
```

**Al procesar:**

```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requiere_csrf(); // Verificará automáticamente o abortará
    
    // Procesar formulario
}
?>
```

---

## 14. DATOS DE PRUEBA

### 14.1 Características de Buenos Datos de Prueba

✅ **Realistas:**
- Nombres de personas reales (no "test1", "test2")
- Direcciones de ciudades reales
- Teléfonos con formato correcto
- Emails con dominios reales

✅ **Variados:**
- Usuarios de todos los roles
- Productos de diferentes categorías
- Clientes con diferentes tipos
- Transacciones de diferentes montos

✅ **Completos:**
- Datos en todas las tablas importantes
- Relaciones correctas entre tablas
- Suficiente cantidad para probar paginación

✅ **Consistentes:**
- Fechas lógicas (no fechas futuras donde no tienen sentido)
- Stocks realistas
- Precios coherentes

### 14.2 Estructura de seed.sql

```sql
-- 1. Datos de configuración
-- 2. Datos maestros (sucursales, categorías)
-- 3. Usuarios
-- 4. Catálogos (productos, servicios)
-- 5. Clientes
-- 6. Transacciones de ejemplo (opcional)
```

### 14.3 Generación Automatizada

Para proyectos grandes, usar librerías como `Faker` (con Composer):

```php
<?php
require 'vendor/autoload.php';

$faker = Faker\Factory::create('es_ES'); // Español

// Generar 50 clientes
for ($i = 0; $i < 50; $i++) {
    $nombre = $faker->name;
    $email = $faker->email;
    $telefono = $faker->numerify('####-####');
    
    echo "INSERT INTO clientes (nombre, email, telefono) VALUES ('$nombre', '$email', '$telefono');\n";
}
?>
```

---

## 15. SEGURIDAD

### 15.1 Top 10 Vulnerabilidades Web (OWASP)

#### 1. SQL Injection

**Prevención:**
✅ Usar prepared statements SIEMPRE
✅ Nunca concatenar variables en queries

```php
// MAL
$sql = "SELECT * FROM usuarios WHERE email = '$email'";

// BIEN
$sql = "SELECT * FROM usuarios WHERE email = ?";
$result = db_query_one($sql, [$email]);
```

---

#### 2. XSS (Cross-Site Scripting)

**Prevención:**
✅ Sanitizar TODA entrada de usuario
✅ Escapar TODA salida a HTML

```php
// Sanitizar entrada
$nombre = limpiar_texto($_POST['nombre']);

// Escapar salida
echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
```

---

#### 3. CSRF (Cross-Site Request Forgery)

**Prevención:**
✅ Usar tokens CSRF en formularios
✅ Verificar tokens al procesar

```php
// En el formulario
echo csrf_field();

// Al procesar
requiere_csrf();
```

---

#### 4. Autenticación Rota

**Prevención:**
✅ Passwords hasheados con bcrypt
✅ Sesiones seguras (httponly, samesite)
✅ Timeout de sesión
✅ Regenerar session ID al login

---

#### 5. Exposición de Datos Sensibles

**Prevención:**
✅ Usar HTTPS en producción
✅ No guardar datos sensibles en cookies
✅ Encriptar datos sensibles en BD
✅ No mostrar información detallada en errores

```php
// En producción
ini_set('display_errors', 0);
error_reporting(0);
```

---

#### 6. Control de Acceso Roto

**Prevención:**
✅ Verificar autenticación en CADA página
✅ Verificar autorización (permisos)
✅ Validar que el usuario sea dueño del recurso

```php
// Verificar que el usuario pueda ver este registro
if (!puede_ver_recurso('ventas', $venta_id)) {
    mensaje_error('No tienes acceso a esta venta');
    redirigir('dashboard.php');
}
```

---

#### 7. Configuración Incorrecta de Seguridad

**Prevención:**
✅ Deshabilitar listado de directorios
✅ Ocultar versión de PHP/Apache
✅ Configurar `.htaccess` correctamente
✅ Permisos correctos en archivos

```apache
# .htaccess
Options -Indexes
ServerSignature Off
```

---

#### 8. Deserialización Insegura

**Prevención:**
✅ Evitar `unserialize()` de datos no confiables
✅ Usar JSON en lugar de serialize()

```php
// BIEN: Usar JSON
$data = json_encode($array);
$array = json_decode($data, true);
```

---

#### 9. Logging y Monitoreo Insuficiente

**Prevención:**
✅ Loguear todos los eventos de seguridad
✅ Loguear intentos fallidos de login
✅ Loguear cambios en datos críticos

```php
escribir_log("Login fallido: $email", 'WARNING');
escribir_log("Acceso denegado a: $url", 'WARNING');
```

---

#### 10. Falta de Validación de Input

**Prevención:**
✅ Validar TODO input del usuario
✅ Validar tipo de dato
✅ Validar longitud
✅ Validar formato

```php
// Validar email
if (!validar_email($email)) {
    $errores[] = 'Email inválido';
}

// Validar longitud
if (strlen($nombre) < 3 || strlen($nombre) > 100) {
    $errores[] = 'Nombre debe tener entre 3 y 100 caracteres';
}

// Validar rango numérico
if (!validar_rango($edad, 18, 120)) {
    $errores[] = 'Edad debe estar entre 18 y 120';
}
```

---

### 15.2 Checklist de Seguridad

**Antes de pasar a producción:**

- [ ] Todas las queries usan prepared statements
- [ ] Todas las entradas son sanitizadas
- [ ] Todas las salidas son escapadas
- [ ] Tokens CSRF implementados
- [ ] Sesiones configuradas correctamente
- [ ] HTTPS habilitado
- [ ] Errores ocultos en producción
- [ ] `.htaccess` configurado
- [ ] `config.php` no en Git
- [ ] Permisos de archivos correctos (644 archivos, 755 directorios)
- [ ] Passwords hasheados con bcrypt
- [ ] Rate limiting en login
- [ ] Logging implementado
- [ ] Backup automático de BD

---

## 16. MANEJO DE ERRORES

### 16.1 Niveles de Error

```php
<?php
// En development: Mostrar todos los errores
if (DEVELOPMENT_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    // En production: Ocultar, solo loguear
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOGS_PATH . 'php-errors.log');
}
?>
```

### 16.2 Manejo de Excepciones

```php
<?php
try {
    // Código que puede fallar
    $resultado = db_execute($sql, $params);
    
    if (!$resultado) {
        throw new Exception('No se pudo ejecutar la operación');
    }
    
    mensaje_exito('Operación exitosa');
    
} catch (PDOException $e) {
    // Error de base de datos
    escribir_log("Error de BD: " . $e->getMessage(), 'ERROR');
    mensaje_error('Error al conectar con la base de datos');
    
} catch (Exception $e) {
    // Otros errores
    escribir_log("Error general: " . $e->getMessage(), 'ERROR');
    mensaje_error('Ocurrió un error inesperado');
}
?>
```

### 16.3 Página de Error Personalizada

**Archivo:** `error.php`

```php
<?php
$error_code = $_GET['code'] ?? '500';
$error_title = $_GET['title'] ?? 'Error';
$error_message = $_GET['message'] ?? 'Ha ocurrido un error inesperado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error <?php echo $error_code; ?></title>
    <style>
        body {
            font-family: Arial;
            text-align: center;
            padding: 50px;
            background: #f5f5f5;
        }
        .error-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            max-width: 600px;
            margin: 0 auto;
        }
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-code"><?php echo $error_code; ?></div>
        <h1><?php echo $error_title; ?></h1>
        <p><?php echo $error_message; ?></p>
        <a href="dashboard.php">Volver al Dashboard</a>
    </div>
</body>
</html>
```

---

## 17. TESTING BÁSICO

### 17.1 Tipos de Tests

#### Unit Tests (Unitarios)
Prueban funciones individuales.

Ya implementado en: `tests/test-auth.php`

#### Integration Tests (Integración)
Prueban interacción entre componentes.

**Ejemplo:** `tests/test-venta-completa.php`

```php
<?php
/**
 * Test de flujo completo de venta
 */
require_once '../config.php';
require_once '../includes/db.php';
require_once '../includes/funciones.php';

echo "<h1>Test: Flujo Completo de Venta</h1>";

try {
    // 1. Verificar que existe el cliente
    $cliente = db_query_one("SELECT * FROM clientes WHERE id = 1");
    assert(!empty($cliente), 'Cliente debe existir');
    echo "✅ Cliente encontrado: {$cliente['nombre']}<br>";
    
    // 2. Verificar que existe el producto
    $producto = db_query_one("SELECT * FROM productos WHERE id = 1");
    assert(!empty($producto), 'Producto debe existir');
    echo "✅ Producto encontrado: {$producto['nombre']}<br>";
    
    // 3. Verificar stock disponible
    $inventario = db_query_one("SELECT cantidad FROM inventario WHERE producto_id = 1 AND sucursal_id = 1");
    assert($inventario['cantidad'] > 0, 'Debe haber stock disponible');
    echo "✅ Stock disponible: {$inventario['cantidad']}<br>";
    
    // 4. Simular creación de venta (sin hacerla realmente)
    $subtotal = $producto['precio'] * 2;
    $descuento = 0;
    $total = $subtotal - $descuento;
    
    echo "✅ Cálculos correctos: Subtotal Q" . formato_dinero($subtotal) . ", Total Q" . formato_dinero($total) . "<br>";
    
    echo "<br><strong>✅ Todos los tests de integración pasaron!</strong>";
    
} catch (AssertionError $e) {
    echo "<br>❌ <strong>Test falló:</strong> " . $e->getMessage();
}
?>
```

#### Manual Tests (Manuales)
Pruebas hechas por humanos.

**Crear:** `tests/manual-test-checklist.md`

```markdown
# Checklist de Tests Manuales

## Login
- [ ] Login con credenciales correctas funciona
- [ ] Login con credenciales incorrectas muestra error
- [ ] Login sin completar campos muestra error
- [ ] Después de 5 intentos fallidos, bloquea temporalmente
- [ ] Logout funciona correctamente

## Dashboard
- [ ] Dashboard muestra estadísticas correctas
- [ ] Estadísticas varían según rol
- [ ] Menú se ajusta según permisos

## Permisos
- [ ] Usuario sin permiso no puede acceder a módulos restringidos
- [ ] Redirección funciona correctamente
- [ ] Mensajes de error se muestran apropiadamente

## Sesiones
- [ ] Sesión expira después de inactividad
- [ ] Mensaje de sesión expirada se muestra
- [ ] Redirección a login funciona
```

---

### 17.2 Automatización de Tests

**Archivo:** `tests/run-all-tests.php`

```php
<?php
/**
 * Ejecuta todos los tests automáticamente
 */

$tests = [
    'test-auth.php',
    'test-venta-completa.php',
    // Agregar más tests aquí
];

echo "<h1>🧪 Ejecutando Todos los Tests</h1>";

$total_tests = count($tests);
$tests_passed = 0;

foreach ($tests as $test) {
    echo "<h2>Ejecutando: $test</h2>";
    
    ob_start();
    include $test;
    $output = ob_get_clean();
    
    // Verificar si el test pasó (buscar "✅")
    if (strpos($output, '✅') !== false && strpos($output, '❌') === false) {
        $tests_passed++;
        echo "<p style='color: green;'>✅ Test pasó</p>";
    } else {
        echo "<p style='color: red;'>❌ Test falló</p>";
    }
    
    echo $output;
    echo "<hr>";
}

echo "<h2>Resumen</h2>";
echo "<p>Tests ejecutados: $total_tests</p>";
echo "<p>Tests pasados: $tests_passed</p>";
echo "<p>Tests fallados: " . ($total_tests - $tests_passed) . "</p>";

if ($tests_passed === $total_tests) {
    echo "<p style='color: green; font-weight: bold;'>✅ ¡Todos los tests pasaron!</p>";
} else {
    echo "<p style='color: orange; font-weight: bold;'>⚠️ Algunos tests fallaron. Revisar.</p>";
}
?>
```

---

## 18. DOCUMENTACIÓN

### 18.1 Documentar Funciones con PHPDoc

```php
<?php
/**
 * Descripción breve de la función
 * 
 * Descripción más detallada si es necesario.
 * Puede ocupar múltiples líneas.
 * 
 * @param string $param1 Descripción del parámetro 1
 * @param int $param2 Descripción del parámetro 2
 * @param array $param3 Descripción del parámetro 3 (opcional)
 * @return bool Descripción de lo que retorna
 * @throws Exception Descripción de cuándo lanza excepción
 * 
 * @example
 * $resultado = mi_funcion('valor1', 123, ['key' => 'value']);
 */
function mi_funcion($param1, $param2, $param3 = []) {
    // Código
}
?>
```

### 18.2 README.md del Proyecto

**Archivo:** `README.md` (raíz)

```markdown
# Nombre del Sistema

Sistema de gestión desarrollado con PHP vanilla + MySQL.

## Requisitos

- PHP 8.0+
- MySQL 5.7+ o MariaDB 10.3+
- Apache con mod_rewrite
- Extensiones PHP: PDO, pdo_mysql, mbstring

## Instalación

1. Clonar el repositorio
2. Copiar `config.example.php` a `config.php`
3. Editar `config.php` con tus datos de BD
4. Importar `database/schema.sql` en MySQL
5. Importar `database/seed.sql` (datos de prueba)
6. Acceder a `http://localhost/tu-proyecto`

## Credenciales de Prueba

- **Admin:** admin@empresa.com / 123456
- **Vendedor:** vendedor@empresa.com / 123456

## Estructura del Proyecto

```
proyecto/
├── includes/       # Archivos PHP core
├── models/         # Modelos de datos
├── modules/        # Módulos funcionales
├── assets/         # CSS, JS, imágenes
├── uploads/        # Archivos subidos
├── logs/           # Logs del sistema
└── tests/          # Tests
```

## Desarrollo

- **Fase actual:** Fase 1 completada
- **Próxima fase:** Fase 2 - Módulos específicos

## Licencia

Propietario - Todos los derechos reservados
```

---

## 19. CHECKLIST DE COMPLETITUD

### 19.1 Checklist Completo Fase 1

#### ✅ ESTRUCTURA

- [ ] Todas las carpetas creadas
- [ ] `.gitignore` configurado
- [ ] Archivos `.gitkeep` en carpetas vacías
- [ ] `README.md` actualizado

#### ✅ CONFIGURACIÓN

- [ ] `config.php` creado y configurado
- [ ] `config.example.php` creado
- [ ] Todas las constantes definidas
- [ ] Zona horaria configurada
- [ ] Manejo de errores según entorno

#### ✅ BASE DE DATOS

- [ ] `schema.sql` ejecutado
- [ ] `seed.sql` creado y ejecutado
- [ ] Conexión PDO funcional
- [ ] Funciones helper de BD implementadas
- [ ] Datos de prueba realistas cargados

#### ✅ AUTENTICACIÓN

- [ ] `includes/auth.php` implementado
- [ ] `login.php` funcional y con diseño
- [ ] `logout.php` funcional
- [ ] `dashboard.php` protegido y funcional
- [ ] `index.php` con redirecciones
- [ ] Sesiones seguras configuradas
- [ ] Passwords hasheados con bcrypt
- [ ] Logout completo (destruye sesión y cookie)

#### ✅ AUTORIZACIÓN

- [ ] Sistema de roles implementado
- [ ] Función `tiene_rol()` funcional
- [ ] Función `tiene_permiso()` funcional
- [ ] `requiere_rol()` funciona
- [ ] Dashboard dinámico según rol
- [ ] Menú dinámico implementado

#### ✅ FUNCIONES HELPER

- [ ] `includes/funciones.php` con 25+ funciones
- [ ] Funciones de validación
- [ ] Funciones de formato
- [ ] Funciones de seguridad
- [ ] Mensajes flash implementados
- [ ] Sistema de logging

#### ✅ MIDDLEWARE Y SEGURIDAD

- [ ] `includes/middleware.php` creado
- [ ] Protección CSRF implementada
- [ ] Rate limiting en login
- [ ] `.htaccess` configurado
- [ ] Prepared statements en todas las queries
- [ ] XSS prevention implementado

#### ✅ TESTING

- [ ] `tests/test-auth.php` creado
- [ ] Todos los tests pasando (100%)
- [ ] Tests de integración creados
- [ ] Checklist de tests manuales

#### ✅ DOCUMENTACIÓN

- [ ] PHPDoc en funciones principales
- [ ] `FASE-1-COMPLETADA.md` creado
- [ ] `README.md` actualizado
- [ ] Comentarios en código complejo

#### ✅ GIT

- [ ] Repositorio inicializado
- [ ] `.gitignore` funcionando
- [ ] Commits incrementales hechos
- [ ] Tag v1.0-fase1 creado

---

### 19.2 Criterios de Aceptación

**La Fase 1 está 100% completa cuando:**

1. ✅ Un usuario puede hacer login con email/password
2. ✅ El login verifica credenciales contra la BD
3. ✅ Passwords están hasheados con bcrypt
4. ✅ La sesión se crea correctamente al hacer login
5. ✅ El usuario es redirigido al dashboard después de login
6. ✅ El dashboard muestra el nombre del usuario
7. ✅ El dashboard muestra estadísticas básicas
8. ✅ El contenido del dashboard varía según el rol
9. ✅ El usuario puede cerrar sesión
10. ✅ Al cerrar sesión, es redirigido al login
11. ✅ Páginas protegidas redirigen a login si no hay sesión
12. ✅ Usuarios sin permisos no pueden acceder a páginas restringidas
13. ✅ Todos los tests automáticos pasan
14. ✅ No hay errores en el log de PHP
15. ✅ El código está documentado
16. ✅ El proyecto está en Git con commits lógicos

**Si TODO lo anterior funciona: ✅ Fase 1 COMPLETADA**

---

## 20. ERRORES COMUNES

### 20.1 Errores de Conexión a BD

#### Error: "Access denied for user"

**Causa:** Usuario/password incorrecto en `config.php`

**Solución:**
```php
// Verificar en config.php
define('DB_USER', 'root');  // Usuario correcto
define('DB_PASS', '');      // Password correcto
```

---

#### Error: "Unknown database"

**Causa:** Base de datos no existe

**Solución:**
```sql
CREATE DATABASE nombre_base_datos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

#### Error: "Connection refused" (puerto)

**Causa:** Puerto incorrecto

**Solución:**
```php
// Verificar puerto en config.php
define('DB_PORT', '3306');  // o 3307 si hay conflicto
```

---

### 20.2 Errores de Sesión

#### Error: "Headers already sent"

**Causa:** Output antes de `session_start()` o `header()`

**Solución:**
- No debe haber NADA antes de `<?php`
- No debe haber espacios antes de `<?php`
- Verificar que archivos estén en UTF-8 sin BOM

```php
<?php  // ← NO espacios antes
require_once 'config.php';  // ← session_start() está aquí
```

---

#### Error: Sesión no persiste

**Causa:** `session_start()` no se llama

**Solución:**
Asegurarse que `config.php` tiene:
```php
session_start();
```

---

### 20.3 Errores de Autenticación

#### Error: Password no coincide

**Causa:** Hash incorrecto en seed.sql

**Solución:**
Generar hash correcto:
```php
<?php
echo password_hash('123456', PASSWORD_DEFAULT);
?>
```

Actualizar en seed.sql.

---

#### Error: Login funciona pero luego pide login de nuevo

**Causa:** Sesión no se guarda o timeout muy corto

**Solución:**
```php
// En config.php, aumentar timeout
define('SESSION_TIMEOUT', 7200); // 2 horas
```

---

### 20.4 Errores de Permisos

#### Error: "Permission denied" en Linux

**Causa:** Permisos de archivos incorrectos

**Solución:**
```bash
# Permisos correctos
chmod 755 -R .
chmod 644 *.php
chmod 777 uploads/
chmod 777 logs/
```

---

### 20.5 Errores de .htaccess

#### Error: "Internal Server Error"

**Causa:** Sintaxis incorrecta en `.htaccess`

**Solución:**
- Verificar que `mod_rewrite` esté habilitado
- Comentar todo el `.htaccess` y descomentar línea por línea para encontrar el problema

---

### 20.6 Errores de Includes

#### Error: "Failed to open stream: No such file or directory"

**Causa:** Ruta incorrecta en `require_once`

**Solución:**
```php
// Usar rutas relativas desde el archivo actual
require_once __DIR__ . '/../config.php';

// O usar rutas absolutas con constantes
require_once ROOT_PATH . '/includes/auth.php';
```

---

## 21. PREPARACIÓN PARA FASE 2

### 21.1 ¿Qué sigue en Fase 2?

**Fase 2: Backend - Módulos Específicos**

Desarrollar la lógica de negocio de cada módulo:
- Inventario
- Ventas/POS
- Clientes
- Caja
- Taller (si aplica)
- Proveedores
- etc.

**Duración estimada:** 2-3 semanas

---

### 21.2 Archivos que Claude Necesitará

Para iniciar Fase 2 eficientemente, tener listos:

1. ✅ `FASE-0-COMPLETADA.md` (diseño completo)
2. ✅ `FASE-1-COMPLETADA.md` (implementación base)
3. ✅ `database/schema.sql` (estructura de BD)
4. ✅ `config.php` (configuración actual)
5. ✅ `includes/auth.php` (funciones de auth)
6. ✅ `includes/funciones.php` (helpers)
7. ✅ Lista de prioridades de módulos

---

### 21.3 Prompt Sugerido para Iniciar Fase 2

```
Hola Claude, voy a iniciar la FASE 2 de mi proyecto.

**Proyecto:** [Nombre del Sistema]

**Contexto:**
- ✅ Fase 0 completada (diseño de BD con [X] tablas)
- ✅ Fase 1 completada (arquitectura base y autenticación funcional)

**Stack Tecnológico:**
- PHP 8.2
- MySQL/MariaDB
- Bootstrap 5
- JavaScript Vanilla

**Estructura actual:**
[Describir brevemente la estructura de carpetas]

**Sistema de autenticación:**
- Login/Logout funcional
- 6 roles implementados: [listar roles]
- Sesiones seguras
- Permisos granulares

**Prioridad de Módulos para Fase 2:**
1. [Módulo más crítico] - URGENTE
2. [Segundo módulo]
3. [Tercer módulo]

**Archivos de referencia:**
Adjunto:
1. FASE-0-COMPLETADA.md (diseño completo)
2. FASE-1-COMPLETADA.md (implementación actual)
3. database/schema.sql (estructura BD)

**¿Qué necesito?**
Guía paso a paso para implementar el módulo [NOMBRE DEL MÓDULO]:
- Crear modelo (models/[Modelo].php)
- Crear vistas (modules/[modulo]/)
- CRUD completo
- Validaciones
- Seguridad
- Tests básicos

**Metodología:**
- Un archivo a la vez
- Código completo y funcional
- Explicaciones del "por qué"
- Enfoque en mejores prácticas

¿Listo para empezar con el módulo [NOMBRE]?
```

---

### 21.4 Estructura Esperada de un Módulo en Fase 2

```
modules/inventario/
├── index.php           # Listado de productos
├── nuevo.php          # Crear producto
├── editar.php         # Editar producto
├── ver.php            # Ver detalles
├── eliminar.php       # Eliminar/desactivar
└── acciones.php       # Procesar formularios (opcional)

models/
└── Producto.php       # Lógica de negocio

api/inventario/
├── listar.php         # GET - Lista JSON
├── crear.php          # POST - Crear
├── actualizar.php     # PUT - Actualizar
└── eliminar.php       # DELETE - Eliminar
```

---

### 21.5 Lecciones Aprendidas para Fase 2

**De proyectos reales (Joyería, Clínica):**

✅ **Empezar por el módulo más crítico**
- Identifica el módulo que el cliente más necesita
- Implementa ese primero completamente
- Luego los demás

✅ **Un componente a la vez**
- No saltar entre módulos
- Termina completamente uno antes de empezar otro
- Evita "módulos a medias"

✅ **Probar constantemente**
- Después de cada función, probarla
- No acumular código sin probar
- Detectar bugs temprano

✅ **Reutilizar código de Fase 1**
- Usar funciones helper creadas
- Seguir los patrones establecidos
- Mantener consistencia

✅ **Documentar mientras desarrollas**
- No dejar documentación para el final
- Documentar decisiones importantes
- Mantener `README.md` actualizado

✅ **Commits frecuentes**
- Commit después de cada funcionalidad
- Commits descriptivos
- Fácil hacer rollback si algo falla

---

## 📚 RECURSOS ADICIONALES

### Documentación Oficial

- **PHP:** https://www.php.net/manual/es/
- **MySQL:** https://dev.mysql.com/doc/
- **PDO:** https://www.php.net/manual/es/book.pdo.php
- **Bootstrap 5:** https://getbootstrap.com/docs/5.3/

### Herramientas Útiles

- **XAMPP:** https://www.apachefriends.org/
- **VS Code:** https://code.visualstudio.com/
- **HeidiSQL:** https://www.heidisql.com/
- **Git:** https://git-scm.com/

### Seguridad

- **OWASP Top 10:** https://owasp.org/www-project-top-ten/
- **PHP Security Guide:** https://phptherightway.com/#security

---

## 📝 NOTAS FINALES

### Tiempo Estimado Total Fase 1

| Desarrollador | Tiempo Estimado |
|---------------|----------------|
| **Junior** (< 1 año exp) | 5-7 días |
| **Mid** (1-3 años exp) | 3-5 días |
| **Senior** (3+ años exp) | 2-3 días |

### Diferencias por Complejidad

**Proyecto Simple** (< 10 tablas):
- Sistema de roles básico
- 1-2 módulos
- 3-4 días

**Proyecto Mediano** (10-25 tablas):
- Sistema de roles complejo
- 5-8 módulos
- 4-5 días

**Proyecto Complejo** (25+ tablas):
- Múltiples sucursales
- Permisos granulares
- 8+ módulos
- 5-7 días

---

## ✅ CONCLUSIÓN

Al completar esta **FASE 1** siguiendo esta metodología, tendrás:

1. ✅ **Arquitectura backend sólida y profesional**
2. ✅ **Sistema de autenticación robusto y seguro**
3. ✅ **Roles y permisos implementados**
4. ✅ **Código base reutilizable para todo el proyecto**
5. ✅ **Datos de prueba para desarrollo**
6. ✅ **Fundamento sólido para Fase 2**

**Tu proyecto estará listo para escalar y desarrollar módulos específicos con confianza.**

---

## 🎯 PRÓXIMOS PASOS INMEDIATOS

1. **Verificar** que cumples el checklist de completitud al 100%
2. **Ejecutar** todos los tests y verificar que pasan
3. **Documentar** `FASE-1-COMPLETADA.md` con tus hallazgos
4. **Hacer commit final** de Fase 1
5. **Descansar** - has trabajado duro
6. **Planificar Fase 2** - definir prioridades de módulos

---

**Última actualización:** Enero 2026  
**Versión:** 1.0  
**Basado en:** Proyectos reales (Joyería Torre Fuerte, Sistema de Clínica)

═══════════════════════════════════════════════════════════
    📘 METODOLOGÍA PROFESIONAL DE DESARROLLO - FASE 1
           ARQUITECTURA BASE Y AUTENTICACIÓN
═══════════════════════════════════════════════════════════

**Esta guía es la referencia oficial para la Fase 1 de TODOS los proyectos futuros.**
