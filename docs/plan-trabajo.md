# PLAN DE TRABAJO — SISTEMA CLÍNICO DR. BYRON CASTILLO
**Proyecto:** Sistema Clínico Ginecología — Clínica Médica de la Mujer  
**Desarrollador:** Gerbert David García Loaiza — GG-Systems  
**Cliente:** Dr. Byron Daniel Castillo Perea  
**Fecha inicio:** Marzo 2025  
**Fecha entrega Fase 1:** ~Semana 3  
**Fecha entrega final:** ~Semana 4  
**Stack:** PHP 8.1+ vanilla · MySQL · Bootstrap 5 · JS Vanilla · TCPDF

---

## RESUMEN EJECUTIVO DE FASES

| Fase | Nombre | Duración estimada | Rama Git |
|------|--------|-------------------|----------|
| 0 | Planificación y Diseño | 2-3 días | `fase-0-planificacion` |
| 1 | Arquitectura y Base de Datos | 2-3 días | `fase-1-arquitectura` |
| 2.1 | Backend — Autenticación | 1-2 días | `fase-2.1-autenticacion` |
| 2.2 | Backend — Pacientes/Expediente | 2 días | `fase-2.2-backend-pacientes` |
| 2.3 | Backend — Citas/Agenda | 1-2 días | `fase-2.3-backend-citas` |
| 2.4 | Backend — Consultas/Signos | 1 día | `fase-2.4-backend-consultas` |
| 2.5 | Backend — Recetas | 1 día | `fase-2.5-backend-recetas` |
| 2.6 | Backend — Ultrasonido | 1 día | `fase-2.6-backend-ultrasonido` |
| 2.7 | Backend — Facturación | 1-2 días | `fase-2.7-backend-facturacion` |
| 2.8 | Backend — Laboratorios/Inventario | 1 día | `fase-2.8-backend-extras` |
| 3 | APIs y Endpoints AJAX | 2 días | `fase-3-apis` |
| 4 | Frontend Base (header/nav/dashboard) | 2 días | `fase-4-frontend-base` |
| 5.1 | Frontend — Módulo Pacientes | 1-2 días | `fase-5.1-frontend-pacientes` |
| 5.2 | Frontend — Módulo Citas | 1-2 días | `fase-5.2-frontend-citas` |
| 5.3 | Frontend — Módulo Consultas/Recetas | 2 días | `fase-5.3-frontend-consultas` |
| 5.4 | Frontend — Módulo Ultrasonido | 1 día | `fase-5.4-frontend-ultrasonido` |
| 5.5 | Frontend — Módulo Facturación | 1-2 días | `fase-5.5-frontend-facturacion` |
| 5.6 | Frontend — Recordatorios/Landing | 1 día | `fase-5.6-frontend-extras` |
| 6 | Integraciones (PDFs con TCPDF) | 2-3 días | `fase-6-pdfs` |
| 7 | Módulos Fase 2 (Labs + Inventario) | 2 días | `fase-7-fase2-clinica` |
| 8 | Pruebas y Corrección de Bugs | 2 días | `fase-8-pruebas` |
| 9 | Deployment y Capacitación | 1-2 días | `fase-9-deployment` |

---

## FASE 0 — PLANIFICACIÓN Y DISEÑO
**Duración:** 2-3 días  
**Rama Git:** `fase-0-planificacion`  
**Objetivo:** Tener todo planificado antes de escribir código.

### Tareas
- [x] Requerimientos documentados y aprobados por Byron *(ya están en requisitos_sistema_clinico.md)*
- [x] Propuesta firmada y anticipo Q1,200 recibido
- [ ] Crear repositorio GitHub (privado)
- [ ] Crear estructura base de carpetas en el proyecto
- [ ] Crear `.gitignore` adecuado
- [ ] **Diseñar base de datos COMPLETA** (todas las tablas, relaciones, tipos)
- [ ] Validar diseño BD con Claude antes de implementar
- [ ] Crear diagrama ER (Draw.io o Excalidraw) → guardar en `/docs/diseño/`
- [ ] Definir wireframes de pantallas principales (Figma o papel)
- [ ] Mostrar wireframes a Byron para aprobación
- [ ] Definir tabla de roles y permisos documentada
- [ ] Definir casos de uso de: Consulta completa, Registro receta, Facturación

