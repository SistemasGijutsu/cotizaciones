<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Variables inicializadas desde el controlador
$articulo = $articulo ?? [];
$errors = $errors ?? [];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 mb-1">
                        <i class="fas fa-box-open text-primary me-2"></i>
                        Editar Artículo
                    </h2>
                    <p class="text-muted mb-0">Modificar información del artículo</p>
                </div>
                <a href="/mod_cotizacion/index.php?controller=articulo&action=index" class="btn btn-outline-secondary">
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
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Información del Artículo
                    </h5>
                </div>
                <div class="card-body">
                    <form action="/mod_cotizacion/index.php?controller=articulo&action=edit&id=<?= $articulo['id'] ?>" method="POST" id="articuloForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Artículo *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?= htmlspecialchars($articulo['nombre'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="codigo" class="form-label">Código</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" 
                                           value="<?= htmlspecialchars($articulo['codigo'] ?? 'ART-' . $articulo['id']) ?>" readonly>
                                    <small class="text-muted">Código de identificación único</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" 
                                      rows="3" placeholder="Descripción detallada del artículo"><?= htmlspecialchars($articulo['descripcion'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio_costo" class="form-label">Precio de Costo *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="precio_costo" name="precio_costo" 
                                               value="<?= htmlspecialchars($articulo['precio_costo'] ?? '') ?>" 
                                               step="0.01" min="0" required>
                                    </div>
                                    <small class="text-muted">Costo de compra o adquisición</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock Disponible *</label>
                                    <input type="number" class="form-control" id="stock" name="stock" 
                                           value="<?= htmlspecialchars($articulo['stock'] ?? '') ?>" 
                                           min="0" required>
                                    <small class="text-muted">Cantidad actual en inventario</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/mod_cotizacion/index.php?controller=articulo&action=index" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel lateral con información adicional -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Información del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Fecha de Creación</label>
                        <p class="mb-0"><?= date('d/m/Y H:i', strtotime($articulo['created_at'] ?? 'now')) ?></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Estado del Stock</label>
                        <?php 
                        $stock = $articulo['stock'] ?? 0;
                        if ($stock > 10) {
                            echo '<span class="badge bg-success">Stock Óptimo</span>';
                        } elseif ($stock > 0) {
                            echo '<span class="badge bg-warning text-dark">Stock Bajo</span>';
                        } else {
                            echo '<span class="badge bg-danger">Sin Stock</span>';
                        }
                        ?>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="/mod_cotizacion/index.php?controller=articulo&action=show&id=<?= $articulo['id'] ?>" 
                           class="btn btn-outline-info">
                            <i class="fas fa-eye me-1"></i>
                            Ver Detalles Completos
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tips de uso -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Consejos
                    </h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <ul class="mb-0">
                            <li>Actualice el stock al recibir o vender mercancía</li>
                            <li>Mantenga el precio de costo actualizado</li>
                            <li>Una descripción clara ayuda en la identificación</li>
                            <li>Los artículos se venden dentro de paquetes</li>
                        </ul>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validación del formulario
document.getElementById('articuloForm').addEventListener('submit', function(e) {
    const costo = parseFloat(document.getElementById('precio_costo').value) || 0;
    
    if (costo <= 0) {
        e.preventDefault();
        alert('El precio de costo debe ser mayor a 0');
        document.getElementById('precio_costo').focus();
        return false;
    }
});
</script>

<style>
@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 .btn {
        margin-bottom: 0.5rem;
    }
}
</style>