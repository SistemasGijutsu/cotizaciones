<?php $title = 'Gestión de Usuarios'; ?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-users me-2"></i>Gestión de Usuarios</h1>
    <div>
        <a href="index.php?controller=user&action=create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Usuario
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
    <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['username']); ?></td>
                    <td><?php echo htmlspecialchars($u['nombre_completo'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo $u['created_at']; ?></td>
                    <td>
                        <a class="btn btn-sm btn-info" href="index.php?controller=user&action=show&id=<?php echo $u['id']; ?>">Ver</a>
                        <a class="btn btn-sm btn-warning" href="index.php?controller=user&action=edit&id=<?php echo $u['id']; ?>">Editar</a>
                        <a class="btn btn-sm btn-danger" href="index.php?controller=user&action=delete&id=<?php echo $u['id']; ?>" onclick="return confirm('Eliminar usuario?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Paginación simple
$totalPages = (int) ceil(($total ?? count($users)) / ($perPage ?? 10));
$currentPage = $page ?? 1;
if ($totalPages > 1): ?>
<nav class="mt-3">
    <ul class="pagination">
        <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="?controller=user&action=index&page=<?php echo $currentPage-1; ?>">Anterior</a>
        </li>
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <li class="page-item <?php echo $p == $currentPage ? 'active' : ''; ?>">
                <a class="page-link" href="?controller=user&action=index&page=<?php echo $p; ?>"><?php echo $p; ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
            <a class="page-link" href="?controller=user&action=index&page=<?php echo $currentPage+1; ?>">Siguiente</a>
        </li>
    </ul>
</nav>
<?php endif; ?>