### Entregables de Fase 0
- Diagrama ER exportado
- `/docs/plan-de-fases.md` (este documento)
- `/docs/roles-permisos.md`
- `/docs/casos-de-uso.md`
- Wireframes guardados en `/docs/diseño/`

### Checklist de salida
- [ ] Byron aprobó wireframes
- [ ] BD diseñada y validada con Claude
- [ ] Git inicializado y en GitHub
- [ ] Estructura base creada

---

## FASE 1 — ARQUITECTURA Y BASE DE DATOS
**Duración:** 2-3 días  
**Rama Git:** `fase-1-arquitectura`  
**Objetivo:** Infraestructura completa: carpetas, config, BD con datos de prueba.

### Estructura de Carpetas del Proyecto
```
/sistema-clinico/
├── /assets/
│   ├── /css/          (bootstrap.min.css, bootstrap-icons.css, estilos.css)
│   ├── /js/           (bootstrap.bundle.min.js, funciones.js, módulos específicos)
│   └── /img/          (logo clinica, foto doctor, firma)
├── /includes/
│   ├── db.php
│   ├── auth.php
│   ├── funciones.php
│   ├── header.php
│   ├── navbar.php
│   └── footer.php
├── /models/
│   ├── paciente.php
│   ├── cita.php
│   ├── consulta.php
│   ├── receta.php
│   ├── ultrasonido.php
│   ├── factura.php
│   ├── examen.php
│   ├── inventario.php
│   └── usuario.php
├── /modules/
│   ├── /pacientes/
│   ├── /citas/
│   ├── /consultas/
│   ├── /recetas/
│   ├── /ultrasonido/
│   ├── /facturacion/
│   ├── /laboratorios/
│   ├── /inventario/
│   └── /usuarios/
├── /api/
│   ├── /pacientes/
│   ├── /citas/
│   ├── /consultas/
│   └── ... (un directorio por módulo)
├── /pdf/              (librería TCPDF y generadores)
├── /uploads/          (PDFs generados, logos)
├── /database/
│   ├── schema.sql
│   └── seed.sql
├── /docs/
├── /logs/
├── config.php
├── config.example.php
├── index.php          (login)
├── dashboard.php
├── logout.php
└── landing.php        (página pública del doctor)
```

### Tablas de la Base de Datos
*(A diseñar completamente en Fase 0, implementar aquí)*

**Tablas core:**
- `usuarios` (id, nombre, email, password, rol, activo, fechas)
- `pacientes` (id, codigo, nombre, fecha_nac, dpi, telefono, direccion, contacto_emergencia, antec_personales, antec_familiares, antec_quirurgicos, alergias, activo, fechas)
- `citas` (id, paciente_id, fecha, hora_inicio, hora_fin, motivo, estado, notas, usuario_id, fechas)
- `consultas` (id, paciente_id, cita_id, usuario_id, fecha, notas, diagnostico, fechas)
- `signos_vitales` (id, consulta_id, peso, talla, presion_arterial, temperatura, fecha)
- `recetas` (id, consulta_id, paciente_id, indicaciones_generales, fecha_emision, fechas)
- `recetas_detalle` (id, receta_id, medicamento, dosis, via, frecuencia, duracion)
- `ultrasonidos` (id, consulta_id, paciente_id, fecha, edad_gestacional, dbp, cc, ca, lf, peso_estimado, observaciones, conclusion, fechas)
- `facturas` (id, paciente_id, consulta_id, numero_correlativo, fecha, concepto, monto, forma_pago, monto_pagado, cambio, estado, usuario_id, fechas)
- `examenes_laboratorio` (id, paciente_id, consulta_id, tipo_examen, fecha_solicitud, descripcion, estado, resultado, fecha_resultado, fechas)
- `inventario_productos` (id, nombre, unidad, cantidad_actual, stock_minimo, activo, fechas)
- `inventario_movimientos` (id, producto_id, tipo, cantidad, motivo, usuario_id, fecha)

