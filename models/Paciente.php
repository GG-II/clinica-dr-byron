<?php
/**
 * MODELO: Paciente
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Gestión completa del expediente clínico digital:
 * - CRUD de pacientes
 * - Búsquedas y filtros
 * - Historial médico
 * - Validaciones
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');

// ============================================================================
// FUNCIONES DE LECTURA (READ)
// ============================================================================

/**
 * Obtiene todos los pacientes activos
 * 
 * @param PDO $pdo Conexión a BD
 * @param array $filtros Filtros opcionales ['activo' => 1, 'sexo' => 'F']
 * @return array Lista de pacientes
 */
function obtener_pacientes($pdo, $filtros = []) {
    try {
        $sql = "SELECT 
                    id, codigo, nombre, fecha_nacimiento, sexo, 
                    dpi, telefono, email, direccion, 
                    tipo_sangre, alergias, activo, 
                    created_at, updated_at
                FROM pacientes 
                WHERE 1=1";
        
        $params = [];
        
        // Filtro por activo
        if (isset($filtros['activo'])) {
            $sql .= " AND activo = ?";
            $params[] = $filtros['activo'];
        }
        
        // Filtro por sexo
        if (isset($filtros['sexo'])) {
            $sql .= " AND sexo = ?";
            $params[] = $filtros['sexo'];
        }
        
        // Ordenar por nombre
        $sql .= " ORDER BY nombre ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        log_mensaje("Error al obtener pacientes: " . $e->getMessage(), 'error');
        return [];
    }
}

/**
 * Obtiene un paciente por su ID
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID del paciente
 * @return array|false Datos del paciente o false si no existe
 */
function obtener_paciente_por_id($pdo, $id) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                id, codigo, nombre, fecha_nacimiento, sexo,
                dpi, telefono, email, direccion,
                contacto_emergencia, tipo_sangre, alergias,
                antecedentes_personales, antecedentes_familiares, 
                antecedentes_quirurgicos, activo,
                created_at, updated_at
            FROM pacientes 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        log_mensaje("Error al obtener paciente por ID: " . $e->getMessage(), 'error');
        return false;
    }
}

/**
 * Obtiene un paciente por su código
 * 
 * @param PDO $pdo Conexión a BD
 * @param string $codigo Código del paciente (PAC-000001)
 * @return array|false Datos del paciente o false si no existe
 */
function obtener_paciente_por_codigo($pdo, $codigo) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM pacientes WHERE codigo = ?
        ");
        $stmt->execute([$codigo]);
        
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        log_mensaje("Error al obtener paciente por código: " . $e->getMessage(), 'error');
        return false;
    }
}

/**
 * Busca pacientes por nombre, código o DPI
 * 
 * @param PDO $pdo Conexión a BD
 * @param string $termino Término de búsqueda
 * @return array Lista de pacientes que coinciden
 */
function buscar_pacientes($pdo, $termino) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                id, codigo, nombre, fecha_nacimiento, sexo,
                telefono, email, activo
            FROM pacientes 
            WHERE (nombre LIKE ? OR codigo LIKE ? OR dpi LIKE ?)
            AND activo = 1
            ORDER BY nombre ASC
            LIMIT 50
        ");
        
        $termino_busqueda = '%' . $termino . '%';
        $stmt->execute([$termino_busqueda, $termino_busqueda, $termino_busqueda]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        log_mensaje("Error al buscar pacientes: " . $e->getMessage(), 'error');
        return [];
    }
}

/**
 * Cuenta el total de pacientes
 * 
 * @param PDO $pdo Conexión a BD
 * @param array $filtros Filtros opcionales
 * @return int Total de pacientes
 */
function contar_pacientes($pdo, $filtros = []) {
    try {
        $sql = "SELECT COUNT(*) FROM pacientes WHERE 1=1";
        $params = [];
        
        if (isset($filtros['activo'])) {
            $sql .= " AND activo = ?";
            $params[] = $filtros['activo'];
        }
        
        if (isset($filtros['sexo'])) {
            $sql .= " AND sexo = ?";
            $params[] = $filtros['sexo'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int) $stmt->fetchColumn();
        
    } catch (PDOException $e) {
        log_mensaje("Error al contar pacientes: " . $e->getMessage(), 'error');
        return 0;
    }
}

/**
 * Obtiene el historial de consultas de un paciente
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $paciente_id ID del paciente
 * @param int $limit Límite de registros (opcional)
 * @return array Historial de consultas
 */
function obtener_historial_consultas($pdo, $paciente_id, $limit = null) {
    try {
        $sql = "SELECT 
                    c.id, c.fecha, c.diagnostico, c.tratamiento,
                    c.observaciones, u.nombre as doctor
                FROM consultas c
                LEFT JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.paciente_id = ?
                ORDER BY c.fecha DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$paciente_id]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        log_mensaje("Error al obtener historial: " . $e->getMessage(), 'error');
        return [];
    }
}

