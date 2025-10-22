<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Variables inicializadas desde el controlador
$articulos = $articulos ?? [];
$errors = $errors ?? [];
$data = $data ?? [];
$articulosSeleccionados = $articulosSeleccionados ?? [];
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
                    <form action="/mod_cotizacion/index.php?controller=paquete&action=store" method="POST" id="paqueteForm" enctype="multipart/form-data">
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
                              placeholder="Ej: Paquete Básico de Oficina" required
                              value="<?= isset($data['nombre']) ? htmlspecialchars($data['nombre']) : '' ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" 
                                                      rows="3" placeholder="Describe qué incluye este paquete..."><?= isset($data['descripcion']) ? htmlspecialchars($data['descripcion']) : '' ?></textarea>
                                            <small class="text-muted">Opcional: describe los beneficios o características del paquete</small>
                                        </div>

                                        <div class="alert alert-info mb-3">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Nota:</strong> El precio de venta se definirá al momento de agregar este paquete a una cotización.
                                        </div>

                                        <div class="mb-3">
                                            <label for="imagen" class="form-label">
                                                <i class="fas fa-image me-1"></i>
                                                Imagen del Paquete
                                            </label>
                                            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" onchange="previewImagen(this)">
                                            <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP (Máx. 5MB)</small>
                                            <div id="preview-container" class="mt-3" style="display: none;">
                                                <img id="preview-imagen" src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                                <button type="button" class="btn btn-sm btn-danger ms-2" onclick="eliminarPreview()">
                                                    <i class="fas fa-times"></i> Quitar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Resumen de costos -->
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-calculator me-2"></i>
                                            Resumen del Paquete
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div class="h5 text-info mb-1" id="total-costo">$0.00</div>
                                            <small class="text-muted">Costo Total de Artículos</small>
                                            <hr>
                                            <p class="text-muted small mb-0">
                                                <i class="fas fa-info-circle me-1"></i>
                                                El precio de venta se definirá al agregar a la cotización
                                            </p>
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
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Selecciona los artículos que deseas incluir en el paquete y especifica las cantidades. 
                    Luego haz clic en "Agregar Seleccionados".
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>Artículo</th>
                                <th>Stock</th>
                                <th>Precio Costo</th>
                                <th width="100">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody id="articulos-disponibles">
                            <?php foreach ($articulos as $articulo): ?>
                                <tr data-articulo-id="<?= $articulo['id'] ?>" class="articulo-row">
                                    <td>
                                        <input type="checkbox" class="form-check-input articulo-checkbox" 
                                               data-id="<?= $articulo['id'] ?>"
                                               data-nombre="<?= htmlspecialchars($articulo['nombre']) ?>"
                                               data-precio-costo="<?= $articulo['precio_costo'] ?>"
                                               data-stock="<?= $articulo['stock'] ?>">
                                    </td>
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
                                            <?= $articulo['stock'] ?> unid.
                                        </span>
                                    </td>
                                    <td>$<?= number_format($articulo['precio_costo'], 2) ?></td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm cantidad-modal" 
                                               data-id="<?= $articulo['id'] ?>" 
                                               min="1" max="<?= $articulo['stock'] ?>" 
                                               value="1" disabled>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="agregar-seleccionados">
                    <i class="fas fa-plus me-1"></i>
                    Agregar Seleccionados (<span id="contador-seleccionados">0</span>)
                </button>
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

// Función para agregar artículo al paquete (mantener compatibilidad)
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
}

// Manejar selección múltiple de artículos
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado correctamente');
    console.log('Checkboxes encontrados:', document.querySelectorAll('.articulo-checkbox').length);
    
    // Checkbox "Seleccionar todos"
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            console.log('Select all clicked');
            const isChecked = this.checked;
            document.querySelectorAll('.articulo-checkbox').forEach(function(checkbox) {
                checkbox.checked = isChecked;
            });
            document.querySelectorAll('.cantidad-modal').forEach(function(input) {
                input.disabled = !isChecked;
            });
            actualizarContadorSeleccionados();
        });
    }
    
    // Checkboxes individuales
    document.querySelectorAll('.articulo-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            console.log('Checkbox individual clicked');
            const isChecked = this.checked;
            const id = this.dataset.id;
            const cantidadInput = document.querySelector(`.cantidad-modal[data-id="${id}"]`);
            
            if (cantidadInput) {
                cantidadInput.disabled = !isChecked;
                
                if (!isChecked) {
                    cantidadInput.value = 1;
                }
            }
            
            // Actualizar estado del checkbox "Seleccionar todos"
            const totalCheckboxes = document.querySelectorAll('.articulo-checkbox').length;
            const checkedCheckboxes = document.querySelectorAll('.articulo-checkbox:checked').length;
            if (selectAll) {
                selectAll.checked = totalCheckboxes === checkedCheckboxes;
            }
            
            actualizarContadorSeleccionados();
        });
    });
    
    // Inicializar contador
    actualizarContadorSeleccionados();
    console.log('Eventos vinculados correctamente');
    
    // Botón para agregar artículos seleccionados
    const btnAgregarSeleccionados = document.getElementById('agregar-seleccionados');
    if (btnAgregarSeleccionados) {
        btnAgregarSeleccionados.addEventListener('click', function() {
            const seleccionados = [];
            
            document.querySelectorAll('.articulo-checkbox:checked').forEach(function(checkbox) {
                const id = checkbox.dataset.id;
                const nombre = checkbox.dataset.nombre;
                const precioCosto = parseFloat(checkbox.dataset.precioCosto);
                const stock = parseInt(checkbox.dataset.stock);
                const cantidadInput = document.querySelector(`.cantidad-modal[data-id="${id}"]`);
                const cantidad = cantidadInput ? parseInt(cantidadInput.value) || 1 : 1;
                
                // Validar cantidad vs stock
                if (cantidad > stock) {
                    alert(`La cantidad para "${nombre}" no puede ser mayor al stock disponible (${stock})`);
                    return;
                }
                
                // Verificar si ya está en el paquete
                if (!articulosSeleccionados.find(a => a.id == id)) {
                    seleccionados.push({
                        id: id,
                        nombre: nombre,
                        precio_costo: precioCosto,
                        cantidad: cantidad
                    });
                }
            });
            
            if (seleccionados.length === 0) {
                alert('No hay artículos nuevos seleccionados para agregar');
                return;
            }
            
            // Agregar todos los seleccionados
            articulosSeleccionados.push(...seleccionados);
            
            // Limpiar selecciones del modal
            document.querySelectorAll('.articulo-checkbox').forEach(function(checkbox) {
                checkbox.checked = false;
            });
            document.querySelectorAll('.cantidad-modal').forEach(function(input) {
                input.disabled = true;
                input.value = 1;
            });
            const selectAll = document.getElementById('select-all');
            if (selectAll) {
                selectAll.checked = false;
            }
            actualizarContadorSeleccionados();
            
            // Actualizar vista y cerrar modal
            actualizarVistaArticulos();
            actualizarTotales();
            
            // Cerrar modal usando Bootstrap
            const modal = bootstrap.Modal.getInstance(document.getElementById('articulosModal'));
            if (modal) {
                modal.hide();
            }
            
            // Mostrar mensaje de éxito
            const mensaje = seleccionados.length === 1 ? 
                `Se agregó 1 artículo al paquete` : 
                `Se agregaron ${seleccionados.length} artículos al paquete`;
            mostrarNotificacion(mensaje, 'success');
        });
    }
});

