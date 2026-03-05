# REQUISITOS DEL SISTEMA CLÍNICO - DR. BYRON CASTILLO

**Proyecto:** Sistema Clínico de Ginecología  
**Cliente:** Dr. Byron Daniel Castillo Perea  
**Empresa:** Clínica Médica de la Mujer  
**Desarrollador:** Gerbert David García Loaiza (GG-Systems)  
**Fecha:** Febrero 2025  
**Versión:** 1.0

---

## 1. INFORMACIÓN DEL CLIENTE

### 1.1 Datos del Cliente
- **Nombre completo:** Dr. Byron Daniel Castillo Perea
- **Especialidad:** Ginecología y Obstetricia
- **Clínica:** Clínica Médica de la Mujer
- **Ubicación:** Interior hospital privado, Huehuetenango, Guatemala, Zona 5, Colonia Paula María
- **Contacto:** [Pendiente]

### 1.2 Perfil del Establecimiento
- **Tipo:** Clínica especializada
- **Médicos:** 1 (solo el doctor)
- **Pacientes registrados:** 100-500
- **Pacientes mensuales:** 50-100
- **Ubicaciones:** 1 sola (consultorio fijo)
- **Dispositivos:** 2-3 computadoras Windows
- **Internet:** Estable y rápida
- **Sistema operativo:** Windows

### 1.3 Situación Actual
- **Método actual:** Papel (expedientes físicos, agendas escritas)
- **Migración de datos:** No (empezar desde cero)
- **Urgencia:** 2-4 meses (Pronto)
- **Experiencia previa con sistemas:** Sí (no especificado cuál)

---

## 2. ACUERDO COMERCIAL

### 2.1 Inversión y Pagos
- **Inversión total:** Q3,000
- **Mensualidad:** Q200/mes

### 2.2 Estructura de Pagos
| Pago | Momento | Monto | Porcentaje |
|------|---------|-------|------------|
| 1 | Al iniciar proyecto | Q1,200 | 40% |
| 2 | Entrega Fase 1 (3 semanas) | Q1,000 | 33% |
| 3 | Entrega Fase 2 (4 semanas) | Q800 | 27% |

### 2.3 Servicios Incluidos
- Desarrollo completo del sistema (2 fases)
- Hosting en la nube por 1 año
- Dominio (si lo necesita)
- 2-3 usuarios
- Capacitación completa de usuarios
- 2 meses de soporte ilimitado post-entrega
- Actualizaciones del sistema base
- Backups automáticos

### 2.4 Cronograma
- **Fase 1:** 3 semanas (sistema funcional)
- **Fase 2:** 1 semana (complementos)
- **Total:** 4 semanas

---

## 3. MÓDULOS DEL SISTEMA

### 3.1 FASE 1 - Sistema Funcional (Prioridad 1)

#### 3.1.1 Expediente Clínico Digital
**Prioridad:** 1 (Crítico)

**Funcionalidades:**
- Gestión completa de pacientes (CRUD)
- Datos personales:
  - Nombre completo
  - Edad / Fecha de nacimiento
  - DPI
  - Teléfono
  - Dirección
  - Contacto de emergencia
- Antecedentes médicos:
  - Antecedentes personales (texto)
  - Antecedentes familiares (texto)
  - Antecedentes quirúrgicos (texto)
- Alergias (texto libre)
- Historial de consultas por paciente
- Notas de consulta (texto libre por consulta)
- Diagnósticos (texto libre)
- Signos vitales por consulta:
  - Peso (kg)
  - Talla (cm)
  - Presión arterial (mmHg)
  - Temperatura (°C)
- Evolución del paciente en el tiempo
- Búsqueda rápida de pacientes
- Ficha completa del paciente

**Salidas:**
- Visualización de expediente completo
- Historial cronológico de consultas

---

#### 3.1.2 Agenda y Citas
**Prioridad:** 2

**Funcionalidades:**
- Calendario visual (día/semana/mes)
- Crear cita:
  - Fecha y hora
  - Paciente
  - Motivo de cita
  - Duración estimada
- Editar cita existente
- Cancelar cita
- Estados de cita:
  - Programada
  - Confirmada
  - Atendida
  - Cancelada
- Lista de citas del día
- Búsqueda de citas por:
  - Fecha
  - Paciente
  - Estado
- Filtros por médico (aunque sea uno solo, preparar para escalabilidad)

