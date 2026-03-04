# Clínica Médica de la Mujer — Sistema de Gestión Clínica

Sistema web de gestión para consultorio de Ginecología y Obstetricia.

## Cliente
Dr. Byron Daniel Castillo Perea — Huehuetenango, Guatemala

## Desarrollador
Gerbert David García Loaiza — GG-Systems

## Tecnologías
- PHP 8.2
- MySQL / MariaDB 10.4
- Bootstrap 5
- JavaScript Vanilla
- TCPDF (generación de PDFs)

## Requisitos
- PHP 8.1 o superior
- MySQL 5.7 / MariaDB 10.4 o superior
- Apache 2.4

## Instalación Local

### 1. Clonar repositorio
```bash
git clone https://github.com/tu-usuario/clinica-dr-byron.git
cd clinica-dr-byron
```

### 2. Crear base de datos
```sql
CREATE DATABASE clinica_dr_byron
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

### 3. Ejecutar schema
Importar `database/schema.sql` desde phpMyAdmin o línea de comandos.

### 4. Configurar config.php
```bash
cp config.example.php config.php
# Editar config.php con tus datos de conexión
```

### 5. Verificar instalación
```
http://localhost/clinica-dr-byron/test-conexion.php
```
Debe mostrar 13 tablas y conexión exitosa.

## Estructura del Proyecto
```
clinica-dr-byron/
├── assets/         # CSS, JS, imágenes
├── database/       # Scripts SQL
├── docs/           # Documentación del proyecto
├── includes/       # Archivos PHP reutilizables (db, auth, header...)
├── models/         # Lógica de datos por módulo
├── modules/        # Vistas y controladores por módulo
├── api/            # Endpoints AJAX
├── pdf/            # Generadores de PDF (TCPDF)
├── uploads/        # Archivos subidos
├── logs/           # Logs del sistema
├── config.php      # Configuración local (NO está en Git)
└── config.example.php # Plantilla de configuración
```

## Módulos del Sistema
- Expediente Clínico (pacientes, consultas, signos vitales)
- Agenda y Citas
- Recetas Médicas con PDF
- Informes de Ultrasonido con PDF
- Facturación Básica con PDF
- Recordatorios WhatsApp
- Laboratorios
- Inventario
- Usuarios y Permisos
- Landing Page

## Credenciales por Defecto
**Usuario:** admin@clinica.com
**Contraseña:** Admin2025*

⚠️ Cambiar inmediatamente en producción.

## Estado del Proyecto
🔄 En desarrollo — Fase 1: Arquitectura y Base de Datos

## Licencia
Propietario: Dr. Byron Daniel Castillo Perea
Todos los derechos reservados © 2026