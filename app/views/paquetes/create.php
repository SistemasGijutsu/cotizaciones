<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Variables inicializadas desde el controlador
$articulos = $articulos ?? [];
$errors = $errors ?? [];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 mb-1">
                        <i class="fas fa-box-open text-primary me-2"></i>
                        Nuevo Paquete
                    </h2>
                    <p class="text-muted mb-0">Crear un paquete de artículos</p>
                </div>
                <a href="/mod_cotizacion/index.php?controller=paquete&action=index" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Volver a Lista
                </a>
            </div>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Errores encontrados:</h6>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Información del Paquete
                    </h5>
                </div>
                <div class="card-body">
                    <form action="/mod_cotizacion/index.php?controller=paquete&action=store" method="POST" id="paqueteForm">
                        <div class="row">
                            <div class="col-lg-6">
                                <!-- Información básica del paquete -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Datos Básicos
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre del Paquete *</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                                   placeholder="Ej: Paquete Básico de Oficina" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" 
                                                      rows="4" placeholder="Describe qué incluye este paquete..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Resumen de costos -->
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-calculator me-2"></i>
                                            Resumen de Costos
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="h5 text-info mb-1" id="total-costo">$0.00</div>
                                                <small class="text-muted">Costo Total</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="h5 text-success mb-1" id="total-venta">$0.00</div>
                                                <small class="text-muted">Precio Venta</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="h5 text-warning mb-1" id="utilidad-porcentaje">0%</div>
                                                <small class="text-muted">Utilidad</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <!-- Selección de artículos -->
                                <div class="card">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-shopping-cart me-2"></i>
                                            Artículos del Paquete
                                        </h6>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#articulosModal">
                                            <i class="fas fa-plus me-1"></i>
                                            Agregar Artículo
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="articulos-container">
                                            <!-- Los artículos seleccionados aparecen aquí -->
                                        </div>
                                        
                                        <div class="alert alert-info" id="no-articulos-msg">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No hay artículos agregados. Usa el botón de arriba para comenzar.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="/mod_cotizacion/index.php?controller=paquete&action=index" 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Guardar Paquete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para seleccionar artículos -->
<div class="modal fade" id="articulosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-search me-2"></i>
                    Seleccionar Artículos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="buscar-articulo" 
                           placeholder="Buscar artículo por nombre...">
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Artículo</th>
                                <th>Stock</th>
                                <th>Precio Costo</th>
                                <th>Precio Venta</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="articulos-disponibles">
                            <?php foreach ($articulos as $articulo): ?>
                                <tr data-articulo-id="<?= $articulo['id'] ?>" class="articulo-row">
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($articulo['nombre']) ?></strong>
                                            <?php if (!empty($articulo['descripcion'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars(substr($articulo['descripcion'], 0, 50)) ?>...</small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $articulo['stock'] > 0 ? 'success' : 'danger' ?>">
                                            <?= $articulo['stock'] ?>
                                        </span>
                                    </td>
                                    <td>$<?= number_format($articulo['precio_costo'], 2) ?></td>
                                    <td>$<?= number_format($articulo['precio_venta'], 2) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="agregarArticuloAPaquete(<?= $articulo['id'] ?>, '<?= htmlspecialchars($articulo['nombre']) ?>', <?= $articulo['precio_costo'] ?>, <?= $articulo['precio_venta'] ?>)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.articulo-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    margin-bottom: 0.75rem;
    padding: 1rem;
    background-color: #f8f9fa;
}

@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 .btn {
        margin-bottom: 0.5rem;
    }
}
</style>

<script>
let articulosSeleccionados = [];

// Función para agregar artículo al paquete
function agregarArticuloAPaquete(id, nombre, precioCosto, precioVenta) {
    // Verificar si ya está agregado
    if (articulosSeleccionados.find(a => a.id == id)) {
        alert('Este artículo ya está en el paquete');
        return;
    }
    
    const articulo = {
        id: id,
        nombre: nombre,
        precio_costo: precioCosto,
        precio_venta: precioVenta,
        cantidad: 1
    };
    
    articulosSeleccionados.push(articulo);
    actualizarVistaArticulos();
    actualizarTotales();
    $('#articulosModal').modal('hide');
}

// Actualizar la vista de artículos seleccionados
function actualizarVistaArticulos() {
    const container = $('#articulos-container');
    container.empty();
    
    if (articulosSeleccionados.length === 0) {
        $('#no-articulos-msg').show();
        return;
    }
    
    $('#no-articulos-msg').hide();
    
    articulosSeleccionados.forEach((articulo, index) => {
        const articuloHtml = `
            <div class="articulo-item" data-index="${index}">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <strong>${articulo.nombre}</strong>
                        <input type="hidden" name="articulos[${index}][id]" value="${articulo.id}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Cantidad</label>
                        <input type="number" class="form-control cantidad-input" 
                               name="articulos[${index}][cantidad]" 
                               value="${articulo.cantidad}" min="1" 
                               onchange="actualizarCantidad(${index}, this.value)">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Costo Unit.</label>
                        <div class="form-control-plaintext">$${articulo.precio_costo}</div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Venta Unit.</label>
                        <div class="form-control-plaintext">$${articulo.precio_venta}</div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarArticulo(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(articuloHtml);
    });
}

// Actualizar cantidad de un artículo
function actualizarCantidad(index, nuevaCantidad) {
    articulosSeleccionados[index].cantidad = parseInt(nuevaCantidad) || 1;
    actualizarTotales();
}

// Eliminar artículo del paquete
function eliminarArticulo(index) {
    articulosSeleccionados.splice(index, 1);
    actualizarVistaArticulos();
    actualizarTotales();
}

// Actualizar totales del paquete
function actualizarTotales() {
    let totalCosto = 0;
    let totalVenta = 0;
    
    articulosSeleccionados.forEach(articulo => {
        totalCosto += parseFloat(articulo.precio_costo) * parseInt(articulo.cantidad);
        totalVenta += parseFloat(articulo.precio_venta) * parseInt(articulo.cantidad);
    });
    
    const utilidad = totalVenta - totalCosto;
    const utilidadPorcentaje = totalCosto > 0 ? ((utilidad / totalCosto) * 100) : 0;
    
    $('#total-costo').text('$' + totalCosto.toFixed(2));
    $('#total-venta').text('$' + totalVenta.toFixed(2));
    $('#utilidad-porcentaje').text(utilidadPorcentaje.toFixed(1) + '%');
}

// Filtrar artículos en el modal
$('#buscar-articulo').on('input', function() {
    const termino = $(this).val().toLowerCase();
    $('.articulo-row').each(function() {
        const texto = $(this).text().toLowerCase();
        if (texto.includes(termino)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

// Validación del formulario
$('#paqueteForm').on('submit', function(e) {
    if (articulosSeleccionados.length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un artículo al paquete');
        return false;
    }
    
    if (!$('#nombre').val().trim()) {
        e.preventDefault();
        alert('El nombre del paquete es obligatorio');
        $('#nombre').focus();
        return false;
    }
});
</script>