**Salidas:**
- Vista de calendario
- Lista de citas
- Agenda diaria

---

#### 3.1.3 Recetas Médicas
**Prioridad:** 1 (Crítico)

**Funcionalidades:**
- Crear receta vinculada a consulta
- Agregar medicamentos:
  - Nombre del medicamento
  - Dosis
  - Vía de administración
  - Frecuencia
  - Duración del tratamiento
- Indicaciones generales (texto libre)
- Membrete personalizado:
  - Logo de la clínica
  - Datos del doctor
  - Firma digital del doctor
- Guardar receta en historial del paciente
- Ver recetas anteriores del paciente
- Base de datos de medicamentos (opcional: precargada o manual)
- Plantillas de recetas frecuentes (opcional)

**Salidas:**
- PDF de receta imprimible
- Historial de recetas por paciente

---

#### 3.1.4 Informes de Ultrasonido
**Prioridad:** 1 (Crítico - específico de ginecología)

**Funcionalidades:**
- Formulario especializado para ultrasonido
- Campos a capturar:
  - Fecha del ultrasonido
  - Edad gestacional
  - Medidas fetales:
    - Diámetro biparietal (DBP)
    - Circunferencia cefálica (CC)
    - Circunferencia abdominal (CA)
    - Longitud del fémur (LF)
  - Peso estimado del feto
  - Observaciones (texto libre)
  - Conclusión (texto libre)
- Vincular a paciente y consulta
- Generar PDF profesional del informe
- Incluir logo y datos del doctor
- Archivar en expediente del paciente

**Salidas:**
- PDF de informe de ultrasonido
- Historial de ultrasonidos por paciente

---

#### 3.1.5 Facturación Básica
**Prioridad:** 1 (Crítico)

**Funcionalidades:**
- Generar recibo/factura de consulta
- Datos del recibo:
  - Número correlativo
  - Fecha
  - Paciente
  - Concepto (consulta, procedimiento, etc.)
  - Monto
- Registrar pago:
  - Forma de pago (efectivo, tarjeta, transferencia)
  - Monto pagado
  - Cambio (si aplica)
- Imprimir recibo
- Historial de pagos por paciente
- Reporte de ingresos:
  - Por fecha
  - Por mes
  - Por período personalizado
- Control de cuentas por cobrar (opcional)

**Salidas:**
- Recibo imprimible (PDF o directo)
- Reporte de ingresos

**Exclusiones:**
- NO facturación electrónica FEL (muy complejo)
- NO integración con certificadores
- Facturación básica únicamente

---

#### 3.1.6 Landing Page de Presentación
**Prioridad:** 2

**Funcionalidades:**
- Página web profesional del doctor
- Contenido:
  - Foto del doctor
  - Nombre completo
  - Especialidad
  - Descripción breve/biografía
  - Ubicación de la clínica
  - Horarios de atención
  - Teléfono / WhatsApp
  - Email (opcional)
- Botón "Agendar Cita" que:
  - Redirige a WhatsApp del doctor
  - Con mensaje pre-escrito
- Diseño responsive (móvil y desktop)
- Colores y diseño profesional médico

**Salidas:**
- Página web accesible públicamente
- URL personalizada (opcional: dominio propio)

---

#### 3.1.7 Recordatorios Inteligentes
**Prioridad:** 2

**Funcionalidades:**
- Lista de citas próximas:
  - Mañana
  - En 2 días
  - En 1 semana
- Por cada cita, botón "Recordar por WhatsApp"
- Al hacer clic:
  - Genera mensaje pre-escrito:
    - "Hola [Nombre paciente], le recordamos su cita para el [fecha] a las [hora] con Dr. Byron. ¡Le esperamos!"
  - Abre WhatsApp Web con el mensaje listo
  - Número del paciente ya cargado
  - Doctor solo confirma y envía

**Salidas:**
- Vista de citas próximas con botones de acción
- Links a WhatsApp Web

**Exclusiones:**
- NO envío automático de SMS
- NO integración con APIs de mensajería de pago
- NO recordatorios por email automáticos

---

#### 3.1.8 Usuarios y Permisos
**Prioridad:** 1 (Crítico - seguridad)

**Funcionalidades:**
- Sistema de login/logout
- Gestión de usuarios:
  - Crear usuario
  - Editar usuario
  - Desactivar usuario
- Campos de usuario:
  - Nombre completo
  - Usuario (login)
  - Contraseña (encriptada)
  - Rol
  - Estado (activo/inactivo)