// ============================================================================
// FUNCIONES DE CREACIÓN (CREATE)
// ============================================================================

/**
 * Genera el siguiente código de paciente
 * 
 * @param PDO $pdo Conexión a BD
 * @return string Código generado (PAC-000001)
 */
function generar_codigo_paciente($pdo) {
    try {
        $stmt = $pdo->query("SELECT MAX(id) FROM pacientes");
        $ultimo_id = $stmt->fetchColumn();
        
        $siguiente_numero = ($ultimo_id ?? 0) + 1;
        
        return 'PAC-' . str_pad($siguiente_numero, 6, '0', STR_PAD_LEFT);
        
    } catch (PDOException $e) {
        log_mensaje("Error al generar código: " . $e->getMessage(), 'error');
        return 'PAC-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}

/**
 * Crea un nuevo paciente
 * 
 * @param PDO $pdo Conexión a BD
 * @param array $datos Datos del paciente
 * @return array Resultado ['success' => bool, 'message' => string, 'id' => int]
 */
function crear_paciente($pdo, $datos) {
    // LOG: Datos recibidos en el modelo
    log_mensaje("Modelo Paciente: crear_paciente() llamado con nombre: " . ($datos['nombre'] ?? 'NULL'), 'debug');
    log_mensaje("Modelo Paciente: Datos completos: " . json_encode($datos), 'debug');
    
    try {
        // Validaciones
        $validacion = validar_datos_paciente($datos, true);
        if (!$validacion['success']) {
            return $validacion;
        }
        
        // Verificar que el DPI no exista (si se proporcionó)
        if (!empty($datos['dpi'])) {
            if (dpi_paciente_existe($pdo, $datos['dpi'])) {
                return [
                    'success' => false,
                    'message' => 'El DPI ya está registrado en el sistema.'
                ];
            }
        }
        
        // Generar código si no se proporcionó
        $codigo = $datos['codigo'] ?? generar_codigo_paciente($pdo);
        
        // Insertar paciente
        $stmt = $pdo->prepare("
            INSERT INTO pacientes (
                codigo, nombre, fecha_nacimiento, sexo,
                dpi, telefono, email, direccion,
                contacto_emergencia, tipo_sangre, alergias,
                antecedentes_personales, antecedentes_familiares,
                antecedentes_quirurgicos, activo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

$stmt->execute([
    $codigo,
    $datos['nombre'],
    $datos['fecha_nacimiento'],
    $datos['sexo'],
    $datos['dpi'] ?? null,
    $datos['telefono'] ?? null,
    $datos['email'] ?? null,
    $datos['direccion'] ?? null,
    $datos['contacto_emergencia'] ?? null,  // <-- Este es combinado
    $datos['tipo_sangre'] ?? null,
    $datos['alergias'] ?? null,
    $datos['antecedentes_personales'] ?? null,
    $datos['antecedentes_familiares'] ?? null,
    $datos['antecedentes_quirurgicos'] ?? null,
    $datos['activo'] ?? ESTADO_ACTIVO
]);
        
        $nuevo_id = $pdo->lastInsertId();
        
        // Registrar en auditoría
        if (isset($_SESSION['usuario_id'])) {
            registrar_auditoria($pdo, $_SESSION['usuario_id'], 'CREAR', 'pacientes', $nuevo_id);
        }
        
        log_mensaje("Paciente creado: {$datos['nombre']} ({$codigo})", 'info');
        
        return [
            'success' => true,
            'message' => 'Paciente creado correctamente.',
            'id' => $nuevo_id,
            'codigo' => $codigo
        ];
        
    } catch (PDOException $e) {
        log_mensaje("Modelo Paciente: PDOException - " . $e->getMessage(), 'error');
        log_mensaje("Modelo Paciente: SQL State: " . $e->getCode(), 'error');
        log_mensaje("Modelo Paciente: Stack trace: " . $e->getTraceAsString(), 'error');
        return [
            'success' => false,
            'message' => 'Error al crear el paciente. Por favor intente nuevamente.'
        ];
    }
}

// ============================================================================
// FUNCIONES DE ACTUALIZACIÓN (UPDATE)
// ============================================================================

/**
 * Actualiza los datos de un paciente
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID del paciente
 * @param array $datos Datos a actualizar
 * @return array Resultado ['success' => bool, 'message' => string]
 */
function actualizar_paciente($pdo, $id, $datos) {
    try {
        // Verificar que el paciente existe
        $paciente_actual = obtener_paciente_por_id($pdo, $id);
        if (!$paciente_actual) {
            return [
                'success' => false,
                'message' => 'El paciente no existe.'
            ];
        }
        
        // Validaciones
        $validacion = validar_datos_paciente($datos, false);
        if (!$validacion['success']) {
            return $validacion;
        }
        
        // Si cambió el DPI, verificar que no exista
        if (isset($datos['dpi']) && $datos['dpi'] !== $paciente_actual['dpi']) {
            if (!empty($datos['dpi']) && dpi_paciente_existe($pdo, $datos['dpi'], $id)) {
                return [
                    'success' => false,
                    'message' => 'El DPI ya está registrado por otro paciente.'
                ];
            }
        }
        
        // Construir query dinámicamente
        $campos_actualizar = [];
        $params = [];
        
        $campos_permitidos = [
            'nombre', 'fecha_nacimiento', 'sexo', 'dpi',
            'telefono', 'email', 'direccion', 'contacto_emergencia',
            'tipo_sangre', 'alergias', 'antecedentes_personales',
            'antecedentes_familiares', 'antecedentes_quirurgicos', 'activo'
        ];
        
        foreach ($campos_permitidos as $campo) {
            if (isset($datos[$campo])) {
                $campos_actualizar[] = "$campo = ?";
                $params[] = $datos[$campo];
            }
        }
        
        if (empty($campos_actualizar)) {
            return [
                'success' => false,
                'message' => 'No hay datos para actualizar.'
            ];
        }
        
        // Agregar ID al final de params
        $params[] = $id;
        
        // Ejecutar actualización
        $sql = "UPDATE pacientes SET " . implode(', ', $campos_actualizar) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Registrar en auditoría
        if (isset($_SESSION['usuario_id'])) {
            registrar_auditoria($pdo, $_SESSION['usuario_id'], 'EDITAR', 'pacientes', $id);
        }
        
        log_mensaje("Paciente actualizado ID: {$id}", 'info');
        
        return [
            'success' => true,
            'message' => 'Paciente actualizado correctamente.'
        ];
        
    } catch (PDOException $e) {
        log_mensaje("Error al actualizar paciente: " . $e->getMessage(), 'error');
        return [
            'success' => false,
            'message' => 'Error al actualizar el paciente. Por favor intente nuevamente.'
        ];
    }
}

/**
 * Activa un paciente
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID del paciente
 * @return array Resultado
 */
function activar_paciente($pdo, $id) {
    return actualizar_paciente($pdo, $id, ['activo' => ESTADO_ACTIVO]);
}

/**
 * Desactiva un paciente (soft delete)
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID del paciente
 * @return array Resultado
 */
function desactivar_paciente($pdo, $id) {
    return actualizar_paciente($pdo, $id, ['activo' => ESTADO_INACTIVO]);
}

// ============================================================================
// FUNCIONES DE ELIMINACIÓN (DELETE)
// ============================================================================

/**
 * Elimina un paciente permanentemente
 * ADVERTENCIA: Esto es peligroso. Usar desactivar_paciente() en su lugar.
 * 
 * @param PDO $pdo Conexión a BD
 * @param int $id ID del paciente
 * @return array Resultado
 */
function eliminar_paciente($pdo, $id) {
    try {
        // Verificar que el paciente existe
        $paciente = obtener_paciente_por_id($pdo, $id);
        if (!$paciente) {
            return [
                'success' => false,
                'message' => 'El paciente no existe.'
            ];
        }
        
        // Verificar si tiene consultas (no permitir eliminar si tiene historial)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM consultas WHERE paciente_id = ?");
        $stmt->execute([$id]);
        $total_consultas = $stmt->fetchColumn();
        
        if ($total_consultas > 0) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar. El paciente tiene ' . $total_consultas . ' consulta(s) registrada(s). Use desactivar en su lugar.'
            ];
        }
        
        // Eliminar paciente
        $stmt = $pdo->prepare("DELETE FROM pacientes WHERE id = ?");
        $stmt->execute([$id]);
        
        // Registrar en auditoría
        if (isset($_SESSION['usuario_id'])) {
            registrar_auditoria($pdo, $_SESSION['usuario_id'], 'ELIMINAR', 'pacientes', $id);
        }
        
        log_mensaje("Paciente eliminado: {$paciente['nombre']} (ID: {$id})", 'warning');
        
        return [
            'success' => true,
            'message' => 'Paciente eliminado correctamente.'
        ];
        
    } catch (PDOException $e) {
        log_mensaje("Error al eliminar paciente: " . $e->getMessage(), 'error');
        return [
            'success' => false,
            'message' => 'Error al eliminar el paciente. Puede tener registros relacionados.'
        ];
    }
}

// ============================================================================
// FUNCIONES DE VALIDACIÓN
// ============================================================================

/**
 * Valida los datos de un paciente
 * 
 * @param array $datos Datos a validar
 * @param bool $es_nuevo Si es true, valida campos obligatorios para creación
 * @return array Resultado ['success' => bool, 'message' => string]
 */
function validar_datos_paciente($datos, $es_nuevo = false) {
    // Validar nombre (obligatorio siempre)
    if ($es_nuevo || isset($datos['nombre'])) {
        if (empty($datos['nombre']) || strlen($datos['nombre']) < 3) {
            return [
                'success' => false,
                'message' => 'El nombre debe tener al menos 3 caracteres.'
            ];
        }
    }
    
    // Validar fecha de nacimiento (obligatorio en creación)
    if ($es_nuevo || isset($datos['fecha_nacimiento'])) {
        if (!validar_fecha($datos['fecha_nacimiento'])) {
            return [
                'success' => false,
                'message' => 'La fecha de nacimiento no es válida.'
            ];
        }
        
        // Verificar que no sea fecha futura
        if (strtotime($datos['fecha_nacimiento']) > time()) {
            return [
                'success' => false,
                'message' => 'La fecha de nacimiento no puede ser futura.'
            ];
        }
    }
    
    // Validar sexo (obligatorio en creación)
    if ($es_nuevo || isset($datos['sexo'])) {
        if (!in_array($datos['sexo'], ['M', 'F'])) {
            return [
                'success' => false,
                'message' => 'El sexo debe ser M (Masculino) o F (Femenino).'
            ];
        }
    }
    
    // Validar DPI (opcional pero si se proporciona debe ser válido)
    if (!empty($datos['dpi'])) {
        if (!validar_dpi($datos['dpi'])) {
            return [
                'success' => false,
                'message' => 'El DPI debe tener 13 dígitos.'
            ];
        }
    }
    
    // Validar teléfono (opcional pero si se proporciona debe ser válido)
    if (!empty($datos['telefono'])) {
        if (!validar_telefono($datos['telefono'])) {
            return [
                'success' => false,
                'message' => 'El teléfono debe tener 8 dígitos.'
            ];
        }
    }
    
    // Validar email (opcional pero si se proporciona debe ser válido)
    if (!empty($datos['email'])) {
        if (!validar_email($datos['email'])) {
            return [
                'success' => false,
                'message' => 'El email no es válido.'
            ];
        }
    }
    
    return ['success' => true];
}

/**
 * Verifica si un DPI ya está registrado
 * 
 * @param PDO $pdo Conexión a BD
 * @param string $dpi DPI a verificar
 * @param int|null $excluir_id ID de paciente a excluir (para actualizaciones)
 * @return bool True si el DPI existe
 */
function dpi_paciente_existe($pdo, $dpi, $excluir_id = null) {
    try {
        $sql = "SELECT COUNT(*) FROM pacientes WHERE dpi = ?";
        $params = [$dpi];
        
        if ($excluir_id !== null) {
            $sql .= " AND id != ?";
            $params[] = $excluir_id;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
        
    } catch (PDOException $e) {
        log_mensaje("Error al verificar DPI: " . $e->getMessage(), 'error');
        return false;
    }
}