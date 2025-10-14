<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Obtener datos necesarios
$clientes = $clientes ?? [];
$articulos = $articulos ?? [];
$paquetes = $paquetes ?? [];
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Nueva Cotización</h2>
                    <p class="text-muted mb-0">Crear cotización como una factura</p>
                </div>
                <a href="index.php?controller=cotizacion" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Volver
                </a>
            </div>

            <!-- Formulario -->
            <form id="cotizacionForm" method="POST" action="index.php?controller=cotizacion&action=store">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <!-- Selector Simple de Cliente -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cliente_select" class="form-label">Seleccionar Cliente *</label>
                                <select class="form-select form-select-lg" id="cliente_select" required>
                                    <option value="">-- Seleccione un cliente --</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?= $cliente['id'] ?>" 
                                                data-nombre="<?= htmlspecialchars($cliente['nombre']) ?>"
                                                data-documento="<?= htmlspecialchars($cliente['documento']) ?>"
                                                data-tipo="<?= htmlspecialchars($cliente['tipo_documento'] ?? 'CC') ?>"
                                                data-correo="<?= htmlspecialchars($cliente['correo'] ?? '') ?>"
                                                data-telefono="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>"
                                                data-direccion="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>">
                                            <?= htmlspecialchars($cliente['nombre']) ?> - <?= htmlspecialchars($cliente['documento']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_vencimiento" class="form-label">Fecha Vencimiento *</label>
                                <input type="date" class="form-control" id="fecha_vencimiento" 
                                       name="fecha_vencimiento" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-success d-block w-100" data-bs-toggle="modal" data-bs-target="#nuevoClienteModal">
                                    <i class="fas fa-plus me-1"></i>Nuevo Cliente
                                </button>
                            </div>
                        </div>

                        <!-- Datos del Cliente (Solo Lectura) -->
                        <div class="row">
                            <div class="col-md-2">
                                <label for="tipo_documento" class="form-label">Tipo de documento</label>
                                <input type="text" class="form-control bg-light" id="tipo_documento" name="tipo_documento" readonly placeholder="Seleccione cliente">
                            </div>
                            <div class="col-md-2">
                                <label for="cedula" class="form-label">Cédula</label>
                                <input type="text" class="form-control bg-light" id="cedula" name="cedula" readonly placeholder="Seleccione cliente" required>
                            </div>
                            <div class="col-md-4">
                                <label for="nombre_apellidos" class="form-label">Nombre y Apellidos</label>
                                <input type="text" class="form-control bg-light" id="nombre_apellidos" name="nombre_apellidos" readonly placeholder="Seleccione cliente" required>
                            </div>
                            <div class="col-md-4">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control bg-light" id="telefono" name="telefono" readonly placeholder="Seleccione cliente">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="correo" class="form-label">Correo</label>
                                <input type="email" class="form-control bg-light" id="correo" name="correo" readonly placeholder="Seleccione cliente">
                            </div>
                            <div class="col-md-6">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control bg-light" id="direccion" name="direccion" readonly placeholder="Seleccione cliente">
                            </div>
                        </div>

                        <!-- Indicador de cliente seleccionado -->
                        <div id="cliente_seleccionado_info" class="mt-3 p-3 bg-success text-white rounded d-none">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-1"><i class="fas fa-check-circle me-2"></i>Cliente Seleccionado</h6>
                                    <small id="info_cliente_texto">Cliente listo para cotización</small>
                                </div>
                            </div>
                        </div>

                        <!-- Cliente ID hidden -->
                        <input type="hidden" id="cliente_id" name="cliente_id">
                    </div>
                </div>

                <!-- Items de la Cotización -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Items de la Cotización</h5>
                            <div>
                                <button type="button" class="btn btn-light btn-sm me-2" onclick="agregarArticulo()">
                                    <i class="fas fa-plus me-1"></i>Artículo
                                </button>
                                <button type="button" class="btn btn-light btn-sm" onclick="agregarPaquete()">
                                    <i class="fas fa-box me-1"></i>Paquete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="items_table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Descripción</th>
                                        <th width="15%">Cantidad</th>
                                        <th width="15%">Precio Unit.</th>
                                        <th width="15%">Utilidad %</th>
                                        <th width="15%">Total</th>
                                        <th width="5%">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="items_body">
                                    <tr id="no_items">
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                            No hay items agregados. Use los botones de arriba para agregar.
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                                        <td><strong id="total_general">$0</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                          placeholder="Observaciones adicionales..."></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="utilidad_general" class="form-label">Utilidad General (%)</label>
                                <input type="number" class="form-control" id="utilidad_general" name="utilidad_general" 
                                       value="25" min="0" max="100" onchange="recalcularTotales()">
                                <small class="text-muted">Se aplicará a todos los items</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="text-center mt-4 mb-5">
                    <button type="submit" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-save me-2"></i>Guardar Cotización
                    </button>
                    <button type="button" class="btn btn-success btn-lg" onclick="enviarPorEmail()" disabled id="btn_email">
                        <i class="fas fa-envelope me-2"></i>Enviar por Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Agregar Artículo -->
<div class="modal fade" id="articuloModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Seleccionar Artículo</label>
                    <select class="form-select" id="select_articulo">
                        <option value="">-- Seleccione un artículo --</option>
                        <?php foreach ($articulos as $articulo): ?>
                            <option value="<?= $articulo['id'] ?>" 
                                    data-nombre="<?= htmlspecialchars($articulo['nombre']) ?>"
                                    data-precio="<?= $articulo['precio_venta'] ?? 0 ?>">
                                <?= htmlspecialchars($articulo['nombre']) ?> - $<?= number_format($articulo['precio_venta'] ?? 0, 0) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" class="form-control" id="cantidad_articulo" value="1" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmarArticulo()">Agregar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Paquete -->
<div class="modal fade" id="paqueteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Paquete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Seleccionar Paquete</label>
                    <select class="form-select" id="select_paquete">
                        <option value="">-- Seleccione un paquete --</option>
                        <?php foreach ($paquetes as $paquete): ?>
                            <option value="<?= $paquete['id'] ?>" 
                                    data-nombre="<?= htmlspecialchars($paquete['nombre']) ?>"
                                    data-precio="<?= $paquete['precio_total'] ?>">
                                <?= htmlspecialchars($paquete['nombre']) ?> - $<?= number_format($paquete['precio_total']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" class="form-control" id="cantidad_paquete" value="1" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmarPaquete()">Agregar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="nuevoClienteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Crear Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevoClienteForm">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nuevo_nombre" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipo Documento</label>
                            <select class="form-select" id="nuevo_tipo_doc">
                                <option value="CC">CC</option>
                                <option value="NIT">NIT</option>
                                <option value="CE">CE</option>
                                <option value="PAS">Pasaporte</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Documento *</label>
                            <input type="text" class="form-control" id="nuevo_documento" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control" id="nuevo_correo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="nuevo_telefono">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="nuevo_direccion">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="crearCliente()">
                    <i class="fas fa-save me-1"></i>Crear Cliente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var itemCounter = 0;
$(document).ready(function() {
    // Selector simple de clientes
    $('#cliente_select').change(function() {
        const $option = $(this).find('option:selected');
        
        if (!$option.val()) {
            // Limpiar campos
            $('#cliente_id').val('');
            $('#nombre_apellidos').val('');
            $('#cedula').val('');
            $('#correo').val('');
            $('#telefono').val('');
            $('#direccion').val('');
            $('#tipo_documento').val('');
            $('#cliente_seleccionado_info').addClass('d-none');
            return;
        }
        
        // Llenar los campos con la información del cliente
        const id = $option.val();
        const nombre = $option.data('nombre');
        const documento = $option.data('documento');
        const tipo = $option.data('tipo');
        const correo = $option.data('correo');
        const telefono = $option.data('telefono');
        const direccion = $option.data('direccion');
        
        $('#cliente_id').val(id);
        $('#nombre_apellidos').val(nombre);
        $('#cedula').val(documento);
        $('#tipo_documento').val(tipo);
        $('#correo').val(correo);
        $('#telefono').val(telefono);
        $('#direccion').val(direccion);
        
        // Mostrar indicador de cliente seleccionado
        $('#cliente_seleccionado_info').removeClass('d-none');
        $('#info_cliente_texto').text(nombre + ' - ' + documento + (correo ? ' • ' + correo : ''));
        
        console.log('Cliente seleccionado:', nombre);
    });
});

function crearCliente() {
    const datos = {
        nombre: $('#nuevo_nombre').val().trim(),
        documento: $('#nuevo_documento').val().trim(),
        tipo_documento: $('#nuevo_tipo_doc').val(),
        correo: $('#nuevo_correo').val().trim(),
        telefono: $('#nuevo_telefono').val().trim(),
        direccion: $('#nuevo_direccion').val().trim()
    };
    
    if (!datos.nombre || !datos.documento) {
        alert('Nombre y documento son obligatorios');
        return;
    }
    
    // Crear cliente via AJAX
    $.ajax({
        url: 'index.php?controller=cliente&action=createRapido',
        method: 'POST',
        dataType: 'json',
        data: datos,
        success: function(response) {
            if (response.success) {
                // Llenar los campos con el cliente creado
                seleccionarCliente(
                    response.cliente.id,
                    response.cliente.nombre,
                    response.cliente.documento,
                    response.cliente.correo || '',
                    response.cliente.telefono || '',
                    response.cliente.direccion || '',
                    response.cliente.tipo_documento || 'CC'
                );
                
                $('#nuevoClienteModal').modal('hide');
                $('#nuevoClienteForm')[0].reset();
                
                mostrarNotificacion('Cliente creado y seleccionado exitosamente', 'success');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error al crear el cliente');
        }
    });
}

function agregarArticulo() {
    $('#articuloModal').modal('show');
}

function agregarPaquete() {
    $('#paqueteModal').modal('show');
}

function confirmarArticulo() {
    const select = $('#select_articulo');
    const option = select.find('option:selected');
    const cantidad = parseInt($('#cantidad_articulo').val());
    
    if (!option.val()) {
        alert('Seleccione un artículo');
        return;
    }
    
    const item = {
        id: option.val(),
        tipo: 'articulo',
        nombre: option.data('nombre'),
        precio: parseFloat(option.data('precio')),
        cantidad: cantidad
    };
    
    agregarItemATabla(item);
    $('#articuloModal').modal('hide');
    limpiarModalArticulo();
}

function confirmarPaquete() {
    const select = $('#select_paquete');
    const option = select.find('option:selected');
    const cantidad = parseInt($('#cantidad_paquete').val());
    
    if (!option.val()) {
        alert('Seleccione un paquete');
        return;
    }
    
    const item = {
        id: option.val(),
        tipo: 'paquete',
        nombre: option.data('nombre'),
        precio: parseFloat(option.data('precio')),
        cantidad: cantidad
    };
    
    agregarItemATabla(item);
    $('#paqueteModal').modal('hide');
    limpiarModalPaquete();
}

function agregarItemATabla(item) {
    itemCounter++;
    const utilidad = parseFloat($('#utilidad_general').val()) || 0;
    const precioConUtilidad = item.precio * (1 + utilidad / 100);
    const total = precioConUtilidad * item.cantidad;
    
    const row = `
        <tr id="item_${itemCounter}">
            <td>
                ${item.nombre}
                <input type="hidden" name="items[${itemCounter}][id]" value="${item.id}">
                <input type="hidden" name="items[${itemCounter}][tipo]" value="${item.tipo}">
                <input type="hidden" name="items[${itemCounter}][nombre]" value="${item.nombre}">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${itemCounter}][cantidad]" 
                       value="${item.cantidad}" min="1" onchange="recalcularFila(${itemCounter})">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${itemCounter}][precio]" 
                       value="${item.precio}" step="0.01" onchange="recalcularFila(${itemCounter})">
            </td>
            <td>
                <input type="number" class="form-control" name="items[${itemCounter}][utilidad]" 
                       value="${utilidad}" min="0" max="100" onchange="recalcularFila(${itemCounter})">
            </td>
            <td>
                <strong class="total-fila">$${formatNumber(total)}</strong>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarItem(${itemCounter})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#no_items').hide();
    $('#items_body').append(row);
    recalcularTotales();
}

function recalcularFila(counter) {
    const row = $(`#item_${counter}`);
    const cantidad = parseFloat(row.find('input[name*="[cantidad]"]').val()) || 0;
    const precio = parseFloat(row.find('input[name*="[precio]"]').val()) || 0;
    const utilidad = parseFloat(row.find('input[name*="[utilidad]"]').val()) || 0;
    
    const precioConUtilidad = precio * (1 + utilidad / 100);
    const total = precioConUtilidad * cantidad;
    
    row.find('.total-fila').text('$' + formatNumber(total));
    recalcularTotales();
}

function eliminarItem(counter) {
    $(`#item_${counter}`).remove();
    
    if ($('#items_body tr').length === 0) {
        $('#no_items').show();
    }
    
    recalcularTotales();
}

function recalcularTotales() {
    let total = 0;
    
    $('#items_body tr:not(#no_items)').each(function() {
        const cantidad = parseFloat($(this).find('input[name*="[cantidad]"]').val()) || 0;
        const precio = parseFloat($(this).find('input[name*="[precio]"]').val()) || 0;
        const utilidad = parseFloat($(this).find('input[name*="[utilidad]"]').val()) || 0;
        
        const precioConUtilidad = precio * (1 + utilidad / 100);
        const subtotal = precioConUtilidad * cantidad;
        
        $(this).find('.total-fila').text('$' + formatNumber(subtotal));
        total += subtotal;
    });
    
    $('#total_general').text('$' + formatNumber(total));
    
    // Habilitar botón de email si hay items
    if (total > 0) {
        $('#btn_email').prop('disabled', false);
    } else {
        $('#btn_email').prop('disabled', true);
    }
}

function limpiarModalArticulo() {
    $('#select_articulo').val('');
    $('#cantidad_articulo').val(1);
}

function limpiarModalPaquete() {
    $('#select_paquete').val('');
    $('#cantidad_paquete').val(1);
}

function formatNumber(num) {
    return new Intl.NumberFormat('es-CO').format(num);
}

function enviarPorEmail() {
    // Esta función se implementará después de guardar
    alert('Primero guarde la cotización, luego podrá enviarla por email');
}

// Aplicar utilidad general a todos los items
$('#utilidad_general').change(function() {
    const utilidad = $(this).val();
    $('#items_body input[name*="[utilidad]"]').val(utilidad);
    recalcularTotales();
});
</script>