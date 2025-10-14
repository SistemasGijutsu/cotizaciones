<?php $title = 'Gestión de Clientes'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-users me-2"></i>
        Gestión de Clientes
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?controller=cliente&action=create" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Nuevo Cliente
        </a>
    </div>
</div>

<!-- Formulario de búsqueda -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="controller" value="cliente">
            <input type="hidden" name="action" value="index">
            
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" name="search" 
                           placeholder="Buscar por nombre o correo electrónico..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i>
                    Buscar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de clientes -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Lista de Clientes</h5>
    </div>
    <div class="card-body">
        <?php if (empty($clientes)): ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-users fa-3x mb-3"></i>
                <h5>No hay clientes registrados</h5>
                <p>Comience agregando su primer cliente</p>
                <a href="index.php?controller=cliente&action=create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Agregar Cliente
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?php echo $cliente['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($cliente['nombre']); ?></strong>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($cliente['correo']); ?>">
                                        <?php echo htmlspecialchars($cliente['correo']); ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="tel:<?php echo htmlspecialchars($cliente['telefono']); ?>">
                                        <?php echo htmlspecialchars($cliente['telefono']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($cliente['direccion']); ?></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="index.php?controller=cliente&action=show&id=<?php echo $cliente['id']; ?>" 
                                           class="btn btn-sm btn-outline-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="index.php?controller=cliente&action=edit&id=<?php echo $cliente['id']; ?>" 
                                           class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <a href="index.php?controller=cotizacion&action=create&cliente_id=<?php echo $cliente['id']; ?>" 
                                           class="btn btn-sm btn-outline-success" title="Nueva cotización">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </a>
                                        
                                        <a href="index.php?controller=cliente&action=delete&id=<?php echo $cliente['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" title="Eliminar"
                                           onclick="return confirmarEliminacion('¿Está seguro de eliminar este cliente?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer text-muted">
                <small>
                    <i class="fas fa-info-circle me-1"></i>
                    Total de clientes: <?php echo count($clientes); ?>
                </small>
            </div>
        <?php endif; ?>
    </div>
</div>