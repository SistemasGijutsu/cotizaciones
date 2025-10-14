<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Obtener todos los clientes y artículos para los select
$clientes = $clienteModel->getAll();
$articulos = $articuloModel->getAll();
$paquetes = $paqueteModel->getAll();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 mb-1">
                        <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                        Nueva Cotización
                    </h2>
                    <p class="text-muted mb-0">Crear una nueva cotización para un cliente</p>
                </div>
                <a href="/mod_cotizacion/index.php?controller=cotizacion&action=index" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Volver a Lista
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Datos de la Cotización
                    </h5>
                </div>
                <div class="card-body">
                    <form id="cotizacionForm" action="/mod_cotizacion/index.php?controller=cotizacion&action=store" method="POST">
                        <div class="row">
                            <!-- Información del Cliente -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user me-2"></i>
                                            Información del Cliente
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="cliente_id" class="form-label">Cliente *</label>
                                            <select class="form-select" id="cliente_id" name="cliente_id" required>
                                                <option value="">Seleccionar cliente...</option>
                                                <?php foreach ($clientes as $cliente): ?>
                                                    <option value="<?= $cliente['id'] ?>" 
                                                            data-email="<?= htmlspecialchars($cliente['email']) ?>"
                                                            data-telefono="<?= htmlspecialchars($cliente['telefono']) ?>">
                                                        <?= htmlspecialchars($cliente['nombre']) ?> - <?= htmlspecialchars($cliente['empresa']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento *</label>
                                            <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" 
                                                   value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="observaciones" class="form-label">Observaciones</label>
                                            <textarea class="form-control" id="observaciones" name="observaciones" 
                                                      rows="3" placeholder="Observaciones adicionales..."></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="utilidad_porcentaje" class="form-label">Utilidad (%)</label>
                                            <input type="number" class="form-control" id="utilidad_porcentaje" 
                                                   name="utilidad_porcentaje" value="25" min="0" max="100">
                                            <div class="form-text">Porcentaje de utilidad a aplicar sobre el costo</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Selección de Items -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-shopping-cart me-2"></i>
                                            Items de la Cotización
                                        </h6>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarArticulo()">
                                                <i class="fas fa-plus me-1"></i>Artículo
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="agregarPaquete()">
                                                <i class="fas fa-box me-1"></i>Paquete
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="items-container">
                                            <!-- Los items se agregan dinámicamente aquí -->
                                        </div>
                                        
                                        <div class="alert alert-info" id="no-items-msg">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No hay items agregados. Usa los botones de arriba para comenzar.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen de Totales -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-4 border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-calculator me-2"></i>
                                            Resumen de Totales
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <div class="stat-item">
                                                    <div class="stat-value text-primary" id="subtotal-display">$0.00</div>
                                                    <div class="stat-label">Subtotal</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="stat-item">
                                                    <div class="stat-value text-warning" id="utilidad-display">$0.00</div>
                                                    <div class="stat-label">Utilidad</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="stat-item">
                                                    <div class="stat-value text-info" id="iva-display">$0.00</div>
                                                    <div class="stat-label">IVA (16%)</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="stat-item">
                                                    <div class="stat-value text-success fw-bold" id="total-display">$0.00</div>
                                                    <div class="stat-label">Total</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="/mod_cotizacion/index.php?controller=cotizacion&action=index" 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Guardar Cotización
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Campos ocultos para totales -->
                        <input type="hidden" id="subtotal" name="subtotal" value="0">
                        <input type="hidden" id="utilidad" name="utilidad" value="0">
                        <input type="hidden" id="iva" name="iva" value="0">
                        <input type="hidden" id="total" name="total" value="0">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para seleccionar artículo -->
<div class="modal fade" id="articuloModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articulos as $articulo): ?>
                                <tr>
                                    <td><?= htmlspecialchars($articulo['codigo']) ?></td>
                                    <td><?= htmlspecialchars($articulo['descripcion']) ?></td>
                                    <td>$<?= number_format($articulo['precio'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $articulo['stock'] > 0 ? 'success' : 'danger' ?>">
                                            <?= $articulo['stock'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="seleccionarArticulo(<?= $articulo['id'] ?>, '<?= htmlspecialchars($articulo['descripcion']) ?>', <?= $articulo['precio'] ?>)">
                                            <i class="fas fa-plus"></i> Agregar
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

<!-- Modal para seleccionar paquete -->
<div class="modal fade" id="paqueteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Paquete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paquetes as $paquete): ?>
                                <tr>
                                    <td><?= htmlspecialchars($paquete['nombre']) ?></td>
                                    <td><?= htmlspecialchars($paquete['descripcion']) ?></td>
                                    <td>$<?= number_format($paquete['precio'], 2) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success" 
                                                onclick="seleccionarPaquete(<?= $paquete['id'] ?>, '<?= htmlspecialchars($paquete['nombre']) ?>', <?= $paquete['precio'] ?>)">
                                            <i class="fas fa-plus"></i> Agregar
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
.stat-item {
    padding: 1rem 0;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: #6c757d;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.item-row {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    margin-bottom: 0.75rem;
    padding: 1rem;
    background-color: #f8f9fa;
}

.item-row:hover {
    background-color: #e9ecef;
}

@media (max-width: 768px) {
    .stat-item {
        margin-bottom: 1rem;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 0.5rem;
    }
}
</style>

<script>
let itemCounter = 0;

function agregarArticulo() {
    $('#articuloModal').modal('show');
}

function agregarPaquete() {
    $('#paqueteModal').modal('show');
}

function seleccionarArticulo(id, descripcion, precio) {
    itemCounter++;
    const itemHtml = `
        <div class="item-row" id="item-${itemCounter}">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <strong>Artículo:</strong> ${descripcion}
                    <input type="hidden" name="items[${itemCounter}][tipo]" value="articulo">
                    <input type="hidden" name="items[${itemCounter}][id]" value="${id}">
                    <input type="hidden" name="items[${itemCounter}][precio_unitario]" value="${precio}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cantidad</label>
                    <input type="number" class="form-control cantidad-input" 
                           name="items[${itemCounter}][cantidad]" value="1" min="1" 
                           onchange="calcularTotales()">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Precio Unit.</label>
                    <input type="number" class="form-control precio-input" 
                           name="items[${itemCounter}][precio_unitario]" value="${precio}" 
                           step="0.01" onchange="calcularTotales()">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Subtotal</label>
                    <div class="form-control-plaintext fw-bold item-subtotal">$${precio}</div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarItem(${itemCounter})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    $('#items-container').append(itemHtml);
    $('#no-items-msg').hide();
    $('#articuloModal').modal('hide');
    calcularTotales();
}

function seleccionarPaquete(id, nombre, precio) {
    itemCounter++;
    const itemHtml = `
        <div class="item-row" id="item-${itemCounter}">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <strong>Paquete:</strong> ${nombre}
                    <input type="hidden" name="items[${itemCounter}][tipo]" value="paquete">
                    <input type="hidden" name="items[${itemCounter}][id]" value="${id}">
                    <input type="hidden" name="items[${itemCounter}][precio_unitario]" value="${precio}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cantidad</label>
                    <input type="number" class="form-control cantidad-input" 
                           name="items[${itemCounter}][cantidad]" value="1" min="1" 
                           onchange="calcularTotales()">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Precio Unit.</label>
                    <input type="number" class="form-control precio-input" 
                           name="items[${itemCounter}][precio_unitario]" value="${precio}" 
                           step="0.01" onchange="calcularTotales()">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Subtotal</label>
                    <div class="form-control-plaintext fw-bold item-subtotal">$${precio}</div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarItem(${itemCounter})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    $('#items-container').append(itemHtml);
    $('#no-items-msg').hide();
    $('#paqueteModal').modal('hide');
    calcularTotales();
}

function eliminarItem(itemId) {
    $(`#item-${itemId}`).remove();
    if ($('#items-container .item-row').length === 0) {
        $('#no-items-msg').show();
    }
    calcularTotales();
}

function calcularTotales() {
    let subtotal = 0;
    
    $('.item-row').each(function() {
        const cantidad = parseFloat($(this).find('.cantidad-input').val()) || 0;
        const precio = parseFloat($(this).find('.precio-input').val()) || 0;
        const itemSubtotal = cantidad * precio;
        
        $(this).find('.item-subtotal').text('$' + itemSubtotal.toFixed(2));
        subtotal += itemSubtotal;
    });
    
    const utilidadPorcentaje = parseFloat($('#utilidad_porcentaje').val()) || 0;
    const utilidad = subtotal * (utilidadPorcentaje / 100);
    const subtotalConUtilidad = subtotal + utilidad;
    const iva = subtotalConUtilidad * 0.16;
    const total = subtotalConUtilidad + iva;
    
    // Actualizar displays
    $('#subtotal-display').text('$' + subtotal.toFixed(2));
    $('#utilidad-display').text('$' + utilidad.toFixed(2));
    $('#iva-display').text('$' + iva.toFixed(2));
    $('#total-display').text('$' + total.toFixed(2));
    
    // Actualizar campos ocultos
    $('#subtotal').val(subtotal.toFixed(2));
    $('#utilidad').val(utilidad.toFixed(2));
    $('#iva').val(iva.toFixed(2));
    $('#total').val(total.toFixed(2));
}

// Recalcular cuando cambie el porcentaje de utilidad
$('#utilidad_porcentaje').on('input', calcularTotales);

// Validación del formulario
$('#cotizacionForm').on('submit', function(e) {
    if ($('#items-container .item-row').length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un item a la cotización.');
        return false;
    }
});
</script>