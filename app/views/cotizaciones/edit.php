<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Obtener datos necesarios
$cotizacion = $cotizacion ?? null;
$clientes = $clientes ?? [];
$articulos = $articulos ?? [];
$paquetes = $paquetes ?? [];

if (!$cotizacion) {
    echo '<div class="alert alert-danger">Error: No se encontró la cotización</div>';
    return;
}
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-edit text-warning me-2"></i>Editar Cotización #<?= $cotizacion['id'] ?></h2>
                    <p class="text-muted mb-0">
                        Versión actual: <strong><?= $cotizacion['version'] ?? 1 ?></strong>
                        <?php if ($cotizacion['fecha_modificacion']): ?>
                            | Última modificación: <?= date('d/m/Y H:i', strtotime($cotizacion['fecha_modificacion'])) ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <a href="index.php?controller=cotizacion&action=historial&id=<?= $cotizacion['id'] ?>" 
                       class="btn btn-info me-2">
                        <i class="fas fa-history me-1"></i>Ver Historial
                    </a>
                    <a href="index.php?controller=cotizacion&action=show&id=<?= $cotizacion['id'] ?>" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>

            <!-- Alerta de advertencia -->
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>¡Atención!</strong> Al guardar los cambios, la versión actual se guardará en el historial. 
                Se recomienda agregar un motivo de modificación.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <!-- Formulario -->
            <form id="cotizacionForm" method="POST" action="index.php?controller=cotizacion&action=update">
                <input type="hidden" name="id" value="<?= $cotizacion['id'] ?>">
                
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <!-- Búsqueda de cliente con modal -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Cliente *</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light" id="cliente_display" 
                                           value="<?= htmlspecialchars($cotizacion['cliente']['nombre']) ?> - <?= htmlspecialchars($cotizacion['cliente']['documento']) ?>" 
                                           readonly>
                                    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#buscarClienteModal">
                                        <i class="fas fa-search me-1"></i>Cambiar Cliente
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Datos del Cliente (solo lectura) -->
                        <div class="row">
                            <input type="hidden" id="cliente_id" name="cliente_id" value="<?= $cotizacion['id_cliente'] ?>">
                            <div class="col-md-3">
                                <label for="tipo_documento" class="form-label">Tipo Doc.</label>
                                <input type="text" class="form-control bg-light" id="tipo_documento" 
                                       value="<?= htmlspecialchars($cotizacion['cliente']['tipo_documento'] ?? 'CC') ?>" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="cedula" class="form-label">Documento</label>
                                <input type="text" class="form-control bg-light" id="cedula" 
                                       value="<?= htmlspecialchars($cotizacion['cliente']['documento']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="nombre_apellidos" class="form-label">Nombre y Apellidos</label>
                                <input type="text" class="form-control bg-light" id="nombre_apellidos" 
                                       value="<?= htmlspecialchars($cotizacion['cliente']['nombre']) ?>" readonly>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control bg-light" id="telefono" 
                                       value="<?= htmlspecialchars($cotizacion['cliente']['telefono'] ?? '') ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="correo" class="form-label">Correo</label>
                                <input type="email" class="form-control bg-light" id="correo" 
                                       value="<?= htmlspecialchars($cotizacion['cliente']['correo'] ?? '') ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control bg-light" id="direccion" 
                                       value="<?= htmlspecialchars($cotizacion['cliente']['direccion'] ?? '') ?>" readonly>
                            </div>
                        </div>
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
                                    <?php if (!empty($cotizacion['detalles'])): ?>
                                        <?php foreach ($cotizacion['detalles'] as $index => $detalle): 
                                            $utilidad = 0;
                                            if ($detalle['precio_costo'] > 0) {
                                                $utilidad = (($detalle['precio_venta'] - $detalle['precio_costo']) / $detalle['precio_costo']) * 100;
                                            }
                                            $total = $detalle['precio_venta'] * $detalle['cantidad'];
                                        ?>
                                        <tr id="item_<?= $index ?>">
                                            <td>
                                                <?= htmlspecialchars($detalle['nombre']) ?>
                                                <input type="hidden" name="items[<?= $index ?>][id]" value="<?= $detalle['id_articulo'] ?>">
                                                <input type="hidden" name="items[<?= $index ?>][tipo]" value="articulo">
                                                <input type="hidden" name="items[<?= $index ?>][nombre]" value="<?= htmlspecialchars($detalle['nombre']) ?>">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="items[<?= $index ?>][cantidad]" 
                                                       value="<?= $detalle['cantidad'] ?>" min="1" onchange="recalcularFila(<?= $index ?>)">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="items[<?= $index ?>][precio]" 
                                                       value="<?= $detalle['precio_costo'] ?>" step="0.01" onchange="recalcularFila(<?= $index ?>)">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="items[<?= $index ?>][utilidad]" 
                                                       value="<?= round($utilidad, 2) ?>" min="0" max="100" onchange="recalcularFila(<?= $index ?>)">
                                            </td>
                                            <td>
                                                <strong class="total-fila">$<?= number_format($total, 0, ',', '.') ?></strong>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarItem(<?= $index ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr id="no_items">
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                                No hay items agregados. Use los botones de arriba para agregar.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                                        <td><strong id="total_general">$<?= number_format($cotizacion['total_venta'], 0, ',', '.') ?></strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Motivo de modificación -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Motivo de la Modificación</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="motivo_modificacion" class="form-label">
                                Explique brevemente por qué se modifica esta cotización *
                            </label>
                            <textarea class="form-control" id="motivo_modificacion" name="motivo_modificacion" 
                                      rows="3" placeholder="Ejemplo: Corrección de precios, cambio de cantidad solicitado por el cliente, error en artículos..." required></textarea>
                            <small class="text-muted">Este motivo quedará registrado en el historial de versiones</small>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="text-center mt-4 mb-5">
                    <button type="submit" class="btn btn-warning btn-lg me-3">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                    <a href="index.php?controller=cotizacion&action=show&id=<?= $cotizacion['id'] ?>" 
                       class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
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
                                    data-precio="<?= $articulo['precio_costo'] ?? 0 ?>">
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

<!-- Modal Buscar Cliente -->
<div class="modal fade" id="buscarClienteModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-search me-2"></i>Buscar Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control form-control-lg" id="filtro_clientes" 
                           placeholder="🔍 Buscar por nombre, documento, correo o teléfono...">
                </div>
                
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th width="100">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_clientes">
                            <?php foreach ($clientes as $cliente): ?>
                                <tr class="cliente-row" 
                                    data-nombre="<?= strtolower(htmlspecialchars($cliente['nombre'])) ?>"
                                    data-documento="<?= htmlspecialchars($cliente['documento']) ?>"
                                    data-correo="<?= strtolower(htmlspecialchars($cliente['correo'] ?? '')) ?>"
                                    data-telefono="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>">
                                    <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                                    <td><?= htmlspecialchars($cliente['tipo_documento'] ?? 'CC') ?> - <?= htmlspecialchars($cliente['documento']) ?></td>
                                    <td><?= htmlspecialchars($cliente['correo'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($cliente['telefono'] ?? '-') ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success w-100" 
                                                onclick="seleccionarClienteModal(<?= $cliente['id'] ?>, '<?= htmlspecialchars($cliente['nombre']) ?>', '<?= htmlspecialchars($cliente['documento']) ?>', '<?= htmlspecialchars($cliente['tipo_documento'] ?? 'CC') ?>', '<?= htmlspecialchars($cliente['correo'] ?? '') ?>', '<?= htmlspecialchars($cliente['telefono'] ?? '') ?>', '<?= htmlspecialchars($cliente['direccion'] ?? '') ?>')">
                                            <i class="fas fa-check"></i> Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
var itemCounter = <?= !empty($cotizacion['detalles']) ? count($cotizacion['detalles']) : 0 ?>;

$(document).ready(function() {
    // Ocultar fila "no_items" si hay items
    if ($('#items_body tr:not(#no_items)').length > 0) {
        $('#no_items').hide();
    }
    
    // Filtro en tiempo real para la tabla de clientes
    $('#filtro_clientes').on('keyup', function() {
        const filtro = $(this).val().toLowerCase();
        let visibles = 0;
        
        $('.cliente-row').each(function() {
            const nombre = $(this).data('nombre') || '';
            const documento = $(this).data('documento') || '';
            const correo = $(this).data('correo') || '';
            const telefono = $(this).data('telefono') || '';
            
            const texto = nombre + ' ' + documento + ' ' + correo + ' ' + telefono;
            
            if (texto.indexOf(filtro) > -1) {
                $(this).show();
                visibles++;
            } else {
                $(this).hide();
            }
        });
    });
    
    // Limpiar filtro al abrir el modal
    $('#buscarClienteModal').on('show.bs.modal', function() {
        $('#filtro_clientes').val('');
        $('.cliente-row').show();
    });
    
    // Recalcular totales al cargar
    recalcularTotales();
});

function seleccionarClienteModal(id, nombre, documento, tipo, correo, telefono, direccion) {
    $('#cliente_id').val(id);
    $('#nombre_apellidos').val(nombre);
    $('#cedula').val(documento);
    $('#tipo_documento').val(formatearTipoDocumento(tipo));
    $('#correo').val(correo);
    $('#telefono').val(telefono);
    $('#direccion').val(direccion);
    $('#cliente_display').val(nombre + ' - ' + formatearTipoDocumento(tipo) + ' ' + documento);
    
    $('#buscarClienteModal').modal('hide');
    
    if (typeof mostrarNotificacion === 'function') {
        mostrarNotificacion('Cliente actualizado correctamente', 'success');
    }
}

function formatearTipoDocumento(tipo) {
    const tipos = {
        'cedula': 'CC',
        'nit': 'NIT',
        'pasaporte': 'Pasaporte',
        'CC': 'CC',
        'NIT': 'NIT'
    };
    return tipos[tipo] || tipo.toUpperCase();
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
    const utilidad = 25; // Utilidad por defecto
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
    
    if ($('#items_body tr:not(#no_items)').length === 0) {
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
    return new Intl.NumberFormat('es-CO').format(Math.round(num));
}

function mostrarNotificacion(mensaje, tipo) {
    if (typeof toastr !== 'undefined') {
        if (tipo === 'success') toastr.success(mensaje);
        else if (tipo === 'error') toastr.error(mensaje);
        else if (tipo === 'warning') toastr.warning(mensaje);
        else toastr.info(mensaje);
    } else {
        console.log('[' + tipo + '] ' + mensaje);
    }
}
</script>
