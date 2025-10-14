<?php
$title = 'Gestión de Paquetes';
include_once 'app/views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary">
        <i class="fas fa-boxes me-2"></i>
        Gestión de Paquetes
    </h2>
    <a href="index.php?controller=paquete&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo Paquete
    </a>
</div>

<!-- Barra de búsqueda -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-search me-2"></i>
        Búsqueda y Filtros
    </div>
    <div class="card-body">
        <form method="GET" action="index.php">
            <input type="hidden" name="controller" value="paquete">
            <input type="hidden" name="action" value="index">
            
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="buscar" class="form-label">Buscar paquete</label>
                    <input type="text" class="form-control" id="buscar" name="buscar" 
                           placeholder="Nombre o descripción del paquete..." 
                           value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="index.php?controller=paquete" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de paquetes -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>
            Lista de Paquetes
        </h5>
        <span class="badge bg-info"><?php echo isset($paquetes) ? count($paquetes) : 0; ?> paquetes</span>
    </div>
    <div class="card-body">
        <?php if (empty($paquetes)): ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No hay paquetes registrados</h5>
                <p class="text-muted">Comience creando su primer paquete de productos</p>
                <a href="index.php?controller=paquete&action=create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Primer Paquete
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($paquetes as $paquete): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 hover-shadow">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-box me-1"></i>
                                    <?php echo $paquete['nombre']; ?>
                                </h6>
                                <span class="badge bg-secondary">
                                    <?php echo $paquete['total_articulos'] ?? 0; ?> items
                                </span>
                            </div>
                            
                            <div class="card-body">
                                <p class="card-text text-muted small">
                                    <?php 
                                    echo strlen($paquete['descripcion']) > 100 ? 
                                         substr($paquete['descripcion'], 0, 100) . '...' : 
                                         $paquete['descripcion']; 
                                    ?>
                                </p>
                                
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <small class="text-muted">Precio Costo</small>
                                            <div class="h6 text-danger">
                                                <?php echo '$' . number_format($paquete['precio_costo_total'] ?? 0, 0); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Precio Venta</small>
                                        <div class="h6 text-success">
                                            <?php echo '$' . number_format($paquete['precio_venta_total'] ?? 0, 0); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if (isset($paquete['precio_costo_total']) && isset($paquete['precio_venta_total'])): ?>
                                    <?php 
                                    $utilidad = $paquete['precio_venta_total'] - $paquete['precio_costo_total'];
                                    $utilidadPorcentaje = $paquete['precio_costo_total'] > 0 ? 
                                                        ($utilidad / $paquete['precio_costo_total']) * 100 : 0;
                                    ?>
                                    <div class="text-center mt-2">
                                        <small class="text-muted">Utilidad:</small>
                                        <div class="<?php echo $utilidad > 0 ? 'text-success' : 'text-danger'; ?>">
                                            <strong><?php echo '$' . number_format($utilidad, 0); ?></strong>
                                            <small>(<?php echo number_format($utilidadPorcentaje, 1); ?>%)</small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-footer bg-light">
                                <div class="btn-group w-100" role="group">
                                    <a href="index.php?controller=paquete&action=view&id=<?php echo $paquete['id']; ?>" 
                                       class="btn btn-outline-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="index.php?controller=cotizacion&action=create&paquete_id=<?php echo $paquete['id']; ?>" 
                                       class="btn btn-outline-success btn-sm" title="Cotizar">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </a>
                                    
                                    <a href="index.php?controller=paquete&action=edit&id=<?php echo $paquete['id']; ?>" 
                                       class="btn btn-outline-primary btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <a href="index.php?controller=paquete&action=delete&id=<?php echo $paquete['id']; ?>" 
                                       class="btn btn-outline-danger btn-sm" title="Eliminar"
                                       onclick="return confirmarEliminacion('¿Está seguro de eliminar este paquete?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number"><?php echo isset($paquetes) ? count($paquetes) : 0; ?></div>
            <div class="stats-label">Total Paquetes</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-number">
                <?php 
                $valorPaquetes = 0;
                if (isset($paquetes)) {
                    foreach ($paquetes as $paquete) {
                        $valorPaquetes += $paquete['precio_venta_total'] ?? 0;
                    }
                }
                echo '$' . number_format($valorPaquetes / 1000, 0) . 'K';
                ?>
            </div>
            <div class="stats-label">Valor Total Paquetes</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-number">
                <?php 
                $articulosPorPaquete = 0;
                if (isset($paquetes) && count($paquetes) > 0) {
                    $totalArticulos = array_sum(array_column($paquetes, 'total_articulos'));
                    $articulosPorPaquete = $totalArticulos / count($paquetes);
                }
                echo number_format($articulosPorPaquete, 1);
                ?>
            </div>
            <div class="stats-label">Items por Paquete</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-number">
                <?php 
                $utilidadPromedio = 0;
                if (isset($paquetes) && count($paquetes) > 0) {
                    $totalUtilidad = 0;
                    $paquetesConUtilidad = 0;
                    
                    foreach ($paquetes as $paquete) {
                        if (isset($paquete['precio_costo_total']) && $paquete['precio_costo_total'] > 0) {
                            $utilidad = (($paquete['precio_venta_total'] - $paquete['precio_costo_total']) / $paquete['precio_costo_total']) * 100;
                            $totalUtilidad += $utilidad;
                            $paquetesConUtilidad++;
                        }
                    }
                    
                    if ($paquetesConUtilidad > 0) {
                        $utilidadPromedio = $totalUtilidad / $paquetesConUtilidad;
                    }
                }
                echo number_format($utilidadPromedio, 1) . '%';
                ?>
            </div>
            <div class="stats-label">Utilidad Promedio</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animación de entrada para las tarjetas
    const cards = document.querySelectorAll('.card.h-100');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.3s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }, index * 100);
    });
    
    // Búsqueda en tiempo real
    const buscarInput = document.getElementById('buscar');
    let timeoutId;
    
    if (buscarInput) {
        buscarInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function() {
                if (buscarInput.value.length >= 3 || buscarInput.value.length === 0) {
                    document.querySelector('form').submit();
                }
            }, 500);
        });
    }
    
    // Tooltip para botones
    document.querySelectorAll('[title]').forEach(function(element) {
        element.setAttribute('data-bs-toggle', 'tooltip');
        new bootstrap.Tooltip(element);
    });
    
    // Mejorar UX en móviles
    if (window.innerWidth <= 768) {
        // Cambiar layout de tarjetas en móviles
        document.querySelectorAll('.col-lg-4').forEach(col => {
            col.className = 'col-12 mb-3';
        });
        
        // Hacer estadísticas más compactas
        document.querySelectorAll('.stats-card').forEach(card => {
            card.style.marginBottom = '0.75rem';
        });
    }
});

// Función para vista rápida de paquete
function vistaRapidaPaquete(paqueteId) {
    // Aquí podrías implementar un modal con vista rápida
    fetch(`index.php?controller=paquete&action=quickView&id=${paqueteId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar modal con información del paquete
            showQuickViewModal(data.paquete);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function showQuickViewModal(paquete) {
    // Implementar modal de vista rápida
    alert('Vista rápida de: ' + paquete.nombre);
}
</script>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.card-footer .btn-group .btn {
    border-radius: 0;
}

.card-footer .btn-group .btn:first-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0.375rem;
}

.card-footer .btn-group .btn:last-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0.375rem;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
    
    .btn-group .btn:last-child {
        margin-bottom: 0;
    }
}
</style>

<?php include_once 'app/views/layouts/footer.php'; ?>