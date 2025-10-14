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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="precio_costo" class="form-label">
                                    <i class="fas fa-dollar-sign me-1 text-danger"></i>
                                    Precio de Costo <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio_costo" name="precio_costo" 
                                           step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="precio_venta" class="form-label">
                                    <i class="fas fa-dollar-sign me-1 text-success"></i>
                                    Precio de Venta <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="precio_venta" name="precio_venta" 
                                           step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock" class="form-label">
                                    <i class="fas fa-boxes me-1"></i>
                                    Stock Inicial
                                </label>
                                <input type="number" class="form-control" id="stock" name="stock" 
                                       min="0" value="0">
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
        <!-- Calculadora de utilidad -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calculator me-2"></i>
                Calculadora de Utilidad
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Utilidad Monetaria</label>
                    <div class="h4 text-success" id="utilidad-monetaria">$0</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Utilidad Porcentual</label>
                    <div class="h4 text-info" id="utilidad-porcentual">0%</div>
                </div>
                
                <div class="alert alert-info">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        La utilidad se calcula automáticamente basada en los precios ingresados.
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Consejos -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-lightbulb me-2"></i>
                Consejos
            </div>
            <div class="card-body">
                <div class="small">
                    <p><strong>💰 Precios:</strong> Asegúrese de incluir todos los costos (transporte, impuestos, etc.)</p>
                    <p><strong>📊 Utilidad:</strong> Considere márgenes del 20-50% según el tipo de producto</p>
                    <p><strong>📦 Stock:</strong> Puede iniciar en 0 y actualizar después</p>
                    <p><strong>🔍 Código:</strong> Use códigos únicos para facilitar la búsqueda</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const precioCostoInput = document.getElementById('precio_costo');
    const precioVentaInput = document.getElementById('precio_venta');
    const utilidadMonetaria = document.getElementById('utilidad-monetaria');
    const utilidadPorcentual = document.getElementById('utilidad-porcentual');
    
    // Función para calcular utilidad
    function calcularUtilidad() {
        const costo = parseFloat(precioCostoInput.value) || 0;
        const venta = parseFloat(precioVentaInput.value) || 0;
        
        const utilidadMont = venta - costo;
        const utilidadPorc = costo > 0 ? (utilidadMont / costo) * 100 : 0;
        
        utilidadMonetaria.textContent = '$' + new Intl.NumberFormat('es-CO').format(utilidadMont);
        utilidadPorcentual.textContent = utilidadPorc.toFixed(1) + '%';
        
        // Cambiar colores según utilidad
        if (utilidadMont > 0) {
            utilidadMonetaria.className = 'h4 text-success';
            utilidadPorcentual.className = 'h4 text-success';
        } else if (utilidadMont < 0) {
            utilidadMonetaria.className = 'h4 text-danger';
            utilidadPorcentual.className = 'h4 text-danger';
        } else {
            utilidadMonetaria.className = 'h4 text-muted';
            utilidadPorcentual.className = 'h4 text-muted';
        }
    }
    
    // Event listeners para cálculo en tiempo real
    precioCostoInput.addEventListener('input', calcularUtilidad);
    precioVentaInput.addEventListener('input', calcularUtilidad);
    
    // Sugerir precio de venta basado en margen
    precioCostoInput.addEventListener('blur', function() {
        const costo = parseFloat(this.value);
        if (costo > 0 && !precioVentaInput.value) {
            // Sugerir 30% de margen
            const ventaSugerida = costo * 1.3;
            precioVentaInput.value = ventaSugerida.toFixed(2);
            calcularUtilidad();
        }
    });
    
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
        
        // Validar precios
        const costo = parseFloat(precioCostoInput.value);
        const venta = parseFloat(precioVentaInput.value);
        
        if (costo <= 0) {
            showFieldError(precioCostoInput, 'El precio de costo debe ser mayor a 0');
            valid = false;
        } else {
            hideFieldError(precioCostoInput);
        }
        
        if (venta <= 0) {
            showFieldError(precioVentaInput, 'El precio de venta debe ser mayor a 0');
            valid = false;
        } else {
            hideFieldError(precioVentaInput);
        }
        
        // Advertir si venta < costo
        if (venta > 0 && costo > 0 && venta < costo) {
            if (!confirm('El precio de venta es menor al costo. ¿Está seguro de continuar?')) {
                valid = false;
            }
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