### Tareas
- [ ] Crear rama `fase-1-arquitectura`
- [ ] Crear estructura de carpetas completa
- [ ] Crear `config.example.php` y `config.php`
- [ ] Crear `includes/db.php`
- [ ] Crear `includes/funciones.php` (helpers base)
- [ ] Escribir `database/schema.sql` completo
- [ ] Ejecutar schema en phpMyAdmin → base de datos creada
- [ ] Escribir `database/seed.sql` con datos de prueba realistas
- [ ] Ejecutar seed → datos de prueba cargados
- [ ] Crear `test-conexion.php` y verificar funcionamiento
- [ ] Eliminar `test-conexion.php` después de verificar
- [ ] Commit: *"Fase 1 completa: Arquitectura y base de datos"*

---

## FASE 2 — BACKEND (LÓGICA DE NEGOCIO)
**Objetivo:** Implementar TODA la lógica sin tocar vistas. Solo modelos y funciones.

---

### FASE 2.1 — Autenticación
**Rama:** `fase-2.1-autenticacion`

**Archivos a crear:**
- `includes/auth.php` — `verificarSesion()`, `verificarRol()`, `obtenerUsuarioActual()`
- `models/usuario.php` — CRUD usuarios, validar credenciales
- `login.php` — Formulario + procesamiento
- `logout.php`

**Funciones mínimas en `models/usuario.php`:**
- `validarCredenciales($pdo, $email, $password)`
- `obtenerUsuarios($pdo)`
- `crearUsuario($pdo, $datos)`
- `actualizarUsuario($pdo, $id, $datos)`
- `cambiarPassword($pdo, $id, $nueva_password)`
- `desactivarUsuario($pdo, $id)`

**Roles del sistema:**
| Rol | Acceso |
|-----|--------|
| `admin` | Todo el sistema, gestión de usuarios |
| `medico` | Pacientes, consultas, recetas, ultrasonidos, citas, facturación |
| `asistente` | Citas, datos básicos pacientes, facturación |

---

### FASE 2.2 — Backend Pacientes/Expediente
**Rama:** `fase-2.2-backend-pacientes`  
**Archivo:** `models/paciente.php`

**Funciones:**
- `obtenerPacientes($pdo, $filtros)` — con búsqueda y paginación
- `obtenerPacientePorId($pdo, $id)`
- `buscarPacientes($pdo, $termino)`
- `crearPaciente($pdo, $datos)`
- `actualizarPaciente($pdo, $id, $datos)`
- `desactivarPaciente($pdo, $id)`
- `obtenerHistorialCompleto($pdo, $paciente_id)` — consultas, recetas, ultrasonidos

---

### FASE 2.3 — Backend Citas
**Rama:** `fase-2.3-backend-citas`  
**Archivo:** `models/cita.php`

**Funciones:**
- `obtenerCitas($pdo, $filtros)` — por fecha, paciente, estado
- `obtenerCitasDelDia($pdo, $fecha)`
- `obtenerCitasProximas($pdo, $dias)` — para recordatorios WhatsApp
- `crearCita($pdo, $datos)`
- `actualizarCita($pdo, $id, $datos)`
- `cambiarEstadoCita($pdo, $id, $estado)`
- `cancelarCita($pdo, $id)`
- `obtenerCitasCalendario($pdo, $inicio, $fin)` — formato FullCalendar JSON

---

### FASE 2.4 — Backend Consultas y Signos Vitales
**Rama:** `fase-2.4-backend-consultas`  
**Archivos:** `models/consulta.php`

**Funciones:**
- `crearConsulta($pdo, $datos)`
- `actualizarConsulta($pdo, $id, $datos)`
- `obtenerConsultasPorPaciente($pdo, $paciente_id)`
- `obtenerConsultaPorId($pdo, $id)`
- `guardarSignosVitales($pdo, $consulta_id, $datos)`
- `obtenerEvolucionSignos($pdo, $paciente_id)` — para gráfica de evolución

---

### FASE 2.5 — Backend Recetas
**Rama:** `fase-2.5-backend-recetas`  
**Archivo:** `models/receta.php`

