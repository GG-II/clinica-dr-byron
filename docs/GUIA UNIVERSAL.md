```markdown
Hola Claude, necesito que crees una guía metodológica UNIVERSAL para 
cualquier fase de desarrollo de software, que pueda adaptarse 
dinámicamente según la fase en la que esté.

**CONTEXTO:**

Tengo una metodología profesional de desarrollo estructurada en fases:

- ✅ **FASE 0:** Planificación y Diseño (guía completa creada)
- ⏳ **FASE 1:** Arquitectura y Backend Base (siguiente)
- ⏳ **FASE 2:** Desarrollo de Módulos Específicos
- ⏳ **FASE 3:** Frontend e Integración
- ⏳ **FASE 4:** Pruebas y Optimización
- ⏳ **FASE 5:** Deployment y Producción

He completado exitosamente la Fase 0 en dos proyectos:
1. Sistema de Gestión - Joyería Torre Fuerte (25 tablas, 8 módulos)
2. Sistema de Gestión de Clínica (13 tablas, 7 módulos)

**LO QUE NECESITO:**

Crea un documento markdown llamado:
**"METODOLOGÍA PROFESIONAL DE DESARROLLO - GUÍA UNIVERSAL DE FASES.md"**

**ESTE DOCUMENTO DEBE SER:**

1. **MODULAR Y REUTILIZABLE**
   - Una sección maestra que explica la metodología completa
   - Subsecciones detalladas para cada fase
   - Plantillas adaptables a cualquier proyecto

2. **AUTOSUFICIENTE**
   - Cualquier desarrollador puede usarlo sin contexto previo
   - Incluye TODO lo necesario para cada fase
   - Código de ejemplo real y funcional

3. **ESCALABLE**
   - Sirve para proyectos pequeños (1-2 meses)
   - Sirve para proyectos medianos (3-6 meses)
   - Adaptable a diferentes stacks tecnológicos

**ESTRUCTURA DEL DOCUMENTO:**

```markdown
# METODOLOGÍA PROFESIONAL DE DESARROLLO
## Guía Universal de Fases para Proyectos de Software

## PARTE 1: VISIÓN GENERAL DE LA METODOLOGÍA

### 1.1 Filosofía y Principios
- Por qué esta metodología funciona
- Casos de éxito (joyería, clínica)
- Cuándo usar esta metodología

### 1.2 Mapa Completo de Fases
- Fase 0: Planificación y Diseño (1-2 semanas)
- Fase 1: Arquitectura y Backend Base (1 semana)
- Fase 2: Módulos Core del Negocio (2-4 semanas)
- Fase 3: Frontend e Integración (2-3 semanas)
- Fase 4: Pruebas y Optimización (1-2 semanas)
- Fase 5: Deployment y Producción (1 semana)

### 1.3 Flujo de Trabajo Entre Fases
- Entregables de cada fase
- Dependencias entre fases
- Criterios de completitud
- Cómo preparar el contexto para la siguiente fase

### 1.4 Trabajo con Claude (IA)
- Cómo estructurar conversaciones
- Qué archivos adjuntar en cada fase
- Prompts efectivos
- Gestión de contexto entre sesiones

## PARTE 2: FASE 0 - PLANIFICACIÓN Y DISEÑO

[La guía completa que ya creaste anteriormente]

## PARTE 3: FASE 1 - ARQUITECTURA Y BACKEND BASE

### 3.1 Objetivos de la Fase
- Sistema de autenticación completo
- Roles y permisos implementados
- Arquitectura base del proyecto
- Funciones helper reutilizables
- Datos de prueba (seed.sql)

### 3.2 Duración Esperada
- Mínimo: 3-5 días
- Recomendado: 1 semana
- Máximo: 10 días

### 3.3 Pre-requisitos
- [ ] Fase 0 100% completada
- [ ] Base de datos creada
- [ ] Ambiente de desarrollo funcionando
- [ ] Cliente validó toda la planificación

### 3.4 Estructura Día por Día

