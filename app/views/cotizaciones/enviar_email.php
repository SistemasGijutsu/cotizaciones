<?php
$title = 'Enviar Cotización por Email';
include_once 'app/views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-primary">
        <i class="fas fa-envelope me-2"></i>
        Enviar Cotización por Email
    </h2>
    <a href="index.php?controller=cotizacion&action=view&id=<?php echo $cotizacion['id']; ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-paper-plane me-2"></i>
                Configurar Envío
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?controller=cotizacion&action=enviarEmail&id=<?php echo $cotizacion['id']; ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-at me-1"></i>
                            Email de destino <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo $cliente['correo'] ?? ''; ?>" required>
                        <div class="form-text">
                            Email del cliente donde se enviará la cotización
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="asunto" class="form-label">
                            <i class="fas fa-tag me-1"></i>
                            Asunto del email <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="asunto" name="asunto" 
                               value="Cotización #<?php echo str_pad($cotizacion['id'], 6, '0', STR_PAD_LEFT); ?> - <?php echo $cliente['nombre']; ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="mensaje" class="form-label">
                            <i class="fas fa-comment me-1"></i>
                            Mensaje personalizado
                        </label>
                        <textarea class="form-control" id="mensaje" name="mensaje" rows="6" 
                                  placeholder="Escriba un mensaje personalizado para acompañar la cotización...">Estimado(a) <?php echo $cliente['nombre']; ?>,

Adjunto encontrará la cotización #<?php echo str_pad($cotizacion['id'], 6, '0', STR_PAD_LEFT); ?> solicitada, con fecha de <?php echo date('d/m/Y', strtotime($cotizacion['fecha'])); ?>.

Quedamos atentos a sus comentarios y esperamos poder servirle pronto.

Cordialmente,
Sistema de Cotizaciones</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php?controller=cotizacion&action=view&id=<?php echo $cotizacion['id']; ?>" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Enviar Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Información de la cotización -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>
                Detalles de la Cotización
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Número:</strong></td>
                        <td>#<?php echo str_pad($cotizacion['id'], 6, '0', STR_PAD_LEFT); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cliente:</strong></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?php echo $cliente['correo'] ?? 'No disponible'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Fecha:</strong></td>
                        <td><?php echo date('d/m/Y', strtotime($cotizacion['fecha'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Venta:</strong></td>
                        <td>
                            <strong class="text-success">
                                $<?php echo number_format($cotizacion['total_venta'], 0); ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Utilidad:</strong></td>
                        <td>
                            <strong class="text-info">
                                $<?php echo number_format($cotizacion['utilidad'], 0); ?>
                            </strong>
                        </td>
                    </tr>
                </table>
                </table>
            </div>
        </div>
        
        <!-- Acciones adicionales -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-tools me-2"></i>
                Acciones Adicionales
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="index.php?controller=cotizacion&action=generarPDF&id=<?php echo $cotizacion['id']; ?>" 
                       class="btn btn-outline-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf me-1"></i> Ver PDF
                    </a>
                    
                    <a href="index.php?controller=cotizacion&action=edit&id=<?php echo $cotizacion['id']; ?>" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-1"></i> Editar Cotización
                    </a>
                    
                    <button type="button" class="btn btn-outline-info btn-sm" 
                            onclick="previsualizarEmail()">
                        <i class="fas fa-eye me-1"></i> Previsualizar Email
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Consejos de envío -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-lightbulb me-2"></i>
                Consejos
            </div>
            <div class="card-body">
                <div class="small">
                    <p><strong>📧 Email:</strong> Verifique que el email del cliente sea correcto.</p>
                    <p><strong>✉️ Asunto:</strong> Use asuntos claros y profesionales.</p>
                    <p><strong>💬 Mensaje:</strong> Personalice el mensaje para cada cliente.</p>
                    <p><strong>⏰ Seguimiento:</strong> Haga seguimiento después del envío.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de previsualización -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>
                    Previsualización del Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Contenido de previsualización -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    const form = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const asuntoInput = document.getElementById('asunto');
    
    form.addEventListener('submit', function(e) {
        let valid = true;
        
        // Validar email
        if (!emailInput.value || !isValidEmail(emailInput.value)) {
            showFieldError(emailInput, 'Ingrese un email válido');
            valid = false;
        } else {
            hideFieldError(emailInput);
        }
        
        // Validar asunto
        if (!asuntoInput.value.trim()) {
            showFieldError(asuntoInput, 'El asunto es obligatorio');
            valid = false;
        } else {
            hideFieldError(asuntoInput);
        }
        
        if (!valid) {
            e.preventDefault();
        }
    });
    
    // Validación en tiempo real
    emailInput.addEventListener('blur', function() {
        if (this.value && !isValidEmail(this.value)) {
            showFieldError(this, 'Email no válido');
        } else {
            hideFieldError(this);
        }
    });
});

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

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

function previsualizarEmail() {
    const email = document.getElementById('email').value;
    const asunto = document.getElementById('asunto').value;
    const mensaje = document.getElementById('mensaje').value;
    
    if (!email || !asunto) {
        alert('Complete el email y asunto para ver la previsualización');
        return;
    }
    
    const previewContent = `
        <div style="border: 1px solid #ddd; border-radius: 5px; padding: 20px; background: #f9f9f9;">
            <div style="margin-bottom: 15px;">
                <strong>Para:</strong> ${email}<br>
                <strong>Asunto:</strong> ${asunto}
            </div>
            <hr>
            <div style="background: white; padding: 20px; border-radius: 5px;">
                <h4 style="color: #007bff;">Sistema de Cotizaciones Empresariales</h4>
                <div style="background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;">
                    <h5>Cotización #<?php echo $cotizacion['numero']; ?></h5>
                    <p><strong>Cliente:</strong> <?php echo $cliente['nombre']; ?></p>
                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($cotizacion['fecha'])); ?></p>
                </div>
                ${mensaje ? '<div style="margin: 20px 0;"><h6>Mensaje:</h6><p>' + mensaje.replace(/\n/g, '<br>') + '</p></div>' : ''}
                <p style="color: #666; font-size: 12px; margin-top: 30px;">
                    Este email fue generado automáticamente por el Sistema de Cotizaciones.
                </p>
            </div>
        </div>
    `;
    
    document.getElementById('previewContent').innerHTML = previewContent;
    
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}
</script>

<?php include_once 'app/views/layouts/footer.php'; ?>