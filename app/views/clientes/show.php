<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Variables inicializadas desde el controlador
$cliente = $cliente ?? [];
$historial = $historial ?? [];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 mb-1">
                        <i class="fas fa-user text-primary me-2"></i>
                        Detalles del Cliente
                    </h2>
                    <p class="text-muted mb-0">Información completa y historial de cotizaciones</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/mod_cotizacion/index.php?controller=cliente&action=edit&id=<?= $cliente['id'] ?>" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-edit me-1"></i>
                        Editar
                    </a>
                    <a href="/mod_cotizacion/index.php?controller=cliente&action=index" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información del Cliente -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-id-card me-2"></i>
                        Información Personal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4 class="mt-3 mb-1"><?= htmlspecialchars($cliente['nombre']) ?></h4>
                        <?php if (!empty($cliente['empresa'])): ?>
                            <p class="text-muted mb-0">
                                <i class="fas fa-building me-1"></i>
                                <?= htmlspecialchars($cliente['empresa']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <div class="info-group">
                        <?php if (!empty($cliente['documento'])): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">
                                    <i class="fas fa-id-badge me-2"></i>
                                    <?php
                                    $tipoDoc = '';
                                    switch($cliente['tipo_documento']) {
                                        case 'nit': $tipoDoc = 'NIT'; break;
                                        case 'cedula': $tipoDoc = 'Cédula'; break;
                                        case 'pasaporte': $tipoDoc = 'Pasaporte'; break;
                                        default: $tipoDoc = 'Documento';
                                    }
                                    echo $tipoDoc;
                                    ?>
                                </span>
                                <strong><?= htmlspecialchars($cliente['documento']) ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($cliente['correo'])): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">
                                    <i class="fas fa-envelope me-2"></i>
                                    Correo
                                </span>
                                <a href="mailto:<?= htmlspecialchars($cliente['correo']) ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($cliente['correo']) ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($cliente['telefono'])): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">
                                    <i class="fas fa-phone me-2"></i>
                                    Teléfono
                                </span>
                                <a href="tel:<?= htmlspecialchars($cliente['telefono']) ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($cliente['telefono']) ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($cliente['direccion'])): ?>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="text-muted">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Dirección
                                </span>
                                <span class="text-end"><?= htmlspecialchars($cliente['direccion']) ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">
                                <i class="fas fa-calendar me-2"></i>
                                Registrado
                            </span>
                            <span><?= date('d/m/Y', strtotime($cliente['created_at'])) ?></span>
                        </div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="/mod_cotizacion/index.php?controller=cotizacion&action=create&cliente_id=<?= $cliente['id'] ?>" 
                           class="btn btn-success">
                            <i class="fas fa-file-invoice-dollar me-1"></i>
                            Nueva Cotización
                        </a>
                        
                        <a href="/mod_cotizacion/index.php?controller=cliente&action=edit&id=<?= $cliente['id'] ?>" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-edit me-1"></i>
                            Editar Cliente
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Cotizaciones -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Historial de Cotizaciones
                    </h5>
                    <span class="badge bg-light text-dark">
                        <?= count($historial) ?> cotizaciones
                    </span>
                </div>
                <div class="card-body">
                    <?php if (empty($historial)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">Sin cotizaciones</h5>
                            <p class="text-muted">Este cliente aún no tiene cotizaciones registradas.</p>
                            <a href="/mod_cotizacion/index.php?controller=cotizacion&action=create&cliente_id=<?= $cliente['id'] ?>" 
                               class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                Crear Primera Cotización
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historial as $cotizacion): ?>
                                        <tr>
                                            <td>
                                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                                <?= $cotizacion['fecha_formato'] ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= $cotizacion['total_items'] ?> items
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    $<?= number_format($cotizacion['total'] ?? 0, 2) ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php
                                                $estado = $cotizacion['estado'] ?? 'pendiente';
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
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/mod_cotizacion/index.php?controller=cotizacion&action=show&id=<?= $cotizacion['id'] ?>" 
                                                       class="btn btn-outline-primary" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="/mod_cotizacion/index.php?controller=cotizacion&action=generarPDF&id=<?= $cotizacion['id'] ?>" 
                                                       class="btn btn-outline-danger" title="Descargar PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                    <a href="/mod_cotizacion/index.php?controller=cotizacion&action=enviarEmail&id=<?= $cotizacion['id'] ?>" 
                                                       class="btn btn-outline-info" title="Enviar por email">
                                                        <i class="fas fa-envelope"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Estadísticas rápidas -->
                        <div class="row mt-4">
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="h4 text-primary mb-1"><?= count($historial) ?></div>
                                    <small class="text-muted">Total Cotizaciones</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="h4 text-success mb-1">
                                        $<?= number_format(array_sum(array_column($historial, 'total')), 2) ?>
                                    </div>
                                    <small class="text-muted">Valor Total</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="h4 text-info mb-1">
                                        <?= count(array_filter($historial, fn($c) => ($c['estado'] ?? 'pendiente') === 'aprobada')) ?>
                                    </div>
                                    <small class="text-muted">Aprobadas</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="h4 text-warning mb-1">
                                        <?= count(array_filter($historial, fn($c) => ($c['estado'] ?? 'pendiente') === 'pendiente')) ?>
                                    </div>
                                    <small class="text-muted">Pendientes</small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-group .d-flex {
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 0.5rem;
}

.info-group .d-flex:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>