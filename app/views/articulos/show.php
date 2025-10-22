<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Variables inicializadas desde el controlador
$articulo = $articulo ?? [];
$historialCotizaciones = $historialCotizaciones ?? [];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 mb-1">
                        <i class="fas fa-box text-primary me-2"></i>
                        Detalles del Artículo
                    </h2>
                    <p class="text-muted mb-0">Información completa y estadísticas</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/mod_cotizacion/index.php?controller=articulo&action=edit&id=<?= $articulo['id'] ?>" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-edit me-1"></i>
                        Editar
                    </a>
                    <a href="/mod_cotizacion/index.php?controller=articulo&action=index" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Información del Artículo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="mb-3"><?= htmlspecialchars($articulo['nombre']) ?></h3>
                            
                            <?php if (!empty($articulo['descripcion'])): ?>
                                <div class="mb-4">
                                    <h6 class="text-muted">Descripción:</h6>
                                    <p class="mb-0"><?= nl2br(htmlspecialchars($articulo['descripcion'])) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 text-end">
                            <!-- Estado del stock -->
                            <?php 
                            $stock = $articulo['stock'] ?? 0;
                            if ($stock > 10) {
                                $stockClass = 'bg-success';
                                $stockText = 'Stock Óptimo';
                                $stockIcon = 'fas fa-check-circle';
                            } elseif ($stock > 0) {
                                $stockClass = 'bg-warning text-dark';
                                $stockText = 'Stock Bajo';
                                $stockIcon = 'fas fa-exclamation-triangle';
                            } else {
                                $stockClass = 'bg-danger';
                                $stockText = 'Sin Stock';
                                $stockIcon = 'fas fa-times-circle';
                            }
                            ?>
                            <div class="alert <?= $stockClass ?> text-center mb-3">
                                <i class="<?= $stockIcon ?> fa-2x mb-2"></i>
                                <div class="h4 mb-1"><?= $stock ?></div>
                                <div><?= $stockText ?></div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Información de Precios -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="fas fa-dollar-sign text-info fa-2x mb-2"></i>
                                <div class="h4 text-info mb-1">$<?= number_format($articulo['precio_costo'] ?? 0, 2) ?></div>
                                <div class="text-muted">Precio de Costo</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="fas fa-cubes text-primary fa-2x mb-2"></i>
                                <div class="h4 text-primary mb-1">$<?= number_format(($articulo['precio_costo'] ?? 0) * ($articulo['stock'] ?? 0), 2) ?></div>
                                <div class="text-muted">Valor Total en Inventario</div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Información Adicional -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="fas fa-calendar-alt me-2 text-muted"></i>
                                <strong>Fecha de Registro:</strong>
                                <?= date('d/m/Y H:i', strtotime($articulo['created_at'] ?? 'now')) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <i class="fas fa-hashtag me-2 text-muted"></i>
                                <strong>ID del Sistema:</strong>
                                #<?= str_pad($articulo['id'], 6, '0', STR_PAD_LEFT) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial en Cotizaciones -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Historial en Cotizaciones
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($historialCotizaciones)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-invoice text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">Sin Historial</h5>
                            <p class="text-muted">Este artículo aún no ha sido usado en cotizaciones.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historialCotizaciones as $item): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($item['fecha'])) ?></td>
                                            <td>
                                                <a href="/mod_cotizacion/index.php?controller=cliente&action=show&id=<?= $item['cliente_id'] ?>" 
                                                   class="text-decoration-none">
                                                    <?= htmlspecialchars($item['cliente_nombre']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?= $item['cantidad'] ?></span>
                                            </td>
                                            <td>$<?= number_format($item['precio_unitario'], 2) ?></td>
                                            <td>
                                                <strong class="text-success">
                                                    $<?= number_format($item['cantidad'] * $item['precio_unitario'], 2) ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php
                                                $estado = $item['estado'] ?? 'pendiente';
                                                $badgeClass = '';
                                                switch($estado) {
                                                    case 'aprobada': $badgeClass = 'bg-success'; break;
                                                    case 'rechazada': $badgeClass = 'bg-danger'; break;
                                                    case 'enviada': $badgeClass = 'bg-info'; break;
                                                    default: $badgeClass = 'bg-warning text-dark';
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?>">
                                                    <?= ucfirst($estado) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="col-lg-4">
            <!-- Acciones Rápidas -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/mod_cotizacion/index.php?controller=articulo&action=edit&id=<?= $articulo['id'] ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>
                            Editar Artículo
                        </a>
                        
                        <a href="/mod_cotizacion/index.php?controller=cotizacion&action=create&articulo_id=<?= $articulo['id'] ?>" 
                           class="btn btn-success">
                            <i class="fas fa-file-invoice-dollar me-1"></i>
                            Crear Cotización
                        </a>
                        
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#stockModal">
                            <i class="fas fa-boxes me-1"></i>
                            Actualizar Stock
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-dark text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estadísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-primary mb-1"><?= count($historialCotizaciones) ?></div>
                            <small class="text-muted">Cotizaciones</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success mb-1">
                                <?= array_sum(array_column($historialCotizaciones, 'cantidad')) ?>
                            </div>
                            <small class="text-muted">Unidades Cotizadas</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <div class="h5 text-info mb-1">
                            $<?= number_format(array_sum(array_map(fn($item) => $item['cantidad'] * $item['precio_unitario'], $historialCotizaciones)), 2) ?>
                        </div>
                        <small class="text-muted">Valor Total Cotizado</small>
                    </div>
                </div>
            </div>

            <!-- Alerta de Stock -->
            <?php if ($stock <= 5): ?>
                <div class="card shadow-sm mt-3 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Alerta de Stock
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Stock muy bajo:</strong> Solo quedan <?= $stock ?> unidades.
                        </p>
                        <small class="text-muted">
                            Considera reabastecer este artículo para evitar problemas en las cotizaciones.
                        </small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para actualizar stock -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-boxes me-2"></i>
                    Actualizar Stock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="stockForm" action="/mod_cotizacion/index.php?controller=articulo&action=updateStock" method="POST">
                    <input type="hidden" name="id" value="<?= $articulo['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="stock_actual" class="form-label">Stock Actual</label>
                        <input type="number" class="form-control" id="stock_actual" 
                               value="<?= $articulo['stock'] ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nuevo_stock" class="form-label">Nuevo Stock *</label>
                        <input type="number" class="form-control" id="nuevo_stock" name="nuevo_stock" 
                               min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo del Cambio</label>
                        <select class="form-select" id="motivo" name="motivo">
                            <option value="reabastecimiento">Reabastecimiento</option>
                            <option value="ajuste_inventario">Ajuste de Inventario</option>
                            <option value="devolucion">Devolución</option>
                            <option value="daño">Producto Dañado</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="stockForm" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>
                    Actualizar Stock
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.info-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .text-center.p-3 {
        margin-bottom: 1rem;
    }
}
</style>