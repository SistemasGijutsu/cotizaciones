<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Las variables $clientes, $articulos y $paquetes vienen del controlador
// Si no están definidas, inicializarlas como arrays vacíos para evitar errores
$clientes = $clientes ?? [];
$articulos = $articulos ?? [];
$paquetes = $paquetes ?? [];
$errors = $errors ?? [];
$cliente_seleccionado = $cliente_seleccionado ?? '';
$items_seleccionados = $items_seleccionados ?? [];
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
                                            <label for="cliente_search" class="form-label">Cliente *</label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="cliente_search" 
                                                       placeholder="Buscar por nombre, documento, empresa..." 
                                                       autocomplete="off">
                                                <input type="hidden" id="cliente_id" name="cliente_id" required>
                                                
                                                <!-- Dropdown de resultados -->
                                                <div id="cliente_results" class="dropdown-menu w-100" style="max-height: 300px; overflow-y: auto;">
                                                    <!-- Los resultados se cargan aquí -->
                                                </div>
                                                
                                                <!-- Info del cliente seleccionado -->
                                                <div id="cliente_selected" class="mt-2 p-2 bg-light rounded d-none">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong id="cliente_nombre_display"></strong>
                                                            <small class="text-muted d-block" id="cliente_info_display"></small>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarClienteSeleccionado()">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Botón para crear cliente nuevo cuando no se encuentra -->
                                            <div id="crear_cliente_nuevo" class="mt-2 d-none">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    No se encontraron clientes con ese criterio.
                                                    <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#clienteRapidoModal">
                                                        <i class="fas fa-plus me-1"></i> Crear Cliente
                                                    </button>
                                                </div>
                                            </div>
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
                                    <td>$<?= number_format($articulo['precio_venta'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $articulo['stock'] > 0 ? 'success' : 'danger' ?>">
                                            <?= $articulo['stock'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="seleccionarArticulo(<?= $articulo['id'] ?>, '<?= htmlspecialchars($articulo['descripcion']) ?>', <?= $articulo['precio_venta'] ?>)">
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

<!-- Modal para crear cliente rápido -->
<div class="modal fade" id="clienteRapidoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>
                    Crear Cliente Rápido
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="clienteRapidoForm">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nuevo_cliente_nombre" class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control" id="nuevo_cliente_nombre" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="nuevo_cliente_tipo_doc" class="form-label">Tipo Documento</label>
                                <select class="form-select" id="nuevo_cliente_tipo_doc">
                                    <option value="cedula">Cédula</option>
                                    <option value="nit">NIT</option>
                                    <option value="pasaporte">Pasaporte</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nuevo_cliente_documento" class="form-label">Número Documento *</label>
                                <input type="text" class="form-control" id="nuevo_cliente_documento" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nuevo_cliente_empresa" class="form-label">Empresa</label>
                                <input type="text" class="form-control" id="nuevo_cliente_empresa" 
                                       placeholder="Opcional">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nuevo_cliente_correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="nuevo_cliente_correo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nuevo_cliente_telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="nuevo_cliente_telefono">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nuevo_cliente_direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="nuevo_cliente_direccion">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="crearClienteRapido()">
                    <i class="fas fa-save me-1"></i>
                    Crear Cliente
                </button>
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

/* Estilos para búsqueda de clientes */
.dropdown-menu.show {
    display: block;
}

.cliente-option {
    padding: 0.75rem 1rem;
}

.cliente-option:hover {
    background-color: #f8f9fa;
}

.cliente-option:focus {
    background-color: #e9ecef;
}

#cliente_selected {
    border: 1px solid #dee2e6;
    background-color: #f8f9fa !important;
}

#cliente_results {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    border: 1px solid #dee2e6;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
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
    
    #cliente_results {
        position: fixed;
        left: 1rem;
        right: 1rem;
        width: auto;
    }
}
</style>

<script>
let itemCounter = 0;
let clienteSearchTimeout;
let clienteSeleccionado = null;

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
    
    if (!$('#cliente_id').val()) {
        e.preventDefault();
        alert('Debe seleccionar un cliente.');
        $('#cliente_search').focus();
        return false;
    }
});

// === FUNCIONALIDAD DE BÚSQUEDA DE CLIENTES ===

// Búsqueda en tiempo real de clientes
$('#cliente_search').on('input', function() {
    const term = $(this).val().trim();
    
    // Limpiar timeout anterior
    if (clienteSearchTimeout) {
        clearTimeout(clienteSearchTimeout);
    }
    
    if (term.length < 2) {
        $('#cliente_results').removeClass('show');
        $('#crear_cliente_nuevo').addClass('d-none');
        return;
    }
    
    // Buscar con delay para evitar muchas peticiones
    clienteSearchTimeout = setTimeout(() => {
        buscarClientes(term);
    }, 300);
});

// Función para buscar clientes via AJAX
function buscarClientes(term) {
    $.ajax({
        url: '/mod_cotizacion/index.php?controller=cliente&action=search',
        method: 'GET',
        data: { term: term },
        dataType: 'json',
        success: function(clientes) {
            mostrarResultadosClientes(clientes, term);
        },
        error: function() {
            console.error('Error al buscar clientes');
        }
    });
}