- Roles base:
  - Administrador (acceso total)
  - Médico (acceso a pacientes, citas, recetas, consultas)
  - Asistente/Recepcionista (solo citas y datos básicos)
- Cambio de contraseña
- Control de sesiones
- 2-3 usuarios simultáneos

**Salidas:**
- Panel de gestión de usuarios
- Login seguro

---

### 3.2 FASE 2 - Módulos Complementarios

#### 3.2.1 Laboratorios y Exámenes (SIMPLE)
**Prioridad:** 1

**Funcionalidades:**
- Registrar examen solicitado
- Datos del examen:
  - Paciente
  - Tipo de examen (dropdown con comunes + opción texto libre):
    - Hemograma completo
    - Química sanguínea
    - Perfil hormonal
    - Papanicolaou
    - Colposcopia
    - Examen general de orina
    - Ultrasonido (si no está en módulo específico)
    - Otro (texto libre)
  - Fecha solicitado
  - Descripción/observaciones (texto libre)
- Estado del examen:
  - Solicitado
  - Recibido
- Registrar resultado:
  - Campo de texto libre para escribir resultado
  - O subir PDF del resultado (opcional, evaluar espacio)
  - Fecha del resultado
- Ver historial de exámenes por paciente
- Búsqueda de exámenes

**Salidas:**
- Lista de exámenes solicitados
- Historial por paciente
- Ver detalle de examen con resultado

**Exclusiones (para mantener simplicidad):**
- NO formularios específicos por tipo de examen
- NO valores de referencia automáticos
- NO alertas de valores anormales
- NO gráficas de evolución
- NO cálculos automáticos

**Justificación de simplicidad:**
Byron mencionó que los exámenes los "archiva", sugiere que solo necesita:
- Registro de qué se solicitó
- Guardar el resultado (texto o PDF externo)
- Ver historial

---

#### 3.2.2 Inventario de Medicamentos (REDUCIDO)
**Prioridad:** 1

**Funcionalidades:**
- Lista de productos/medicamentos
- Datos del producto:
  - Nombre
  - Cantidad actual en stock
  - Stock mínimo (alerta)
  - Unidad (cajas, frascos, unidades, etc.)
- Registro de movimientos:
  - Tipo: Entrada / Salida / Venta
  - Cantidad
  - Fecha
  - Motivo/descripción (texto corto)
  - Usuario que registra
- Ver historial de movimientos
- Alertas de stock bajo (visual, no automático)
- Búsqueda de productos

**Salidas:**
- Lista de inventario actual
- Historial de movimientos por producto
- Productos con stock bajo

**Exclusiones (NO incluir para mantener simple):**
- NO punto de venta completo
- NO cálculo de precios
- NO facturación de ventas de medicamentos
- NO control de lotes
- NO fechas de vencimiento
- NO múltiples precios
- NO proveedores
- NO órdenes de compra

**Justificación:**
Byron dijo que el inventario es "reducido" y "no es lo más importante", solo quiere llevar control de:
- Qué tiene
- Cuánto tiene
- Entradas y salidas básicas

---

## 4. ESPECIFICACIONES TÉCNICAS

### 4.1 Stack Tecnológico

**Backend:**
- PHP 8.1 o superior
- Sin frameworks (vanilla PHP)
- Patrón MVC simplificado

**Base de Datos:**
- MySQL 8.0 o superior
- Diseño normalizado (3FN)
- Prepared statements obligatorio (seguridad)

**Frontend:**
- HTML5
- CSS3
- Bootstrap 5 (framework CSS)
- JavaScript vanilla (sin jQuery, React, Vue, Angular)

**Servidor de Desarrollo:**
- XAMPP (Windows)
- Apache 2.4
- PHP 8.1
- MySQL 8.0

**Servidor de Producción:**
- Hosting compartido
- cPanel (Hostinger Business o similar)
- PHP 8.1+
- MySQL 8.0+
- Espacio: 250MB (parte de hosting compartido para 3 clientes)

### 4.2 Arquitectura

**Tipo:** Monolítica modular

