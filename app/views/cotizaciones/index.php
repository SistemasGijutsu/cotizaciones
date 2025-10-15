<?php
$title = 'Gestión de Cotizaciones';
include_once 'app/views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary">
        <i class="fas fa-file-invoice-dollar me-2"></i>
        Gestión de Cotizaciones
    </h2>
    <a href="index.php?controller=cotizacion&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Cotización
    </a>
</div>

<!-- Filtros de búsqueda -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-2"></i>
        Filtros de Búsqueda
    </div>
    <div class="card-body">
        <form method="GET" action="index.php">
            <input type="hidden" name="controller" value="cotizacion">
            <input type="hidden" name="action" value="index">
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select class="form-select" id="cliente_id" name="cliente_id">
                        <option value="">Todos los clientes</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?php echo $cliente['id']; ?>" 
                                    <?php echo ($filtros['cliente_id'] == $cliente['id']) ? 'selected' : ''; ?>>
                                <?php echo $cliente['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="fecha_desde" class="form-label">Fecha desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                           value="<?php echo $filtros['fecha_desde']; ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="fecha_hasta" class="form-label">Fecha hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                           value="<?php echo $filtros['fecha_hasta']; ?>">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="index.php?controller=cotizacion" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de cotizaciones -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>
            Lista de Cotizaciones
        </h5>
        <span class="badge bg-info"><?php echo count($cotizaciones); ?> cotizaciones</span>
    </div>
    <div class="card-body">
        <?php if (empty($cotizaciones)): ?>
            <div class="text-center py-5">
                <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No hay cotizaciones registradas</h5>
                <p class="text-muted">Comience creando su primera cotización</p>
                <a href="index.php?controller=cotizacion&action=create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Primera Cotización
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Vencimiento</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cotizaciones as $cotizacion): ?>
                            <tr>
                                <td>
                                    <strong class="text-primary">#<?php echo str_pad($cotizacion['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($cotizacion['cliente_nombre'] ?? 'N/A'); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($cotizacion['cliente_correo'] ?? 'Sin email'); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($cotizacion['fecha'])); ?>
                                </td>
                                <td>
                                    <span class="text-muted">-</span>
                                </td>
                                <td>
                                    <strong class="text-success">
                                        $<?php echo number_format($cotizacion['total_venta'] ?? 0, 0); ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge bg-success">Activa</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="index.php?controller=cotizacion&action=show&id=<?php echo $cotizacion['id']; ?>" 
                                           class="btn btn-outline-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="index.php?controller=cotizacion&action=generarPDF&id=<?php echo $cotizacion['id']; ?>" 
                                           class="btn btn-outline-danger" title="Generar PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        
                                        <a href="index.php?controller=cotizacion&action=enviarEmail&id=<?php echo $cotizacion['id']; ?>" 
                                           class="btn btn-outline-warning" title="Enviar por email">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                        
                                        <a href="index.php?controller=cotizacion&action=edit&id=<?php echo $cotizacion['id']; ?>" 
                                           class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <a href="index.php?controller=cotizacion&action=delete&id=<?php echo $cotizacion['id']; ?>" 
                                           class="btn btn-outline-danger" title="Eliminar"
                                           onclick="return confirmarEliminacion('¿Está seguro de eliminar esta cotización?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-number"><?php echo count($cotizaciones); ?></div>
            <div class="stats-label">Total Cotizaciones</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-number">
                <?php 
                $totalVenta = array_sum(array_column($cotizaciones, 'total_venta'));
                echo '$' . number_format($totalVenta, 0);
                ?>
            </div>
            <div class="stats-label">Ventas Totales</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-number">
                <?php 
                $totalUtilidad = array_sum(array_column($cotizaciones, 'utilidad'));
                echo '$' . number_format($totalUtilidad, 0);
                ?>
            </div>
            <div class="stats-label">Utilidad Total</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number">
                <?php 
                $totalCosto = array_sum(array_column($cotizaciones, 'total_costo'));
                echo '$' . number_format($totalCosto, 0);
                ?>
            </div>
            <div class="stats-label">Costos Totales</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-actualizar cada 5 minutos
    setInterval(function() {
        if (document.hidden) return; // No actualizar si la pestaña no está visible
        
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok && !response.headers.get('content-type')?.includes('application/json')) {
                // Solo mostrar notificación si hay cambios
                console.log('Datos actualizados automáticamente');
            }
        })
        .catch(error => console.log('Error en actualización automática:', error));
    }, 300000); // 5 minutos
    
    // Mejorar UX en móviles
    if (window.innerWidth <= 768) {
        // Hacer las tarjetas de estadísticas más compactas
        document.querySelectorAll('.stats-card').forEach(card => {
            card.style.padding = '1rem';
            const number = card.querySelector('.stats-number');
            const label = card.querySelector('.stats-label');
            if (number) number.style.fontSize = '1.5rem';
            if (label) label.style.fontSize = '0.8rem';
        });
    }
});
</script>

<?php include_once 'app/views/layouts/footer.php'; ?>