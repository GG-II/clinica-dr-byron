-- ============================================
-- DATOS DE PRUEBA - SISTEMA CLÍNICO DR. BYRON
-- BASE DE DATOS: clinica_dr_byron
-- VERSIÓN: 1.0
-- FECHA: Marzo 2025
-- ============================================

USE clinica_dr_byron;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- LIMPIAR DATOS EXISTENTES (si los hay)
-- ============================================

TRUNCATE TABLE audit_log;
TRUNCATE TABLE inventario_movimientos;
TRUNCATE TABLE inventario_productos;
TRUNCATE TABLE examenes_laboratorio;
TRUNCATE TABLE facturas;
TRUNCATE TABLE ultrasonidos;
TRUNCATE TABLE recetas_detalle;
TRUNCATE TABLE recetas;
TRUNCATE TABLE signos_vitales;
TRUNCATE TABLE consultas;
TRUNCATE TABLE citas;
TRUNCATE TABLE pacientes;
TRUNCATE TABLE usuarios;

-- ============================================
-- TABLA: usuarios
-- Password para TODOS: password123
-- Hash generado con: password_hash('password123', PASSWORD_BCRYPT)
-- ============================================

INSERT INTO usuarios (nombre, email, password, rol, activo) VALUES
-- Admin (para desarrollo/configuración)
('Gerbert García', 'admin@ggsystems.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1),

-- Doctor Byron (médico principal)
('Dr. Byron Castillo', 'byron@clinica.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'medico', 1),

-- Asistente/Recepcionista
('María López', 'maria@clinica.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'asistente', 1);

-- ============================================
-- TABLA: pacientes
-- Pacientes de prueba con datos realistas
-- ============================================

INSERT INTO pacientes (codigo, nombre, fecha_nacimiento, dpi, telefono, direccion, contacto_emergencia_nombre, contacto_emergencia_telefono, antecedentes_personales, antecedentes_familiares, antecedentes_quirurgicos, alergias, activo) VALUES

-- Paciente 1: Embarazada
('PAC-000001', 'Ana María Pérez González', '1992-05-15', '2587 45612 0101', '5551-2345', 'Zona 3, Calle Principal 5-20, Huehuetenango', 'Carlos Pérez (esposo)', '5551-2346', 'Embarazo anterior sin complicaciones. G2P1A0.', 'Madre: Diabetes tipo 2. Padre: Hipertensión.', 'Cesárea en 2020', 'Penicilina', 1),

-- Paciente 2: Control ginecológico
('PAC-000002', 'Luisa Fernanda Morales', '1985-08-22', '1845 23698 0101', '5552-3456', 'Zona 5, Avenida 2-15, Huehuetenango', 'Juan Morales (hermano)', '5552-3457', 'Ciclos menstruales regulares. Antecedente de quiste ovárico resuelto.', 'Madre: Cáncer de mama (en remisión)', 'Ninguna', 'Ninguna', 1),

-- Paciente 3: Primera vez
('PAC-000003', 'Carmen Judith Ramírez', '1998-11-30', '2998 78945 0101', '5553-4567', 'Aldea La Esperanza, Huehuetenango', 'Rosa Ramírez (madre)', '5553-4568', 'Menarca a los 13 años. Sin antecedentes relevantes.', 'Sin antecedentes familiares relevantes', 'Apendicectomía en 2015', 'Ninguna', 1),

-- Paciente 4: Control prenatal
('PAC-000004', 'Sofía Isabel López Hernández', '1990-03-10', '2490 12367 0101', '5554-5678', 'Zona 7, Colonia Los Pinos, Huehuetenango', 'Roberto López (esposo)', '5554-5679', 'Primer embarazo. G1P0A0. FUM: 15/10/2024', 'Sin antecedentes relevantes', 'Ninguna', 'Ninguna', 1),

-- Paciente 5: Menopausia
('PAC-000005', 'Martha Elena Contreras', '1968-07-05', '1468 34521 0101', '5555-6789', 'Zona 1, 3ra Calle 8-45, Huehuetenango', 'Pedro Contreras (esposo)', '5555-6780', 'Menopausia hace 2 años. Sofocos ocasionales.', 'Madre: Osteoporosis', 'Histerectomía parcial en 2010', 'Ninguna', 1),

-- Paciente 6: Control rutinario
('PAC-000006', 'Alejandra Patricia Vásquez', '1995-12-18', '2595 67890 0101', '5556-7890', 'Zona 4, Barrio El Centro, Huehuetenango', 'Laura Vásquez (madre)', '5556-7891', 'Sin antecedentes relevantes. Última citología hace 1 año: normal.', 'Sin antecedentes relevantes', 'Ninguna', 'Ibuprofeno', 1),

-- Paciente 7: Embarazo gemelar
('PAC-000007', 'Cristina Beatriz Salazar', '1988-09-25', '2388 45123 0101', '5557-8901', 'Aldea San José, Huehuetenango', 'Miguel Salazar (esposo)', '5557-8902', 'Embarazo gemelar actual. G3P2A0. FUM: 20/11/2024', 'Hermana: Embarazo gemelar', 'Ninguna', 'Ninguna', 1),

-- Paciente 8: Planificación familiar
('PAC-000008', 'Diana Carolina Mendoza', '1993-04-08', '2593 78456 0101', '5558-9012', 'Zona 6, Residenciales Vista Hermosa, Huehuetenango', 'Fernando Mendoza (esposo)', '5558-9013', 'Uso de anticonceptivos orales por 3 años. Desea cambiar método.', 'Sin antecedentes relevantes', 'Ninguna', 'Ninguna', 1),

-- Paciente 9: Infección urinaria recurrente
('PAC-000009', 'Elena Marisol Arriola', '1987-06-14', '2387 23456 0101', '5559-0123', 'Zona 2, Calzada Principal 12-30, Huehuetenango', 'Carlos Arriola (esposo)', '5559-0124', 'Infecciones urinarias recurrentes (3 en el último año).', 'Madre: Diabetes tipo 2', 'Ninguna', 'Sulfa', 1),

-- Paciente 10: Primera consulta prenatal
('PAC-000010', 'Gabriela Susana Torres', '1996-01-20', '2596 34567 0101', '5550-1234', 'Aldea El Paraíso, Huehuetenango', 'José Torres (esposo)', '5550-1235', 'Primer embarazo. Test casero positivo. G1P0A0.', 'Sin antecedentes relevantes', 'Ninguna', 'Ninguna', 1);

-- ============================================
-- TABLA: citas
-- Citas de ejemplo (pasadas, hoy, futuras)
-- ============================================

INSERT INTO citas (paciente_id, usuario_id, fecha, hora_inicio, hora_fin, motivo, estado, notas) VALUES
-- Citas pasadas (atendidas)
(1, 2, '2025-03-01', '09:00:00', '09:30:00', 'Control prenatal 20 semanas', 'atendida', NULL),
(2, 2, '2025-03-01', '10:00:00', '10:30:00', 'Control ginecológico anual', 'atendida', NULL),
(4, 2, '2025-03-02', '09:00:00', '09:30:00', 'Control prenatal 24 semanas', 'atendida', NULL),

-- Citas de hoy (para que Byron las vea en su agenda)
(3, 2, CURDATE(), '10:00:00', '10:30:00', 'Primera consulta ginecológica', 'confirmada', 'Paciente nueva'),
(5, 2, CURDATE(), '11:00:00', '11:30:00', 'Control menopausia', 'programada', NULL),

-- Citas futuras (próximos días)
(6, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', '09:30:00', 'Resultado de Papanicolaou', 'programada', NULL),
(7, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', '14:30:00', 'Control embarazo gemelar', 'confirmada', 'Traer últimos laboratorios'),
(8, 2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '09:30:00', '10:00:00', 'Planificación familiar', 'programada', NULL),
(9, 2, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '10:00:00', '10:30:00', 'Seguimiento ITU', 'programada', NULL),
(10, 2, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '09:00:00', '09:30:00', 'Primera consulta prenatal', 'programada', NULL);

-- ============================================
-- TABLA: consultas
-- Consultas realizadas (vinculadas a citas)
-- ============================================

INSERT INTO consultas (paciente_id, cita_id, usuario_id, fecha, tipo_consulta, motivo_consulta, notas, diagnostico) VALUES

-- Consulta 1: Control prenatal Ana María
(1, 1, 2, '2025-03-01 09:00:00', 'control', 'Control prenatal 20 semanas', 
'Paciente refiere sentir movimientos fetales. Sin molestias. Última ecografía: feto único, líquido amniótico normal.',
'Embarazo 20 semanas. Evolución adecuada. Signos vitales estables.'),

-- Consulta 2: Control ginecológico Luisa
(2, 2, 2, '2025-03-01 10:00:00', 'control', 'Control ginecológico anual',
'Paciente asintomática. Última citología hace 1 año: negativa. Ciclos regulares 28 días.',
'Control ginecológico anual. Sin hallazgos patológicos. Se solicita Papanicolaou.'),

-- Consulta 3: Control prenatal Sofía
(4, 3, 2, '2025-03-02 09:00:00', 'control', 'Control prenatal 24 semanas',
'Paciente refiere sentirse bien. Movimientos fetales adecuados. Sin contracciones. Sin sangrado.',
'Embarazo 24 semanas. Evolución satisfactoria. Se solicita glucosa y ecografía de detalle.');

-- ============================================
-- TABLA: signos_vitales
-- Signos vitales de las consultas
-- ============================================

INSERT INTO signos_vitales (consulta_id, peso, talla, presion_arterial, temperatura) VALUES
(1, 68.5, 158.0, '110/70', 36.5),
(2, 62.0, 165.0, '120/80', 36.8),
(3, 72.0, 160.0, '115/75', 36.6);

-- ============================================
-- TABLA: recetas
-- Recetas generadas en las consultas
-- ============================================

INSERT INTO recetas (numero_receta, consulta_id, paciente_id, indicaciones_generales, fecha_emision) VALUES
(1, 1, 1, 'Tomar medicamentos con alimentos. Abundantes líquidos.', '2025-03-01'),
(2, 3, 4, 'Tomar complejo vitamínico en ayunas. Evitar automedicación.', '2025-03-02');

-- ============================================
-- TABLA: recetas_detalle
-- Medicamentos de cada receta
-- ============================================

INSERT INTO recetas_detalle (receta_id, medicamento, dosis, via, frecuencia, duracion, orden) VALUES
-- Receta 1 (Ana María - control prenatal)
(1, 'Ácido Fólico', '5 mg', 'Oral', 'Una vez al día', '30 días', 1),
(1, 'Sulfato Ferroso', '300 mg', 'Oral', 'Una vez al día', '30 días', 2),
(1, 'Calcio', '1000 mg', 'Oral', 'Una vez al día', '30 días', 3),

-- Receta 2 (Sofía - control prenatal)
(2, 'Complejo Vitamínico Prenatal', '1 tableta', 'Oral', 'Una vez al día', '30 días', 1),
(2, 'Hierro Elemental', '60 mg', 'Oral', 'Una vez al día con las comidas', '30 días', 2);

-- ============================================
-- TABLA: ultrasonidos
-- Ultrasonidos ginecológicos realizados
-- ============================================

INSERT INTO ultrasonidos (consulta_id, paciente_id, fecha, edad_gestacional, dbp, cc, ca, lf, peso_estimado, observaciones, conclusion) VALUES

-- Ultrasonido 1: Ana María (20 semanas)
(1, 1, '2025-03-01', '20 semanas 2 días', 47.5, 178.3, 165.2, 32.8, 350.00,
'Feto único vivo, activo. Frecuencia cardíaca fetal: 145 lpm. Placenta anterior grado I. Líquido amniótico normal. Anatomía fetal sin alteraciones evidentes.',
'Embarazo único de 20 semanas 2 días por biometría fetal. Anatomía fetal adecuada para edad gestacional. Placenta normoinserta. Líquido amniótico normal.');

-- ============================================
-- TABLA: facturas
-- Recibos de pago de las consultas
-- ============================================

INSERT INTO facturas (numero_correlativo, paciente_id, consulta_id, usuario_id, fecha, concepto, monto, forma_pago, monto_pagado, cambio, estado) VALUES
(1, 1, 1, 2, '2025-03-01', 'Consulta prenatal + Ultrasonido', 300.00, 'efectivo', 300.00, 0.00, 'pagado'),
(2, 2, 2, 2, '2025-03-01', 'Consulta ginecológica', 200.00, 'efectivo', 200.00, 0.00, 'pagado'),
(3, 4, 3, 2, '2025-03-02', 'Control prenatal', 250.00, 'tarjeta', 250.00, 0.00, 'pagado');

-- ============================================
-- TABLA: examenes_laboratorio
-- Exámenes solicitados en consultas
-- ============================================

INSERT INTO examenes_laboratorio (paciente_id, consulta_id, tipo_examen, fecha_solicitud, descripcion, estado) VALUES
(2, 2, 'Papanicolaou', '2025-03-01', 'Control anual de rutina', 'solicitado'),
(4, 3, 'Glucosa en ayunas', '2025-03-02', 'Tamizaje diabetes gestacional', 'solicitado'),
(4, 3, 'Hemograma completo', '2025-03-02', 'Control prenatal', 'solicitado');

-- ============================================
-- TABLA: inventario_productos
-- Productos e insumos básicos
-- ============================================

INSERT INTO inventario_productos (nombre, unidad, cantidad_actual, stock_minimo, activo) VALUES
('Ácido Fólico 5mg', 'cajas', 15, 5, 1),
('Sulfato Ferroso 300mg', 'cajas', 12, 5, 1),
('Complejo Vitamínico Prenatal', 'cajas', 20, 8, 1),
('Guantes de látex talla M', 'cajas', 8, 3, 1),
('Gasas estériles', 'paquetes', 25, 10, 1),
('Alcohol gel', 'frascos', 10, 5, 1),
('Jeringas 5ml', 'cajas', 6, 2, 1),
('Paracetamol 500mg', 'cajas', 18, 8, 1),
('Ibuprofeno 400mg', 'cajas', 10, 5, 1),
('Anticonceptivos orales', 'cajas', 12, 5, 1);

-- ============================================
-- TABLA: inventario_movimientos
-- Movimientos recientes de inventario
-- ============================================

INSERT INTO inventario_movimientos (producto_id, usuario_id, tipo, cantidad, motivo, fecha) VALUES
-- Entradas (compras recientes)
(1, 2, 'entrada', 10, 'Compra a farmacia', '2025-03-01 08:00:00'),
(2, 2, 'entrada', 10, 'Compra a farmacia', '2025-03-01 08:00:00'),
(3, 2, 'entrada', 15, 'Compra a farmacia', '2025-03-01 08:00:00'),

-- Salidas (uso en consultas)
(1, 2, 'salida', 1, 'Receta paciente Ana María Pérez', '2025-03-01 09:30:00'),
(2, 2, 'salida', 1, 'Receta paciente Ana María Pérez', '2025-03-01 09:30:00'),
(3, 2, 'salida', 1, 'Receta paciente Sofía López', '2025-03-02 09:30:00'),
(4, 2, 'salida', 2, 'Uso en consulta', '2025-03-01 10:00:00');

-- ============================================
-- RESTAURAR CONFIGURACIÓN
-- ============================================

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- VERIFICACIÓN
-- ============================================

SELECT 'DATOS DE PRUEBA INSERTADOS CORRECTAMENTE' AS Resultado;

SELECT 
    'Usuarios' AS Tabla, COUNT(*) AS Total FROM usuarios
UNION ALL
SELECT 'Pacientes', COUNT(*) FROM pacientes
UNION ALL
SELECT 'Citas', COUNT(*) FROM citas
UNION ALL
SELECT 'Consultas', COUNT(*) FROM consultas
UNION ALL
SELECT 'Signos Vitales', COUNT(*) FROM signos_vitales
UNION ALL
SELECT 'Recetas', COUNT(*) FROM recetas
UNION ALL
SELECT 'Recetas Detalle', COUNT(*) FROM recetas_detalle
UNION ALL
SELECT 'Ultrasonidos', COUNT(*) FROM ultrasonidos
UNION ALL
SELECT 'Facturas', COUNT(*) FROM facturas
UNION ALL
SELECT 'Exámenes Lab', COUNT(*) FROM examenes_laboratorio
UNION ALL
SELECT 'Inventario Productos', COUNT(*) FROM inventario_productos
UNION ALL
SELECT 'Inventario Movimientos', COUNT(*) FROM inventario_movimientos;

-- ============================================
-- INFORMACIÓN DE USUARIOS DE PRUEBA
-- ============================================

SELECT 
    '=== USUARIOS DE PRUEBA ===' AS Info,
    '' AS Email,
    '' AS Password,
    '' AS Rol
UNION ALL
SELECT 
    '',
    email,
    'password123' AS password,
    rol
FROM usuarios
ORDER BY rol;