**Estructura de carpetas (sugerida):**
```
/sistema-clinico/
├── /public/              # Archivos públicos
│   ├── index.php         # Punto de entrada
│   ├── /css/
│   ├── /js/
│   ├── /img/
│   └── /uploads/         # PDFs generados
├── /app/                 # Aplicación
│   ├── /controllers/     # Controladores
│   ├── /models/          # Modelos
│   ├── /views/           # Vistas
│   └── /config/          # Configuración
├── /database/            # SQL y migraciones
│   └── schema.sql
└── /docs/                # Documentación
```

### 4.3 Seguridad

**Obligatorio:**
- Encriptación de contraseñas (bcrypt o Argon2)
- Sesiones seguras (session_regenerate_id)
- Prepared statements para SQL (prevenir SQL injection)
- Validación de input en backend
- Escape de output (XSS prevention)
- HTTPS en producción
- Headers de seguridad

**Recomendado:**
- CSRF tokens
- Rate limiting en login
- Logging de acciones críticas
- Backups automáticos

### 4.4 Base de Datos

**Tablas principales (estimado):**
- usuarios
- pacientes
- citas
- consultas
- signos_vitales
- recetas
- recetas_detalle (medicamentos)
- ultrasonidos
- facturas
- pagos
- examenes_laboratorio
- inventario_productos
- inventario_movimientos

**Relaciones:**
- pacientes 1:N consultas
- consultas 1:N signos_vitales
- consultas 1:1 receta
- recetas 1:N recetas_detalle
- pacientes 1:N ultrasonidos
- pacientes 1:N facturas
- facturas 1:N pagos
- pacientes 1:N examenes_laboratorio

### 4.5 Generación de PDFs

**Opciones:**
1. **TCPDF** (recomendado)
   - Librería PHP pura
   - Sin dependencias
   - Soporte UTF-8
   - Fácil de usar

2. **mPDF** (alternativa)
   - Más moderno
   - Mejor soporte CSS
   - Más pesado

**Documentos a generar:**
- Recetas médicas
- Informes de ultrasonido
- Recibos de pago
- Constancias médicas (futuro)

### 4.6 Restricciones y Limitaciones

**NO implementar:**
- Facturación electrónica FEL (muy complejo)
- Recordatorios SMS automáticos (costo adicional)
- Subida de archivos pesados (límite de hosting)
- Frameworks frontend complejos (React, Vue, Angular)
- Frameworks backend (Laravel, Symfony)
- Múltiples idiomas
- Modo offline
- App móvil nativa

**Simplicidad sobre complejidad:**
- Código legible sobre "clever code"
- Funcionalidad sobre diseño elaborado
- Soluciones probadas sobre experimentales

---

## 5. REQUISITOS NO FUNCIONALES

### 5.1 Usabilidad
- Interfaz intuitiva para usuarios no técnicos
- Navegación clara
- Mensajes de error descriptivos
- Confirmaciones para acciones críticas
- Tiempo de carga < 3 segundos por página

### 5.2 Rendimiento
- Soportar hasta 100 pacientes concurrentes (estimado muy alto)
- Consultas a BD < 1 segundo
- Generación de PDFs < 5 segundos

### 5.3 Compatibilidad
- Navegadores: Chrome, Firefox, Edge (últimas 2 versiones)
- Dispositivos: Desktop Windows
- Resoluciones: 1366x768 mínimo

### 5.4 Mantenibilidad
- Código comentado en español
- Nombres de variables descriptivos
- Separación de lógica (MVC)
- Documentación básica de funciones

### 5.5 Disponibilidad
- Uptime: 99% (responsabilidad del hosting)
- Backups: Diarios automáticos
- Recuperación: < 24 horas en caso de fallo

---

## 6. CASOS DE USO PRINCIPALES

### 6.1 Flujo de Atención de Paciente

1. **Recepcionista:**
   - Busca paciente en sistema
   - Si es nuevo: registra datos del paciente
   - Agenda cita
   - Confirma cita

2. **Día de la cita:**
   - Recepcionista marca cita como "Confirmada"
   - Doctor ve agenda del día
   - Cuando llega paciente, marca como "Atendida"

3. **Durante la consulta:**
   - Doctor abre expediente del paciente
   - Registra nueva consulta
   - Captura signos vitales
   - Escribe notas de la consulta
   - Registra diagnóstico
   - Si es embarazada: captura datos de ultrasonido
   - Genera receta médica
   - Solicita exámenes de laboratorio (si aplica)

4. **Al finalizar consulta:**
   - Recepcionista genera recibo de pago
   - Registra pago
   - Imprime recibo para paciente
   - Imprime receta
   - Imprime informe de ultrasonido (si aplica)

