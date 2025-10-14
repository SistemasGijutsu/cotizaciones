<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /mod_cotizacion/index.php?controller=auth&action=login');
    exit;
}

// Variables inicializadas desde el controlador
$cliente = $cliente ?? [];
$errors = $errors ?? [];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h4 mb-1">
                        <i class="fas fa-user-edit text-primary me-2"></i>
                        Editar Cliente
                    </h2>
                    <p class="text-muted mb-0">Modificar información del cliente</p>
                </div>
                <a href="/mod_cotizacion/index.php?controller=cliente&action=index" class="btn btn-outline-secondary">
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
        <div class="col-lg-8 col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Información del Cliente
                    </h5>
                </div>
                <div class="card-body">
                    <form action="/mod_cotizacion/index.php?controller=cliente&action=edit&id=<?= $cliente['id'] ?>" method="POST">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre Completo *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tipo_documento" class="form-label">Tipo Documento</label>
                                    <select class="form-select" id="tipo_documento" name="tipo_documento">
                                        <option value="cedula" <?= ($cliente['tipo_documento'] ?? '') === 'cedula' ? 'selected' : '' ?>>Cédula</option>
                                        <option value="nit" <?= ($cliente['tipo_documento'] ?? '') === 'nit' ? 'selected' : '' ?>>NIT</option>
                                        <option value="pasaporte" <?= ($cliente['tipo_documento'] ?? '') === 'pasaporte' ? 'selected' : '' ?>>Pasaporte</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="documento" class="form-label">Número Documento</label>
                                    <input type="text" class="form-control" id="documento" name="documento" 
                                           value="<?= htmlspecialchars($cliente['documento'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="empresa" class="form-label">Empresa</label>
                                    <input type="text" class="form-control" id="empresa" name="empresa" 
                                           value="<?= htmlspecialchars($cliente['empresa'] ?? '') ?>"
                                           placeholder="Opcional">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="correo" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="correo" name="correo" 
                                           value="<?= htmlspecialchars($cliente['correo'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                   value="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>">
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/mod_cotizacion/index.php?controller=cliente&action=index" 
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
        <div class="col-lg-4 col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Información Adicional
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Fecha de Registro</label>
                        <p class="mb-0"><?= date('d/m/Y H:i', strtotime($cliente['created_at'] ?? 'now')) ?></p>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="/mod_cotizacion/index.php?controller=cliente&action=show&id=<?= $cliente['id'] ?>" 
                           class="btn btn-outline-info">
                            <i class="fas fa-eye me-1"></i>
                            Ver Detalles Completos
                        </a>
                        
                        <a href="/mod_cotizacion/index.php?controller=cotizacion&action=create&cliente_id=<?= $cliente['id'] ?>" 
                           class="btn btn-outline-success">
                            <i class="fas fa-file-invoice-dollar me-1"></i>
                            Nueva Cotización
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
                            <li>El documento es único y no puede repetirse</li>
                            <li>Para empresas, use NIT como tipo de documento</li>
                            <li>El correo se usa para envío de cotizaciones</li>
                            <li>Todos los campos excepto nombre son opcionales</li>
                        </ul>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

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