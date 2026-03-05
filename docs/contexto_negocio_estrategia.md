# GUÍA DE CONTEXTO Y ESTRATEGIA - PROYECTO SISTEMA CLÍNICO

**Documento:** Contexto del Negocio y Estrategia de Desarrollo  
**Desarrollador:** Gerbert David García Loaiza (GG-Systems)  
**Fecha:** Febrero 2025

---

## 1. CONTEXTO DEL DESARROLLADOR

### 1.1 Perfil Profesional
- **Nombre comercial:** GG-Systems
- **Nombre completo:** Gerbert David García Loaiza
- **Ubicación:** Huehuetenango, Guatemala
- **Nivel de experiencia:** Desarrollador freelance intermedio
- **Educación:** Estudiante de Maestría en Ciberseguridad, Redes y Telecomunicaciones (Universidad Mariano Gálvez, Campus Huehuetenango)

### 1.2 Proyectos Previos
**Proyecto 1: Sistema de Gestión de Joyería**
- Cliente: Joyería (hermanos de iglesia)
- Precio: Q1,500 desarrollo + Q150/mes
- Tecnología: PHP vanilla, MySQL, Bootstrap 5
- Tiempo: 1 mes efectivo (2 semanas backend, 1 semana frontend, descuidos)
- Hosting: Hostinger Q450/año (250MB, 3 BD)
- Estado: Completado y en producción

**Lecciones aprendidas:**
- Desarrollo simple y funcional funciona mejor que complejo
- Los clientes valoran velocidad sobre perfección
- La mensualidad es clave para ingresos recurrentes
- XAMPP local para desarrollo es suficiente
- Bootstrap 5 acelera mucho el frontend

### 1.3 Proyecto Actual (Sistema Clínico)
- **Cliente:** Dr. Byron Castillo (hermano de iglesia)
- **Precio acordado:** Q3,000 desarrollo + Q200/mes
- **Tiempo estimado:** 4 semanas (3 semanas Fase 1, 1 semana Fase 2)
- **Desafío:** Sistema más complejo que joyería (módulos especializados)
- **Ventaja:** Cliente con paciencia y confianza mutua

---

## 2. MODELO DE NEGOCIO GG-SYSTEMS

### 2.1 Estrategia de Precios

**Sistema Base (Tienda/Inventario simple):**
- Desarrollo: Q1,500
- Mensualidad: Q150/mes
- Tiempo: 2-3 semanas

**Sistema Intermedio (Clínica base):**
- Desarrollo: Q4,000-5,000
- Mensualidad: Q200/mes
- Tiempo: 4-6 semanas

**Sistema Completo (Clínica con todos los módulos):**
- Desarrollo: Q6,500-8,000
- Mensualidad: Q250/mes
- Tiempo: 8-10 semanas

### 2.2 Modelo de Ingresos Recurrentes

**Hosting compartido (actual):**
- Costo: Q450/año para 3 clientes
- Capacidad: 250MB, 3 bases de datos
- Costo por cliente: Q150/año = Q12.50/mes

**Ingresos mensuales por cliente:**
- Joyería: Q150/mes - Q12.50 costo = Q137.50 ganancia (92% margen)
- Clínica: Q200/mes - Q12.50 costo = Q187.50 ganancia (94% margen)

**Proyección con 3 clientes:**
- Cliente 1 (Joyería): Q150/mes
- Cliente 2 (Dr. Byron): Q200/mes
- Cliente 3 (futuro): Q200/mes
- **Total:** Q550/mes = Q6,600/año ingresos recurrentes

**Costos recurrentes:**
- Hosting: Q450/año
- Claude Pro: Q0 (solo cuando desarrollo, ~Q775/mes por 1-2 meses)
- **Ganancia neta año 2+:** ~Q6,150/año pasivo (sin desarrollo)

### 2.3 Estrategia de Escalamiento

**Año 1:**
- 3-5 clientes
- Enfoque: sistemas simples (joyerías, tiendas, clínicas)
- Ingresos objetivo: Q15,000-25,000

**Año 2:**
- 10-15 clientes
- Sistema base reutilizable (clínicas)
- Hosting: 2-3 planes compartidos
- Ingresos objetivo: Q40,000-60,000