#### DÍA 1: Arquitectura Base
- Sistema de configuración robusto
- Conexión a BD con manejo de errores
- Funciones helper generales
- Estructura de carpetas mejorada

#### DÍA 2: Autenticación
- Sistema de login/logout
- Sesiones seguras
- Protección CSRF
- Recuperación de contraseña

#### DÍA 3: Roles y Permisos
- Tabla de permisos
- Middleware de autorización
- Decoradores de protección
- ACL (Access Control List)

#### DÍA 4: Modelos Base
- Clase Model genérica
- Modelo Usuario completo
- CRUD base reutilizable
- Validaciones

#### DÍA 5: Datos de Prueba y Tests
- seed.sql con datos realistas
- Tests de autenticación
- Tests de permisos
- Verificación de arquitectura

### 3.5 Componentes a Desarrollar (con código completo)

[Para cada componente:]
- Propósito y ubicación
- Código completo comentado
- Ejemplos de uso
- Pruebas
- Errores comunes

#### 3.5.1 config.php Mejorado
```php
[Código completo con todas las configuraciones profesionales]
```

#### 3.5.2 includes/db.php Robusto
```php
[Código con manejo de errores, reconexión, logging]
```

#### 3.5.3 includes/funciones.php
```php
[Helpers útiles: sanitización, validación, fechas, etc.]
```

#### 3.5.4 includes/auth.php
```php
[Sistema completo de autenticación]
```

#### 3.5.5 includes/middleware.php
```php
[Protección de rutas y verificación de permisos]
```

#### 3.5.6 models/Model.php
```php
[Clase base con CRUD genérico]
```

#### 3.5.7 models/Usuario.php
```php
[Modelo completo de ejemplo]
```

#### 3.5.8 database/seed.sql
```sql
[Datos de prueba realistas]
```

### 3.6 Seguridad en Fase 1
- Prepared statements (siempre)
- Password hashing (bcrypt)
- Sesiones seguras (httponly, secure)
- Protección CSRF
- SQL injection prevention
- XSS prevention
- Validación de entrada
- Sanitización de salida

### 3.7 Checklist de Completitud Fase 1
- [ ] Sistema de login funciona
- [ ] Logout cierra sesión correctamente
- [ ] Roles definidos en BD
- [ ] Permisos funcionan
- [ ] Middleware protege rutas
- [ ] Datos de prueba cargados
- [ ] Tests pasan exitosamente
- [ ] Código documentado
- [ ] Git commits organizados
- [ ] FASE-1-COMPLETADA.md creado

### 3.8 Errores Comunes en Fase 1
[Basados en experiencia real]

### 3.9 Preparación para Fase 2
- Archivos a adjuntar a Claude
- Prompt sugerido
- Estructura lista para módulos

## PARTE 4: FASE 2 - MÓDULOS CORE DEL NEGOCIO

### 4.1 Objetivos de la Fase
- Implementar módulos críticos del sistema
- Backend completo de funcionalidades core
- APIs REST (si aplica)
- Lógica de negocio

### 4.2 Pre-requisitos
- [ ] Fase 1 100% completada
- [ ] Autenticación funcionando
- [ ] Permisos implementados
- [ ] Datos de prueba disponibles

### 4.3 Metodología de Desarrollo por Módulo

#### Enfoque Recomendado: Un Módulo a la Vez
**NO hacer:** Empezar todos los módulos simultáneamente
**SÍ hacer:** Completar un módulo 100% antes del siguiente

#### Orden de Implementación
1. Módulo más CRÍTICO según priorización de Fase 0
2. Módulo con más DEPENDENCIAS (si otros dependen de él)
3. Módulos IMPORTANTES
4. Módulos DESEABLES

### 4.4 Estructura de un Módulo Completo

Para cada módulo, implementar:

#### 4.4.1 Backend del Módulo
```
modules/[nombre_modulo]/
├── index.php (lista/tabla)
├── crear.php (formulario nuevo)
├── editar.php (formulario edición)
├── ver.php (detalle)
├── eliminar.php (confirmación)
└── procesar.php (lógica de guardado)
```

