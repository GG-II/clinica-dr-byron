<?php
/**
 * USUARIOS - Lista de Usuarios
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Gestión de usuarios del sistema (solo Administradores)
 */

define('ACCESS_GRANTED', true);
session_start();

require_once '../../config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'funciones.php';
require_once MODELS_PATH . 'Usuario.php';

// Verificar autenticación y permisos (SOLO ADMIN)
verificar_sesion();

if (!es_admin()) {
    $_SESSION['mensaje_error'] = 'No tienes permisos para acceder a esta sección';
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
}

// Obtener filtros
$filtros = [];
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$rol_filtro = isset($_GET['rol']) ? $_GET['rol'] : '';
$estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : '';

if ($buscar) {
    $filtros['buscar'] = $buscar;
}
if ($rol_filtro) {
    $filtros['rol'] = $rol_filtro;
}
if ($estado_filtro !== '') {
    $filtros['activo'] = (int)$estado_filtro;
}

// Paginación
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$por_pagina = 50;
$offset = ($pagina - 1) * $por_pagina;

// Obtener usuarios
$usuarios = obtener_usuarios($pdo, $filtros, $por_pagina, $offset);
$total_usuarios = contar_usuarios($pdo, $filtros);
$total_paginas = ceil($total_usuarios / $por_pagina);