**Servicios adicionales (monetización extra):**
- Módulos opcionales: Q1,500-2,500 c/u
- Capacitación adicional: Q500/sesión
- Soporte premium: Incluido en mensualidad
- Personalizaciones: Q500-1,000 por cambio mayor

---

## 3. COSTOS DEL PROYECTO ACTUAL

### 3.1 Costos Directos

**Desarrollo (4 semanas):**
- Claude Pro: Q775 × 1 mes = Q775
- Tiempo personal: 4 semanas × 40 horas = 160 horas
- Hosting año 1: Q150 (parte proporcional)
- **Total costos:** Q925

**Ganancia desarrollo:**
- Ingresos: Q3,000
- Costos: Q925
- **Ganancia:** Q2,075 (69% margen)

### 3.2 Ingresos Proyectados

**Año 1:**
- Desarrollo: Q2,075
- Mensualidades (8 meses): Q200 × 8 = Q1,600
- **Total año 1:** Q3,675

**Año 2+:**
- Mensualidades: Q200 × 12 = Q2,400
- Costos: Q150 (hosting)
- **Ganancia anual:** Q2,250 pasivo

### 3.3 ROI (Retorno de Inversión)

**Inversión de tiempo:**
- 160 horas de desarrollo
- Ganancia total 3 años: Q2,075 + Q2,250 + Q2,250 = Q6,575
- **Por hora:** Q41/hora promedio

**Comparativa:**
- Salario programador Jr. Guatemala: Q3,000-5,000/mes = Q18-30/hora
- **Conclusión:** Por encima del mercado para nivel actual

---

## 4. ESTRATEGIA DE DESARROLLO

### 4.1 Enfoque Metodológico

**Desarrollo Iterativo con Claude:**
1. Planificación detallada inicial (BD, estructura)
2. Desarrollo módulo por módulo
3. Pruebas después de cada módulo
4. Validación con cliente (Byron) semanalmente
5. Ajustes incrementales

**Herramientas:**
- Claude Pro (solo durante desarrollo)
- XAMPP (desarrollo local)
- VS Code
- Git (opcional, recomendado)
- cPanel (producción)

### 4.2 Gestión de Riesgos

**Riesgo: Scope creep (cambios de alcance)**
- Mitigación: Documento de requisitos firmado
- Costo adicional por módulos extra: Q1,500 mínimo

**Riesgo: Validación médica incorrecta**
- Mitigación: Byron valida formatos y cálculos
- Disclaimer: Sistema no sustituye validación profesional

**Riesgo: Tiempo de desarrollo excedido**
- Mitigación: Buffer de 1 semana incluido
- Plan B: Entregar Fase 1 perfecta, Fase 2 después

**Riesgo: Problemas de hosting**
- Mitigación: Tener plan alternativo (otro hosting)
- Backup local siempre antes de desplegar

### 4.3 Comunicación con Cliente

**Frecuencia:**
- Reporte semanal de avances
- Validación al finalizar cada módulo importante
- Disponibilidad por WhatsApp para dudas

**Formato:**
- WhatsApp para comunicación rápida
- Email para documentación formal
- Reunión final para capacitación (2-3 horas)

---

## 5. SISTEMA CLÍNICO - CONSIDERACIONES ESPECIALES

### 5.1 Diferencias vs Joyería

| Aspecto | Joyería | Clínica |
|---------|---------|---------|
| Complejidad | Baja | Media-Alta |
| Módulos | 3-4 | 10 |
| Personalización | Mínima | Alta (médica) |
| Generación PDFs | No | Sí (recetas, informes) |
| Seguridad | Estándar | Alta (datos sensibles) |
| Validación | Simple | Médica (crítica) |

### 5.2 Módulos Críticos a Priorizar

**Semana 1:**
1. Base de datos completa
2. Login y usuarios
3. Expediente clínico básico (CRUD pacientes)

**Semana 2:**
4. Consultas y notas
5. Signos vitales
6. Citas básicas

**Semana 3:**
7. Recetas médicas (con PDF)
8. Informes de ultrasonido (con PDF)
9. Facturación básica
10. Recordatorios (WhatsApp links)

**Semana 4:**
11. Laboratorios simple
12. Inventario reducido
13. Ajustes finales
14. Landing page

