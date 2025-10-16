<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

$cotizacion = $cotizacion ?? null;
$version = $version ?? null;

if (!$version) {
    echo '<div class="alert alert-danger">Error: No se encontró la versión</div>';
    return;
}
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>
                        <i class="fas fa-file-alt text-secondary me-2"></i>
                        Versión <?= $version['version'] ?> - Cotización #<?= $cotizacion['id'] ?>
                    </h2>
                    <p class="text-muted mb-0">
                        Vista de versión histórica (solo lectura)
                    </p>
                </div>
                <div>
                    <a href="index.php?controller=cotizacion&action=historial&id=<?= $cotizacion['id'] ?>" 
                       class="btn btn-outline-secondary me-2">
                        <i class="fas fa-history me-1"></i>Volver al Historial
                    </a>
                    <a href="index.php?controller=cotizacion&action=show&id=<?= $cotizacion['id'] ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-eye me-1"></i>Ver Versión Actual
                    </a>
                </div>
            </div>

            <!-- Alerta de versión histórica -->
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Nota:</strong> Esta es una versión histórica de la cotización. 
                Los datos mostrados corresponden al estado de la cotización en ese momento.
            </div>

            <!-- Información de la versión -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información de la Versión
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Versión:</strong><br>
                            <span class="badge bg-secondary fs-6">v<?= $version['version'] ?></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha de esta Versión:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($version['fecha_version'])) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Modificado Por:</strong><br>
                            <i class="fas fa-user me-1"></i>
                            <?= htmlspecialchars($version['usuario_modifico'] ?? 'N/A') ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha Modificación:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($version['fecha_modificacion'])) ?>
                        </div>
                    </div>
                    <?php if ($version['motivo_modificacion']): ?>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info mb-0">
                                <strong>Motivo de la modificación:</strong><br>
                                <?= nl2br(htmlspecialchars($version['motivo_modificacion'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Nombre:</strong> <?= htmlspecialchars($version['cliente']['nombre']) ?></p>
                            <p class="mb-2"><strong>Documento:</strong> <?= htmlspecialchars($version['cliente']['tipo_documento'] ?? 'CC') ?> - <?= htmlspecialchars($version['cliente']['documento']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Correo:</strong> <?= htmlspecialchars($version['cliente']['correo'] ?? 'N/A') ?></p>
                            <p class="mb-2"><strong>Teléfono:</strong> <?= htmlspecialchars($version['cliente']['telefono'] ?? 'N/A') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles de la cotización -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Items de la Cotización</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="35%">Artículo</th>
                                    <th width="15%" class="text-center">Cantidad</th>
                                    <th width="15%" class="text-end">Precio Costo</th>
                                    <th width="15%" class="text-end">Precio Venta</th>
                                    <th width="15%" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($version['detalles'])): ?>
                                    <?php foreach ($version['detalles'] as $index => $detalle): 
                                        $subtotal = $detalle['precio_venta'] * $detalle['cantidad'];
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($detalle['nombre']) ?></strong>
                                            <?php if (!empty($detalle['descripcion'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($detalle['descripcion']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?= $detalle['cantidad'] ?></span>
                                        </td>
                                        <td class="text-end text-success">
                                            $<?= number_format($detalle['precio_costo'], 0, ',', '.') ?>
                                        </td>
                                        <td class="text-end text-primary">
                                            $<?= number_format($detalle['precio_venta'], 0, ',', '.') ?>
                                        </td>
                                        <td class="text-end">
                                            <strong>$<?= number_format($subtotal, 0, ',', '.') ?></strong>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No hay detalles disponibles para esta versión
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end"><strong>TOTAL COSTO:</strong></td>
                                    <td class="text-end">
                                        <strong class="text-success">$<?= number_format($version['total_costo'], 0, ',', '.') ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>TOTAL VENTA:</strong></td>
                                    <td class="text-end">
                                        <strong class="text-primary">$<?= number_format($version['total_venta'], 0, ',', '.') ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>UTILIDAD:</strong></td>
                                    <td class="text-end">
                                        <strong class="text-info">$<?= number_format($version['utilidad'], 0, ',', '.') ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>UTILIDAD %:</strong></td>
                                    <td class="text-end">
                                        <strong class="text-warning"><?= number_format($version['utilidad_porcentaje'], 2) ?>%</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Comparación con versión actual -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>Comparación con Versión Actual
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="25%">Concepto</th>
                                    <th width="37.5%" class="text-center">Versión <?= $version['version'] ?> (Histórica)</th>
                                    <th width="37.5%" class="text-center">Versión <?= $cotizacion['version'] ?? 1 ?> (Actual)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Total Costo</strong></td>
                                    <td class="text-center">$<?= number_format($version['total_costo'], 0, ',', '.') ?></td>
                                    <td class="text-center">$<?= number_format($cotizacion['total_costo'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Venta</strong></td>
                                    <td class="text-center">$<?= number_format($version['total_venta'], 0, ',', '.') ?></td>
                                    <td class="text-center">$<?= number_format($cotizacion['total_venta'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Utilidad</strong></td>
                                    <td class="text-center">$<?= number_format($version['utilidad'], 0, ',', '.') ?></td>
                                    <td class="text-center">$<?= number_format($cotizacion['utilidad'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Diferencia</strong></td>
                                    <td colspan="2" class="text-center">
                                        <?php 
                                        $diferencia = $cotizacion['total_venta'] - $version['total_venta'];
                                        $clase = $diferencia > 0 ? 'text-success' : ($diferencia < 0 ? 'text-danger' : 'text-muted');
                                        $icono = $diferencia > 0 ? 'arrow-up' : ($diferencia < 0 ? 'arrow-down' : 'minus');
                                        ?>
                                        <span class="<?= $clase ?>">
                                            <i class="fas fa-<?= $icono ?> me-1"></i>
                                            $<?= number_format(abs($diferencia), 0, ',', '.') ?>
                                            (<?= $diferencia > 0 ? '+' : '' ?><?= number_format($diferencia, 0, ',', '.') ?>)
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.badge.fs-6 {
    font-size: 1.1rem !important;
}
</style>
