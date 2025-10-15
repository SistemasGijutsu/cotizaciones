<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Variables inicializadas desde el controlador
$cotizacion = $cotizacion ?? [];
$cliente = $cotizacion['cliente'] ?? [];
$detalles = $cotizacion['detalles'] ?? [];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 mb-1">
                        <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                        Cotización #<?= str_pad($cotizacion['id'], 6, '0', STR_PAD_LEFT) ?>
                    </h2>
                    <p class="text-muted mb-0">Detalles completos de la cotización</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/mod_cotizacion/index.php?controller=cotizacion&action=generarPDF&id=<?= $cotizacion['id'] ?>" 
                       class="btn btn-outline-danger">
                        <i class="fas fa-file-pdf me-1"></i>PDF
                    </a>
                    <a href="/mod_cotizacion/index.php?controller=cotizacion&action=enviarEmail&id=<?= $cotizacion['id'] ?>" 
                       class="btn btn-outline-info">
                        <i class="fas fa-envelope me-1"></i>Email
                    </a>
                    <a href="/mod_cotizacion/index.php?controller=cotizacion&action=index" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Información del Cliente -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><?= htmlspecialchars($cliente['nombre']) ?></h5>
                            <?php if (!empty($cliente['empresa'])): ?>
                                <p class="text-muted mb-1"><?= htmlspecialchars($cliente['empresa']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($cliente['documento'])): ?>
                                <p class="mb-1">
                                    <strong><?= $cliente['tipo_documento'] === 'nit' ? 'NIT' : 'CC' ?>:</strong> 
                                    <?= htmlspecialchars($cliente['documento']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <?php if (!empty($cliente['correo'])): ?>
                                <p class="mb-1">
                                    <i class="fas fa-envelope me-2"></i>
                                    <?= htmlspecialchars($cliente['correo']) ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($cliente['telefono'])): ?>
                                <p class="mb-1">
                                    <i class="fas fa-phone me-2"></i>
                                    <?= htmlspecialchars($cliente['telefono']) ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($cliente['direccion'])): ?>
                                <p class="mb-1">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <?= htmlspecialchars($cliente['direccion']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles de la Cotización -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Items Cotizados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Descripción</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles as $detalle): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($detalle['nombre']) ?></strong>
                                            <?php if (!empty($detalle['descripcion'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($detalle['descripcion']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= $detalle['cantidad'] ?></span>
                                        </td>
                                        <td>$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                                        <td>
                                            <strong class="text-success">
                                                $<?= number_format($detalle['cantidad'] * $detalle['precio_unitario'], 2) ?>
                                            </strong>
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
            <!-- Resumen de Totales -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Resumen</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="h2 text-success">${{ number_format($cotizacion['total_venta'] ?? 0, 2) }}</div>
                        <small class="text-muted">Total de la Cotización</small>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Costo Total:</span>
                        <strong>${{ number_format($cotizacion['total_costo'] ?? 0, 2) }}</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Precio Venta:</span>
                        <strong class="text-success">${{ number_format($cotizacion['total_venta'] ?? 0, 2) }}</strong>
                    </div>
                    
                    <?php if (($cotizacion['utilidad'] ?? 0) > 0): ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Utilidad:</span>
                            <strong class="text-info">${{ number_format($cotizacion['utilidad'], 2) }}</strong>
                        </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <a href="/mod_cotizacion/index.php?controller=cotizacion&action=generarPDF&id=<?= $cotizacion['id'] ?>" 
                           class="btn btn-danger">
                            <i class="fas fa-file-pdf me-1"></i>
                            Descargar PDF
                        </a>
                        <a href="/mod_cotizacion/index.php?controller=cotizacion&action=enviarEmail&id=<?= $cotizacion['id'] ?>" 
                           class="btn btn-info">
                            <i class="fas fa-envelope me-1"></i>
                            Enviar por Email
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-dark text-white">
                    <h6 class="card-title mb-0">Información</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Fecha:</strong> <?= date('d/m/Y', strtotime($cotizacion['created_at'])) ?>
                    </div>
                    <div class="mb-2">
                        <strong>Estado:</strong>
                        <?php
                        $estado = $cotizacion['estado'] ?? 'pendiente';
                        $badgeClass = match($estado) {
                            'aprobada' => 'bg-success',
                            'rechazada' => 'bg-danger',
                            'enviada' => 'bg-info',
                            default => 'bg-warning text-dark'
                        };
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= ucfirst($estado) ?>
                        </span>
                    </div>
                    <?php if (!empty($cotizacion['fecha_vencimiento'])): ?>
                        <div class="mb-2">
                            <strong>Vencimiento:</strong> 
                            <?= date('d/m/Y', strtotime($cotizacion['fecha_vencimiento'])) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($cotizacion['observaciones'])): ?>
                        <div class="mb-2">
                            <strong>Observaciones:</strong><br>
                            <small><?= nl2br(htmlspecialchars($cotizacion['observaciones'])) ?></small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>