### 5.3 Generación de PDFs

**Librería recomendada: TCPDF**

**Documentos a diseñar:**

1. **Receta Médica:**
   - Header: Logo + datos doctor
   - Datos paciente
   - Lista de medicamentos (tabla)
   - Indicaciones
   - Firma digital
   - Footer: Datos de contacto

2. **Informe de Ultrasonido:**
   - Header: Logo + datos doctor
   - Datos paciente + edad gestacional
   - Tabla de medidas fetales
   - Observaciones
   - Conclusión
   - Firma digital

3. **Recibo de Pago:**
   - Header: Logo
   - Número correlativo
   - Datos paciente
   - Concepto y monto
   - Forma de pago
   - Footer

---

## 6. APRENDIZAJES Y MEJORES PRÁCTICAS

### 6.1 Lecciones del Proyecto Joyería

**Lo que funcionó bien:**
- PHP vanilla (simple, directo, sin curva de aprendizaje)
- Bootstrap 5 (UI profesional sin diseñar desde cero)
- XAMPP local (desarrollo rápido sin complicaciones)
- Enfoque modular (fácil de mantener)

**Lo que mejorar:**
- Usar Git desde el inicio (control de versiones)
- Comentar código más (facilita mantenimiento)
- Hacer backup antes de cada cambio grande
- Documentar decisiones técnicas

### 6.2 Para Sistema Clínico

**Hacer diferente:**
- Diseñar BD completa ANTES de codificar
- Crear wireframes de pantallas principales
- Validar con Byron cada módulo antes de continuar
- Usar Git obligatorio
- Testing manual sistemático (checklist)
- Backups diarios durante desarrollo

**Mantener igual:**
- PHP vanilla (no frameworks)
- Bootstrap 5
- Desarrollo iterativo
- Comunicación frecuente con cliente

---

## 7. PLANTILLAS Y DOCUMENTOS

### 7.1 Documentos Generados para Byron

1. **Propuesta Comercial** (PDF profesional)
   - Módulos incluidos
   - Precio y forma de pago
   - Tiempos de entrega
   - Generado con GG-Systems Generator

2. **Recibos de Pago** (PDFs)
   - Recibo 1: Q1,200 (al iniciar)
   - Recibo 2: Q1,000 (Fase 1)
   - Recibo 3: Q800 (Fase 2)
   - Recibos mensuales: Q200/mes

3. **Facturas de Mantenimiento** (PDFs)
   - Opción mensual: Q200/mes
   - Opción anual: Q2,000/año (2 meses gratis)

### 7.2 Herramienta: GG-Systems Generator

**Ubicación:** gg-systems-generator.html

**Funcionalidades:**
- Generar propuestas de desarrollo profesionales
- Generar recibos de pago
- Generar facturas de servicios (desarrollo + mantenimiento)
- Cálculo automático de descuentos (anual)
- PDFs con branding GG-Systems

**Uso:**
- Abrir HTML en navegador
- Llenar formularios
- Generar PDF
- Enviar a cliente

---

## 8. PRÓXIMOS PASOS PARA DR. BYRON

### 8.1 Comunicación Inicial de Cobro

**Mensaje sugerido (ver documento separado):**

Para enviar junto con:
1. Factura_Mensual_Dr_Byron.pdf
2. Factura_Anual_Dr_Byron.pdf

**Contenido del mensaje:**
- Explicar servicio de mantenimiento
- Mostrar dos opciones (mensual vs anual)
- Destacar ahorro de opción anual (Q300 = 2 meses gratis)
- Incluir qué cubre (hosting, soporte, correcciones)
- Tono: profesional pero amigable

### 8.2 Preparación para Inicio de Desarrollo

**Antes de empezar a codificar:**
1. Confirmar que Byron pagó primer anticipo (Q1,200)
2. Solicitar a Byron:
   - Logo de la clínica (PNG/JPG, alta calidad)
   - Foto del doctor (profesional)
   - Datos completos para membrete
   - Firma digital (escaneo o imagen)
3. Confirmar acceso a hosting
4. Crear repositorio Git (opcional pero recomendado)

### 8.3 Durante el Desarrollo

