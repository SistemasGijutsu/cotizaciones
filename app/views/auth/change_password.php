<?php $title = 'Cambiar Contraseña'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-key me-2"></i>
        Cambiar Contraseña
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Volver
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-shield-alt me-2"></i>
                    Seguridad de la Cuenta
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?controller=auth&action=changePassword">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">
                            <i class="fas fa-lock me-1"></i>
                            Contraseña Actual *
                        </label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">
                            <i class="fas fa-key me-1"></i>
                            Nueva Contraseña *
                        </label>
                        <input type="password" class="form-control" id="new_password" name="new_password" 
                               minlength="6" required>
                        <div class="form-text">
                            La contraseña debe tener al menos 6 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-check-double me-1"></i>
                            Confirmar Nueva Contraseña *
                        </label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               minlength="6" required>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-1"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Recomendaciones de Seguridad
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <ul class="mb-0 small">
                        <li>Use al menos 8 caracteres</li>
                        <li>Combine letras, números y símbolos</li>
                        <li>No use información personal</li>
                        <li>Cambie su contraseña regularmente</li>
                        <li>No comparta sus credenciales</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validar que las contraseñas coincidan
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword && confirmPassword) {
        if (newPassword !== confirmPassword) {
            this.setCustomValidity('Las contraseñas no coinciden');
        } else {
            this.setCustomValidity('');
        }
    }
});

// Mostrar fortaleza de la contraseña
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    let strength = 0;
    
    if (password.length >= 6) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    const colors = ['danger', 'warning', 'info', 'primary', 'success'];
    const texts = ['Muy débil', 'Débil', 'Regular', 'Buena', 'Excelente'];
    
    // Remover clases anteriores
    this.classList.remove('border-danger', 'border-warning', 'border-info', 'border-primary', 'border-success');
    
    if (password.length > 0) {
        this.classList.add('border-' + colors[strength - 1]);
    }
});
</script>