// Mostrar resultados de la búsqueda
function mostrarResultadosClientes(clientes, term) {
    const $results = $('#cliente_results');
    $results.empty();
    
    if (clientes.length === 0) {
        // No se encontraron clientes
        $('#crear_cliente_nuevo').removeClass('d-none');
        $results.removeClass('show');
        return;
    }
    
    $('#crear_cliente_nuevo').addClass('d-none');
    
    clientes.forEach(cliente => {
        const empresaText = cliente.empresa ? ` - ${cliente.empresa}` : '';
        const documentoText = cliente.documento ? ` (${cliente.documento_formato})` : '';
        
        const $item = $(`
            <a href="#" class="dropdown-item cliente-option" data-cliente-id="${cliente.id}">
                <div>
                    <strong>${cliente.nombre}${empresaText}</strong>
                    <small class="text-muted d-block">
                        ${documentoText}
                        ${cliente.correo ? ` • ${cliente.correo}` : ''}
                        ${cliente.telefono ? ` • ${cliente.telefono}` : ''}
                    </small>
                </div>
            </a>
        `);
        
        $item.on('click', function(e) {
            e.preventDefault();
            seleccionarCliente(cliente);
        });
        
        $results.append($item);
    });
    
    $results.addClass('show');
}

// Seleccionar un cliente
function seleccionarCliente(cliente) {
    clienteSeleccionado = cliente;
    $('#cliente_id').val(cliente.id);
    $('#cliente_search').val('');
    $('#cliente_results').removeClass('show');
    $('#crear_cliente_nuevo').addClass('d-none');
    
    // Mostrar información del cliente seleccionado
    const empresaText = cliente.empresa ? ` - ${cliente.empresa}` : '';
    const documentoText = cliente.documento ? ` (${cliente.documento_formato})` : '';
    
    $('#cliente_nombre_display').text(cliente.nombre + empresaText);
    $('#cliente_info_display').text(documentoText + (cliente.correo ? ` • ${cliente.correo}` : ''));
    $('#cliente_selected').removeClass('d-none');
}

// Limpiar cliente seleccionado
function limpiarClienteSeleccionado() {
    clienteSeleccionado = null;
    $('#cliente_id').val('');
    $('#cliente_search').val('');
    $('#cliente_selected').addClass('d-none');
    $('#cliente_results').removeClass('show');
    $('#crear_cliente_nuevo').addClass('d-none');
}

// Ocultar resultados cuando se hace clic fuera
$(document).on('click', function(e) {
    if (!$(e.target).closest('#cliente_search, #cliente_results').length) {
        $('#cliente_results').removeClass('show');
    }
});

// === CREACIÓN RÁPIDA DE CLIENTE ===

// Llenar modal con datos de búsqueda
$('#clienteRapidoModal').on('show.bs.modal', function() {
    const searchTerm = $('#cliente_search').val();
    if (searchTerm) {
        // Si parece un documento (solo números), llenarlo en documento
        if (/^\d+$/.test(searchTerm)) {
            $('#nuevo_cliente_documento').val(searchTerm);
        } else {
            // Si no, asumir que es un nombre
            $('#nuevo_cliente_nombre').val(searchTerm);
        }
    }
});

// Función para crear cliente rápido
function crearClienteRapido() {
    const formData = {
        nombre: $('#nuevo_cliente_nombre').val().trim(),
        documento: $('#nuevo_cliente_documento').val().trim(),
        tipo_documento: $('#nuevo_cliente_tipo_doc').val(),
        empresa: $('#nuevo_cliente_empresa').val().trim(),
        correo: $('#nuevo_cliente_correo').val().trim(),
        telefono: $('#nuevo_cliente_telefono').val().trim(),
        direccion: $('#nuevo_cliente_direccion').val().trim()
    };
    
    // Validaciones básicas
    if (!formData.nombre) {
        alert('El nombre es obligatorio');
        $('#nuevo_cliente_nombre').focus();
        return;
    }
    
    if (!formData.documento) {
        alert('El documento es obligatorio');
        $('#nuevo_cliente_documento').focus();
        return;
    }
    
    // Mostrar loading
    const $btn = $(event.target);
    const originalText = $btn.html();
    $btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Creando...').prop('disabled', true);
    
    // Crear cliente via AJAX
    $.ajax({
        url: '/mod_cotizacion/index.php?controller=cliente&action=createRapido',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Cliente creado exitosamente
                seleccionarCliente(response.cliente);
                $('#clienteRapidoModal').modal('hide');
                
                // Limpiar formulario
                $('#clienteRapidoForm')[0].reset();
                
                alert('Cliente creado exitosamente');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error al crear el cliente. Intente nuevamente.');
        },
        complete: function() {
            // Restaurar botón
            $btn.html(originalText).prop('disabled', false);
        }
    });
}

// Limpiar formulario cuando se cierra el modal
$('#clienteRapidoModal').on('hidden.bs.modal', function() {
    $('#clienteRapidoForm')[0].reset();
});
</script>