#### 4.4.2 Modelo del Módulo
```php
models/[NombreModelo].php
- Hereda de Model.php
- Métodos específicos del negocio
- Validaciones de negocio
- Cálculos especiales
```

#### 4.4.3 API del Módulo (opcional)
```php
api/[nombre_modulo]/
├── listar.php
├── crear.php
├── actualizar.php
├── eliminar.php
└── buscar.php
```

### 4.5 Desarrollo Paso a Paso de un Módulo

#### PASO 1: Modelo (Backend Puro)
```php
[Código completo del modelo con todos los métodos]
```

#### PASO 2: Lógica de Procesamiento
```php
[procesar.php con validaciones y transacciones]
```

#### PASO 3: Tests del Módulo
```php
[tests/test-[modulo].php]
```

#### PASO 4: Vistas Básicas (HTML mínimo)
```php
[Formularios y tablas básicas]
```

### 4.6 Patrones a Seguir en Módulos

#### Patrón de Validación
```php
[Código de ejemplo de validación robusta]
```

#### Patrón de Transacciones
```php
[Código de ejemplo con try-catch y rollback]
```

#### Patrón de Auditoría
```php
[Código para registrar en audit_log]
```

### 4.7 Módulos Típicos y Sus Particularidades

#### Módulo de Inventario
- Control de stock
- Movimientos de entrada/salida
- Alertas de stock mínimo
- Transferencias entre sucursales

#### Módulo de Ventas/Punto de Venta
- Múltiples formas de pago
- Actualización de inventario
- Generación de facturas
- Créditos

#### Módulo de Clientes/Pacientes
- Historial completo
- Relaciones con otras entidades
- Búsquedas rápidas

#### Módulo de Caja
- Apertura/cierre
- Cuadre de caja
- Movimientos detallados

#### Módulo de Reportes
- Consultas complejas
- Exportación (PDF, Excel)
- Filtros avanzados
- Gráficos

### 4.8 Checklist por Módulo

Para cada módulo completado:
- [ ] Modelo implementado
- [ ] CRUD completo funciona
- [ ] Validaciones robustas
- [ ] Transacciones donde corresponde
- [ ] Auditoría registrada
- [ ] Tests pasan
- [ ] Permisos verificados
- [ ] Datos de prueba agregados
- [ ] Documentado en código
- [ ] Commit organizado

### 4.9 Checklist General de Fase 2
- [ ] Todos los módulos CRÍTICOS completos
- [ ] Módulos IMPORTANTES implementados
- [ ] Integración entre módulos funciona
- [ ] Tests de integración pasan
- [ ] Documentación actualizada
- [ ] seed.sql con datos de todos los módulos
- [ ] FASE-2-COMPLETADA.md creado
- [ ] Git organizado por módulos

### 4.10 Preparación para Fase 3
[Contexto y archivos para Frontend]

## PARTE 5: FASE 3 - FRONTEND E INTEGRACIÓN

### 5.1 Objetivos de la Fase
- Interfaces visuales profesionales
- Experiencia de usuario fluida
- Integración completa frontend-backend
- Responsive design
- Interactividad (AJAX donde mejore UX)

### 5.2 Pre-requisitos
- [ ] Fase 2 100% completada
- [ ] Todos los módulos backend funcionan
- [ ] APIs disponibles (si aplica)
- [ ] Wireframes de Fase 0 disponibles

### 5.3 Stack Frontend Recomendado
- HTML5 semántico
- CSS3 (o Bootstrap 5)
- JavaScript Vanilla
- AJAX (fetch API)
- NO frameworks (React, Vue, etc.)

### 5.4 Estructura de Desarrollo

#### DÍA 1-2: Plantillas Base
- header.php
- footer.php
- navbar.php
- sidebar.php
- Estructura HTML común

#### DÍA 3-4: Dashboard
- Panel principal
- KPIs importantes
- Gráficos (Chart.js)
- Accesos rápidos

#### DÍA 5-8: Frontend por Módulo
- Un módulo a la vez
- Conectar con backend existente
- Validaciones en cliente
- Mensajes de feedback

