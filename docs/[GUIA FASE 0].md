# 🎯 METODOLOGÍA PROFESIONAL DE DESARROLLO - FASE 0
## Guía para Planificación y Diseño de Proyectos de Software

**Versión:** 2.0  
**Última actualización:** Enero 2026  
**Basado en:** Proyecto exitoso "Sistema de Gestión - Joyería Torre Fuerte"

---

## 📋 TABLA DE CONTENIDOS

1. [Introducción](#introducción)
2. [¿Qué es la Fase 0?](#qué-es-la-fase-0)
3. [Por Qué la Fase 0 es Crítica](#por-qué-la-fase-0-es-crítica)
4. [Estructura de la Fase 0](#estructura-de-la-fase-0)
5. [Día 1: Levantamiento de Requerimientos](#día-1-levantamiento-de-requerimientos)
6. [Día 2: Diseño de Base de Datos](#día-2-diseño-de-base-de-datos)
7. [Día 3: Diseño de Interfaz (Wireframes)](#día-3-diseño-de-interfaz-wireframes)
8. [Día 4: Configuración de Ambiente](#día-4-configuración-de-ambiente)
9. [Checklist de Completitud](#checklist-de-completitud)
10. [Errores Comunes a Evitar](#errores-comunes-a-evitar)
11. [Plantillas y Formatos](#plantillas-y-formatos)
12. [Preparación para Fase 1](#preparación-para-fase-1)

---

## 1. INTRODUCCIÓN

### ¿Para Quién es Esta Guía?

Esta metodología está diseñada para **desarrolladores que trabajan con Claude (IA)** en proyectos de software de gestión empresarial (ERP, CRM, POS, sistemas administrativos).

**Ideal para:**
- Freelancers que desarrollan sistemas para clientes
- Equipos pequeños (1-5 desarrolladores)
- Proyectos de 1-6 meses de duración
- Sistemas web de gestión empresarial
- Proyectos con presupuestos limitados
- Desarrolladores que trabajan de forma remota

**No ideal para:**
- Proyectos enormes (>1 año, >10 desarrolladores)
- Aplicaciones móviles nativas
- Videojuegos
- Aplicaciones de tiempo real críticas

---

### Filosofía de Esta Metodología

**Principios fundamentales:**

1. **Planear primero, programar después**
   - 2 semanas de planificación ahorran 2 meses de rehacer código
   - Cambiar documentos es fácil, cambiar código es costoso
   - Cliente valida conceptualmente antes de invertir en desarrollo

2. **Documentación exhaustiva**
   - Si no está documentado, no existe
   - Futuro-tú te agradecerá tener todo por escrito
   - Facilita trabajo con Claude en sesiones futuras

3. **Diseño completo de BD antes de primera línea de código**
   - La BD es el corazón del sistema
   - Cambiarla después de tener código es doloroso
   - Invertir tiempo aquí ahorra dolores de cabeza después

4. **Validación temprana con cliente**
   - Mostrar diseños antes de programar
   - Cliente puede pedir cambios sin costo
   - Evita sorpresas al final

5. **Iteración progresiva con Claude**
   - Cada sesión debe tener contexto completo
   - Documentar decisiones para futuras sesiones
   - Claude necesita archivos de referencia, no solo memoria

---

## 2. ¿QUÉ ES LA FASE 0?

### Definición

**Fase 0 = Planificación y Diseño Completo**

Es la etapa donde defines **QUÉ** vas a construir, **CÓMO** estará organizado, y **CON QUÉ** lo vas a hacer, **antes de escribir una sola línea de código de producción**.

### Duración Esperada

- **Mínimo:** 3-4 días
- **Recomendado:** 5-7 días
- **Máximo necesario:** 10 días

**Regla de oro:** Si tu Fase 0 toma más de 2 semanas, probablemente el proyecto es muy grande y deberías dividirlo en sub-proyectos.

### Entregables de la Fase 0

Al finalizar esta fase debes tener:

1. ✅ Documento de requerimientos formales completo
2. ✅ Base de datos diseñada al 100% (diagrama ER + SQL)
3. ✅ Módulos del sistema identificados y descritos
4. ✅ Funcionalidades priorizadas (Crítico/Importante/Deseable)
5. ✅ Wireframes básicos de interfaces principales
6. ✅ Ambiente de desarrollo configurado y funcionando
7. ✅ Estructura de carpetas creada
8. ✅ Git inicializado con .gitignore
9. ✅ README.md con instrucciones de setup
10. ✅ Base de datos creada y probada

**Si falta cualquiera de estos, NO has terminado la Fase 0.**

---

## 3. POR QUÉ LA FASE 0 ES CRÍTICA

### Lecciones del Proyecto Joyería Torre Fuerte

**✅ Lo que salió bien:**

1. **Documentación exhaustiva permitió desarrollo rápido después**
   - En Fase 2-3 no hubo dudas sobre qué construir
   - Claude siempre tuvo referencia clara
   - Cliente no pidió cambios porque validó todo antes

2. **Base de datos completa desde día 1**
   - No hubo que modificar tablas después
   - No hubo migraciones dolorosas
   - Relaciones bien pensadas evitaron bugs

3. **Priorización clara guió el desarrollo**
   - Se trabajó en lo crítico primero
   - Cliente vio valor rápidamente
   - Funcionalidades deseables se dejaron para v2.0

4. **Ambiente configurado evitó problemas después**
   - Conflictos de puertos resueltos antes de programar
   - Git funcionando desde día 1
   - Estructura organizada facilitó ubicar archivos

**⚠️ Lo que faltó:**

1. **Wireframes**
   - Se debió hacer antes de Fase 4-5 (frontend)
   - Hubiera ahorrado tiempo en diseño visual
   - Cliente hubiera validado UX antes

**Resultado:** Fase 0 de 2 días ahorró semanas de desarrollo.

---

### Costo de NO Hacer Fase 0

**Escenario sin Fase 0:**

1. **Semana 1-2:** Empiezas a programar "lo que crees que necesita"
2. **Semana 3:** Cliente dice "no, así no es como lo quiero"
3. **Semana 4:** Rehaces toda la estructura de BD
4. **Semana 5:** Migraciones dolorosas de datos
5. **Semana 6-7:** Reescribes código para nueva BD
6. **Semana 8:** Cliente pide "una función que se me olvidó mencionar"
7. **Semana 9-10:** Refactoring masivo
8. **Semana 11:** Bugs por cambios apresurados
9. **Semana 12:** Frustración total

**Total:** 12 semanas de desarrollo caótico

**Escenario CON Fase 0:**

1. **Semana 1:** Planificación y diseño completo
2. **Semana 2:** Cliente valida TODO antes de programar
3. **Semana 3-8:** Desarrollo lineal sin sorpresas
4. **Semana 9:** Pruebas y ajustes menores
5. **Semana 10:** Deploy y capacitación

**Total:** 10 semanas de desarrollo fluido

**Ahorro:** 2 semanas + mucho menos estrés

---

## 4. ESTRUCTURA DE LA FASE 0

### Vista General

```
FASE 0: PLANIFICACIÓN Y DISEÑO
├── DÍA 1: Levantamiento de Requerimientos (6-8 hrs)
│   ├── Análisis del formulario/brief del cliente
│   ├── Documento de requerimientos formales
│   ├── Definición de módulos del sistema
│   └── Priorización de funcionalidades
│
├── DÍA 2: Diseño de Base de Datos (6-8 hrs)
│   ├── Identificación de entidades
│   ├── Definición de relaciones
│   ├── Creación de diagrama ER
│   ├── Generación de schema.sql
│   └── Validación de cobertura 100% de requerimientos
│
├── DÍA 3: Diseño de Interfaz (4-6 hrs)
│   ├── Wireframes de pantallas principales
│   ├── Flujos de usuario
│   ├── Paleta de colores y tipografía
│   └── Componentes reutilizables identificados
│
└── DÍA 4: Configuración de Ambiente (4-6 hrs)
    ├── Instalación de stack tecnológico
    ├── Configuración de base de datos
    ├── Creación de estructura de carpetas
    ├── Inicialización de Git
    └── Test de conexión exitoso
```

**Total tiempo:** 20-28 horas de trabajo (3-5 días calendario)

---

## 5. DÍA 1: LEVANTAMIENTO DE REQUERIMIENTOS

### Objetivo del Día

Transformar las necesidades del cliente en **documentación técnica clara y completa**.

### Materiales Necesarios

- Brief del cliente
- Formulario de requisitos completado
- Conversaciones/emails con el cliente
- Sistemas similares de referencia

---

### ACTIVIDAD 1.1: Análisis del Brief del Cliente (1-2 hrs)

**Qué hacer:**

1. **Lee TODO el material del cliente mínimo 2 veces**
   - Primera lectura: Entender el panorama general
   - Segunda lectura: Identificar necesidades específicas

2. **Identifica los problemas críticos que quiere resolver**
   - ¿Qué proceso manual quiere automatizar?
   - ¿Qué problema actual tiene que le causa dolor?
   - ¿Qué riesgo quiere mitigar?

3. **Anota preguntas y ambigüedades**
   - No asumas nada
   - Si algo no está claro, pregúntalo

**Ejemplo de problemas críticos identificados:**

```markdown
PROBLEMAS CRÍTICOS DEL CLIENTE:

1. **Pérdida de piezas en taller** (CRÍTICO)
   - Piezas pasan de empleado a empleado sin registro
   - No hay forma de saber quién tiene qué
   - Clientes reclaman y no se puede rastrear responsabilidad

2. **Inventario desorganizado** (IMPORTANTE)
   - No saben qué tienen en cada sucursal
   - Venden productos que no hay en stock
   - No hay alertas de stock mínimo

3. **Reportes manuales toman mucho tiempo** (IMPORTANTE)
   - Dueño pasa 2 días al mes haciendo reportes en Excel
   - Datos pueden tener errores
   - No hay reportes en tiempo real
```

---

### ACTIVIDAD 1.2: Documento de Requerimientos Formales (3-4 hrs)

Este es el documento más importante de todo el proyecto.

**Estructura recomendada:**

```markdown
# REQUERIMIENTOS FORMALES - [NOMBRE DEL PROYECTO]

## 1. INFORMACIÓN GENERAL
- Nombre del proyecto
- Cliente
- Desarrollador
- Fecha de inicio
- Presupuesto
- Timeline

## 2. DESCRIPCIÓN DEL PROYECTO
Resumen ejecutivo de qué hace el sistema (2-3 párrafos)

## 3. OBJETIVOS
### Objetivos del Negocio
- ¿Qué quiere lograr el cliente?
- ¿Qué métricas de éxito hay?

### Objetivos del Sistema
- ¿Qué debe hacer el software?
- ¿Qué problemas específicos resuelve?

## 4. ALCANCE
### Qué SÍ Incluye
Lista completa de funcionalidades que ESTARÁN en el sistema

### Qué NO Incluye
Lista de cosas que NO estarán (para evitar malentendidos)

## 5. USUARIOS DEL SISTEMA
### Tipos de Usuario
- Usuario 1: [Rol, responsabilidades, permisos]
- Usuario 2: [Rol, responsabilidades, permisos]
- ...

### Cantidad Esperada de Usuarios
- Usuarios simultáneos
- Crecimiento esperado

## 6. REQUERIMIENTOS FUNCIONALES
Lista numerada de TODAS las funcionalidades:

### Módulo 1: [Nombre]
- RF-1.1: [Descripción detallada]
- RF-1.2: [Descripción detallada]
- ...

### Módulo 2: [Nombre]
- RF-2.1: [Descripción detallada]
- ...

## 7. REQUERIMIENTOS NO FUNCIONALES
- Rendimiento (tiempos de respuesta)
- Seguridad (encriptación, autenticación)
- Disponibilidad (uptime esperado)
- Escalabilidad (crecimiento futuro)
- Compatibilidad (navegadores, dispositivos)

## 8. RESTRICCIONES TÉCNICAS
- Stack tecnológico acordado
- Hosting (servidor, características)
- Integraciones necesarias
- Limitaciones conocidas

## 9. DEPENDENCIAS
- Sistemas externos
- APIs de terceros
- Hardware especial (impresoras, lectores)

## 10. RIESGOS IDENTIFICADOS
| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| ...    | Alta/Media/Baja | Alto/Medio/Bajo | ... |

## 11. SUPUESTOS
- Cosas que asumes como verdaderas
- Validar con el cliente

## 12. CRITERIOS DE ACEPTACIÓN
¿Cómo sabré que el proyecto está completo?
- [ ] Criterio 1
- [ ] Criterio 2
- ...

## 13. PLAN DE CONTINGENCIA
¿Qué pasa si...?
- Cliente pide cambios mayores
- Se descubre funcionalidad imposible
- Problemas de presupuesto/tiempo

## 14. ANEXOS
- Glosario de términos
- Referencias
- Documentos adicionales
```

**Ejemplo real de requerimiento funcional bien escrito:**

```markdown
RF-3.2: Transferencia de Trabajos Entre Empleados

DESCRIPCIÓN:
El sistema debe permitir transferir un trabajo de taller de un empleado 
a otro, registrando esta operación en un historial inmutable.

FLUJO:
1. Usuario selecciona un trabajo en estado "En Proceso"
2. Usuario selecciona empleado destino
3. Usuario ingresa motivo de transferencia (opcional)
4. Sistema registra:
   - Empleado origen (actual)
   - Empleado destino (nuevo)
   - Fecha/hora exacta de transferencia
   - Usuario que realizó la transferencia
   - Motivo
5. Sistema actualiza empleado_actual_id del trabajo
6. Sistema NO permite eliminar/modificar este registro (inmutable)

VALIDACIONES:
- Solo empleados del rol "orfebre" pueden recibir trabajos
- No se puede transferir a sí mismo
- Trabajo debe estar en estado "En Proceso"

PERMISOS:
- Solo "administrador" y "dueño" pueden transferir trabajos

PRIORIDAD: CRÍTICA
```

**❌ Ejemplo de requerimiento MAL escrito:**

```markdown
RF-3.2: Los trabajos se pueden mover entre empleados
```

(Demasiado vago, no especifica cómo, no tiene validaciones)

---

### ACTIVIDAD 1.3: Definición de Módulos (1-2 hrs)

**Objetivo:** Dividir el sistema en piezas manejables.

**Plantilla de módulo:**

```markdown
## MÓDULO: [NOMBRE]

### Descripción
[1-2 párrafos explicando qué hace este módulo]

### Propósito del Negocio
¿Por qué este módulo es importante para el cliente?

### Funcionalidades Principales
1. Función 1
2. Función 2
3. ...

### Complejidad
- Técnica: Baja / Media / Alta
- Lógica de negocio: Baja / Media / Alta

### Tiempo Estimado de Desarrollo
[X] días (desglosado: backend [Y] días, frontend [Z] días)

### Dependencias
- Depende de: [Módulo A, Módulo B]
- Es requerido por: [Módulo C]

### Prioridad
Crítico / Importante / Deseable

### Tablas de Base de Datos Involucradas
- tabla_1
- tabla_2
- ...

### Roles que Usan Este Módulo
- Rol 1: [Permisos específicos]
- Rol 2: [Permisos específicos]

### Reportes Generados
- Reporte 1: [Descripción]
- Reporte 2: [Descripción]

### Integraciones
- Sistema/API externa (si aplica)

### Notas Técnicas
Consideraciones especiales de implementación
```

**Ejemplo de módulos típicos:**

- Autenticación y Seguridad
- Gestión de Usuarios
- Catálogos/Configuración
- [Módulo core del negocio 1]
- [Módulo core del negocio 2]
- Reportes y Analítica
- Configuración del Sistema

---

### ACTIVIDAD 1.4: Priorización de Funcionalidades (1-2 hrs)

**Objetivo:** Saber qué construir primero.

**Metodología MoSCoW:**

- **M** = Must have (CRÍTICO) - Sin esto el sistema NO funciona
- **S** = Should have (IMPORTANTE) - Importante pero no bloquea MVP
- **C** = Could have (DESEABLE) - Sería bueno tenerlo
- **W** = Won't have (FUERA DE ALCANCE) - No en esta versión

**Plantilla:**

```markdown
# PRIORIZACIÓN DE FUNCIONALIDADES

## CRÍTICAS (Must Have) - Para MVP v1.0

| ID | Funcionalidad | Módulo | Días | Razón |
|----|---------------|--------|------|-------|
| F-01 | Login/Logout | Auth | 1 | Base del sistema |
| F-02 | Control de trabajos | Taller | 5 | Problema #1 del cliente |
| ... | ... | ... | ... | ... |

**Total días críticos:** [X] días

## IMPORTANTES (Should Have) - Para v1.0 si hay tiempo

| ID | Funcionalidad | Módulo | Días | Razón |
|----|---------------|--------|------|-------|
| F-20 | Alertas de stock | Inventario | 2 | Mejora operación |
| ... | ... | ... | ... | ... |

**Total días importantes:** [Y] días

## DESEABLES (Could Have) - Para v1.5 o v2.0

| ID | Funcionalidad | Módulo | Días | Razón |
|----|---------------|--------|------|-------|
| F-40 | App móvil | N/A | 30 | Futuro, no urgente |
| ... | ... | ... | ... | ... |

**Total días deseables:** [Z] días

## FUERA DE ALCANCE (Won't Have)

- Funcionalidad X: Razón de exclusión
- Funcionalidad Y: Razón de exclusión

## ESTRATEGIA DE IMPLEMENTACIÓN

### Semana 1-2: Infraestructura
- Funcionalidades base (login, permisos, estructura)

### Semana 3-4: Funcionalidad Crítica #1
- [Descripción]

### Semana 5-6: Funcionalidad Crítica #2
- [Descripción]

...
```

**Criterios para priorizar:**

1. **¿Bloquea otras funcionalidades?** → CRÍTICO
2. **¿Resuelve el problema principal del cliente?** → CRÍTICO
3. **¿Genera valor inmediato?** → IMPORTANTE
4. **¿Es requisito legal/normativo?** → CRÍTICO
5. **¿Es "nice to have"?** → DESEABLE

---

### ENTREGABLES DEL DÍA 1

Al final del Día 1 debes tener:

- [ ] `requerimientos-formales.md` (completo, 15-20 páginas)
- [ ] `modulos-del-sistema.md` (8-12 módulos descritos)
- [ ] `priorizacion-funcionalidades.md` (todas las funcionalidades clasificadas)
- [ ] Lista de preguntas para el cliente (si hay ambigüedades)

**Tiempo invertido:** 6-8 horas

**Checkpoint:** ¿Tienes claridad total de qué vas a construir? Si no, sigue documentando.

---

## 6. DÍA 2: DISEÑO DE BASE DE DATOS

### Objetivo del Día

Diseñar **100% de la estructura de datos** antes de escribir código.

### Por Qué Esto es Crítico

**Regla de oro:** Cambiar BD con código en producción es 100x más costoso que cambiarla en papel.

- Sin código: Cambiar una tabla toma 2 minutos
- Con código: Cambiar una tabla requiere:
  - Modificar modelos
  - Migración de datos
  - Actualizar queries
  - Probar que nada se rompió
  - Potencialmente perder datos

**Invierte 8 horas en diseñar bien la BD ahora = Ahorra 80 horas después.**

---

### ACTIVIDAD 2.1: Identificación de Entidades (1-2 hrs)

**Entidad** = Cualquier "cosa" de la que necesitas guardar información.

**Proceso:**

1. **Lee los requerimientos funcionales uno por uno**
2. **Subraya todos los sustantivos**
3. **Clasifica cada sustantivo:**
   - ¿Es una entidad? (tabla)
   - ¿Es un atributo de otra entidad? (campo)
   - ¿Es un valor de referencia? (catálogo)

**Ejemplo de análisis:**

```
Requerimiento: "El sistema debe registrar VENTAS con múltiples 
PRODUCTOS, calculando el TOTAL según las CANTIDADES y PRECIOS. 
Cada venta puede tener múltiples FORMAS DE PAGO y puede generar 
un CRÉDITO al CLIENTE."
```

**Análisis:**
- **VENTA** → Entidad (tabla `ventas`)
- **PRODUCTO** → Entidad (tabla `productos`)
- **CANTIDAD** → Atributo de la relación venta-producto
- **PRECIO** → Atributo de producto
- **TOTAL** → Campo calculado en venta
- **FORMA DE PAGO** → Entidad (tabla `formas_pago_venta`)
- **CRÉDITO** → Entidad (tabla `creditos_clientes`)
- **CLIENTE** → Entidad (tabla `clientes`)

**Resultado:**
```
Entidades identificadas:
1. ventas
2. productos  
3. detalle_ventas (relación N:M entre ventas y productos)
4. formas_pago_venta
5. creditos_clientes
6. clientes
```

**Lista típica de entidades por tipo de sistema:**

**Sistema de Gestión Empresarial:**
- usuarios, roles, permisos
- clientes, proveedores
- productos, servicios, categorias
- inventario, movimientos_inventario
- ventas, detalle_ventas, formas_pago
- compras, detalle_compras
- caja, movimientos_caja
- facturas
- audit_log

**Sistema de Clínica:**
- usuarios, roles
- pacientes
- medicos, especialidades
- citas, historias_clinicas
- consultas, diagnosticos
- recetas, medicamentos
- examenes, resultados_examenes
- pagos, formas_pago
- seguros_medicos
- audit_log

**Sistema de Restaurante:**
- usuarios, roles
- mesas, areas
- productos, categorias, ingredientes
- recetas, recetas_ingredientes
- ordenes, detalle_ordenes
- caja, movimientos_caja
- reservaciones, clientes
- proveedores, compras
- inventario, movimientos_inventario
- audit_log

---

### ACTIVIDAD 2.2: Definición de Campos por Tabla (2-3 hrs)

Para cada entidad, define **todos sus campos**.

**Plantilla por tabla:**

```markdown
## TABLA: nombre_tabla

### Propósito
[Descripción de qué información guarda]

### Campos

| Campo | Tipo | Nulo | Default | Descripción |
|-------|------|------|---------|-------------|
| id | INT AUTO_INCREMENT | NO | - | PK |
| campo_1 | VARCHAR(100) | NO | - | ... |
| campo_2 | DECIMAL(10,2) | NO | 0.00 | ... |
| campo_3 | ENUM('val1','val2') | NO | 'val1' | ... |
| activo | TINYINT(1) | NO | 1 | Soft delete |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | ... |
| updated_at | TIMESTAMP | NO | CURRENT_TIMESTAMP ON UPDATE | ... |

### Índices
- PRIMARY KEY (id)
- INDEX idx_campo_1 (campo_1)
- UNIQUE KEY uk_campo_2 (campo_2)

### Foreign Keys
- campo_fk REFERENCES otra_tabla(id) ON DELETE RESTRICT

### Campos Calculados (si aplica)
- total AS (subtotal - descuento) STORED

### Validaciones de Negocio
- campo_1 no puede estar vacío
- campo_2 debe ser > 0
- ...
```

**Tipos de datos comunes:**

**Numéricos:**
- `INT` - Enteros (IDs, cantidades)
- `BIGINT` - Enteros grandes (teléfonos, códigos largos)
- `DECIMAL(10,2)` - Dinero (siempre DECIMAL, nunca FLOAT)
- `TINYINT(1)` - Booleanos (0/1)

**Texto:**
- `VARCHAR(X)` - Texto corto (nombres, códigos)
- `TEXT` - Texto largo (descripciones, notas)
- `ENUM('val1','val2')` - Lista cerrada de valores

**Fechas:**
- `DATE` - Solo fecha (2026-01-20)
- `DATETIME` - Fecha y hora (2026-01-20 14:30:00)
- `TIMESTAMP` - Fecha/hora con zona horaria (mejor para created_at)
- `TIME` - Solo hora (14:30:00)

**Campos estándar que TODAS las tablas deben tener:**

```sql
id INT AUTO_INCREMENT PRIMARY KEY,
activo TINYINT(1) NOT NULL DEFAULT 1,  -- Soft delete
created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

**❌ Errores comunes:**

1. **Usar FLOAT/DOUBLE para dinero** → Usar DECIMAL
2. **No poner índices** → BD lenta después
3. **No planear soft delete** → Perder datos al "borrar"
4. **Poner todo en VARCHAR(255)** → Desperdiciar espacio
5. **No documentar qué guarda cada campo** → Confusión después

---

### ACTIVIDAD 2.3: Definición de Relaciones (1-2 hrs)

**Tipos de relaciones:**

**1. Uno a Muchos (1:N) - Más común**
```
Un cliente → Muchas ventas
Una venta → Un cliente

Implementación:
tabla_muchos.tabla_uno_id (FK)
```

**Ejemplo:**
```sql
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);
```

**2. Muchos a Muchos (N:M)**
```
Una venta → Muchos productos
Un producto → Muchas ventas

Implementación: Tabla intermedia
```

**Ejemplo:**
```sql
CREATE TABLE detalle_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);
```

**3. Uno a Uno (1:1) - Raro**
```
Un usuario → Un perfil extendido

Implementación: 
FK en cualquiera de las dos tablas (generalmente la dependiente)
```

**Reglas de integridad referencial:**

```sql
-- NO permitir borrar si hay registros dependientes
ON DELETE RESTRICT

-- Borrar en cascada (PELIGROSO, usar con cuidado)
ON DELETE CASCADE

-- Poner en NULL si se borra el padre
ON DELETE SET NULL

-- Actualizar en cascada si cambia PK (raro)
ON UPDATE CASCADE
```

**Cuándo usar cada uno:**

- `RESTRICT`: Por defecto, más seguro
- `CASCADE`: Cuando el hijo no tiene sentido sin el padre (ej: detalle_venta sin venta)
- `SET NULL`: Cuando la relación es opcional (ej: venta sin vendedor asignado)

---

### ACTIVIDAD 2.4: Normalización (1 hr)

**Objetivo:** Eliminar redundancia sin complicar demasiado.

**Reglas básicas:**

**1ra Forma Normal (1FN):**
- ❌ NO guardar múltiples valores en un campo
- ✅ Cada campo debe tener un solo valor

**Ejemplo INCORRECTO:**
```sql
productos (
    id INT,
    nombre VARCHAR(100),
    precios VARCHAR(255)  -- "10.00,15.00,20.00" ❌
)
```

**Ejemplo CORRECTO:**
```sql
productos (
    id INT,
    nombre VARCHAR(100)
)

precios_producto (
    id INT,
    producto_id INT,
    tipo ENUM('publico','mayorista','especial'),
    precio DECIMAL(10,2)
)
```

**2da Forma Normal (2FN):**
- Cada campo debe depender de TODA la clave primaria
- Aplicable solo en tablas con PK compuesta

**3ra Forma Normal (3FN):**
- No debe haber dependencias transitivas
- Si campo A depende de campo B, y B no es PK, separar

**Ejemplo:**
```sql
-- ❌ INCORRECTO
ventas (
    id INT,
    cliente_id INT,
    cliente_nombre VARCHAR(100),  -- Redundante!
    cliente_telefono VARCHAR(20)  -- Redundante!
)

-- ✅ CORRECTO
ventas (
    id INT,
    cliente_id INT  -- Solo la FK
)

clientes (
    id INT,
    nombre VARCHAR(100),
    telefono VARCHAR(20)
)
```

**Cuándo NO normalizar tanto:**

- Reportes/análisis que requieren velocidad
- Campos calculados que se usan mucho
- Datos históricos que no deben cambiar

**Regla de oro:** Normaliza hasta 3FN, desnormaliza solo si tienes razón de performance comprobada.

---

### ACTIVIDAD 2.5: Creación de Diagrama ER (1 hr)

**Herramientas recomendadas:**

1. **dbdiagram.io** (recomendado)
   - Gratuito
   - Código simple
   - Genera SQL automáticamente

2. **draw.io**
   - Gratuito
   - Más visual
   - Más lento

3. **MySQL Workbench**
   - Profesional
   - Curva de aprendizaje
   - Genera código directo

**Sintaxis de dbdiagram.io:**

```
Table usuarios {
  id int [pk, increment]
  nombre varchar(100) [not null]
  email varchar(100) [unique, not null]
  rol enum('admin','user')
  activo tinyint [default: 1]
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
}

Table ventas {
  id int [pk, increment]
  cliente_id int [ref: > clientes.id]
  total decimal(10,2)
}

Table clientes {
  id int [pk, increment]
  nombre varchar(100)
}
```

**Resultado:** Diagrama visual automático + código SQL

---

### ACTIVIDAD 2.6: Generación de schema.sql (1-2 hrs)

**Estructura recomendada:**

```sql
-- ============================================
-- SISTEMA: [Nombre del Sistema]
-- BASE DE DATOS: nombre_bd
-- VERSIÓN: 1.0
-- FECHA: 2026-XX-XX
-- DESCRIPCIÓN: [Breve descripción]
-- ============================================

-- Configuración inicial
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- TABLAS DE CONFIGURACIÓN Y USUARIOS
-- ============================================

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'usuario') NOT NULL DEFAULT 'usuario',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Usuarios del sistema';

-- ============================================
-- TABLAS DE CATÁLOGOS
-- ============================================

CREATE TABLE IF NOT EXISTS categorias (
    -- ...
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLAS TRANSACCIONALES
-- ============================================

CREATE TABLE IF NOT EXISTS ventas (
    -- ...
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLAS DE AUDITORÍA
-- ============================================

CREATE TABLE IF NOT EXISTS audit_log (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(50) NOT NULL,
    tabla_afectada VARCHAR(50),
    registro_id INT,
    datos_anteriores TEXT,
    datos_nuevos TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_usuario (usuario_id),
    INDEX idx_tabla (tabla_afectada),
    INDEX idx_created (created_at),
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Registro de auditoría de todas las operaciones';

-- Restaurar configuración
SET FOREIGN_KEY_CHECKS = 1;
```

**Orden de creación:**

1. Tablas sin dependencias (usuarios, catálogos)
2. Tablas con 1 nivel de dependencia
3. Tablas con múltiples dependencias
4. Tablas de auditoría (último)

---

### ACTIVIDAD 2.7: Validación de Cobertura (30 min)

**Checklist:**

```markdown
## VALIDACIÓN: ¿La BD cubre 100% de los requerimientos?

### Requerimiento 1: [Descripción]
- [ ] Tabla(s) involucrada(s): [lista]
- [ ] Campos necesarios creados: [lista]
- [ ] Relaciones definidas correctamente
- [ ] ✅ Cubierto / ⚠️ Parcial / ❌ No cubierto

### Requerimiento 2: [Descripción]
- ...

## RESUMEN
- Total requerimientos funcionales: [X]
- Cubiertos completamente: [Y]
- Parcialmente cubiertos: [Z]
- No cubiertos: [W]

**Cobertura:** [Y/X * 100]%
```

**Si cobertura < 100%:**
- Identificar qué falta
- Agregar tablas/campos necesarios
- Volver a validar

**No avances a Día 3 hasta tener 100% de cobertura.**

---

### ENTREGABLES DEL DÍA 2

Al final del Día 2 debes tener:

- [ ] Lista completa de entidades identificadas
- [ ] Cada tabla con todos sus campos documentados
- [ ] Relaciones definidas y documentadas
- [ ] Diagrama ER visual (dbdiagram.io o similar)
- [ ] `database/schema.sql` completo y ejecutable
- [ ] Documento de validación de cobertura 100%

**Tiempo invertido:** 6-8 horas

**Checkpoint:** ¿Puedes crear la BD completa ejecutando schema.sql? ¿Cubre 100% de requerimientos?

---

## 7. DÍA 3: DISEÑO DE INTERFAZ (WIREFRAMES)

### Objetivo del Día

Diseñar **cómo se verá** el sistema antes de programar frontend.

### Por Qué Hacer Wireframes

1. **Cliente valida UX antes de programar**
   - Evita rehacer pantallas después
   - Cliente ve cómo va a funcionar

2. **Guía clara para desarrollo frontend**
   - Sabes exactamente qué componentes necesitas
   - No improvisas el diseño mientras programas

3. **Identifica problemas de flujo temprano**
   - Descubres botones que faltan
   - Ves si un proceso tiene muchos pasos

**Lección:** 1 hora de wireframes ahorra 10 horas de rehacer HTML/CSS.

---

### ACTIVIDAD 3.1: Identificar Pantallas Principales (1 hr)

**Lista típica de pantallas:**

**Sistema Administrativo:**
1. Login
2. Dashboard (página principal después de login)
3. Lista de registros (tabla con búsqueda/filtros)
4. Formulario de creación/edición
5. Vista de detalle
6. Reportes
7. Configuración

**Para cada módulo repites 3-5.**

**Ejemplo de lista completa:**

```markdown
## PANTALLAS DEL SISTEMA

### Autenticación
- [ ] LOGIN: Página de inicio de sesión
- [ ] RECUPERAR PASSWORD: Restablecer contraseña

### Dashboard
- [ ] DASHBOARD GENERAL: Resumen con KPIs principales

### Módulo Clientes
- [ ] CLIENTES_LISTA: Tabla de clientes con búsqueda
- [ ] CLIENTES_CREAR: Formulario nuevo cliente
- [ ] CLIENTES_EDITAR: Formulario editar cliente
- [ ] CLIENTES_DETALLE: Vista de cliente con historial

### Módulo Ventas
- [ ] VENTAS_POS: Punto de venta (crítico)
- [ ] VENTAS_LISTA: Historial de ventas
- [ ] VENTAS_DETALLE: Ver detalle de venta

...

Total pantallas: [X]
```

---

### ACTIVIDAD 3.2: Wireframes de Pantallas Críticas (2-3 hrs)

**Herramientas recomendadas:**

1. **Papel y lápiz** (sí, en serio)
   - Más rápido para bocetos iniciales
   - Fácil de cambiar

2. **Excalidraw** (excalidraw.com)
   - Gratuito
   - Estilo de dibujo a mano
   - Colaborativo

3. **Figma** (figma.com)
   - Profesional
   - Gratuito para uso básico
   - Componentes reutilizables

4. **Balsamiq**
   - Especializado en wireframes
   - Estilo "sketch"

**Nivel de detalle necesario:**

**❌ NO necesitas:**
- Colores finales
- Tipografías exactas
- Imágenes reales
- Animaciones
- Código CSS

**✅ SÍ necesitas:**
- Ubicación de elementos
- Tamaño relativo de componentes
- Flujo entre pantallas
- Texto de botones/labels
- Qué información se muestra

**Componentes a definir:**

```markdown
## WIREFRAME: [Nombre de Pantalla]

### Secciones
1. **Header**
   - Logo (izquierda)
   - Nombre usuario + menú desplegable (derecha)
   - Notificaciones

2. **Sidebar** (si aplica)
   - Menú de navegación
   - Colapsable en móvil

3. **Contenido Principal**
   - Título de página
   - Breadcrumb (si aplica)
   - Contenido específico

4. **Footer**
   - Copyright
   - Enlaces

### Elementos Interactivos
- Botón "Guardar" (primario, esquina superior derecha)
- Botón "Cancelar" (secundario, junto a Guardar)
- Campos del formulario: [lista]
- Validaciones que se mostrarán

### Flujo
Al hacer clic en "Guardar":
1. Validar campos
2. Si error → Mostrar mensaje junto a campo
3. Si éxito → Mensaje de confirmación + Redireccionar a lista
```

**Ejemplo de wireframe básico (texto):**

```
+----------------------------------------------------------+
|  LOGO              [Usuario ▼] [🔔] [⚙️]                  |
+----------------------------------------------------------+
|                                                          |
|  INICIO > CLIENTES > NUEVO                              |
|                                                          |
|  +----------------------------------------------------+  |
|  |  CREAR NUEVO CLIENTE                    [Guardar]  |  |
|  |                                        [Cancelar]   |  |
|  +----------------------------------------------------+  |
|  |                                                    |  |
|  |  Nombre completo: [_____________________]         |  |
|  |  Email:           [_____________________]         |  |
|  |  Teléfono:        [_____________________]         |  |
|  |  Tipo:            [○ Público ○ Mayorista]         |  |
|  |  Dirección:       [_____________________]         |  |
|  |                   [_____________________]         |  |
|  |  Notas:           [                     ]         |  |
|  |                   [                     ]         |  |
|  |                                                    |  |
|  +----------------------------------------------------+  |
|                                                          |
+----------------------------------------------------------+
|  © 2026 [Nombre Sistema]                                |
+----------------------------------------------------------+
```

**Pantallas prioritarias para wireframe:**

1. **Dashboard** - Primera impresión
2. **Punto de Venta** - Si aplica (crítico)
3. **Lista principal del módulo más importante**
4. **Formulario principal del módulo más importante**

---

### ACTIVIDAD 3.3: Flujos de Usuario (1 hr)

**Objetivo:** Documentar cómo el usuario completa tareas importantes.

**Plantilla de flujo:**

```markdown
## FLUJO: [Nombre de Tarea]

**Objetivo:** [Qué quiere lograr el usuario]

**Actor:** [Rol del usuario]

**Punto de inicio:** [Dónde empieza]

**Pasos:**

1. Usuario hace clic en [botón/enlace]
   - Sistema muestra [pantalla]
   
2. Usuario llena campos:
   - Campo 1: [validación]
   - Campo 2: [validación]
   
3. Usuario hace clic en "Guardar"
   - Sistema valida
   - Si error: muestra [mensaje]
   - Si éxito: 
     - Guarda en BD
     - Muestra confirmación
     - Redirecciona a [pantalla]

**Punto de finalización:** [Dónde termina]

**Variantes:**
- ¿Qué pasa si cancela?
- ¿Qué pasa si hay error de BD?
- ¿Qué pasa si pierde conexión?

**Mockup:** [Referencia a wireframe]
```

**Ejemplo de flujo:**

```markdown
## FLUJO: Registrar Nueva Venta

**Objetivo:** Vender productos a un cliente

**Actor:** Vendedor o Cajero

**Punto de inicio:** Dashboard principal

**Pasos:**

1. Usuario hace clic en "Nueva Venta" en sidebar
   → Sistema muestra pantalla POS vacía

2. Usuario busca cliente:
   - Escribe nombre o teléfono
   - Sistema muestra sugerencias mientras escribe
   - Usuario selecciona cliente
   
3. Usuario agrega productos:
   - Escanea código de barras O busca por nombre
   - Sistema muestra producto con precio
   - Usuario confirma cantidad
   - Sistema agrega a lista de productos
   
4. Usuario repite paso 3 hasta terminar

5. Sistema muestra:
   - Subtotal (calculado automáticamente)
   - Campo para aplicar descuento (opcional)
   - Total final

6. Usuario selecciona forma(s) de pago:
   - Puede ser múltiple (ej: Q100 efectivo + Q50 tarjeta)
   - Sistema valida que suma = total
   
7. Usuario hace clic en "Completar Venta"
   - Sistema valida:
     - ¿Hay stock suficiente?
     - ¿Suma de pagos = total?
   - Si error: Muestra mensaje, no guarda
   - Si éxito:
     - Guarda venta en BD
     - Actualiza inventario
     - Registra movimiento de caja
     - Genera ticket de venta
     - Imprime ticket (si configurado)
     - Muestra confirmación
     - Limpia formulario para nueva venta

**Variantes:**
- Si es crédito: Registra en tabla creditos_clientes
- Si producto sin stock: Opción de vender sin stock (configurable)
- Si pierde conexión: Guardar en localStorage, sincronizar después

**Wireframes involucrados:**
- VENTAS_POS
```

---

### ACTIVIDAD 3.4: Paleta de Colores y Tipografía (30 min)

**Objetivo:** Definir identidad visual básica.

**Paleta de colores:**

```markdown
## PALETA DE COLORES

### Colores Primarios
- **Primario:** #007bff (Azul) - Botones principales, links
- **Secundario:** #6c757d (Gris) - Botones secundarios
- **Éxito:** #28a745 (Verde) - Mensajes de éxito
- **Advertencia:** #ffc107 (Amarillo) - Alertas
- **Error:** #dc3545 (Rojo) - Errores, validaciones
- **Info:** #17a2b8 (Azul claro) - Información

### Colores de Fondo
- **Fondo principal:** #ffffff (Blanco)
- **Fondo secundario:** #f8f9fa (Gris muy claro)
- **Fondo sidebar:** #343a40 (Gris oscuro)

### Tipografía
- **Encabezados:** 'Roboto', sans-serif
- **Cuerpo:** 'Open Sans', sans-serif
- **Monospace (código):** 'Courier New', monospace

### Tamaños de Fuente
- h1: 2.5rem
- h2: 2rem
- h3: 1.75rem
- h4: 1.5rem
- Cuerpo: 1rem
- Pequeño: 0.875rem
```

**Herramientas útiles:**
- coolors.co - Generador de paletas
- fonts.google.com - Fuentes gratuitas

---

### ENTREGABLES DEL DÍA 3

Al final del Día 3 debes tener:

- [ ] Lista completa de pantallas del sistema
- [ ] Wireframes de 5-10 pantallas principales
- [ ] Flujos de usuario de tareas críticas (3-5 flujos)
- [ ] Paleta de colores definida
- [ ] Tipografía seleccionada
- [ ] Componentes reutilizables identificados

**Tiempo invertido:** 4-6 horas

**Checkpoint:** ¿El cliente entiende cómo se verá y funcionará el sistema? ¿Aprobó los wireframes?

---

## 8. DÍA 4: CONFIGURACIÓN DE AMBIENTE

### Objetivo del Día

Tener **todo listo para empezar a programar**.

---

### ACTIVIDAD 4.1: Instalación de Software (1-2 hrs)

**Stack recomendado para sistema de gestión web:**

**Servidor Local:**
- **XAMPP** (Windows/Mac/Linux)
  - Apache
  - MySQL
  - PHP
  - phpMyAdmin

**Editor de Código:**
- **VS Code** (recomendado)
  - Extensiones recomendadas:
    - PHP Intelephense
    - MySQL
    - GitLens
    - Prettier
    - ESLint
    - Live Server

**Control de Versiones:**
- **Git**
  - Cliente: Git Bash (Windows) o Terminal (Mac/Linux)
  - GUI opcional: GitHub Desktop, GitKraken

**Navegadores para Pruebas:**
- Chrome (principal)
- Firefox (secundario)

**Herramientas Adicionales:**
- Postman (pruebas de API)
- DBeaver o MySQL Workbench (gestión de BD)

---

### ACTIVIDAD 4.2: Configuración de XAMPP (1 hr)

**Paso 1: Verificar puertos disponibles**

En Windows:
```cmd
netstat -ano | findstr :80
netstat -ano | findstr :443
netstat -ano | findstr :3306
```

Si están ocupados, hay que cambiar puertos.

**Paso 2: Configurar puertos (si hay conflictos)**

**MySQL:**
```ini
# C:\xampp\mysql\bin\my.ini
[mysqld]
port=3307  # Cambiar de 3306 a 3307
```

**Apache HTTPS:**
```apache
# C:\xampp\apache\conf\extra\httpd-ssl.conf
Listen 4433  # Cambiar de 443 a 4433
<VirtualHost _default_:4433>
```

**phpMyAdmin:**
```php
// C:\xampp\phpMyAdmin\config.inc.php
$cfg['Servers'][$i]['port'] = '3307';  // Actualizar puerto
```

**Paso 3: Iniciar servicios**
- Abrir XAMPP Control Panel
- Start Apache
- Start MySQL
- Verificar que ambos tengan fondo verde

**Paso 4: Probar acceso**
- http://localhost → Ver página de bienvenida de XAMPP
- http://localhost/phpmyadmin → Ver phpMyAdmin

---

### ACTIVIDAD 4.3: Configuración de PHP (30 min)

**Archivo:** `C:\xampp\php\php.ini`

**Configuraciones importantes:**

```ini
; Memoria disponible para scripts
memory_limit = 256M

; Tiempo máximo de ejecución
max_execution_time = 300

; Tamaño máximo de archivos subidos
upload_max_filesize = 64M
post_max_size = 64M

; Mostrar errores (solo desarrollo)
display_errors = On
display_startup_errors = On
error_reporting = E_ALL

; Logs de errores
log_errors = On
error_log = "C:\xampp\php\logs\php_error_log"

; Zona horaria
date.timezone = America/Guatemala  # Ajustar según país

; Extensiones necesarias (descomentar si están comentadas)
extension=curl
extension=fileinfo
extension=gd
extension=gettext
extension=mbstring
extension=mysqli
extension=pdo_mysql
```

**Reiniciar Apache después de cambios.**

---

### ACTIVIDAD 4.4: Creación de Base de Datos (30 min)

**Opción A: Desde phpMyAdmin**
1. Ir a http://localhost/phpmyadmin
2. Click en "Nueva"
3. Nombre: `nombre_proyecto`
4. Cotejamiento: `utf8mb4_unicode_ci`
5. Crear
6. Pestaña "SQL"
7. Pegar todo el contenido de `schema.sql`
8. Ejecutar

**Opción B: Desde línea de comandos**
```bash
# Ubicarse en carpeta del proyecto
cd C:\xampp\htdocs\nombre_proyecto

# Conectar a MySQL
mysql -u root -p -P 3307

# Crear BD
CREATE DATABASE nombre_proyecto CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Usar la BD
USE nombre_proyecto;

# Importar schema
SOURCE database/schema.sql;

# Verificar
SHOW TABLES;
```

**Verificación:**
- Contar tablas creadas: debe coincidir con diseño
- Verificar foreign keys: `SHOW CREATE TABLE nombre_tabla;`

---

### ACTIVIDAD 4.5: Estructura del Proyecto (1 hr)

**Crear estructura completa:**

```
nombre_proyecto/
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── custom.css
│   ├── js/
│   │   ├── app.js
│   │   └── validaciones.js
│   ├── img/
│   │   └── logo.png
│   └── fonts/
├── config.php
├── config.example.php
├── database/
│   ├── schema.sql
│   └── seed.sql (opcional)
├── docs/
│   ├── requerimientos-formales.md
│   ├── modulos-del-sistema.md
│   ├── priorizacion-funcionalidades.md
│   └── wireframes/
├── includes/
│   ├── db.php
│   ├── funciones.php
│   ├── auth.php
│   ├── header.php
│   ├── footer.php
│   └── navbar.php
├── models/
│   └── (archivos de modelo por entidad)
├── modules/
│   ├── dashboard/
│   ├── usuarios/
│   ├── (un folder por módulo)
│   └── reportes/
├── api/
│   └── (endpoints REST si aplica)
├── uploads/
│   └── .gitkeep
├── logs/
│   └── .gitkeep
├── index.php
├── login.php
├── logout.php
├── dashboard.php
├── test-conexion.php
├── .gitignore
├── .htaccess (opcional)
└── README.md
```

---

### ACTIVIDAD 4.6: Archivos de Configuración (1 hr)

**Archivo: config.php**

```php
<?php
/**
 * Configuración principal del sistema
 * 
 * IMPORTANTE: Este archivo NO se sube a Git
 * Copiar de config.example.php y ajustar valores
 */

// Prevenir acceso directo
if (!defined('ACCESS_GRANTED')) {
    die('Acceso denegado');
}

// ==================================================
// CONFIGURACIÓN DE BASE DE DATOS
// ==================================================
define('DB_HOST', 'localhost');
define('DB_PORT', '3307');  // Puerto de MySQL (3306 por defecto)
define('DB_NAME', 'nombre_proyecto');
define('DB_USER', 'root');
define('DB_PASS', '');  // Contraseña de MySQL (vacía en XAMPP local)
define('DB_CHARSET', 'utf8mb4');

// ==================================================
// CONFIGURACIÓN GENERAL
// ==================================================
define('APP_NAME', 'Sistema de Gestión');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/nombre_proyecto/');

// ==================================================
// ENTORNO
// ==================================================
define('ENVIRONMENT', 'development');  // development | production

// ==================================================
// RUTAS
// ==================================================
define('ROOT_PATH', __DIR__ . '/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('LOGS_PATH', ROOT_PATH . 'logs/');

// ==================================================
// CONFIGURACIÓN DE SESIÓN
// ==================================================
define('SESSION_NAME', 'app_session');
define('SESSION_LIFETIME', 7200);  // 2 horas en segundos
define('SESSION_COOKIE_HTTPONLY', true);
define('SESSION_COOKIE_SECURE', false);  // true en producción con HTTPS

// ==================================================
// CONFIGURACIÓN DE ERRORES
// ==================================================
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOGS_PATH . 'php_errors.log');
}

// ==================================================
// ZONA HORARIA
// ==================================================
date_default_timezone_set('America/Guatemala');  // Ajustar según país

// ==================================================
// CONFIGURACIÓN DE ARCHIVOS
// ==================================================
define('MAX_FILE_SIZE', 10485760);  // 10 MB en bytes
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'xlsx', 'docx']);

// ==================================================
// CONFIGURACIÓN DE PAGINACIÓN
// ==================================================
define('RECORDS_PER_PAGE', 25);

// ==================================================
// CONFIGURACIÓN DE SEGURIDAD
// ==================================================
define('PASSWORD_MIN_LENGTH', 8);
define('HASH_ALGORITHM', PASSWORD_BCRYPT);
define('HASH_COST', 12);

// ==================================================
// VARIABLES DE ENTORNO ESPECÍFICAS (opcional)
// ==================================================
// Para APIs, keys, etc.
```

**Archivo: config.example.php**

Copia exacta de `config.php` pero con valores de ejemplo:
```php
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_password');
```

---

**Archivo: includes/db.php**

```php
<?php
/**
 * Conexión a base de datos
 * 
 * Uso:
 * require_once 'includes/db.php';
 * global $pdo;
 * $stmt = $pdo->query("SELECT * FROM usuarios");
 */

// Prevenir acceso directo
if (!defined('ACCESS_GRANTED')) {
    die('Acceso denegado');
}

try {
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=%s",
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT         => false
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // En producción, NO mostrar detalles del error
    if (ENVIRONMENT === 'development') {
        die('Error de conexión: ' . $e->getMessage());
    } else {
        error_log('DB Error: ' . $e->getMessage());
        die('Error de conexión a la base de datos. Contacte al administrador.');
    }
}
```

---

**Archivo: test-conexion.php**

```php
<?php
/**
 * Test de configuración del sistema
 * 
 * Verificar:
 * - Versión de PHP
 * - Conexión a BD
 * - Tablas creadas
 * 
 * ELIMINAR EN PRODUCCIÓN
 */

define('ACCESS_GRANTED', true);
require_once 'config.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Configuración</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>🧪 Test de Configuración del Sistema</h1>

    <h2>1. Versión de PHP</h2>
    <div class="status success">
        <strong>✅ PHP <?= phpversion() ?></strong>
    </div>

    <h2>2. Conexión a Base de Datos</h2>
    <?php
    try {
        $stmt = $pdo->query("SELECT DATABASE() as db_name, VERSION() as mysql_version");
        $info = $stmt->fetch();
        ?>
        <div class="status success">
            <strong>✅ Conexión exitosa</strong><br>
            Base de datos: <strong><?= $info['db_name'] ?></strong><br>
            MySQL versión: <strong><?= $info['mysql_version'] ?></strong>
        </div>
        <?php
    } catch (PDOException $e) {
        ?>
        <div class="status error">
            <strong>❌ Error de conexión</strong><br>
            <?= $e->getMessage() ?>
        </div>
        <?php
    }
    ?>

    <h2>3. Tablas Creadas</h2>
    <?php
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $count = count($tables);
        ?>
        <div class="status success">
            <strong>✅ <?= $count ?> tablas encontradas</strong>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre de Tabla</th>
                    <th>Registros</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables as $index => $table): ?>
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM `$table`");
                    $count = $stmt->fetch()['total'];
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $table ?></td>
                        <td><?= $count ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
    } catch (PDOException $e) {
        ?>
        <div class="status error">
            <strong>❌ Error al obtener tablas</strong><br>
            <?= $e->getMessage() ?>
        </div>
        <?php
    }
    ?>

    <h2>4. Configuraciones</h2>
    <table>
        <tr>
            <th>Configuración</th>
            <th>Valor</th>
        </tr>
        <tr>
            <td>APP_NAME</td>
            <td><?= APP_NAME ?></td>
        </tr>
        <tr>
            <td>ENVIRONMENT</td>
            <td><?= ENVIRONMENT ?></td>
        </tr>
        <tr>
            <td>BASE_URL</td>
            <td><?= BASE_URL ?></td>
        </tr>
        <tr>
            <td>DB_HOST</td>
            <td><?= DB_HOST ?></td>
        </tr>
        <tr>
            <td>DB_PORT</td>
            <td><?= DB_PORT ?></td>
        </tr>
        <tr>
            <td>DB_NAME</td>
            <td><?= DB_NAME ?></td>
        </tr>
    </table>

    <hr>
    <p><small>⚠️ <strong>IMPORTANTE:</strong> Eliminar este archivo en producción.</small></p>
</body>
</html>
```

---

**Archivo: .gitignore**

```gitignore
# ==========================================
# ARCHIVOS DE CONFIGURACIÓN
# ==========================================
config.php
.env
.env.local

# ==========================================
# ARCHIVOS SUBIDOS POR USUARIOS
# ==========================================
/uploads/*
!/uploads/.gitkeep

# ==========================================
# LOGS
# ==========================================
/logs/*
!/logs/.gitkeep
*.log

# ==========================================
# ARCHIVOS TEMPORALES
# ==========================================
*.tmp
*.temp
*.cache
*.bak
*.swp
*~

# ==========================================
# SISTEMA OPERATIVO
# ==========================================
.DS_Store
Thumbs.db
desktop.ini

# ==========================================
# EDITORES
# ==========================================
.vscode/
.idea/
*.sublime-project
*.sublime-workspace

# ==========================================
# DEPENDENCIAS (si usas Composer/NPM)
# ==========================================
/vendor/
/node_modules/

# ==========================================
# ARCHIVOS DE PRUEBA
# ==========================================
test-*.php
debug.php
```

---

**Archivo: README.md**

```markdown
# [NOMBRE DEL PROYECTO]

Sistema de gestión desarrollado en PHP + MySQL.

## 📋 Requerimientos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache 2.4
- Git

## 🚀 Instalación

### 1. Clonar repositorio

```bash
git clone https://github.com/usuario/proyecto.git
cd proyecto
```

### 2. Configurar base de datos

```bash
# Crear base de datos
mysql -u root -p
CREATE DATABASE nombre_proyecto CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nombre_proyecto;
SOURCE database/schema.sql;
```

### 3. Configurar archivo config.php

```bash
cp config.example.php config.php
# Editar config.php con tus datos de conexión
```

### 4. Verificar instalación

Abrir en navegador:
```
http://localhost/proyecto/test-conexion.php
```

Debe mostrar conexión exitosa y tablas creadas.

## 📁 Estructura del Proyecto

```
proyecto/
├── assets/        # Recursos estáticos (CSS, JS, imágenes)
├── config.php     # Configuración (NO subir a Git)
├── database/      # Scripts SQL
├── docs/          # Documentación
├── includes/      # Archivos PHP reutilizables
├── models/        # Modelos de datos
├── modules/       # Módulos funcionales
├── uploads/       # Archivos subidos por usuarios
└── logs/          # Logs del sistema
```

## 🔐 Usuarios por Defecto

**Usuario:** admin  
**Contraseña:** admin123

⚠️ **Cambiar en producción**

## 🛠️ Tecnologías

- PHP 8.x
- MySQL 8.x
- Bootstrap 5
- JavaScript Vanilla

## 📖 Documentación

Ver carpeta `/docs` para documentación completa:
- Requerimientos formales
- Módulos del sistema
- Diagrama de base de datos

## 🤝 Contribuir

Este es un proyecto privado.

## 📞 Contacto

**Desarrollador:** [Tu Nombre]  
**Email:** [tu@email.com]  
**Fecha:** [Fecha]

## 📝 Licencia

Propietario: [Cliente]  
Todos los derechos reservados.
```

---

### ACTIVIDAD 4.7: Inicialización de Git (30 min)

**Comandos:**

```bash
# Ubicarse en carpeta del proyecto
cd C:\xampp\htdocs\nombre_proyecto

# Inicializar Git
git init

# Configurar usuario (primera vez)
git config --global user.name "Tu Nombre"
git config --global user.email "tu@email.com"

# Agregar todos los archivos
git add .

# Primer commit
git commit -m "Initial commit - Estructura base del proyecto"

# Crear repositorio en GitHub (desde web)
# Luego conectar:
git remote add origin https://github.com/usuario/proyecto.git
git branch -M main
git push -u origin main
```

---

### ENTREGABLES DEL DÍA 4

Al final del Día 4 debes tener:

- [ ] XAMPP instalado y funcionando
- [ ] PHP configurado correctamente
- [ ] Base de datos creada con todas las tablas
- [ ] Estructura completa de carpetas
- [ ] `config.php` funcionando
- [ ] `includes/db.php` funcionando
- [ ] `test-conexion.php` mostrando éxito
- [ ] `.gitignore` configurado
- [ ] Git inicializado
- [ ] Repositorio en GitHub creado
- [ ] README.md completo

**Tiempo invertido:** 4-6 horas

**Checkpoint:** ¿Al ejecutar `test-conexion.php` ves todas las tablas? ¿Git está funcionando?

---

## 9. CHECKLIST DE COMPLETITUD

### ✅ Fase 0 Completada Cuando...

```markdown
## DOCUMENTACIÓN
- [ ] requerimientos-formales.md (15-20 páginas)
- [ ] modulos-del-sistema.md (8-12 módulos)
- [ ] priorizacion-funcionalidades.md (todas las funciones clasificadas)
- [ ] Cliente ha leído y aprobado los 3 documentos anteriores

## BASE DE DATOS
- [ ] Diagrama ER creado (visual)
- [ ] schema.sql completo (ejecutable)
- [ ] Validación 100% de cobertura de requerimientos
- [ ] Base de datos creada en MySQL
- [ ] test-conexion.php muestra todas las tablas

## DISEÑO DE INTERFAZ
- [ ] Lista de pantallas completa
- [ ] Wireframes de 5-10 pantallas principales
- [ ] Flujos de usuario de tareas críticas
- [ ] Paleta de colores definida
- [ ] Cliente ha visto y aprobado wireframes

## AMBIENTE DE DESARROLLO
- [ ] XAMPP instalado y corriendo
- [ ] PHP configurado
- [ ] MySQL configurado (con puerto correcto)
- [ ] Estructura de carpetas creada
- [ ] config.php funcionando
- [ ] includes/db.php funcionando
- [ ] Git inicializado
- [ ] Repositorio GitHub creado
- [ ] README.md completo
- [ ] .gitignore configurado

## VALIDACIÓN FINAL
- [ ] http://localhost/proyecto/test-conexion.php → ✅
- [ ] Cliente aprobó documentación
- [ ] Cliente aprobó diseño de BD
- [ ] Cliente aprobó wireframes
- [ ] Git tiene primer commit
- [ ] Tienes claridad 100% de qué construir
```

**SI TODOS ESTÁN ✅ → FASE 0 COMPLETADA**

**SI FALTA ALGUNO → NO AVANZAR A FASE 1**

---

## 10. ERRORES COMUNES A EVITAR

### ❌ Error 1: Empezar a programar sin terminar documentación

**Síntoma:**
"Ya tengo una idea general, voy a ir documentando mientras programo"

**Consecuencia:**
- Rehacer código 3-4 veces
- Cliente pide cambios tarde
- Módulos sin diseñar bien

**Solución:**
Disciplina. NO escribir código hasta tener documentación completa.

---

### ❌ Error 2: Base de datos incompleta

**Síntoma:**
"Ya hice las tablas principales, las demás las hago después"

**Consecuencia:**
- Agregar tablas después obliga a modificar código
- Relaciones mal pensadas generan bugs
- Migraciones dolorosas

**Solución:**
Invertir 8 horas en diseñar 100% de la BD ahora.

---

### ❌ Error 3: Saltarse wireframes

**Síntoma:**
"Diseño la interfaz mientras programo el frontend"

**Consecuencia:**
- Rehacer HTML/CSS múltiples veces
- Cliente no le gusta el diseño al final
- UX confusa

**Solución:**
Wireframes básicos en papel toman 2 horas, ahorran 20 horas de frontend.

---

### ❌ Error 4: No validar con cliente

**Síntoma:**
"Yo sé lo que necesita, no necesito mostrárselo"

**Consecuencia:**
- Sorpresas al final
- "Así no es como lo quería"
- Rehacer trabajo

**Solución:**
Mostrar documentación, wireframes, diseño de BD. Obtener aprobación por escrito.

---

### ❌ Error 5: Configurar ambiente al final

**Síntoma:**
"Configuro el ambiente cuando vaya a deployar"

**Consecuencia:**
- Problemas de última hora
- Bugs que no se detectaron
- Estrés innecesario

**Solución:**
Configurar todo en Fase 0. Probar que funciona.

---

### ❌ Error 6: No usar Git desde el inicio

**Síntoma:**
"Subo a Git cuando termine el proyecto"

**Consecuencia:**
- Perder trabajo si falla la PC
- No poder volver a versiones anteriores
- Un solo commit gigante

**Solución:**
Git desde día 1. Commits frecuentes.

---

### ❌ Error 7: No documentar decisiones técnicas

**Síntoma:**
"Me acuerdo por qué decidí hacer esto"

**Consecuencia:**
- Olvidar razones después
- Claude (IA) no tiene contexto en siguiente sesión
- Repensar decisiones ya tomadas

**Solución:**
Documentar TODO. Futuro-tú te lo agradecerá.

---

## 11. PLANTILLAS Y FORMATOS

### Plantilla: Prompt Inicial para Claude

```
Hola Claude, voy a iniciar un nuevo proyecto de desarrollo de software.

**PROYECTO:** [Nombre del proyecto]
**TIPO:** [ERP / CRM / POS / Sistema de gestión / etc.]
**CLIENTE:** [Nombre del cliente]
**INDUSTRIA:** [Joyería / Clínica / Restaurante / etc.]
**DURACIÓN ESTIMADA:** [X] meses
**PRESUPUESTO:** [Y] (si aplica)

**OBJETIVO:**
[Descripción en 2-3 párrafos de qué quiere el cliente]

**PROBLEMAS QUE RESUELVE:**
1. [Problema crítico 1]
2. [Problema crítico 2]
3. ...

**USUARIOS:**
- [Tipo de usuario 1]: [Cantidad] personas
- [Tipo de usuario 2]: [Cantidad] personas

**STACK TECNOLÓGICO DESEADO:**
- Backend: [PHP / Node.js / Python / etc.]
- Frontend: [HTML/CSS/JS / React / Vue / etc.]
- BD: [MySQL / PostgreSQL / MongoDB]
- Hosting: [Hostinger / AWS / Heroku / etc.]

Quiero seguir una metodología profesional de desarrollo, empezando
por la Fase 0: Planificación y Diseño.

Tengo el documento "METODOLOGÍA PROFESIONAL DE DESARROLLO - FASE 0"
que detalla cómo hacer esta fase correctamente.

¿Podemos empezar con el Día 1: Levantamiento de Requerimientos?

Adjunto:
- [Formulario de requisitos del cliente]
- [Brief del proyecto]
- [Cualquier otro documento relevante]
```

---

### Plantilla: Email de Validación con Cliente

```
Asunto: [Proyecto] - Validación de Fase 0: Planificación Completa

Estimado/a [Nombre del cliente],

He completado la Fase 0 (Planificación y Diseño) de [Nombre del Proyecto].

Antes de iniciar la programación, necesito que revise y apruebe los 
siguientes documentos para asegurarme de que estamos alineados:

1. **Requerimientos Formales**
   - Define TODO lo que hará el sistema
   - Roles y permisos de usuarios
   - Alcance: qué SÍ y qué NO incluye
   
2. **Diseño de Base de Datos**
   - Diagrama visual de cómo se relacionan los datos
   - Script SQL listo para crear la base de datos
   
3. **Wireframes (Bocetos de Pantallas)**
   - Cómo se verán las pantallas principales
   - Flujos de uso del sistema

4. **Priorización de Funcionalidades**
   - Qué se construirá primero (crítico)
   - Qué se puede dejar para después (deseable)

**DOCUMENTOS ADJUNTOS:**
- requerimientos-formales.pdf
- diagrama-base-datos.pdf
- wireframes.pdf
- priorizacion.pdf

**PRÓXIMOS PASOS:**
Una vez que apruebe estos documentos, iniciaré la Fase 1: Desarrollo
del Backend. Se estima que el proyecto completo tomará [X] semanas.

**IMPORTANTE:** 
Cambios a estos documentos después de iniciar la programación pueden
retrasar el proyecto y/o incrementar el costo. Por favor, revise con
cuidado antes de aprobar.

¿Tiene alguna duda o cambio que sugerir?

Saludos,
[Tu nombre]
```

---

## 12. PREPARACIÓN PARA FASE 1

### Contexto Necesario para Claude en Fase 1

Al iniciar una nueva sesión de Claude para la Fase 1, proporciona:

**Archivos obligatorios:**
1. `FASE-0-COMPLETADA.md` (este documento generado al final)
2. `database/schema.sql`
3. `requerimientos-formales.md`
4. `modulos-del-sistema.md`
5. `priorizacion-funcionalidades.md`

**Prompt sugerido:**

```
Hola Claude, voy a iniciar la Fase 1 del proyecto "[Nombre]".

He completado exitosamente la Fase 0: Planificación y Diseño.

Te adjunto:
- FASE-0-COMPLETADA.md (resumen de todo lo hecho)
- schema.sql (estructura de BD completa)
- requerimientos-formales.md (qué debe hacer el sistema)
- modulos-del-sistema.md (8 módulos definidos)
- priorizacion-funcionalidades.md (qué es crítico)

**ESTADO ACTUAL:**
✅ Base de datos diseñada (25 tablas)
✅ Ambiente configurado (XAMPP funcionando)
✅ Estructura del proyecto creada
✅ Git inicializado
✅ Cliente validó toda la planificación

**UBICACIÓN DEL PROYECTO:**
C:\xampp\htdocs\nombre_proyecto\

**ACCESO:**
http://localhost/nombre_proyecto/

**OBJETIVO DE FASE 1:**
Crear la arquitectura base del backend:
1. Mejorar sistema de configuración
2. Crear funciones helper generales
3. Implementar manejo robusto de errores
4. Crear datos de prueba (seed.sql)
5. Implementar sistema de logging

Tengo el documento de metodología de Fase 1. ¿Por dónde empezamos?
```

---

## 📊 MÉTRICAS DE ÉXITO

### Indicadores de que la Fase 0 fue exitosa:

- [ ] **Documentación completa:** >50 páginas de documentos técnicos
- [ ] **Claridad 100%:** Sabes exactamente qué construir
- [ ] **Cliente satisfecho:** Validó y aprobó todo
- [ ] **BD diseñada:** 100% de requerimientos cubiertos
- [ ] **Ambiente funcionando:** test-conexion.php exitoso
- [ ] **Git activo:** Primer commit realizado
- [ ] **Tiempo invertido:** 20-30 horas (3-5 días)
- [ ] **Confianza alta:** Listo para programar sin dudas

---

## 🎯 CONCLUSIÓN

**La Fase 0 no es opcional. Es la base de todo.**

Un proyecto sin Fase 0 es como construir una casa sin planos:
- Se ve el progreso rápido al inicio
- Pero después hay que demoler y reconstruir
- Cuesta 3x más tiempo y dinero
- El resultado final es mediocre

**Invierte 1 semana en Fase 0, ahorra 1 mes en desarrollo.**

---

## 📞 RECURSOS ADICIONALES

**Herramientas Recomendadas:**
- dbdiagram.io - Diseño de BD
- excalidraw.com - Wireframes
- github.com - Control de versiones
- draw.io - Diagramas generales
- coolors.co - Paletas de colores
- fonts.google.com - Fuentes

**Lecturas Recomendadas:**
- "The Pragmatic Programmer"
- "Clean Code" de Robert C. Martin
- "Database Design for Mere Mortals"

---

**Última actualización:** Enero 2026  
**Versión:** 2.0  
**Basado en:** Proyecto Joyería Torre Fuerte (éxito comprobado)

═══════════════════════════════════════════════════════
          🎯 METODOLOGÍA DE FASE 0 COMPLETADA
            APLICA ESTO EN CADA PROYECTO
═══════════════════════════════════════════════════════