**Funciones:**
- `crearReceta($pdo, $datos)` — incluye medicamentos en transacción
- `obtenerRecetaPorId($pdo, $id)`
- `obtenerRecetasPorPaciente($pdo, $paciente_id)`
- `obtenerMedicamentosReceta($pdo, $receta_id)`

---

### FASE 2.6 — Backend Ultrasonido
**Rama:** `fase-2.6-backend-ultrasonido`  
**Archivo:** `models/ultrasonido.php`

**Funciones:**
- `crearUltrasonido($pdo, $datos)`
- `obtenerUltrasonidoPorId($pdo, $id)`
- `obtenerUltrasonidosPorPaciente($pdo, $paciente_id)`

---

### FASE 2.7 — Backend Facturación
**Rama:** `fase-2.7-backend-facturacion`  
**Archivo:** `models/factura.php`

**Funciones:**
- `generarNumeroCorrelativo($pdo)`
- `crearFactura($pdo, $datos)`
- `obtenerFacturasPorPaciente($pdo, $paciente_id)`
- `obtenerReporteIngresos($pdo, $fecha_inicio, $fecha_fin)`
- `obtenerResumenDiario($pdo, $fecha)`
- `obtenerResumenMensual($pdo, $mes, $año)`

---

### FASE 2.8 — Backend Laboratorios e Inventario
**Rama:** `fase-2.8-backend-extras`  
**Archivos:** `models/examen.php`, `models/inventario.php`

**Funciones en `models/examen.php`:**
- `registrarExamen($pdo, $datos)`
- `actualizarExamen($pdo, $id, $datos)`
- `registrarResultado($pdo, $examen_id, $resultado)`
- `obtenerExamenesPorPaciente($pdo, $paciente_id)`

**Funciones en `models/inventario.php`:**
- `obtenerProductos($pdo)`
- `registrarMovimiento($pdo, $datos)` — entrada/salida/venta en transacción
- `obtenerProductosStockBajo($pdo)`
- `obtenerMovimientosPorProducto($pdo, $producto_id)`

---

## FASE 3 — APIs Y ENDPOINTS AJAX
**Duración:** 2 días  
**Rama:** `fase-3-apis`  
**Objetivo:** Endpoints que el frontend consumirá via fetch/AJAX.

### Endpoints a crear
*(Formato: `api/[módulo]/[acción].php`)*

**Pacientes:** buscar.php, listar.php  
**Citas:** listar-calendario.php, citas-proximas.php, cambiar-estado.php  
**Facturación:** generar-correlativo.php, reporte-ingresos.php  
**Dashboard:** estadisticas.php (resumen del día)

> ℹ️ La mayoría de operaciones CRUD van directo por POST en los módulos PHP. Solo se crean endpoints AJAX para operaciones que lo requieren (búsquedas en tiempo real, cambios de estado sin reload, datos de calendario).

**Respuesta JSON estándar:**
```json
{ "success": true, "data": {}, "message": "Operación exitosa" }
{ "success": false, "error": "Descripción", "code": "CODIGO" }
```

---

## FASE 4 — FRONTEND BASE
**Duración:** 2 días  
**Rama:** `fase-4-frontend-base`  
**Objetivo:** Estructura visual: header, navbar, footer, dashboard con datos reales básicos.

### Archivos a crear
- `includes/header.php`
- `includes/navbar.php` *(con menú dinámico según rol)*
- `includes/footer.php`
- `dashboard.php` *(tarjetas de estadísticas del día)*
- `assets/css/estilos.css` *(personalización sobre Bootstrap)*
- `assets/js/funciones.js` *(helpers JS globales)*

### Librerías a descargar (locales, NO CDN)
- Bootstrap 5.3 (CSS + JS bundle)
- Bootstrap Icons
- SweetAlert2 (confirmaciones)
- FullCalendar (para citas)
- TCPDF (backend, para PDFs)

---

## FASE 5 — FRONTEND MÓDULOS FUNCIONALES
**Objetivo:** Conectar frontend con backend. Todo funcional.

---

### FASE 5.1 — Módulo Pacientes
**Rama:** `fase-5.1-frontend-pacientes`

