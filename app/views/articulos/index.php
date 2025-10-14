<?php
$title = 'Gestión de Artículos';
include_once 'app/views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary">
        <i class="fas fa-box me-2"></i>
        Gestión de Artículos
    </h2>
    <a href="index.php?controller=articulo&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo Artículo
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
            <input type="hidden" name="controller" value="articulo">
            <input type="hidden" name="action" value="index">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="buscar" class="form-label">Buscar artículo</label>
                    <input type="text" class="form-control" id="buscar" name="buscar" 
                           placeholder="Nombre, código o descripción..." 
                           value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="stock_minimo" class="form-label">Stock mínimo</label>
                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" 
                           placeholder="0" min="0"
                           value="<?php echo isset($_GET['stock_minimo']) ? $_GET['stock_minimo'] : ''; ?>">
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="index.php?controller=articulo" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de artículos -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>
            Lista de Artículos
        </h5>
        <span class="badge bg-info"><?php echo isset($articulos) ? count($articulos) : 0; ?> artículos</span>
    </div>
    <div class="card-body">
        <?php if (empty($articulos)): ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No hay artículos registrados</h5>
                <p class="text-muted">Comience agregando su primer artículo al inventario</p>
                <a href="index.php?controller=articulo&action=create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Primer Artículo
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Stock</th>
                            <th>Precio Costo</th>
                            <th>Precio Venta</th>
                            <th>Utilidad</th>
                            <th width="150">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articulos as $articulo): ?>
                            <?php 
                            $utilidad = $articulo['precio_venta'] - $articulo['precio_costo'];
                            $utilidadPorcentaje = $articulo['precio_costo'] > 0 ? 
                                                ($utilidad / $articulo['precio_costo']) * 100 : 0;
                            $stockBajo = $articulo['stock'] <= 5;
                            ?>
                            <tr>
                                <td>
                                    <strong class="text-primary"><?php echo $articulo['codigo'] ?? 'ART-' . $articulo['id']; ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo $articulo['nombre']; ?></strong>
                                        <?php if ($stockBajo): ?>
                                            <span class="badge bg-warning text-dark ms-1">Stock Bajo</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo strlen($articulo['descripcion']) > 50 ? 
                                                  substr($articulo['descripcion'], 0, 50) . '...' : 
                                                  $articulo['descripcion']; ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge <?php echo $stockBajo ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo number_format($articulo['stock']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-danger">
                                        <?php echo '$' . number_format($articulo['precio_costo'], 0); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-success">
                                        <?php echo '$' . number_format($articulo['precio_venta'], 0); ?>
                                    </strong>
                                </td>
                                <td>
                                    <div>
                                        <span class="<?php echo $utilidad > 0 ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo '$' . number_format($utilidad, 0); ?>
                                        </span>
                                        <br>
                                        <small class="<?php echo $utilidadPorcentaje > 0 ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo number_format($utilidadPorcentaje, 1); ?>%
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="index.php?controller=articulo&action=view&id=<?php echo $articulo['id']; ?>" 
                                           class="btn btn-outline-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="index.php?controller=articulo&action=edit&id=<?php echo $articulo['id']; ?>" 
                                           class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <a href="index.php?controller=articulo&action=delete&id=<?php echo $articulo['id']; ?>" 
                                           class="btn btn-outline-danger" title="Eliminar"
                                           onclick="return confirmarEliminacion('¿Está seguro de eliminar este artículo?')">
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
        <div class="stats-card">
            <div class="stats-number"><?php echo isset($articulos) ? count($articulos) : 0; ?></div>
            <div class="stats-label">Total Artículos</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="stats-number">
                <?php 
                $stockBajo = 0;
                if (isset($articulos)) {
                    $stockBajo = count(array_filter($articulos, function($a) { return $a['stock'] <= 5; }));
                }
                echo $stockBajo;
                ?>
            </div>
            <div class="stats-label">Stock Bajo</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="stats-number">
                <?php 
                $valorInventario = 0;
                if (isset($articulos)) {
                    foreach ($articulos as $art) {
                        $valorInventario += $art['precio_costo'] * $art['stock'];
                    }
                }
                echo '$' . number_format($valorInventario / 1000, 0) . 'K';
                ?>
            </div>
            <div class="stats-label">Valor Inventario</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="stats-number">
                <?php 
                $utilidadPromedio = 0;
                if (isset($articulos) && count($articulos) > 0) {
                    $totalUtilidad = 0;
                    foreach ($articulos as $art) {
                        if ($art['precio_costo'] > 0) {
                            $utilidad = (($art['precio_venta'] - $art['precio_costo']) / $art['precio_costo']) * 100;
                            $totalUtilidad += $utilidad;
                        }
                    }
                    $utilidadPromedio = $totalUtilidad / count($articulos);
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
    // Búsqueda en tiempo real
    const buscarInput = document.getElementById('buscar');
    let timeoutId;
    
    if (buscarInput) {
        buscarInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function() {
                if (buscarInput.value.length >= 3 || buscarInput.value.length === 0) {
                    // Auto-submit del formulario después de 500ms
                    document.querySelector('form').submit();
                }
            }, 500);
        });
    }
    
    // Resaltar filas con stock bajo
    document.querySelectorAll('tbody tr').forEach(function(row) {
        const stockBadge = row.querySelector('.badge');
        if (stockBadge && stockBadge.classList.contains('bg-danger')) {
            row.style.backgroundColor = 'rgba(220, 53, 69, 0.05)';
        }
    });
    
    // Mejorar UX en móviles
    if (window.innerWidth <= 768) {
        // Hacer las estadísticas más compactas
        document.querySelectorAll('.stats-card').forEach(card => {
            card.style.marginBottom = '0.75rem';
        });
    }
});

// Función para actualizar stock rápidamente
function actualizarStock(articuloId, nuevoStock) {
    if (confirm('¿Actualizar stock a ' + nuevoStock + ' unidades?')) {
        fetch('index.php?controller=articulo&action=updateStock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id: articuloId,
                stock: nuevoStock
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al actualizar stock: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión');
        });
    }
}
</script>

<?php include_once 'app/views/layouts/footer.php'; ?>