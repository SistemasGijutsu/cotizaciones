<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

$estadisticasGenerales = $estadisticasGenerales ?? [];
$resumenClientes = $resumenClientes ?? [];
$cotizacionesPorPeriodo = $cotizacionesPorPeriodo ?? [];
$topClientes = $topClientes ?? [];
$articulosMasCotizados = $articulosMasCotizados ?? [];
$clientesNuevos = $clientesNuevos ?? [];
$clientesRecurrentes = $clientesRecurrentes ?? [];
$datosGrafico = $datosGrafico ?? ['labels' => [], 'valores' => []];

$tipoFiltro = $tipoFiltro ?? 'mes';
$fechaInicio = $fechaInicio ?? date('Y-m-01');
$fechaFin = $fechaFin ?? date('Y-m-t');
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-chart-line text-primary me-2"></i>Reportes y Estadísticas</h2>
                    <p class="text-muted mb-0">Dashboard de análisis del negocio</p>
                </div>
                <div>
                    <a href="index.php?controller=reporte&action=exportarCSV&tipo=general&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>" 
                       class="btn btn-success">
                        <i class="fas fa-file-excel me-1"></i>Exportar CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Período</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php">
                <input type="hidden" name="controller" value="reporte">
                <input type="hidden" name="action" value="index">
                
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Período</label>
                        <select name="tipo" class="form-select" id="tipoFiltro">
                            <option value="dia" <?= $tipoFiltro === 'dia' ? 'selected' : '' ?>>Por Día</option>
                            <option value="mes" <?= $tipoFiltro === 'mes' ? 'selected' : '' ?>>Por Mes</option>
                            <option value="año" <?= $tipoFiltro === 'año' ? 'selected' : '' ?>>Por Año</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?= $fechaInicio ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" value="<?= $fechaFin ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                    </div>
                </div>
                
                <!-- Botones rápidos -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="filtrarHoy()">Hoy</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="filtrarSemana()">Esta Semana</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="filtrarMes()">Este Mes</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="filtrarAño()">Este Año</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Período seleccionado -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Período seleccionado:</strong> 
        <?= date('d/m/Y', strtotime($fechaInicio)) ?> - <?= date('d/m/Y', strtotime($fechaFin)) ?>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row mb-4">
        <!-- Total Clientes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Clientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($resumenClientes['total'] ?? 0) ?>
                            </div>
                            <small class="text-muted">
                                <?= $resumenClientes['activos'] ?? 0 ?> activos en período
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cotizaciones Creadas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Cotizaciones Creadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($estadisticasGenerales['total_cotizaciones'] ?? 0) ?>
                            </div>
                            <small class="text-muted">
                                Promedio: $<?= number_format($estadisticasGenerales['promedio_venta'] ?? 0, 0) ?>
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clientes Nuevos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Clientes Nuevos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($resumenClientes['nuevos'] ?? 0) ?>
                            </div>
                            <small class="text-muted">
                                Primera cotización en período
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clientes Recurrentes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Clientes Recurrentes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($resumenClientes['recurrentes'] ?? 0) ?>
                            </div>
                            <small class="text-muted">
                                2 o más cotizaciones
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-redo fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Ventas -->
    <div class="row mb-4">
        <!-- Total Ventas -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Ventas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($estadisticasGenerales['total_ventas'] ?? 0, 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Costos -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Costos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($estadisticasGenerales['total_costos'] ?? 0, 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Utilidad -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Utilidad Neta
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($estadisticasGenerales['total_utilidad'] ?? 0, 0) ?>
                            </div>
                            <?php 
                            $porcentajeUtilidad = 0;
                            if (($estadisticasGenerales['total_costos'] ?? 0) > 0) {
                                $porcentajeUtilidad = (($estadisticasGenerales['total_utilidad'] ?? 0) / $estadisticasGenerales['total_costos']) * 100;
                            }
                            ?>
                            <small class="text-muted">
                                <?= number_format($porcentajeUtilidad, 2) ?>% de margen
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <!-- Gráfico de Ventas -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Evolución de Ventas</h5>
                </div>
                <div class="card-body">
                    <canvas id="ventasChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Distribución de Clientes -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Distribución de Clientes</h5>
                </div>
                <div class="card-body">
                    <canvas id="clientesChart"></canvas>
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            Total: <?= $resumenClientes['total'] ?? 0 ?> clientes registrados
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Clientes y Artículos -->
    <div class="row mb-4">
        <!-- Top 10 Clientes -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 10 Clientes</h5>
                        <a href="index.php?controller=reporte&action=exportarCSV&tipo=top_clientes&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>" 
                           class="btn btn-sm btn-dark">
                            <i class="fas fa-download me-1"></i>Exportar
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th class="text-center">Cotizaciones</th>
                                    <th class="text-end">Total Comprado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topClientes)): ?>
                                    <?php foreach ($topClientes as $index => $cliente): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($cliente['nombre']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($cliente['documento']) ?></small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?= $cliente['total_cotizaciones'] ?></span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">$<?= number_format($cliente['total_comprado'], 0) ?></strong>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No hay datos en este período
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Artículos Más Cotizados -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-box me-2"></i>Artículos Más Cotizados</h5>
                        <a href="index.php?controller=reporte&action=exportarCSV&tipo=articulos&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>" 
                           class="btn btn-sm btn-light">
                            <i class="fas fa-download me-1"></i>Exportar
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Artículo</th>
                                    <th class="text-center">Veces</th>
                                    <th class="text-end">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($articulosMasCotizados)): ?>
                                    <?php foreach ($articulosMasCotizados as $index => $articulo): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($articulo['nombre']) ?></strong>
                                            <?php if (!empty($articulo['descripcion'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars(substr($articulo['descripcion'], 0, 30)) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info"><?= $articulo['veces_cotizado'] ?></span>
                                        </td>
                                        <td class="text-end">
                                            <strong><?= number_format($articulo['cantidad_total']) ?></strong>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No hay datos en este período
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clientes Nuevos y Recurrentes -->
    <div class="row mb-4">
        <!-- Clientes Nuevos -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Clientes Nuevos</h5>
                        <a href="index.php?controller=reporte&action=exportarCSV&tipo=clientes_nuevos&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>" 
                           class="btn btn-sm btn-light">
                            <i class="fas fa-download me-1"></i>Exportar
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Primera Cotización</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($clientesNuevos)): ?>
                                    <?php foreach ($clientesNuevos as $cliente): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($cliente['nombre']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($cliente['documento']) ?></small>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($cliente['primera_cotizacion'])) ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?= $cliente['total_cotizaciones'] ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No hay clientes nuevos en este período
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clientes Recurrentes -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-redo me-2"></i>Clientes Recurrentes</h5>
                        <a href="index.php?controller=reporte&action=exportarCSV&tipo=clientes_recurrentes&fecha_inicio=<?= $fechaInicio ?>&fecha_fin=<?= $fechaFin ?>" 
                           class="btn btn-sm btn-dark">
                            <i class="fas fa-download me-1"></i>Exportar
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Cliente</th>
                                    <th class="text-center">Cotizaciones</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($clientesRecurrentes)): ?>
                                    <?php foreach ($clientesRecurrentes as $cliente): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($cliente['nombre']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($cliente['documento']) ?></small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><?= $cliente['total_cotizaciones'] ?></span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">$<?= number_format($cliente['total_comprado'], 0) ?></strong>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No hay clientes recurrentes en este período
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
// Datos para los gráficos
const datosGrafico = <?= json_encode($datosGrafico) ?>;
const resumenClientes = <?= json_encode($resumenClientes) ?>;

// Gráfico de Ventas
const ctxVentas = document.getElementById('ventasChart').getContext('2d');
const ventasChart = new Chart(ctxVentas, {
    type: 'line',
    data: {
        labels: datosGrafico.labels,
        datasets: [{
            label: 'Ventas ($)',
            data: datosGrafico.valores,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += '$' + new Intl.NumberFormat('es-CO').format(context.parsed.y);
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + new Intl.NumberFormat('es-CO').format(value);
                    }
                }
            }
        }
    }
});

