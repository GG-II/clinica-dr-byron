-- ============================================
-- SISTEMA: Clínica Dr. Byron Castillo
-- BASE DE DATOS: clinica_dr_byron
-- VERSIÓN: 1.0
-- FECHA: Marzo 2025
-- DESARROLLADOR: Gerbert García - GG-Systems
-- ============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- CREAR BASE DE DATOS
-- ============================================

CREATE DATABASE IF NOT EXISTS clinica_dr_byron
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE clinica_dr_byron;

-- ============================================
-- TABLA: usuarios
-- Usuarios del sistema (admin, médico, asistente)
-- ============================================

CREATE TABLE IF NOT EXISTS usuarios (
    id          INT             AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100)    NOT NULL,
    email       VARCHAR(100)    NOT NULL,
    password    VARCHAR(255)    NOT NULL,
    rol         ENUM('admin','medico','asistente') NOT NULL,
    activo      TINYINT(1)      NOT NULL DEFAULT 1,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE  KEY uk_email  (email),
    INDEX   idx_rol       (rol),
    INDEX   idx_activo    (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Usuarios del sistema';


-- ============================================
-- TABLA: pacientes
-- Expediente base de cada paciente
-- ============================================

CREATE TABLE IF NOT EXISTS pacientes (
    id                          INT             AUTO_INCREMENT PRIMARY KEY,
    codigo                      VARCHAR(20)     NOT NULL,
    nombre                      VARCHAR(150)    NOT NULL,
    fecha_nacimiento            DATE            NULL,
    dpi                         VARCHAR(20)     NULL,
    telefono                    VARCHAR(20)     NOT NULL,
    direccion                   TEXT            NULL,
    contacto_emergencia_nombre  VARCHAR(100)    NULL,
    contacto_emergencia_telefono VARCHAR(20)    NULL,
    antecedentes_personales     TEXT            NULL,
    antecedentes_familiares     TEXT            NULL,
    antecedentes_quirurgicos    TEXT            NULL,
    alergias                    TEXT            NULL,
    activo                      TINYINT(1)      NOT NULL DEFAULT 1,
    created_at                  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE  KEY uk_codigo    (codigo),
    INDEX   idx_nombre       (nombre),
    INDEX   idx_telefono     (telefono),
    INDEX   idx_activo       (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Expediente base de pacientes';


-- ============================================
-- TABLA: citas
-- Agenda del consultorio
-- ============================================

CREATE TABLE IF NOT EXISTS citas (
    id          INT             AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT             NOT NULL,
    usuario_id  INT             NOT NULL,
    fecha       DATE            NOT NULL,
    hora_inicio TIME            NOT NULL,
    hora_fin    TIME            NULL,
    motivo      VARCHAR(255)    NULL,
    estado      ENUM('programada','confirmada','atendida','cancelada') NOT NULL DEFAULT 'programada',
    notas       TEXT            NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_paciente  (paciente_id),
    INDEX idx_fecha     (fecha),
    INDEX idx_estado    (estado),

    CONSTRAINT fk_citas_paciente FOREIGN KEY (paciente_id)
        REFERENCES pacientes(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_citas_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Agenda de citas del consultorio';


-- ============================================
-- TABLA: consultas
-- Registro de cada consulta médica
-- ============================================

CREATE TABLE IF NOT EXISTS consultas (
    id              INT             AUTO_INCREMENT PRIMARY KEY,
    paciente_id     INT             NOT NULL,
    cita_id         INT             NULL,
    usuario_id      INT             NOT NULL,
    fecha           DATETIME        NOT NULL,
    tipo_consulta   ENUM('primera_vez','control','urgencia','procedimiento') NOT NULL DEFAULT 'control',
    motivo_consulta TEXT            NULL,
    notas           TEXT            NULL,
    diagnostico     TEXT            NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_paciente      (paciente_id),
    INDEX idx_cita          (cita_id),
    INDEX idx_fecha         (fecha),
    INDEX idx_tipo          (tipo_consulta),

    CONSTRAINT fk_consultas_paciente FOREIGN KEY (paciente_id)
        REFERENCES pacientes(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_consultas_cita FOREIGN KEY (cita_id)
        REFERENCES citas(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_consultas_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Registro de consultas médicas';


-- ============================================
-- TABLA: signos_vitales
-- Signos por consulta
-- ============================================

CREATE TABLE IF NOT EXISTS signos_vitales (
    id                  INT             AUTO_INCREMENT PRIMARY KEY,
    consulta_id         INT             NOT NULL,
    peso                DECIMAL(5,2)    NULL COMMENT 'kg',
    talla               DECIMAL(5,2)    NULL COMMENT 'cm',
    presion_arterial    VARCHAR(20)     NULL COMMENT 'Ej: 120/80',
    temperatura         DECIMAL(4,1)    NULL COMMENT 'Celsius',
    created_at          TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_consulta (consulta_id),

    CONSTRAINT fk_signos_consulta FOREIGN KEY (consulta_id)
        REFERENCES consultas(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Signos vitales por consulta';


-- ============================================
-- TABLA: recetas
-- Cabecera de receta médica
-- ============================================

CREATE TABLE IF NOT EXISTS recetas (
    id                      INT         AUTO_INCREMENT PRIMARY KEY,
    numero_receta           INT         NOT NULL,
    consulta_id             INT         NOT NULL,
    paciente_id             INT         NOT NULL,
    indicaciones_generales  TEXT        NULL,
    fecha_emision           DATE        NOT NULL,
    created_at              TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE  KEY uk_numero_receta (numero_receta),
    INDEX   idx_consulta         (consulta_id),
    INDEX   idx_paciente         (paciente_id),

    CONSTRAINT fk_recetas_consulta FOREIGN KEY (consulta_id)
        REFERENCES consultas(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_recetas_paciente FOREIGN KEY (paciente_id)
        REFERENCES pacientes(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Cabecera de recetas médicas';


-- ============================================
-- TABLA: recetas_detalle
-- Medicamentos de cada receta
-- ============================================

CREATE TABLE IF NOT EXISTS recetas_detalle (
    id          INT             AUTO_INCREMENT PRIMARY KEY,
    receta_id   INT             NOT NULL,
    medicamento VARCHAR(200)    NOT NULL,
    dosis       VARCHAR(100)    NULL,
    via         VARCHAR(50)     NULL,
    frecuencia  VARCHAR(100)    NULL,
    duracion    VARCHAR(100)    NULL,
    orden       TINYINT         NOT NULL DEFAULT 1,

    INDEX idx_receta (receta_id),

    CONSTRAINT fk_detalle_receta FOREIGN KEY (receta_id)
        REFERENCES recetas(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Medicamentos por receta';


-- ============================================
-- TABLA: ultrasonidos
-- Informes de ultrasonido ginecológico
-- ============================================

CREATE TABLE IF NOT EXISTS ultrasonidos (
    id                  INT             AUTO_INCREMENT PRIMARY KEY,
    consulta_id         INT             NOT NULL,
    paciente_id         INT             NOT NULL,
    fecha               DATE            NOT NULL,
    edad_gestacional    VARCHAR(50)     NULL COMMENT 'Ej: 20 semanas 3 días',
    dbp                 DECIMAL(6,2)    NULL COMMENT 'Diámetro biparietal mm',
    cc                  DECIMAL(6,2)    NULL COMMENT 'Circunferencia cefálica mm',
    ca                  DECIMAL(6,2)    NULL COMMENT 'Circunferencia abdominal mm',
    lf                  DECIMAL(6,2)    NULL COMMENT 'Longitud del fémur mm',
    peso_estimado       DECIMAL(7,2)    NULL COMMENT 'Peso estimado feto en gramos',
    observaciones       TEXT            NULL,
    conclusion          TEXT            NULL,
    created_at          TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_consulta  (consulta_id),
    INDEX idx_paciente  (paciente_id),

    CONSTRAINT fk_ultrasonidos_consulta FOREIGN KEY (consulta_id)
        REFERENCES consultas(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_ultrasonidos_paciente FOREIGN KEY (paciente_id)
        REFERENCES pacientes(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Informes de ultrasonido ginecológico';


-- ============================================
-- TABLA: facturas
-- Recibos y cobros de consultas
-- ============================================

CREATE TABLE IF NOT EXISTS facturas (
    id                  INT             AUTO_INCREMENT PRIMARY KEY,
    numero_correlativo  INT             NOT NULL,
    paciente_id         INT             NOT NULL,
    consulta_id         INT             NULL,
    usuario_id          INT             NOT NULL,
    fecha               DATE            NOT NULL,
    concepto            VARCHAR(255)    NOT NULL,
    monto               DECIMAL(8,2)    NOT NULL,
    forma_pago          ENUM('efectivo','tarjeta','transferencia') NOT NULL DEFAULT 'efectivo',
    monto_pagado        DECIMAL(8,2)    NOT NULL,
    cambio              DECIMAL(8,2)    NOT NULL DEFAULT 0.00,
    estado              ENUM('pagado','pendiente','anulado') NOT NULL DEFAULT 'pagado',
    notas               VARCHAR(255)    NULL,
    created_at          TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE  KEY uk_correlativo  (numero_correlativo),
    INDEX   idx_paciente        (paciente_id),
    INDEX   idx_fecha           (fecha),
    INDEX   idx_estado          (estado),

    CONSTRAINT fk_facturas_paciente FOREIGN KEY (paciente_id)
        REFERENCES pacientes(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_facturas_consulta FOREIGN KEY (consulta_id)
        REFERENCES consultas(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_facturas_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Recibos y cobros de consultas';


-- ============================================
-- TABLA: examenes_laboratorio
-- Exámenes solicitados y resultados
-- ============================================

CREATE TABLE IF NOT EXISTS examenes_laboratorio (
    id              INT             AUTO_INCREMENT PRIMARY KEY,
    paciente_id     INT             NOT NULL,
    consulta_id     INT             NULL,
    tipo_examen     VARCHAR(150)    NOT NULL,
    fecha_solicitud DATE            NOT NULL,
    descripcion     TEXT            NULL,
    estado          ENUM('solicitado','recibido') NOT NULL DEFAULT 'solicitado',
    resultado       TEXT            NULL,
    fecha_resultado DATE            NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_paciente  (paciente_id),
    INDEX idx_estado    (estado),

    CONSTRAINT fk_examenes_paciente FOREIGN KEY (paciente_id)
        REFERENCES pacientes(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_examenes_consulta FOREIGN KEY (consulta_id)
        REFERENCES consultas(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Exámenes de laboratorio solicitados';


-- ============================================
-- TABLA: inventario_productos
-- Medicamentos e insumos del consultorio
-- ============================================

CREATE TABLE IF NOT EXISTS inventario_productos (
    id              INT             AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(150)    NOT NULL,
    unidad          VARCHAR(50)     NOT NULL COMMENT 'cajas, frascos, unidades...',
    cantidad_actual INT             NOT NULL DEFAULT 0,
    stock_minimo    INT             NOT NULL DEFAULT 0,
    activo          TINYINT(1)      NOT NULL DEFAULT 1,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_nombre    (nombre),
    INDEX idx_activo    (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Productos e insumos del consultorio';


-- ============================================
-- TABLA: inventario_movimientos
-- Historial de entradas y salidas
-- ============================================

CREATE TABLE IF NOT EXISTS inventario_movimientos (
    id          INT             AUTO_INCREMENT PRIMARY KEY,
    producto_id INT             NOT NULL,
    usuario_id  INT             NOT NULL,
    tipo        ENUM('entrada','salida','venta') NOT NULL,
    cantidad    INT             NOT NULL,
    motivo      VARCHAR(255)    NULL,
    fecha       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_producto  (producto_id),
    INDEX idx_fecha     (fecha),

    CONSTRAINT fk_movimientos_producto FOREIGN KEY (producto_id)
        REFERENCES inventario_productos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_movimientos_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Movimientos de inventario';


-- ============================================
-- TABLA: audit_log
-- Registro de acciones críticas del sistema
-- ============================================

CREATE TABLE IF NOT EXISTS audit_log (
    id              BIGINT          AUTO_INCREMENT PRIMARY KEY,
    usuario_id      INT             NULL,
    accion          VARCHAR(50)     NOT NULL COMMENT 'VER, CREAR, EDITAR, ELIMINAR',
    tabla_afectada  VARCHAR(50)     NULL,
    registro_id     INT             NULL,
    ip_address      VARCHAR(45)     NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_usuario   (usuario_id),
    INDEX idx_tabla     (tabla_afectada),
    INDEX idx_fecha     (created_at),

    CONSTRAINT fk_audit_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Auditoría de acciones del sistema';


-- ============================================
-- RESTAURAR CONFIGURACIÓN
-- ============================================

SET FOREIGN_KEY_CHECKS = 1;