**Pantallas:**
- `modules/pacientes/lista.php` — tabla con búsqueda, paginación
- `modules/pacientes/nuevo.php` — formulario completo
- `modules/pacientes/editar.php` — editar datos
- `modules/pacientes/expediente.php` — historial completo del paciente
- `modules/pacientes/nueva-consulta.php` — iniciar consulta desde expediente

---

### FASE 5.2 — Módulo Citas
**Rama:** `fase-5.2-frontend-citas`

**Pantallas:**
- `modules/citas/calendario.php` — vista FullCalendar
- `modules/citas/lista.php` — agenda del día (lista)
- `modules/citas/nueva.php` — agendar cita
- `modules/citas/editar.php` — editar cita

---

### FASE 5.3 — Módulo Consultas y Recetas
**Rama:** `fase-5.3-frontend-consultas`

**Pantallas:**
- `modules/consultas/nueva.php` — formulario de consulta + signos vitales + diagnóstico
- `modules/consultas/ver.php` — ver consulta completa
- `modules/recetas/nueva.php` — crear receta desde consulta (agregar medicamentos dinámicamente con JS)
- `modules/recetas/ver.php` — ver receta + botón descargar PDF

---

### FASE 5.4 — Módulo Ultrasonido
**Rama:** `fase-5.4-frontend-ultrasonido`

**Pantallas:**
- `modules/ultrasonido/nuevo.php` — formulario medidas ginecológicas
- `modules/ultrasonido/ver.php` — ver informe + botón descargar PDF
- `modules/ultrasonido/historial.php` — ultrasonidos por paciente

---

### FASE 5.5 — Módulo Facturación
**Rama:** `fase-5.5-frontend-facturacion`

**Pantallas:**
- `modules/facturacion/nueva.php` — generar recibo de consulta
- `modules/facturacion/historial.php` — historial de pagos
- `modules/facturacion/reportes.php` — ingresos por fecha/mes
- `modules/facturacion/ver.php` — ver recibo + botón descargar PDF

---

### FASE 5.6 — Recordatorios, Usuarios y Landing
**Rama:** `fase-5.6-frontend-extras`

**Pantallas:**
- `modules/recordatorios/index.php` — lista citas próximas + botones WhatsApp
- `modules/usuarios/index.php` — gestión de usuarios (solo admin)
- `landing.php` — página pública del doctor (accesible sin login)