**Validaciones semanales:**
- Semana 1: Mostrar estructura de BD y primeras pantallas
- Semana 2: Demo de expediente y citas funcionando
- Semana 3: Demo completa de Fase 1
- Semana 4: Entrega final con capacitación

---

## 9. CRECIMIENTO FUTURO

### 9.1 Sistema Clínico como Producto

**Visión:** Convertir este desarrollo en un "Sistema Clínico Base" reutilizable

**Estrategia:**
1. Completar sistema para Byron (proyecto piloto)
2. Documentar qué es común vs qué es personalizado
3. Identificar módulos opcionales
4. Crear versión "base" + módulos "premium"

**Pricing futuro:**
- Sistema base: Q4,500 (Expediente, Citas, Recetas, Facturación)
- + Ultrasonido especializado: Q1,500
- + Laboratorios: Q1,500
- + Inventario: Q2,000
- Mensualidad: Q200/mes

**Mercado potencial en Huehuetenango:**
- ~20-30 médicos privados
- Si logras 5 clientes = Q1,000/mes pasivo

### 9.2 Otros Proyectos Identificados

**Proyecto RH - Centro Médico (padre):**
- Cliente: Centro Médico Los Ángeles
- Sistema: RH con nómina multi-empresa (4 empresas, 79 empleados)
- Precio sugerido: Q12,000
- Tiempo: 3 meses
- Estado: En evaluación
- Complejidad: Alta (nómina guatemalteca compleja)

**Otros sistemas potenciales:**
- Tiendas y minisupers
- Restaurantes
- Ferreterías
- Farmacias
- Cooperativas

---

## 10. RECURSOS Y REFERENCIAS

### 10.1 Tecnologías

**Documentación oficial:**
- PHP: https://www.php.net/docs.php
- MySQL: https://dev.mysql.com/doc/
- Bootstrap 5: https://getbootstrap.com/docs/5.3/

**Librerías recomendadas:**
- TCPDF: https://tcpdf.org/
- PHPMailer: https://github.com/PHPMailer/PHPMailer (para emails futuros)

### 10.2 Hosting

**Hostinger Business:**
- Costo: Q450/año aprox (depende de promociones)
- Incluye: 250MB espacio, 3 BD MySQL, SSL gratis, backups
- Panel: cPanel
- PHP: 8.1+

**Alternativas:**
- SiteGround (más caro pero mejor soporte)
- Bluehost
- HostGator

### 10.3 Herramientas de Desarrollo

**Esenciales:**
- XAMPP: https://www.apachefriends.org/
- VS Code: https://code.visualstudio.com/
- Git: https://git-scm.com/

**Extensiones VS Code recomendadas:**
- PHP Intelephense
- MySQL (Weijan Chen)
- Bootstrap 5 Quick Snippets
- Live Server

---

## 11. CONCLUSIONES

### 11.1 Estado Actual del Proyecto

**Fase:** Pre-desarrollo (planificación completa)
**Documentos listos:**
- ✅ Requisitos completos
- ✅ Propuesta comercial
- ✅ Acuerdo de pagos
- ✅ Alcance definido
- ✅ Contexto documentado

**Siguiente paso:** Diseñar base de datos completa

### 11.2 Factores de Éxito

**Técnicos:**
- Stack simple y conocido (PHP + MySQL + Bootstrap)
- Desarrollo iterativo módulo por módulo
- Validación constante con Byron
- Enfoque en funcionalidad sobre perfección

**Comerciales:**
- Relación de confianza con cliente
- Precio justo para ambas partes
- Expectativas claras y documentadas
- Ingreso recurrente garantizado

**Personales:**
- Ganancia de Q2,075 en desarrollo
- Ingreso pasivo de Q2,250/año después
- Experiencia en sistema más complejo
- Portafolio mejorado

### 11.3 Métricas de Éxito

**Proyecto será exitoso si:**
- [ ] Se entrega en 4-5 semanas
- [ ] Byron puede realizar consultas completas
- [ ] No hay bugs críticos
- [ ] Byron está satisfecho y recomienda
- [ ] Sistema genera valor real para su trabajo
- [ ] Se cobra la mensualidad sin problemas

---

**Fecha:** Febrero 2025  
**Autor:** Gerbert David García Loaiza  
**Empresa:** GG-Systems  
**Versión:** 1.0
