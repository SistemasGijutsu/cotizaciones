<?php $title = 'Dashboard'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-tachometer-alt me-2"></i>
        Dashboard
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="index.php?controller=cotizacion&action=create" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Nueva Cotización
            </a>
        </div>
    </div>
</div>

<!-- Estadísticas principales -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number"><?php echo $totalClientes; ?></div>
            <div class="stats-label">
                <i class="fas fa-users me-1"></i>
                Total Clientes
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-number"><?php echo $totalArticulos; ?></div>
            <div class="stats-label">
                <i class="fas fa-box me-1"></i>
                Total Artículos
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-number"><?php echo $estadisticasCotizaciones['total_cotizaciones'] ?? 0; ?></div>
            <div class="stats-label">
                <i class="fas fa-file-invoice-dollar me-1"></i>
                Cotizaciones (30 días)
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-number"><?php echo Helper::formatCurrency($estadisticasCotizaciones['total_utilidad'] ?? 0); ?></div>
            <div class="stats-label">
                <i class="fas fa-chart-line me-1"></i>
                Utilidad Total
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Cotizaciones Recientes -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Cotizaciones Recientes
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($cotizacionesRecientes)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No hay cotizaciones registradas</p>
                        <a href="index.php?controller=cotizacion&action=create" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Crear Primera Cotización
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Total Venta</th>
                                    <th>Utilidad</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cotizacionesRecientes as $cotizacion): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo str_pad($cotizacion['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                                        </td>
                                        <td><?php echo $cotizacion['cliente_nombre']; ?></td>
                                        <td><?php echo $cotizacion['fecha_formato']; ?></td>
                                        <td>
                                            <span class="badge bg-success">
                                                <?php echo Helper::formatCurrency($cotizacion['total_venta']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo Helper::formatCurrency($cotizacion['utilidad']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="index.php?controller=cotizacion&action=show&id=<?php echo $cotizacion['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center">
                        <a href="index.php?controller=cotizacion&action=index" class="btn btn-link">
                            Ver todas las cotizaciones <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Artículos Más Cotizados -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-star me-2"></i>
                    Más Cotizados
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($articulosMasCotizados)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-box-open fa-2x mb-2"></i>
                        <p class="small">No hay datos disponibles</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($articulosMasCotizados as $articulo): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-1"><?php echo $articulo['nombre']; ?></h6>
                                    <small class="text-muted">
                                        <?php echo Helper::formatCurrency($articulo['precio_venta']); ?>
                                    </small>
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo $articulo['total_cotizaciones'] ?? 0; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="index.php?controller=articulo&action=estadisticas" class="btn btn-link btn-sm">
                            Ver estadísticas completas
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Accesos Rápidos -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-rocket me-2"></i>
                    Accesos Rápidos
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <a href="index.php?controller=cliente&action=create" class="btn btn-outline-primary btn-lg w-100">
                            <i class="fas fa-user-plus fa-2x d-block mb-2"></i>
                            Nuevo Cliente
                        </a>
                    </div>
                    
                    <div class="col-md-3 text-center mb-3">
                        <a href="index.php?controller=articulo&action=create" class="btn btn-outline-success btn-lg w-100">
                            <i class="fas fa-plus-square fa-2x d-block mb-2"></i>
                            Nuevo Artículo
                        </a>
                    </div>
                    
                    <div class="col-md-3 text-center mb-3">
                        <a href="index.php?controller=paquete&action=create" class="btn btn-outline-warning btn-lg w-100">
                            <i class="fas fa-layer-group fa-2x d-block mb-2"></i>
                            Nuevo Paquete
                        </a>
                    </div>
                    
                    <div class="col-md-3 text-center mb-3">
                        <a href="index.php?controller=cotizacion&action=create" class="btn btn-outline-info btn-lg w-100">
                            <i class="fas fa-file-invoice-dollar fa-2x d-block mb-2"></i>
                            Nueva Cotización
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>