5. **Seguimiento:**
   - Doctor revisa historial en próxima consulta
   - Ve resultados de exámenes anteriores
   - Compara evolución (signos vitales, peso, etc.)

### 6.2 Flujo de Recordatorios

1. Doctor o asistente abre "Citas próximas"
2. Ve lista de pacientes con cita mañana o pasado
3. Por cada paciente, hace clic en "Recordar"
4. Se abre WhatsApp Web con mensaje listo
5. Revisa mensaje y presiona enviar
6. Repite para cada paciente

---

## 7. EXCLUSIONES EXPLÍCITAS

**NO se incluye en este proyecto:**

1. Facturación electrónica FEL
2. Integración con laboratorios externos
3. Sistema de farmacia con punto de venta completo
4. Telemedicina / consultas virtuales
5. Portal del paciente
6. App móvil
7. Integración con sistemas de seguros médicos
8. Control de hospitalización
9. Expediente electrónico con firma digital legal
10. Cumplimiento HIPAA u otras normativas internacionales
11. Múltiples sucursales
12. Sistema multi-tenant para varios doctores
13. Reportes estadísticos complejos
14. Dashboard con gráficas avanzadas
15. Exportación a sistemas contables

---

## 8. ENTREGABLES

### 8.1 Entregable 1 (Semana 3)
- Sistema base funcional en servidor de prueba
- Módulos: Expediente, Citas, Recetas, Ultrasonido, Facturación, Landing Page, Recordatorios, Usuarios
- Base de datos completa
- Manual básico de usuario

### 8.2 Entregable 2 (Semana 4)
- Sistema completo en servidor de producción
- Módulos: Laboratorios, Inventario
- Capacitación de usuarios (2-3 horas)
- Manual completo de usuario
- Credenciales de acceso
- Documentación técnica básica

### 8.3 Soporte Post-Entrega
- 2 meses de soporte ilimitado
- Corrección de bugs
- Ajustes menores
- Consultas por WhatsApp/teléfono

---

## 9. CRITERIOS DE ACEPTACIÓN

### 9.1 Fase 1
- [ ] Todos los módulos de Fase 1 funcionan correctamente
- [ ] Se pueden registrar pacientes y consultas
- [ ] Se generan PDFs de recetas e informes
- [ ] El sistema es navegable y comprensible
- [ ] No hay errores críticos
- [ ] Byron puede hacer una consulta completa de prueba

### 9.2 Fase 2
- [ ] Laboratorios e inventario funcionan
- [ ] Se pueden registrar exámenes y productos
- [ ] Todo el sistema está integrado
- [ ] Byron y su asistente están capacitados
- [ ] Sistema está en producción con dominio
- [ ] Backups funcionando

---

## 10. SUPUESTOS Y DEPENDENCIAS

### 10.1 Supuestos
- Byron proveerá logo, datos y foto para personalización
- Byron validará los cálculos y formatos médicos
- Hosting estará disponible para despliegue
- Byron tiene acceso a computadora con internet durante desarrollo
- No habrá cambios mayores de alcance durante desarrollo

### 10.2 Dependencias
- Acceso a servidor/hosting
- Registro de dominio (si se requiere)
- Logo y materiales gráficos del doctor
- Disponibilidad de Byron para validaciones
- Internet estable para desarrollo y pruebas

---

## 11. RIESGOS IDENTIFICADOS

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Cambios de alcance | Media | Alto | Documentar bien, acuerdo firmado |
| Problemas de hosting | Baja | Medio | Tener plan B de hosting |
| Validación médica incorrecta | Media | Alto | Byron valida todo antes de usar |
| Datos de prueba incorrectos | Baja | Bajo | Usar datos reales de Byron |
| Incompatibilidad de navegadores | Baja | Medio | Probar en Chrome y Firefox |

---

## 12. CONTACTOS

**Desarrollador:**
- Nombre: Gerbert David García Loaiza
- Empresa: GG-Systems
- Teléfono: [Pendiente]
- Email: [Pendiente]

**Cliente:**
- Nombre: Dr. Byron Daniel Castillo Perea
- Teléfono: [Pendiente]
- Email: [Pendiente]

---

**Fecha de creación:** Febrero 2025  
**Última actualización:** Febrero 2025  
**Estado:** Aprobado por ambas partes  
**Próxima revisión:** Al finalizar Fase 1
