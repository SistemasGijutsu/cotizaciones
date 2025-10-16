<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

$cotizacion = $cotizacion ?? null;
$historial = $historial ?? [];

if (!$cotizacion) {
    echo '<div class="alert alert-danger">Error: No se encontró la cotizacion</div>';
    return;
}
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-history text-info me-2"></i>Historial de Versiones</h2>
                    <p class="text-muted mb-0">
                        Cotización #<?= $cotizacion['id'] ?> 
                        | Versión actual: <strong><?= $cotizacion['version'] ?? 1 ?></strong>
                    </p>
                </div>
                <a href="index.php?controller=cotizacion&action=show&id=<?= $cotizacion['id'] ?>" 
                   class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Volver a Cotización
                </a>
            </div>

            <!-- Información actual -->
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Versión Actual (v<?= $cotizacion['version'] ?? 1 ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Total Costo:</strong><br>
                            <span class="text-success fs-5">$<?= number_format($cotizacion['total_costo'], 0, ',', '.') ?></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Venta:</strong><br>
                            <span class="text-primary fs-5">$<?= number_format($cotizacion['total_venta'], 0, ',', '.') ?></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Utilidad:</strong><br>
                            <span class="text-info fs-5">$<?= number_format($cotizacion['utilidad'], 0, ',', '.') ?></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha Creación:</strong><br>
                            <span class="fs-6"><?= date('d/m/Y H:i', strtotime($cotizacion['fecha'])) ?></span>
                        </div>
                    </div>
                    <?php if ($cotizacion['fecha_modificacion']): ?>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Última modificación:</strong> 
                                <?= date('d/m/Y H:i', strtotime($cotizacion['fecha_modificacion'])) ?>
                                <?php if ($cotizacion['id_usuario_modifico']): ?>
                                    | <strong>Por:</strong> Usuario #<?= $cotizacion['id_usuario_modifico'] ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Historial de versiones -->
            <?php if (empty($historial)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Esta cotización aún no tiene versiones anteriores. El historial se crea cuando se edita la cotización.
                </div>
            <?php else: ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>Versiones Anteriores 
                            <span class="badge bg-light text-dark"><?= count($historial) ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="8%">Versión</th>
                                        <th width="15%">Fecha Versión</th>
                                        <th width="12%">Total Costo</th>
                                        <th width="12%">Total Venta</th>
                                        <th width="12%">Utilidad</th>
                                        <th width="15%">Modificado Por</th>
                                        <th width="15%">Fecha Modificación</th>
                                        <th width="11%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historial as $version): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">v<?= $version['version'] ?></span>
                                        </td>
                                        <td><?= $version['fecha_version_formato'] ?></td>
                                        <td class="text-success">
                                            $<?= number_format($version['total_costo'], 0, ',', '.') ?>
                                        </td>
                                        <td class="text-primary">
                                            $<?= number_format($version['total_venta'], 0, ',', '.') ?>
                                        </td>
                                        <td class="text-info">
                                            $<?= number_format($version['utilidad'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-user me-1"></i>
                                            <?= htmlspecialchars($version['usuario_modifico'] ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= $version['fecha_modificacion_formato'] ?>
                                        </td>
                                        <td>
                                            <a href="index.php?controller=cotizacion&action=verVersion&id=<?= $cotizacion['id'] ?>&version=<?= $version['version'] ?>" 
                                               class="btn btn-sm btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php if ($version['motivo_modificacion']): ?>
                                    <tr class="table-light">
                                        <td colspan="8" class="ps-5">
                                            <small>
                                                <i class="fas fa-comment-dots me-2 text-muted"></i>
                                                <strong>Motivo:</strong> 
                                                <?= htmlspecialchars($version['motivo_modificacion']) ?>
                                            </small>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Resumen de cambios -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>Resumen de Cambios
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="p-3 bg-light rounded">
                                    <h6 class="text-muted mb-2">Total de Versiones</h6>
                                    <h2 class="mb-0"><?= count($historial) + 1 ?></h2>
                                    <small class="text-muted">(incluyendo actual)</small>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="p-3 bg-light rounded">
                                    <h6 class="text-muted mb-2">Primera Versión</h6>
                                    <h5 class="mb-0">
                                        <?php 
                                        $primeraVersion = end($historial);
                                        if ($primeraVersion) {
                                            echo date('d/m/Y', strtotime($primeraVersion['fecha_version']));
                                        }
                                        ?>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="p-3 bg-light rounded">
                                    <h6 class="text-muted mb-2">Última Modificación</h6>
                                    <h5 class="mb-0">
                                        <?php 
                                        if ($cotizacion['fecha_modificacion']) {
                                            echo date('d/m/Y', strtotime($cotizacion['fecha_modificacion']));
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.table tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
