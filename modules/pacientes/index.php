<?php
/**
 * LISTA DE PACIENTES
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Muestra la lista de todos los pacientes con búsqueda y paginación
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

session_start();
define('ACCESS_GRANTED', true);
require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'middleware.php';
require_once MODELS_PATH . 'Paciente.php';

// Proteger la ruta (admin, médico, asistente)
proteger_ruta();

// Variables para la página
$page_title = 'Lista de Pacientes';
$breadcrumb_items = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard'],
    ['titulo' => 'Lista de Pacientes', 'url' => null]
];

// Parámetros de búsqueda y paginación
$busqueda = $_GET['buscar'] ?? '';
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$registros_por_pagina = 25;

// Obtener pacientes
try {
    if (!empty($busqueda)) {
        // Si hay búsqueda, no paginar (mostrar todos los resultados)
        $pacientes = buscar_pacientes($pdo, $busqueda);
        $total_pacientes = count($pacientes);
        $total_paginas = 1;
    } else {
        // Sin búsqueda, obtener todos con paginación
        $offset = calcular_offset($pagina_actual, $registros_por_pagina);
        
        // Obtener todos los pacientes activos
        $stmt = $pdo->prepare("
            SELECT 
                p.id, p.codigo, p.nombre, p.fecha_nacimiento,
                p.telefono, p.activo, p.created_at,
                (SELECT MAX(c.fecha) FROM consultas c WHERE c.paciente_id = p.id) as ultima_consulta
            FROM pacientes p
            WHERE p.activo = 1
            ORDER BY p.nombre ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$registros_por_pagina, $offset]);
        $pacientes = $stmt->fetchAll();
        
        // Contar total para paginación
        $total_pacientes = contar_pacientes($pdo, ['activo' => 1]);
        $total_paginas = ceil($total_pacientes / $registros_por_pagina);
    }
    
} catch (PDOException $e) {
    log_mensaje("Error al cargar pacientes: " . $e->getMessage(), 'error');
    mensaje_error('Error al cargar la lista de pacientes.');
    $pacientes = [];
    $total_pacientes = 0;
    $total_paginas = 0;
}

// Incluir header
include INCLUDES_PATH . 'header.php';
?>

<!-- Título y botón de acción -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Pacientes</h1>
    <?php if (puede_crear()): ?>
    <a href="nuevo.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Paciente
    </a>
    <?php endif; ?>
</div>

<!-- Card principal -->
<div class="table-card">
    <div class="table-card-body">
        <!-- Buscador -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search"></i>
                </span>
                <input 
                    type="text" 
                    name="buscar" 
                    class="form-control" 
                    placeholder="Buscar por nombre, teléfono o código..."
                    value="<?= e($busqueda) ?>"
                    autofocus
                >
                <?php if (!empty($busqueda)): ?>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i> Limpiar
                </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Tabla de pacientes -->
        <?php if (count($pacientes) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 100px;">CÓDIGO</th>
                        <th>NOMBRE</th>
                        <th style="width: 150px;">TELÉFONO</th>
                        <th style="width: 150px;">FECHA REGISTRO</th>
                        <th style="width: 150px;">ÚLTIMA CONSULTA</th>
                        <th style="width: 150px;" class="text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $paciente): ?>
                    <tr class="<?= $paciente['activo'] ? '' : 'table-secondary' ?>">
                        <!-- Código -->
                        <td>
                            <strong><?= e($paciente['codigo']) ?></strong>
                        </td>
                        
                        <!-- Nombre (link a expediente) -->
                        <td>
                            <a href="ver.php?id=<?= $paciente['id'] ?>" class="text-primary text-decoration-none fw-bold">
                                <?= e($paciente['nombre']) ?>
                            </a>
                        </td>
                        
                        <!-- Teléfono -->
                        <td>
                            <?php if (!empty($paciente['telefono'])): ?>
                                <?= formatear_telefono($paciente['telefono']) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        
                        <!-- Fecha de registro -->
                        <td>
                            <?= formatear_fecha($paciente['created_at']) ?>
                        </td>
                        
                        <!-- Última consulta -->
                        <td>
                            <?php if ($paciente['ultima_consulta']): ?>
                                <?= formatear_fecha($paciente['ultima_consulta']) ?>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        
                        <!-- Acciones -->
                        <td class="text-center">
                            <!-- Ver expediente -->
                            <a 
                                href="ver.php?id=<?= $paciente['id'] ?>" 
                                class="btn btn-sm btn-outline-info" 
                                title="Ver expediente"
                            >
                                <i class="bi bi-eye"></i>
                            </a>
                            
                            <!-- Editar (solo admin y médico) -->
                            <?php if (puede_editar()): ?>
                            <a 
                                href="editar.php?id=<?= $paciente['id'] ?>" 
                                class="btn btn-sm btn-outline-warning" 
                                title="Editar paciente"
                            >
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                            
                            <!-- Toggle activo/inactivo (solo admin) -->
                            <?php if (puede_eliminar()): ?>
                                <?php if ($paciente['activo']): ?>
                                <button 
                                    onclick="desactivarPaciente(<?= $paciente['id'] ?>)" 
                                    class="btn btn-sm btn-outline-success" 
                                    title="Paciente activo - Click para desactivar"
                                >
                                    <i class="bi bi-toggle-on"></i>
                                </button>
                                <?php else: ?>
                                <button 
                                    onclick="activarPaciente(<?= $paciente['id'] ?>)" 
                                    class="btn btn-sm btn-outline-secondary" 
                                    title="Paciente inactivo - Click para activar"
                                >
                                    <i class="bi bi-toggle-off"></i>
                                </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Información de paginación -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Mostrando 
                <?= number_format(count($pacientes)) ?> 
                <?= !empty($busqueda) ? 'resultado(s) de búsqueda' : 'de ' . number_format($total_pacientes) . ' paciente(s)' ?>
            </div>
            
            <!-- Paginación (solo si no hay búsqueda) -->
            <?php if (empty($busqueda) && $total_paginas > 1): ?>
            <nav>
                <ul class="pagination mb-0">
                    <!-- Anterior -->
                    <li class="page-item <?= $pagina_actual <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina_actual - 1 ?>">
                            Anterior
                        </a>
                    </li>
                    
                    <!-- Números de página -->
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <?php if ($i == 1 || $i == $total_paginas || ($i >= $pagina_actual - 2 && $i <= $pagina_actual + 2)): ?>
                        <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php elseif ($i == $pagina_actual - 3 || $i == $pagina_actual + 3): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <!-- Siguiente -->
                    <li class="page-item <?= $pagina_actual >= $total_paginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $pagina_actual + 1 ?>">
                            Siguiente
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>

        <?php else: ?>
        <!-- Sin resultados -->
        <div class="text-center py-5">
            <i class="bi bi-search" style="font-size: 64px; color: #dee2e6;"></i>
            <h5 class="mt-3 text-muted">
                <?= !empty($busqueda) ? 'No se encontraron pacientes' : 'No hay pacientes registrados' ?>
            </h5>
            <?php if (!empty($busqueda)): ?>
            <p class="text-muted">
                Intenta con otro término de búsqueda
            </p>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Ver todos los pacientes
            </a>
            <?php else: ?>
            <p class="text-muted">
                Comienza agregando tu primer paciente
            </p>
            <?php if (puede_crear()): ?>
            <a href="nuevo.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Paciente
            </a>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
// JavaScript adicional
$additional_js = <<<'JS'
<script>
// Activar paciente
function activarPaciente(id) {
    if (confirm('¿Desea activar este paciente?')) {
        window.location.href = 'acciones.php?accion=activar&id=' + id;
    }
}

// Desactivar paciente
function desactivarPaciente(id) {
    if (confirm('¿Desea desactivar este paciente? Podrá reactivarlo después.')) {
        window.location.href = 'acciones.php?accion=desactivar&id=' + id;
    }
}

// Auto-submit del formulario de búsqueda al escribir (opcional)
/*
const searchInput = document.querySelector('input[name="buscar"]');
let searchTimeout;
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (this.value.length >= 3 || this.value.length === 0) {
            this.form.submit();
        }
    }, 500);
});
*/
</script>
JS;

// Incluir footer
include INCLUDES_PATH . 'footer.php';
?>