#### DÍA 9-10: Interactividad y UX
- Búsquedas en tiempo real
- Autocomplete
- Confirmaciones
- Mensajes toast
- Loading states

### 5.5 Componentes Frontend

[Código de componentes reutilizables]

#### 5.5.1 Sistema de Mensajes
```php
[Código para mostrar success/error/warning]
```

#### 5.5.2 Tablas con DataTables
```javascript
[Configuración de tablas interactivas]
```

#### 5.5.3 Formularios con Validación
```javascript
[Validación en cliente antes de submit]
```

#### 5.5.4 Modales Reutilizables
```html/javascript
[Código de modales para confirmaciones]
```

### 5.6 Integración Frontend-Backend

#### Patrón de Integración
```javascript
[fetch API, manejo de respuestas, errores]
```

#### AJAX para Operaciones Comunes
```javascript
[Código para búsquedas, filtros, etc.]
```

### 5.7 Checklist de Fase 3
- [ ] Todas las vistas creadas
- [ ] Responsive en móvil/tablet/desktop
- [ ] Navegación intuitiva
- [ ] Validación en cliente
- [ ] Mensajes de feedback claros
- [ ] Loading states implementados
- [ ] Cross-browser compatible
- [ ] Sin errores en consola
- [ ] FASE-3-COMPLETADA.md creado

### 5.8 Preparación para Fase 4
[Contexto para pruebas]

## PARTE 6: FASE 4 - PRUEBAS Y OPTIMIZACIÓN

### 6.1 Objetivos de la Fase
- Testing exhaustivo
- Corrección de bugs
- Optimización de performance
- Mejoras de UX
- Preparación para producción

### 6.2 Tipos de Pruebas

#### 6.2.1 Pruebas Funcionales
- Cada funcionalidad hace lo que debe
- Happy path
- Edge cases
- Casos de error

#### 6.2.2 Pruebas de Integración
- Módulos trabajan juntos
- Flujos completos funcionan
- Datos fluyen correctamente

#### 6.2.3 Pruebas de Seguridad
- SQL injection (intentar)
- XSS (intentar)
- CSRF verificado
- Sesiones seguras
- Permisos no bypasseables

#### 6.2.4 Pruebas de Performance
- Queries lentas
- N+1 problems
- Carga de imágenes
- Tiempo de respuesta

#### 6.2.5 Pruebas de Usuario
- Cliente prueba el sistema
- Usuarios reales prueban
- Feedback de UX

### 6.3 Checklist de Pruebas Completo
[Lista exhaustiva de qué probar]

### 6.4 Optimización

#### 6.4.1 Base de Datos
- Índices faltantes
- Queries ineficientes
- Normalización vs desnormalización

#### 6.4.2 Backend
- Caching de queries
- Lazy loading
- Eliminación de código muerto

#### 6.4.3 Frontend
- Minificación
- Compresión de imágenes
- Lazy loading de imágenes
- Carga diferida

### 6.5 Checklist de Fase 4
- [ ] Todas las pruebas realizadas
- [ ] Bugs críticos corregidos
- [ ] Bugs importantes corregidos
- [ ] Performance aceptable
- [ ] Cliente satisfecho con pruebas
- [ ] Documentación de bugs conocidos
- [ ] FASE-4-COMPLETADA.md creado

### 6.6 Preparación para Fase 5
[Contexto para deployment]

## PARTE 7: FASE 5 - DEPLOYMENT Y PRODUCCIÓN

### 7.1 Objetivos de la Fase
- Subir a servidor de producción
- Configurar dominio
- SSL/HTTPS
- Backups automáticos
- Monitoreo
- Capacitación

### 7.2 Checklist Pre-Deployment
- [ ] Todas las fases anteriores completadas
- [ ] Cliente aprobó el sistema
- [ ] Datos reales listos para migrar
- [ ] Hosting contratado
- [ ] Dominio apuntando

### 7.3 Proceso de Deployment

#### 7.3.1 Preparación del Código
```bash
[Comandos y configuraciones para producción]
```