// Gráfico de Clientes (Pie)
const ctxClientes = document.getElementById('clientesChart').getContext('2d');
const clientesChart = new Chart(ctxClientes, {
    type: 'doughnut',
    data: {
        labels: ['Activos', 'Nuevos', 'Recurrentes', 'Inactivos'],
        datasets: [{
            data: [
                resumenClientes.activos || 0,
                resumenClientes.nuevos || 0,
                resumenClientes.recurrentes || 0,
                resumenClientes.inactivos || 0
            ],
            backgroundColor: [
                'rgba(54, 162, 235, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(201, 203, 207, 0.8)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(201, 203, 207, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Funciones de filtros rápidos
function filtrarHoy() {
    const hoy = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="fecha_inicio"]').value = hoy;
    document.querySelector('input[name="fecha_fin"]').value = hoy;
    document.querySelector('#tipoFiltro').value = 'dia';
    document.querySelector('form').submit();
}

function filtrarSemana() {
    const hoy = new Date();
    const inicioSemana = new Date(hoy.setDate(hoy.getDate() - hoy.getDay()));
    const finSemana = new Date(hoy.setDate(hoy.getDate() - hoy.getDay() + 6));
    
    document.querySelector('input[name="fecha_inicio"]').value = inicioSemana.toISOString().split('T')[0];
    document.querySelector('input[name="fecha_fin"]').value = finSemana.toISOString().split('T')[0];
    document.querySelector('#tipoFiltro').value = 'dia';
    document.querySelector('form').submit();
}

function filtrarMes() {
    const hoy = new Date();
    const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    const finMes = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
    
    document.querySelector('input[name="fecha_inicio"]').value = inicioMes.toISOString().split('T')[0];
    document.querySelector('input[name="fecha_fin"]').value = finMes.toISOString().split('T')[0];
    document.querySelector('#tipoFiltro').value = 'mes';
    document.querySelector('form').submit();
}

function filtrarAño() {
    const hoy = new Date();
    const inicioAño = new Date(hoy.getFullYear(), 0, 1);
    const finAño = new Date(hoy.getFullYear(), 11, 31);
    
    document.querySelector('input[name="fecha_inicio"]').value = inicioAño.toISOString().split('T')[0];
    document.querySelector('input[name="fecha_fin"]').value = finAño.toISOString().split('T')[0];
    document.querySelector('#tipoFiltro').value = 'año';
    document.querySelector('form').submit();
}
</script>

<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
.border-left-danger {
    border-left: 4px solid #e74a3b !important;
}
.text-xs {
    font-size: 0.7rem;
}
</style>
