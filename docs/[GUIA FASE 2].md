# 📦 METODOLOGÍA PROFESIONAL DE DESARROLLO - FASE 2
## Desarrollo de Módulos Backend y Lógica de Negocio

**Versión:** 1.0  
**Fecha:** Enero 2026  
**Basado en:** Proyecto Joyería Torre Fuerte (13 módulos, 195+ tests, 100% éxito)  
**Stack:** PHP 8+ | MySQL | Prepared Statements | PHPDoc

---

## 📋 ÍNDICE

1. [¿Qué es la Fase 2?](#1-qué-es-la-fase-2)
2. [Preparación](#2-preparación)
3. [Metodología: Un Módulo a la Vez](#3-metodología-un-módulo-a-la-vez)
4. [Orden de Implementación](#4-orden-de-implementación)
5. [Estructura de un Módulo](#5-estructura-de-un-módulo)
6. [Paso 1: Análisis del Módulo](#6-paso-1-análisis-del-módulo)
7. [Paso 2: Implementación del Modelo](#7-paso-2-implementación-del-modelo)
8. [Paso 3: Tests Automatizados](#8-paso-3-tests-automatizados)
9. [Patrones de Código Esenciales](#9-patrones-de-código-esenciales)
10. [Validaciones](#10-validaciones)
11. [Transacciones SQL](#11-transacciones-sql)
12. [Checklist de Completitud](#12-checklist-de-completitud)
13. [Errores Comunes](#13-errores-comunes)
14. [Preparación para Fase 3](#14-preparación-para-fase-3)

---

## 1. ¿QUÉ ES LA FASE 2?

### Definición

Implementar **TODA la lógica de negocio** del sistema. Convertir cada tabla de la BD en un **modelo PHP funcional** con CRUD completo y operaciones específicas.

**Metáfora:** Fase 0 = plano, Fase 1 = cimientos, **Fase 2 = construir todas las habitaciones del edificio**.

### Objetivos

Al completar esta fase tendrás:

- ✅ **Backend 100% funcional** - Todos los modelos PHP implementados
- ✅ **Tests automatizados** - Mínimo 10 tests por módulo
- ✅ **Código de calidad** - Prepared statements, validaciones, manejo de errores
- ✅ **Base para Fase 3** - APIs listas para consumir desde frontend

### Duración

**Cálculo:** `3-7 días por módulo` según complejidad

| Complejidad | Tiempo | Ejemplo |
|-------------|--------|---------|
| SIMPLE | 3-4 días | Categorías, Sucursales |
| MEDIA | 4-5 días | Productos, Clientes |
| COMPLEJA | 5-7 días | Ventas, Inventario, Créditos |

**Proyecto típico (10 módulos):** 6-8 semanas

---

## 2. PREPARACIÓN

### Pre-requisitos OBLIGATORIOS

Antes de iniciar, debes tener:

- ✅ **Fase 0 completada** - BD diseñada y creada
- ✅ **Fase 1 completada** - Auth, permisos, funciones helper
- ✅ **Archivos base listos:**
  - `config.php`
  - `includes/db.php`
  - `includes/funciones.php`
  - `includes/auth.php`
  - `base_datos.sql`

### Estructura de Carpetas

```
proyecto/
├── models/              # ⭐ MODELOS (Fase 2)
│   ├── Usuario.php
│   ├── Producto.php
│   ├── Inventario.php
│   └── ...
│
├── tests/               # ⭐ TESTS (Fase 2)
│   ├── index.php
│   ├── test-producto.php
│   └── ...
│
├── includes/            # Ya creados en Fase 1
├── modules/             # Frontend (Fase 3)
└── database/
```

### Inventario de Módulos

Lista TODOS tus módulos con prioridad y complejidad:

| # | Módulo | Prioridad | Complejidad | Días |
|---|--------|-----------|-------------|------|
| 1 | Usuario | CRÍTICO | SIMPLE | 3 |
| 2 | Producto | CRÍTICO | MEDIA | 4 |
| 3 | Inventario | CRÍTICO | COMPLEJA | 6 |
| 4 | Venta | CRÍTICO | COMPLEJA | 7 |
| ... | ... | ... | ... | ... |

---

## 3. METODOLOGÍA: UN MÓDULO A LA VEZ

### ❌ ERROR: Empezar todos simultáneamente

```
models/
├── usuario.php      (70% completo)
├── producto.php     (40% completo)
├── venta.php        (10% completo)
└── cliente.php      (5% completo)
```

**Problemas:** Nada funcional, no puedes probar, pierdes contexto, bugs ocultos

### ✅ CORRECTO: 100% uno antes del siguiente

```
models/
├── usuario.php      ✅ 100% + Tests
├── producto.php     ✅ 100% + Tests
├── inventario.php   🔄 80% (en progreso)
├── venta.php        ⏳ Pendiente
└── cliente.php      ⏳ Pendiente
```

**Ventajas:** Funcional desde día 1, pruebas constantes, momentum positivo

### Definición de "100% Completo"

Un módulo está completo cuando tiene:

- ✅ Modelo PHP con todos los métodos (CRUD + específicos)
- ✅ PHPDoc completo
- ✅ Tests automatizados (mínimo 10)
- ✅ Todos los tests pasando al 100%
- ✅ Código probado manualmente
- ✅ Sin errores conocidos

---

## 4. ORDEN DE IMPLEMENTACIÓN

### Regla de Dependencias

Implementa primero los módulos de los que otros dependen:

```
Sucursales (no depende de nadie)
    ↓
Usuarios (necesita sucursal_id)
    ↓
Categorías
    ↓
Productos (necesita categoría)
    ↓
Inventario (necesita productos)
    ↓
Ventas (necesita productos + inventario)
```

### Orden Recomendado - Proyecto Joyería

Basado en experiencia real:

1. **Sucursal** - Sin dependencias, simple
2. **Categoría** - Sin dependencias, simple
3. **Usuario** - Necesita sucursal
4. **Producto** - Necesita categoría
5. **PrecioProducto** - Necesita producto
6. **Cliente** - Standalone
7. **Proveedor** - Standalone
8. **Inventario** - Necesita producto y sucursal
9. **TransferenciaInventario** - Necesita inventario
10. **Venta** - Necesita producto, inventario, cliente
11. **Credito** - Necesita venta y cliente
12. **Caja** - Necesita ventas
13. **Reporte** - Cross-table, al final

---

## 5. ESTRUCTURA DE UN MÓDULO

### Anatomía Completa

```php
<?php
/**
 * Modelo [Nombre]
 * 
 * [Descripción breve de qué hace]
 * 
 * @author Sistema [Proyecto]
 * @version 1.0
 * @date 2026-01-XX
 */

class NombreModelo {
    
    // ==============================
    // CONSTANTES
    // ==============================
    const CONSTANTE = 'valor';
    
    // ==============================
    // CRUD BÁSICO (5 obligatorios)
    // ==============================
    public static function crear($datos) { }
    public static function obtenerPorId($id) { }
    public static function listar($filtros = []) { }
    public static function editar($id, $datos) { }
    public static function eliminar($id) { }
    
    // ==============================
    // MÉTODOS ESPECÍFICOS
    // ==============================
    public static function metodoPropioDelNegocio() { }
    
    // ==============================
    // VALIDACIONES PRIVADAS
    // ==============================
    private static function validar($datos) { }
}
```

### Métodos CRUD Obligatorios

**1. CREAR**
```php
public static function crear($datos) {
    try {
        // 1. Validar
        $errores = self::validar($datos);
        if (!empty($errores)) {
            throw new Exception(implode(', ', $errores));
        }
        
        // 2. Insertar
        $sql = "INSERT INTO tabla (campo1, campo2) VALUES (?, ?)";
        $resultado = db_execute($sql, [$datos['campo1'], $datos['campo2']]);
        
        // 3. Auditoría
        if ($resultado) {
            registrar_auditoria('tabla', 'INSERT', $resultado, "Creado");
        }
        
        return $resultado;
        
    } catch (Exception $e) {
        registrar_error("Error: " . $e->getMessage());
        return false;
    }
}
```

**2. OBTENER POR ID**
```php
public static function obtenerPorId($id) {
    try {
        $sql = "SELECT * FROM tabla WHERE id = ?";
        return db_query_one($sql, [$id]);
    } catch (Exception $e) {
        registrar_error("Error: " . $e->getMessage());
        return false;
    }
}
```

**3. LISTAR CON FILTROS**
```php
public static function listar($filtros = []) {
    try {
        $where = [];
        $params = [];
        
        if (isset($filtros['activo'])) {
            $where[] = "activo = ?";
            $params[] = $filtros['activo'];
        }
        
        if (isset($filtros['buscar'])) {
            $where[] = "nombre LIKE ?";
            $params[] = "%{$filtros['buscar']}%";
        }
        
        $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT * FROM tabla $where_sql ORDER BY nombre ASC";
        
        return db_query($sql, $params);
        
    } catch (Exception $e) {
        registrar_error("Error: " . $e->getMessage());
        return [];
    }
}
```

**4. EDITAR**
```php
public static function editar($id, $datos) {
    try {
        $errores = self::validar($datos);
        if (!empty($errores)) {
            throw new Exception(implode(', ', $errores));
        }
        
        $sql = "UPDATE tabla SET campo1 = ?, campo2 = ? WHERE id = ?";
        $resultado = db_execute($sql, [$datos['campo1'], $datos['campo2'], $id]);
        
        if ($resultado) {
            registrar_auditoria('tabla', 'UPDATE', $id, "Actualizado");
        }
        
        return $resultado;
        
    } catch (Exception $e) {
        registrar_error("Error: " . $e->getMessage());
        return false;
    }
}
```

**5. ELIMINAR (Soft Delete)**
```php
public static function eliminar($id) {
    try {
        // Soft delete - marcar como inactivo
        $sql = "UPDATE tabla SET activo = 0 WHERE id = ?";
        $resultado = db_execute($sql, [$id]);
        
        if ($resultado) {
            registrar_auditoria('tabla', 'DELETE', $id, "Eliminado (soft)");
        }
        
        return $resultado;
        
    } catch (Exception $e) {
        registrar_error("Error: " . $e->getMessage());
        return false;
    }
}
```

---

## 6. PASO 1: ANÁLISIS DEL MÓDULO

### Preguntas Clave

Antes de programar, responde:

1. **¿Qué entidad representa?** - Producto, Cliente, Venta, etc.
2. **¿Qué tablas usa?** - Principal + relacionadas
3. **¿Qué operaciones CRUD necesita?** - Crear, leer, actualizar, eliminar
4. **¿Qué lógica específica tiene?** - Cálculos, validaciones especiales
5. **¿Qué relaciones tiene?** - Otras tablas que afecta o consulta

### Plantilla de Análisis

```markdown
# ANÁLISIS: Módulo [NOMBRE]

## DESCRIPCIÓN
[Qué es y para qué sirve]

## TABLAS
- Principal: `tabla_principal`
- Relacionadas: `tabla1`, `tabla2`

## OPERACIONES
- [x] Crear
- [x] Leer (individual)
- [x] Listar (todos)
- [x] Actualizar
- [x] Eliminar
- [x] [Operación especial 1]

## VALIDACIONES
- campo1: Requerido, mínimo 3 caracteres
- campo2: Único, formato XXX-NNN

## COMPLEJIDAD
[x] SIMPLE / [ ] MEDIA / [ ] COMPLEJA

## TIEMPO ESTIMADO
[X] días
```

---

## 7. PASO 2: IMPLEMENTACIÓN DEL MODELO

### Ciclo de Implementación

```
1. Crear archivo models/[Nombre].php
2. Implementar CRUD básico (5 métodos)
3. Probar manualmente cada método
4. Implementar métodos específicos
5. Agregar validaciones
6. Agregar PHPDoc
7. Revisión final
```

### Estándares de Código

#### ✅ CORRECTO

```php
// Prepared statements SIEMPRE
$sql = "SELECT * FROM productos WHERE id = ?";
db_query_one($sql, [$id]);

// Try-catch en métodos públicos
public static function crear($datos) {
    try {
        // ... código ...
    } catch (Exception $e) {
        registrar_error(...);
        return false;
    }
}

// PHPDoc completo
/**
 * Crea un nuevo producto
 * 
 * @param array $datos Datos del producto
 * @return int|false ID creado o false
 */
public static function crear($datos) { }
```

#### ❌ INCORRECTO

```php
// Sin prepared statements
$sql = "SELECT * FROM productos WHERE id = $id"; // ⚠️ SQL Injection

// Sin manejo de errores
public static function crear($datos) {
    $sql = "INSERT INTO ...";
    return db_execute($sql, $datos); // Sin try-catch
}

// Sin documentación
public static function crear($datos) { } // Sin PHPDoc
```

---

## 8. PASO 3: TESTS AUTOMATIZADOS

### Estructura de un Test

```php
<?php
/**
 * Tests para Modelo [Nombre]
 */

session_start();
$_SESSION['usuario_id'] = 1;
$_SESSION['usuario_rol'] = 'administrador';

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../models/[Nombre].php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>🧪 Test: [Nombre]</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-success { background-color: #d4edda; color: #155724; }
        .test-error { background-color: #f8d7da; color: #721c24; }
        .test-info { background-color: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">🧪 Test: Modelo [Nombre]</h1>
    
<?php
$tests_passed = 0;
$tests_failed = 0;

// TEST 1: Crear
echo '<div class="card mb-3"><div class="card-header"><h5>TEST 1: Crear</h5></div><div class="card-body">';
try {
    $id = NombreModelo::crear(['campo' => 'valor']);
    
    if ($id) {
        echo '<div class="alert test-success">✅ ÉXITO</div>';
        $tests_passed++;
    } else {
        echo '<div class="alert test-error">❌ ERROR</div>';
        $tests_failed++;
    }
} catch (Exception $e) {
    echo '<div class="alert test-error">❌ EXCEPCIÓN: ' . $e->getMessage() . '</div>';
    $tests_failed++;
}
echo '</div></div>';

// TEST 2, 3, 4... (mínimo 10 tests)

// RESUMEN
$total = $tests_passed + $tests_failed;
$porcentaje = round(($tests_passed / $total) * 100, 1);
echo '<div class="card">
        <div class="card-body">
            <h4>📊 Resumen</h4>
            <p>Exitosos: ' . $tests_passed . ' | Fallidos: ' . $tests_failed . ' | Total: ' . $total . ' | Éxito: ' . $porcentaje . '%</p>
        </div>
      </div>';
?>
</div>
</body>
</html>
```

### Tests Mínimos Obligatorios

| # | Test | Qué Verifica |
|---|------|--------------|
| 1 | Crear | Puede crear registro |
| 2 | Validar duplicados | Rechaza duplicados |
| 3 | Obtener por ID | Recupera registro |
| 4 | Editar | Actualiza registro |
| 5 | Eliminar | Desactiva registro |
| 6 | Listar | Lista todos |
| 7 | Filtros | Filtros funcionan |
| 8 | Validar datos inválidos | Rechaza inválidos |
| 9 | Método específico 1 | Funcionalidad propia |
| 10 | Método específico 2 | Funcionalidad propia |

### Índice de Tests (tests/index.php)

```php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>🧪 Suite de Tests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="text-center mb-5">🧪 Tests Automatizados</h1>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Productos</h5>
                    <a href="test-producto.php" class="btn btn-primary">Ejecutar</a>
                </div>
            </div>
        </div>
        <!-- Más módulos... -->
    </div>
</div>
</body>
</html>
```

---

## 9. PATRONES DE CÓDIGO ESENCIALES

### Patrón de Transacción

```php
public static function operacionCompleja($datos) {
    global $pdo;
    
    try {
        // 1. Validar ANTES de transacción
        $errores = self::validar($datos);
        if (!empty($errores)) {
            throw new Exception(implode(', ', $errores));
        }
        
        // 2. Iniciar transacción
        $pdo->beginTransaction();
        
        // 3. Ejecutar operaciones
        $id1 = db_execute($sql1, $params1);
        if (!$id1) throw new Exception('Falló paso 1');
        
        $id2 = db_execute($sql2, $params2);
        if (!$id2) throw new Exception('Falló paso 2');
        
        // 4. Confirmar
        $pdo->commit();
        
        // 5. Auditoría
        registrar_auditoria('tabla', 'INSERT', $id1, "Operación completada");
        
        return $id1;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        registrar_error("Error: " . $e->getMessage());
        return false;
    }
}
```

### Patrón de Soft Delete

```php
public static function eliminar($id) {
    try {
        // Verificar si tiene relaciones
        if (self::tieneRelaciones($id)) {
            throw new Exception('No se puede eliminar: tiene registros relacionados');
        }
        
        // Soft delete
        $sql = "UPDATE tabla SET activo = 0 WHERE id = ?";
        $resultado = db_execute($sql, [$id]);
        
        if ($resultado) {
            registrar_auditoria('tabla', 'DELETE', $id, "Desactivado");
        }
        
        return $resultado;
    } catch (Exception $e) {
        registrar_error("Error: " . $e->getMessage());
        return false;
    }
}
```

---

## 10. VALIDACIONES

### Método Validar Estándar

```php
/**
 * Valida los datos antes de guardar
 * 
 * @param array $datos Datos a validar
 * @return array Errores encontrados (vacío si OK)
 */
private static function validar($datos) {
    $errores = [];
    
    // Requerido
    if (empty($datos['nombre'])) {
        $errores[] = 'El nombre es requerido';
    }
    
    // Longitud
    if (strlen($datos['nombre']) < 3) {
        $errores[] = 'Mínimo 3 caracteres';
    }
    
    // Email
    if (!empty($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Email inválido';
    }
    
    // Número positivo
    if (isset($datos['precio']) && (!is_numeric($datos['precio']) || $datos['precio'] <= 0)) {
        $errores[] = 'Precio debe ser positivo';
    }
    
    // Unicidad
    if (self::codigoExiste($datos['codigo'])) {
        $errores[] = 'El código ya existe';
    }
    
    // Relación (FK)
    if (!self::categoriaExiste($datos['categoria_id'])) {
        $errores[] = 'La categoría no existe';
    }
    
    return $errores;
}
```

### Validaciones Comunes

```php
// Requerido
if (empty($datos['campo'])) {
    $errores[] = 'Campo requerido';
}

// Longitud mín/máx
if (strlen($datos['campo']) < 3 || strlen($datos['campo']) > 100) {
    $errores[] = 'Entre 3 y 100 caracteres';
}

// Email
if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'Email inválido';
}

// Numérico
if (!is_numeric($datos['campo'])) {
    $errores[] = 'Debe ser número';
}

// Positivo
if ($datos['campo'] <= 0) {
    $errores[] = 'Debe ser positivo';
}

// Rango
if ($datos['edad'] < 18 || $datos['edad'] > 100) {
    $errores[] = 'Entre 18 y 100';
}

// Enum
if (!in_array($datos['estado'], ['activo', 'inactivo'])) {
    $errores[] = 'Estado inválido';
}
```

---

## 11. TRANSACCIONES SQL

### ¿Cuándo Usar Transacciones?

Usa transacciones cuando una operación involucra **múltiples queries** que deben ejecutarse **todas o ninguna**.

**REQUIEREN transacción:**
- ✅ Venta (venta + detalle + inventario + caja)
- ✅ Transferencia inventario (origen + destino + registro)
- ✅ Crédito con abono (crédito + abono + saldo)

**NO requieren transacción:**
- ❌ Crear producto (single INSERT)
- ❌ Actualizar nombre (single UPDATE)
- ❌ Soft delete (single UPDATE)

### Ejemplo Real: Venta con Transacción

```php
public static function procesarVenta($datos) {
    global $pdo;
    
    try {
        // 1. Validar ANTES
        $errores = self::validar($datos);
        if (!empty($errores)) {
            throw new Exception(implode(', ', $errores));
        }
        
        // 2. Verificar stock
        foreach ($datos['productos'] as $item) {
            $stock = Inventario::obtenerStock($item['producto_id'], $datos['sucursal_id']);
            if ($stock < $item['cantidad']) {
                throw new Exception("Stock insuficiente");
            }
        }
        
        // 3. INICIAR TRANSACCIÓN
        $pdo->beginTransaction();
        
        // 4. Crear venta
        $venta_id = db_execute("INSERT INTO ventas (...) VALUES (...)", $params);
        if (!$venta_id) throw new Exception('Error crear venta');
        
        // 5. Guardar detalle
        foreach ($datos['productos'] as $item) {
            $detalle_id = db_execute("INSERT INTO detalle_ventas (...) VALUES (...)", $params);
            if (!$detalle_id) throw new Exception('Error guardar detalle');
            
            // 6. Descontar inventario
            $ok = Inventario::descontar($item['producto_id'], $datos['sucursal_id'], $item['cantidad']);
            if (!$ok) throw new Exception('Error actualizar inventario');
        }
        
        // 7. Registrar en caja
        Caja::registrarMovimiento(['tipo' => 'venta', 'monto' => $datos['total']]);
        
        // 8. CONFIRMAR
        $pdo->commit();
        
        // 9. Auditoría
        registrar_auditoria('ventas', 'INSERT', $venta_id, "Venta procesada");
        
        return $venta_id;
        
    } catch (Exception $e) {
        // ROLLBACK
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        registrar_error("Error venta: " . $e->getMessage());
        return false;
    }
}
```

---

## 12. CHECKLIST DE COMPLETITUD

### ✅ Fase 2 está 100% completa cuando:

#### POR MÓDULO

- [ ] **Modelo PHP implementado**
  - Archivo `models/[Nombre].php` creado
  - CRUD básico (5 métodos) funcionando
  - Métodos específicos del negocio implementados
  - PHPDoc completo en todos los métodos

- [ ] **Tests automatizados**
  - Archivo `tests/test-[nombre].php` creado
  - Mínimo 10 tests implementados
  - 100% tests pasando (0 errores)
  - Casos límite probados

- [ ] **Código de calidad**
  - Prepared statements en todas las queries
  - Try-catch en métodos públicos
  - Validaciones completas
  - Transacciones donde necesario
  - Auditoría en operaciones CRUD

- [ ] **Sin bugs conocidos**
  - Cero warnings PHP
  - Cero errores SQL
  - Todo funciona como esperado

#### GLOBAL DEL PROYECTO

- [ ] **Todos los módulos completados**
  - Lista de módulos de Fase 0 cubierta 100%
  - Cada módulo marcado como completo
  
- [ ] **Índice de tests funcionando**
  - `tests/index.php` con todos los módulos
  - Ejecución de tests desde interfaz web
  
- [ ] **Documentación**
  - Archivo `FASE-2-COMPLETADA.md` creado
  - README actualizado con módulos
  
- [ ] **Git organizado**
  - Commits descriptivos por módulo
  - Branch limpio sin código experimental

---

## 13. ERRORES COMUNES

### ❌ ERROR 1: Olvidar Prepared Statements

```php
// ❌ MAL
$sql = "SELECT * FROM productos WHERE id = $id";
db_query($sql);

// ✅ BIEN
$sql = "SELECT * FROM productos WHERE id = ?";
db_query($sql, [$id]);
```

### ❌ ERROR 2: No Usar Transacciones

```php
// ❌ MAL: Venta sin transacción
public static function crear($datos) {
    db_execute("INSERT INTO ventas ...");
    db_execute("INSERT INTO detalle_ventas ...");
    db_execute("UPDATE inventario ..."); // Si falla, venta queda a medias
}

// ✅ BIEN: Con transacción
public static function crear($datos) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        // ... todas las operaciones ...
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}
```

### ❌ ERROR 3: Sin Manejo de Errores

```php
// ❌ MAL
public static function crear($datos) {
    $sql = "INSERT INTO ...";
    return db_execute($sql, $datos); // Sin try-catch
}

// ✅ BIEN
public static function crear($datos) {
    try {
        $sql = "INSERT INTO ...";
        return db_execute($sql, $datos);
    } catch (Exception $e) {
        registrar_error("Error: " . $e->getMessage());
        return false;
    }
}
```

### ❌ ERROR 4: Validar Después de Insertar

```php
// ❌ MAL
public static function crear($datos) {
    $id = db_execute("INSERT ...");
    if (empty($datos['nombre'])) { // Ya se insertó!
        return false;
    }
}

// ✅ BIEN
public static function crear($datos) {
    $errores = self::validar($datos); // Validar PRIMERO
    if (!empty($errores)) {
        throw new Exception(...);
    }
    $id = db_execute("INSERT ...");
}
```

### ❌ ERROR 5: N+1 Problem

```php
// ❌ MAL: Query por cada producto
public static function listar() {
    $productos = db_query("SELECT * FROM productos");
    foreach ($productos as &$p) {
        $cat = db_query_one("SELECT nombre FROM categorias WHERE id = ?", [$p['categoria_id']]);
        $p['categoria'] = $cat['nombre']; // N+1 queries!
    }
    return $productos;
}

// ✅ BIEN: Un solo query con JOIN
public static function listar() {
    $sql = "SELECT p.*, c.nombre as categoria_nombre
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id";
    return db_query($sql);
}
```

---

## 14. PREPARACIÓN PARA FASE 3

### Documento FASE-2-COMPLETADA.md

Crear archivo en la raíz del proyecto:

```markdown
# FASE 2 - COMPLETADA ✅

## FECHA DE FINALIZACIÓN
[Fecha]

## RESUMEN

- **Total módulos:** 13
- **Líneas de código:** ~9,500
- **Tests implementados:** 150+
- **Tasa de éxito:** 100%

## MÓDULOS COMPLETADOS

1. ✅ Usuario - 10 tests
2. ✅ Sucursal - 10 tests
3. ✅ Producto - 12 tests
4. ✅ Inventario - 15 tests
5. ✅ Venta - 10 tests
... (listar todos)

## ARQUITECTURA FINAL

- Modelos PHP: 13 archivos
- Tests automatizados: 13 archivos
- Índice de tests: Funcionando
- Prepared statements: 100%
- Transacciones: Implementadas donde necesario
- Auditoría: Completa

## ESTADÍSTICAS

- Promedio: 730 líneas por modelo
- Promedio: 11 tests por módulo
- Complejidad promedio: Media
- Tiempo total: 8 semanas

## PENDIENTES PARA FASE 3

- Frontend de todos los módulos
- Integración con modelos
- Dashboard principal
- Diseño responsive

## ARCHIVOS IMPORTANTES

- `models/` - Todos los modelos
- `tests/` - Suite completa de tests
- `database/base_datos.sql` - Schema final
- `README.md` - Documentación actualizada

## PRÓXIMOS PASOS

Iniciar Fase 3: Frontend e Integración
```

### Archivos a Adjuntar para Fase 3

Al iniciar Fase 3 con Claude, adjuntar:

1. `FASE-2-COMPLETADA.md`
2. `base_datos.sql`
3. Un modelo de ejemplo (ej: `models/Producto.php`)
4. `config.php`
5. `includes/funciones.php`

### Prompt Sugerido para Fase 3

```
Hola Claude, completé la Fase 2 exitosamente.

CONTEXTO:
- Backend 100% funcional con 13 modelos
- 150+ tests automatizados, todos pasando
- Prepared statements, transacciones, auditoría completa

Adjunto:
- FASE-2-COMPLETADA.md (resumen completo)
- base_datos.sql
- models/Producto.php (modelo de ejemplo)

Ahora inicio FASE 3: Frontend e Integración

Stack para frontend:
- HTML5 + Bootstrap 5
- JavaScript vanilla
- AJAX para consumir modelos PHP

Por favor ayúdame a crear:
1. Estructura de carpetas para frontend
2. Plantillas base (header, footer, sidebar)
3. Dashboard principal
4. Primer módulo frontend (Productos)

Trabajemos paso a paso, completando cada componente antes del siguiente.
```

---

## CONCLUSIÓN

Has completado la Fase 2 cuando:
- ✅ Todos los modelos PHP funcionan
- ✅ Tests al 100%
- ✅ Código de calidad empresarial
- ✅ Documento FASE-2-COMPLETADA.md creado

**Siguiente paso:** Fase 3 - Frontend e Integración

---

**Fin de la Guía de Fase 2**