#### 7.3.2 Configuración del Servidor
```bash
[Apache/Nginx, PHP, MySQL en producción]
```

#### 7.3.3 Migración de BD
```bash
[Cómo migrar datos de desarrollo a producción]
```

#### 7.3.4 Configuración de SSL
```bash
[Let's Encrypt o certificado pagado]
```

### 7.4 Post-Deployment

#### 7.4.1 Verificación
- [ ] Todas las URLs funcionan
- [ ] HTTPS forzado
- [ ] Login funciona
- [ ] Todas las funcionalidades ok
- [ ] Emails se envían
- [ ] Backups configurados

#### 7.4.2 Monitoreo
- Google Analytics
- Error logging
- Uptime monitoring

#### 7.4.3 Capacitación
- Manual de usuario
- Sesiones de capacitación
- Videos tutoriales

### 7.5 Mantenimiento y Soporte
- Periodo de garantía
- Corrección de bugs
- Actualizaciones futuras

### 7.6 Checklist de Fase 5
- [ ] Sistema en producción
- [ ] HTTPS funcionando
- [ ] Backups automáticos
- [ ] Monitoreo activo
- [ ] Usuarios capacitados
- [ ] Documentación entregada
- [ ] PROYECTO-COMPLETADO.md creado

## PARTE 8: PLANTILLAS UNIVERSALES

### 8.1 Plantilla: Prompt para Iniciar Cualquier Fase
```
[Template con variables adaptables]
```

### 8.2 Plantilla: FASE-X-COMPLETADA.md
```markdown
[Template reutilizable para cualquier fase]
```

### 8.3 Plantilla: Email de Validación con Cliente
```
[Template adaptable a cualquier fase]
```

### 8.4 Plantilla: Checklist de Fase
```markdown
[Template con checkboxes reutilizable]
```

## PARTE 9: GESTIÓN DEL PROYECTO

### 9.1 Control de Versiones (Git)
- Estrategia de branching
- Commits organizados
- Tags por fase

### 9.2 Gestión del Tiempo
- Estimaciones realistas
- Buffer de tiempo
- Qué hacer si te atrasas

### 9.3 Comunicación con Cliente
- Frecuencia de updates
- Manejo de cambios
- Expectativas claras

### 9.4 Trabajo con Claude
- Cómo estructurar sesiones
- Gestión de contexto
- Documentación entre sesiones

## PARTE 10: CASOS DE ESTUDIO

### 10.1 Proyecto Joyería Torre Fuerte
[Timeline real, decisiones, resultados]

### 10.2 Proyecto Clínica
[Timeline real, decisiones, resultados]

### 10.3 Lecciones Aprendidas
[Qué funcionó, qué no, qué mejorar]

## APÉNDICES

### A. Código de Referencia Completo
[Todos los archivos base con código completo]

### B. Stack Tecnológico Recomendado
[Por tipo de proyecto]

### C. Herramientas Útiles
[Lista curada de herramientas]

### D. Recursos de Aprendizaje
[Libros, cursos, documentación]

### E. Glosario de Términos
[Términos técnicos explicados]
```

**REQUISITOS DE CALIDAD:**

1. **Código completo y funcional** en TODOS los ejemplos
2. **Explicaciones claras** del "por qué" de cada decisión
3. **Basado en experiencia real** de los proyectos completados
4. **Énfasis en seguridad** en cada fase
5. **Plantillas reutilizables** para cualquier proyecto
6. **Checklists verificables** para medir progreso
7. **Ejemplos diversos** (joyería, clínica, restaurante, etc.)
8. **Warnings claros** de errores comunes
9. **Formato profesional** con markdown bien estructurado
10. **Autosuficiente** - no requiere contexto externo

**OBJETIVO FINAL:**

Una guía COMPLETA que pueda usar en CUALQUIER proyecto de software 
de gestión, en CUALQUIER fase, con CUALQUIER cliente, y que me 
garantice resultados profesionales consistentes.

Esta guía será la BIBLIA de mi metodología de desarrollo.

¿Puedes crear esta guía universal completa ahora?
```