<?php
$title = 'Crear Nuevo Artículo';
include_once 'app/views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary">
        <i class="fas fa-plus me-2"></i>
        Crear Nuevo Artículo
    </h2>
    <a href="index.php?controller=articulo" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-box me-2"></i>
                Información del Artículo
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?controller=articulo&action=create" id="articuloForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-tag me-1"></i>
                                    Nombre del artículo <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="codigo" class="form-label">
                                    <i class="fas fa-barcode me-1"></i>
                                    Código del artículo
                                </label>
                                <input type="text" class="form-control" id="codigo" name="codigo" 
                                       placeholder="Opcional - se genera automático">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left me-1"></i>
                            Descripción
                        </label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                  placeholder="Descripción detallada del artículo..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="precio_costo" class="form-label">
                                    <i class="fas fa-dollar-sign me-1 text-primary"></i>
                                    Precio de Costo <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio_costo" name="precio_costo" 
                                           step="0.01" min="0" required>
                                </div>
                                <small class="text-muted">Precio de compra o costo del artículo</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">
                                    <i class="fas fa-boxes me-1"></i>
                                    Stock Inicial
                                </label>
                                <input type="number" class="form-control" id="stock" name="stock" 
                                       min="0" value="0">
                                <small class="text-muted">Cantidad inicial en inventario</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php?controller=articulo" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Guardar Artículo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Consejos -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-lightbulb me-2"></i>
                Consejos
            </div>
            <div class="card-body">
                <div class="small">
                    <p><strong>💰 Precio Costo:</strong> Incluya todos los costos (transporte, impuestos, etc.)</p>
                    <p><strong>� Stock:</strong> Puede iniciar en 0 y actualizar después al recibir mercancía</p>
                    <p><strong>� Código:</strong> Use códigos únicos para facilitar la búsqueda</p>
                    <p><strong>� Descripción:</strong> Sea claro y específico para identificar fácilmente</p>
                    <p><strong>� Paquetes:</strong> Los artículos se venden como parte de paquetes con precio específico</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    const form = document.getElementById('articuloForm');
    form.addEventListener('submit', function(e) {
        let valid = true;
        
        // Validar nombre
        const nombre = document.getElementById('nombre');
        if (!nombre.value.trim()) {
            showFieldError(nombre, 'El nombre es obligatorio');
            valid = false;
        } else {
            hideFieldError(nombre);
        }
        
        // Validar precio costo
        const costo = parseFloat(document.getElementById('precio_costo').value);
        
        if (!costo || costo <= 0) {
            showFieldError(document.getElementById('precio_costo'), 'El precio de costo debe ser mayor a 0');
            valid = false;
        } else {
            hideFieldError(document.getElementById('precio_costo'));
        }
        
        if (!valid) {
            e.preventDefault();
        }
    });
    
    // Generar código automático basado en nombre
    document.getElementById('nombre').addEventListener('blur', function() {
        const codigoInput = document.getElementById('codigo');
        if (!codigoInput.value && this.value) {
            const codigo = 'ART-' + this.value.substring(0, 3).toUpperCase() + '-' + 
                          Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            codigoInput.value = codigo;
        }
    });
});

function showFieldError(field, message) {
    field.classList.add('is-invalid');
    
    let errorElement = field.parentNode.querySelector('.invalid-feedback');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'invalid-feedback';
        field.parentNode.appendChild(errorElement);
    }
    errorElement.textContent = message;
}

function hideFieldError(field) {
    field.classList.remove('is-invalid');
    const errorElement = field.parentNode.querySelector('.invalid-feedback');
    if (errorElement) {
        errorElement.remove();
    }
}
</script>

<?php include_once 'app/views/layouts/footer.php'; ?>