// Variables para el header
$titulo = 'Gestión de Usuarios';
$breadcrumb = [
    ['titulo' => 'Clínica', 'url' => BASE_URL . 'dashboard.php'],
    ['titulo' => 'Usuarios', 'url' => '']
];

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Contenido principal -->
<div class="container-fluid mt-4">
    
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><?= $titulo ?></h2>
            <p class="text-muted">Gestión de Usuarios</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= BASE_URL ?>modules/usuarios/nuevo.php" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Nuevo Usuario
            </a>
        </div>
    </div>
    
    <!-- Mensajes flash -->
    <?php mostrar_mensajes(); ?>
    
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                
                <!-- Búsqueda -->
                <div class="col-md-4">
                    <label class="form-label text-muted small">BUSCAR</label>
                    <div class="input-group">
                        <input type="text" name="buscar" class="form-control" 
                               placeholder="Nombre o email..." 
                               value="<?= e($buscar) ?>">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Rol -->
                <div class="col-md-3">
                    <label class="form-label text-muted small">ROL</label>
                    <select name="rol" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="admin" <?= $rol_filtro === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        <option value="medico" <?= $rol_filtro === 'medico' ? 'selected' : '' ?>>Médico</option>
                        <option value="asistente" <?= $rol_filtro === 'asistente' ? 'selected' : '' ?>>Asistente</option>
                    </select>
                </div>
                
                <!-- Estado -->
                <div class="col-md-3">
                    <label class="form-label text-muted small">ESTADO</label>
                    <select name="estado" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="1" <?= $estado_filtro === '1' ? 'selected' : '' ?>>Activos</option>
                        <option value="0" <?= $estado_filtro === '0' ? 'selected' : '' ?>>Inactivos</option>
                    </select>
                </div>
                
                <!-- Limpiar -->
                <div class="col-md-2 d-flex align-items-end">
                    <a href="<?= BASE_URL ?>modules/usuarios/index.php" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
                
            </form>
        </div>
    </div>
    
    <!-- Tabla de usuarios -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-people"></i>
                    Lista de Usuarios
                </h5>
                <span class="badge bg-secondary">
                    <?= $total_usuarios ?> <?= $total_usuarios === 1 ? 'usuario' : 'usuarios' ?>
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            
            <?php if (empty($usuarios)): ?>
                
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3 mb-0">No se encontraron usuarios</p>
                </div>
                
            <?php else: ?>
                
                <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th class="ps-4">NOMBRE</th>
                <th>EMAIL</th>
                <th>ROL</th>
                <th>ESTADO</th>
                <th class="text-end pe-4">ACCIONES</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <!-- Avatar + Nombre -->
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <?php
                                $iniciales = '';
                                $palabras = explode(' ', $usuario['nombre']);
                                $iniciales = strtoupper(substr($palabras[0], 0, 1));
                                if (isset($palabras[1])) {
                                    $iniciales .= strtoupper(substr($palabras[1], 0, 1));
                                }
                                
                                // Determinar color del rol
                                $color_rol = 'primary';
                                if ($usuario['rol'] === 'admin') {
                                    $color_rol = 'danger';
                                } elseif ($usuario['rol'] === 'medico') {
                                    $color_rol = 'primary';
                                } elseif ($usuario['rol'] === 'asistente') {
                                    $color_rol = 'success';
                                }
                                ?>
                                <div style="width: 45px; height: 45px; border-radius: 50%; background-color: var(--bs-<?= $color_rol ?>); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.875rem;">
                                    <?= $iniciales ?>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold"><?= e($usuario['nombre']) ?></div>
                                <small class="text-muted">
                                    Desde <?= date('M Y', strtotime($usuario['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Email -->
                    <td>
                        <span class="text-muted"><?= e($usuario['email']) ?></span>
                    </td>
                    
                    <!-- Rol -->
                    <td>
                        <?php
                        $badge_color = 'secondary';
                        $rol_nombre = $usuario['rol'];
                        
                        if ($usuario['rol'] === 'admin') {
                            $badge_color = 'danger';
                            $rol_nombre = 'Administrador';
                        } elseif ($usuario['rol'] === 'medico') {
                            $badge_color = 'primary';
                            $rol_nombre = 'Médico';
                        } elseif ($usuario['rol'] === 'asistente') {
                            $badge_color = 'success';
                            $rol_nombre = 'Asistente';
                        }
                        ?>
                        <span class="badge bg-<?= $badge_color ?>">
                            <?= $rol_nombre ?>
                        </span>
                    </td>
                    
                    <!-- Estado -->
                    <td>
                        <?php if ($usuario['activo']): ?>
                            <span class="badge" style="background-color: rgba(25, 135, 84, 0.1); color: #198754;">
                                <i class="bi bi-check-circle-fill"></i> Activo
                            </span>
                        <?php else: ?>
                            <span class="badge" style="background-color: rgba(220, 53, 69, 0.1); color: #dc3545;">
                                <i class="bi bi-x-circle-fill"></i> Inactivo
                            </span>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Acciones -->
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <!-- Ver -->
                            <a href="<?= BASE_URL ?>modules/usuarios/ver.php?id=<?= $usuario['id'] ?>" 
                               class="btn btn-outline-info" 
                               title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </a>
                            
                            <!-- Editar -->
                            <a href="<?= BASE_URL ?>modules/usuarios/editar.php?id=<?= $usuario['id'] ?>" 
                               class="btn btn-outline-warning" 
                               title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            
                            <!-- Cambiar contraseña -->
                            <a href="<?= BASE_URL ?>modules/usuarios/cambiar-password.php?id=<?= $usuario['id'] ?>" 
                               class="btn btn-outline-secondary" 
                               title="Cambiar contraseña">
                                <i class="bi bi-key"></i>
                            </a>
                            
                            <!-- Activar/Desactivar -->
                            <?php if ($usuario['id'] != obtener_usuario_id()): ?>
                                <button type="button" 
                                        class="btn btn-outline-<?= $usuario['activo'] ? 'danger' : 'success' ?>" 
                                        onclick="cambiarEstado(<?= $usuario['id'] ?>, <?= $usuario['activo'] ? 0 : 1 ?>)"
                                        title="<?= $usuario['activo'] ? 'Desactivar' : 'Activar' ?>">
                                    <i class="bi bi-<?= $usuario['activo'] ? 'toggle-on' : 'toggle-off' ?>"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
                
                <!-- Paginación -->
                <?php if ($total_paginas > 1): ?>
                    <div class="card-footer">
                        <nav>
                            <ul class="pagination pagination-sm mb-0 justify-content-center">
                                
                                <!-- Anterior -->
                                <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?pagina=<?= $pagina - 1 ?>&buscar=<?= urlencode($buscar) ?>&rol=<?= $rol_filtro ?>&estado=<?= $estado_filtro ?>">
                                        Anterior
                                    </a>
                                </li>
                                
                                <!-- Páginas -->
                                <?php
                                $rango = 2;
                                $inicio = max(1, $pagina - $rango);
                                $fin = min($total_paginas, $pagina + $rango);
                                
                                if ($inicio > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=1&buscar=<?= urlencode($buscar) ?>&rol=<?= $rol_filtro ?>&estado=<?= $estado_filtro ?>">1</a>
                                    </li>
                                    <?php if ($inicio > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                                    <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                                        <a class="page-link" href="?pagina=<?= $i ?>&buscar=<?= urlencode($buscar) ?>&rol=<?= $rol_filtro ?>&estado=<?= $estado_filtro ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($fin < $total_paginas): ?>
                                    <?php if ($fin < $total_paginas - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?= $total_paginas ?>&buscar=<?= urlencode($buscar) ?>&rol=<?= $rol_filtro ?>&estado=<?= $estado_filtro ?>">
                                            <?= $total_paginas ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <!-- Siguiente -->
                                <li class="page-item <?= $pagina >= $total_paginas ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?pagina=<?= $pagina + 1 ?>&buscar=<?= urlencode($buscar) ?>&rol=<?= $rol_filtro ?>&estado=<?= $estado_filtro ?>">
                                        Siguiente
                                    </a>
                                </li>
                                
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
                
            <?php endif; ?>
            
        </div>
    </div>
    
</div>

<script>
// Cambiar estado de usuario (activar/desactivar)
function cambiarEstado(userId, nuevoEstado) {
    const accion = nuevoEstado ? 'activar' : 'desactivar';
    
    if (!confirm(`¿Está seguro que desea ${accion} este usuario?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('activo', nuevoEstado);
    
    fetch('<?= BASE_URL ?>api/usuarios/cambiar_estado.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'No se pudo cambiar el estado'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}
</script>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>