**Lógica del botón WhatsApp:**
```javascript
// Genera link con mensaje pre-escrito
const mensaje = encodeURIComponent(
  `Hola ${nombre}, le recordamos su cita para el ${fecha} a las ${hora} con el Dr. Byron Castillo. ¡Le esperamos!`
);
window.open(`https://wa.me/502${telefono}?text=${mensaje}`, '_blank');
```

---

## FASE 6 — INTEGRACIÓN PDFS (TCPDF)
**Duración:** 2-3 días  
**Rama:** `fase-6-pdfs`  
**Objetivo:** Los 3 documentos PDF generándose correctamente con membrete profesional.

### PDFs a implementar

**1. Receta Médica** — `pdf/generar-receta.php`
- Header: Logo clínica + Nombre doctor + Especialidad + No. Colegiado
- Datos paciente: Nombre, edad, fecha
- Tabla medicamentos: Medicamento | Dosis | Vía | Frecuencia | Duración
- Indicaciones generales
- Espacio para firma digital
- Footer: Dirección clínica + teléfono

**2. Informe de Ultrasonido** — `pdf/generar-ultrasonido.php`
- Header: Logo + datos doctor
- Datos paciente + fecha + edad gestacional
- Tabla medidas: DBP | CC | CA | LF | Peso estimado
- Observaciones
- Conclusión
- Firma

**3. Recibo de Pago** — `pdf/generar-recibo.php`
- Header: Logo
- No. correlativo + fecha
- Datos paciente
- Concepto
- Monto + forma de pago
- Footer

---

## FASE 7 — MÓDULOS FASE 2 CLÍNICA (Labs + Inventario)
**Duración:** 2 días  
**Rama:** `fase-7-fase2-clinica`

**Pantallas:**
- `modules/laboratorios/lista.php` — exámenes por paciente
- `modules/laboratorios/nuevo.php` — registrar examen
- `modules/laboratorios/resultado.php` — ingresar resultado
- `modules/inventario/lista.php` — stock actual + alertas
- `modules/inventario/movimiento.php` — registrar entrada/salida

---

## FASE 8 — PRUEBAS Y CORRECCIÓN DE BUGS
**Duración:** 2 días  
**Rama:** `fase-8-pruebas`

### Flujo de prueba completo (el más importante)
1. Registrar paciente nuevo
2. Agendar cita
3. Marcar cita como atendida
4. Abrir consulta → registrar signos vitales → escribir diagnóstico
5. Crear receta con 2-3 medicamentos → generar PDF
6. Crear informe ultrasonido → generar PDF
7. Generar recibo de pago → descargar PDF
8. Verificar expediente completo del paciente (que aparezca todo)
9. Enviar recordatorio WhatsApp de otra cita
10. Ver reporte de ingresos del día

### Pruebas de seguridad
- [ ] Acceder sin login → redirige a login
- [ ] Asistente intenta acceder a módulo admin → acceso denegado
- [ ] Intentar SQL injection en búsqueda de pacientes
- [ ] Verificar passwords hasheados en phpMyAdmin

### Pruebas de compatibilidad
- [ ] Chrome ✓
- [ ] Firefox ✓
- [ ] Edge ✓

---

## FASE 9 — DEPLOYMENT Y CAPACITACIÓN
**Duración:** 1-2 días  
**Rama:** `fase-9-deployment`

### Pasos de deployment
1. Merge de todas las ramas a `main`
2. Crear BD en Hostinger desde hPanel
3. Ejecutar `schema.sql` en BD de producción
4. Subir archivos con FileZilla (excluir: `/logs/`, `test-*.php`, `.git/`)
5. Crear `config.php` en producción con datos reales de Hostinger
6. Verificar que todo funcione en dominio real
7. Crear usuario admin de Byron + usuario asistente
8. Subir logo, foto y firma del doctor a `/uploads/img/`

### Sesión de capacitación con Byron
**Duración:** 2-3 horas  
**Temas:**
1. Login y cambio de contraseña
2. Registrar paciente nuevo / buscar paciente existente
3. Agendar y gestionar citas
4. Proceso de consulta completo
5. Generar receta + imprimir
6. Informe de ultrasonido + imprimir
7. Cobrar consulta + imprimir recibo
8. Enviar recordatorio WhatsApp
9. Ver reportes de ingresos
10. Registrar exámenes de laboratorio (Fase 2)
11. Control básico de inventario (Fase 2)

### Entregables finales
- [ ] Sistema funcionando en producción
- [ ] Manual de usuario (PDF con screenshots)
- [ ] Credenciales de acceso entregadas a Byron
- [ ] Acta de entrega firmada
- [ ] Cobro final Q800 (Fase 2) realizado

---

## COBROS RELACIONADOS AL PLAN

| Hito | Monto | Momento |
|------|-------|---------|
| Inicio (anticipo) | Q1,200 | Al firmar/iniciar |
| Entrega Fase 1 (hasta Fase 6 de este plan) | Q1,000 | Semana 3 |
| Entrega Fase 2 (Fase 7 en adelante) | Q800 | Semana 4 |
| **Total desarrollo** | **Q3,000** | |
| Mensualidad (desde mes 2) | Q200/mes | Recurrente |

---

## NOTAS IMPORTANTES

> **Simplicidad primero.** Si algo se puede hacer simple, se hace simple. No se agrega complejidad innecesaria.

> **Validar con Byron.** Después de Fase 5.3 (consultas + recetas) mostrar demo a Byron antes de continuar.

> **Materiales pendientes de Byron:** Logo clínica, foto profesional, datos membrete completos, firma digital (imagen escaneada). Sin esto no se pueden generar los PDFs finales.

> **PDFs.** Se implementan DESPUÉS de tener todo el backend funcional. No antes.

> **Git.** Un commit por cada sub-fase completada. Mensajes descriptivos en español.

---

*Versión 1.0 — Marzo 2025 — GG-Systems*