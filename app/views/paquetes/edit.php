<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Variables inicializadas desde el controlador
$paquete = $paquete ?? [];
$articulos = $articulos ?? [];
$articulosPaquete = $articulosPaquete ?? [];
$errors = $errors ?? [];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 mb-1">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Editar Paquete
                    </h2>
                    <p class="text-muted mb-0">Modificar paquete de artículos</p>
                </div>
                <a href="/mod_cotizacion/index.php?controller=paquete&action=index" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Volver a Lista
                </a>
            </div>
        </div>
    </div>

    <form action="/mod_cotizacion/index.php?controller=paquete&action=edit&id=<?= $paquete['id'] ?>" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Información Básica</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($paquete['nombre'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($paquete['descripcion'] ?? '') ?></textarea>
                            <small class="text-muted">Opcional: describe los beneficios o características del paquete</small>
                        </div>
                        
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> El precio de venta se define al agregar este paquete a una cotización.
                        </div>
                        
                        <div class="mb-3">
                            <label for="imagen" class="form-label">
                                <i class="fas fa-image me-1"></i>
                                Imagen del Paquete
                            </label>
                            <?php if (!empty($paquete['imagen'])): ?>
                                <div class="mb-2">
                                    <img src="/mod_cotizacion/public/images/paquetes/<?= htmlspecialchars($paquete['imagen']) ?>" 
                                         alt="Imagen actual" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    <p class="text-muted small mt-1">Imagen actual</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" onchange="previewImagen(this)">
                            <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP (Máx. 5MB)</small>
                            <div id="preview-container" class="mt-3" style="display: none;">
                                <p class="text-success small"><strong>Nueva imagen:</strong></p>
                                <img id="preview-imagen" src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                <button type="button" class="btn btn-sm btn-danger ms-2" onclick="eliminarPreview()">
                                    <i class="fas fa-times"></i> Quitar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">Artículos del Paquete</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Artículo</th>
                                        <th>Cantidad</th>
                                        <th>Precio Costo</th>
                                        <th>Precio Venta</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($articulosPaquete as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['nombre']) ?></td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" 
                                                       name="articulos[<?= htmlspecialchars($item['id_articulo'] ?? '') ?>]" 
                                                       value="<?= htmlspecialchars($item['cantidad'] ?? 1) ?>" min="1" style="width: 80px;">
                                            </td>
                                            <?php
                                                // Asegurar valores numéricos por defecto para evitar warnings y pasar null a number_format
                                                $precio_costo = isset($item['precio_costo']) && $item['precio_costo'] !== null ? (float)$item['precio_costo'] : 0.0;
                                                $precio_venta = isset($item['precio_venta']) && $item['precio_venta'] !== null ? (float)$item['precio_venta'] : 0.0;
                                                $cantidad = isset($item['cantidad']) && $item['cantidad'] !== null ? (int)$item['cantidad'] : 1;
                                            ?>
                                            <td>$<?= number_format($precio_costo, 2) ?></td>
                                            <td>$<?= number_format($precio_venta, 2) ?></td>
                                            <td>$<?= number_format($precio_venta * $cantidad, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="/mod_cotizacion/index.php?controller=paquete&action=index" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>

<script>
// Preview de imagen
function previewImagen(input) {
    const previewContainer = document.getElementById("preview-container");
    const previewImg = document.getElementById("preview-imagen");
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = "block";
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function eliminarPreview() {
    const input = document.getElementById("imagen");
    const previewContainer = document.getElementById("preview-container");
    const previewImg = document.getElementById("preview-imagen");
    
    input.value = "";
    previewImg.src = "";
    previewContainer.style.display = "none";
}
</script>
