<?php $title = 'Nuevo Cliente'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-user-plus me-2"></i>
        Nuevo Cliente
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?controller=cliente&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información del Cliente</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Errores de validación:</h6>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="index.php?controller=cliente&action=create">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-user me-1"></i>
                                    Nombre Completo *
                                </label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?php echo isset($data['nombre']) ? htmlspecialchars($data['nombre']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correo" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>
                                    Correo Electrónico *
                                </label>
                                <input type="email" class="form-control" id="correo" name="correo" 
                                       value="<?php echo isset($data['correo']) ? htmlspecialchars($data['correo']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">
                                    <i class="fas fa-phone me-1"></i>
                                    Teléfono *
                                </label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       value="<?php echo isset($data['telefono']) ? htmlspecialchars($data['telefono']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="direccion" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    Dirección
                                </label>
                                <input type="text" class="form-control" id="direccion" name="direccion" 
                                       value="<?php echo isset($data['direccion']) ? htmlspecialchars($data['direccion']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php?controller=cliente&action=index" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-1"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Guardar Cliente
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
                    Información
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-lightbulb me-2"></i>Consejos</h6>
                    <ul class="mb-0 small">
                        <li>Los campos marcados con (*) son obligatorios</li>
                        <li>Asegúrese de que el correo electrónico sea válido</li>
                        <li>El teléfono será usado para contacto directo</li>
                        <li>La dirección ayuda en el seguimiento de entregas</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Importante</h6>
                    <p class="mb-0 small">
                        Una vez creado el cliente, podrá generar cotizaciones y realizar seguimiento 
                        de todas sus transacciones comerciales.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>