// Actualizar contador de artículos seleccionados
function actualizarContadorSeleccionados() {
    const cantidad = document.querySelectorAll('.articulo-checkbox:checked').length;
    console.log('Actualizando contador. Checkboxes marcados:', cantidad);
    const contador = document.getElementById('contador-seleccionados');
    const boton = document.getElementById('agregar-seleccionados');
    
    if (contador) {
        contador.textContent = cantidad;
    }
    if (boton) {
        boton.disabled = cantidad === 0;
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'info') {
    // Crear elemento de toast
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${tipo} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i>
                    ${mensaje}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    // Agregar al contenedor de toasts (crear si no existe)
    if (!$('#toast-container').length) {
        $('body').append('<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 11000;"></div>');
    }
    
    $('#toast-container').append(toastHtml);
    
    // Mostrar toast
    const toast = new bootstrap.Toast(document.getElementById(toastId));
    toast.show();
    
    // Remover elemento después de que se oculte
    setTimeout(() => {
        $(`#${toastId}`).remove();
    }, 5000);
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
                    <div class="col-md-3">
                        <label class="form-label">Costo Unit.</label>
                        <div class="form-control-plaintext">$${parseFloat(articulo.precio_costo).toFixed(2)}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Costo Total</label>
                        <div class="form-control-plaintext">$${(parseFloat(articulo.precio_costo) * parseInt(articulo.cantidad)).toFixed(2)}</div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarArticulo(${index})" title="Eliminar artículo">
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
    const articuloEliminado = articulosSeleccionados[index];
    articulosSeleccionados.splice(index, 1);
    actualizarVistaArticulos();
    actualizarTotales();
    
    mostrarNotificacion(`Se eliminó "${articuloEliminado.nombre}" del paquete`, 'warning');
}

// Actualizar totales del paquete
function actualizarTotales() {
    let totalCosto = 0;
    
    articulosSeleccionados.forEach(articulo => {
        totalCosto += parseFloat(articulo.precio_costo) * parseInt(articulo.cantidad);
    });
    
    $('#total-costo').text('$' + totalCosto.toFixed(2));
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

// Preview de imagen
function previewImagen(input) {
    const previewContainer = document.getElementById('preview-container');
    const previewImg = document.getElementById('preview-imagen');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function eliminarPreview() {
    const input = document.getElementById('imagen');
    const previewContainer = document.getElementById('preview-container');
    const previewImg = document.getElementById('preview-imagen');
    
    input.value = '';
    previewImg.src = '';
    previewContainer.style.display = 'none';
}

// Inicializar articulosSeleccionados desde el servidor si se recargó la vista con errores
<?php if (!empty($articulosSeleccionados) && is_array($articulosSeleccionados)): ?>
// Mapear la estructura enviada por el servidor a la estructura que usa el JS
const seleccionadosServer = <?= json_encode(array_values($articulosSeleccionados)) ?>;
seleccionadosServer.forEach(function(item) {
    // Intentar obtener nombre, precios desde la tabla de disponibles
    const row = document.querySelector('tr[data-articulo-id="' + item.id_articulo + '"]');
    let nombre = '';
    let precio_costo = 0;
    let precio_venta = 0;
    if (row) {
        nombre = row.querySelector('strong') ? row.querySelector('strong').innerText.trim() : '';
        // parsear precios desde las celdas (tercera columna para costo)
        const cols = row.querySelectorAll('td');
        if (cols.length >= 4) {
            precio_costo = parseFloat(cols[3].innerText.replace(/[^0-9\.,-]/g, '').replace(',', '.')) || 0;
        }
    }
    articulosSeleccionados.push({
        id: item.id_articulo,
        nombre: nombre,
        precio_costo: precio_costo,
        cantidad: item.cantidad
    });
});
if (articulosSeleccionados.length > 0) {
    actualizarVistaArticulos();
    actualizarTotales();
}
<?php endif; ?>

// Inicializar contador al cargar la página
$(document).ready(function() {
    actualizarContadorSeleccionados();
});
</script>