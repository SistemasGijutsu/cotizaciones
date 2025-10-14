<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Variables inicializadas desde el controlador
$paquete = $paquete ?? [];
$articulosPaquete = $articulosPaquete ?? [];
$precios = $precios ?? ['total_costo' => 0, 'total_venta' => 0, 'utilidad' => 0, 'utilidad_porcentaje' => 0];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 mb-1">
                        <i class="fas fa-box text-primary me-2"></i>
                        <?= htmlspecialchars($paquete['nombre']) ?>
                    </h2>
                    <p class="text-muted mb-0">Detalles del paquete de artículos</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/mod_cotizacion/index.php?controller=paquete&action=edit&id=<?= $paquete['id'] ?>" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <a href="/mod_cotizacion/index.php?controller=paquete&action=index" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Artículos Incluidos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Artículo</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($articulosPaquete as $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($item['nombre']) ?></strong>
                                            <?php if (!empty($item['descripcion'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars(substr($item['descripcion'], 0, 60)) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= $item['cantidad'] ?></span>
                                        </td>
                                        <td>$<?= number_format($item['precio_venta'], 2) ?></td>
                                        <td>
                                            <strong class="text-success">$<?= number_format($item['precio_venta'] * $item['cantidad'], 2) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $item['stock'] >= $item['cantidad'] ? 'success' : 'danger' ?>">
                                                <?= $item['stock'] ?> disponible
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Resumen de Precios</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="h2 text-success">${{ number_format($precios['total_venta'], 2) }}</div>
                        <small class="text-muted">Precio Total del Paquete</small>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Costo Total:</span>
                        <strong>${{ number_format($precios['total_costo'], 2) }}</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Utilidad:</span>
                        <strong class="text-success">${{ number_format($precios['utilidad'], 2) }}</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Margen:</span>
                        <strong class="text-info">{{ number_format($precios['utilidad_porcentaje'], 1) }}%</strong>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="/mod_cotizacion/index.php?controller=cotizacion&action=create&paquete_id=<?= $paquete['id'] ?>" 
                           class="btn btn-success">
                            <i class="fas fa-file-invoice-dollar me-1"></i>
                            Crear Cotización
                        </a>
                        <a href="/mod_cotizacion/index.php?controller=paquete&action=edit&id=<?= $paquete['id'] ?>" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-edit me-1"></i>
                            Editar Paquete
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">Información</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Creado:</strong> <?= date('d/m/Y', strtotime($paquete['created_at'])) ?>
                    </div>
                    <div class="mb-2">
                        <strong>Artículos:</strong> <?= count($articulosPaquete) ?> items
                    </div>
                    <?php if (!empty($paquete['descripcion'])): ?>
                        <div class="mb-2">
                            <strong>Descripción:</strong><br>
                            <small><?= nl2br(htmlspecialchars($paquete['descripcion'])) ?></small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>