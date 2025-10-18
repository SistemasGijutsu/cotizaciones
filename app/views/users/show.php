<?php $title = 'Detalle Usuario'; ?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-user me-2"></i>Detalle Usuario</h1>
    <div>
        <a href="index.php?controller=user&action=index" class="btn btn-secondary">Volver</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">ID</dt>
            <dd class="col-sm-9"><?php echo $user['id']; ?></dd>

            <dt class="col-sm-3">Usuario</dt>
            <dd class="col-sm-9"><?php echo htmlspecialchars($user['username']); ?></dd>

            <dt class="col-sm-3">Nombre Completo</dt>
            <dd class="col-sm-9"><?php echo htmlspecialchars($user['nombre_completo'] ?? ''); ?></dd>

            <dt class="col-sm-3">Email</dt>
            <dd class="col-sm-9"><?php echo htmlspecialchars($user['email']); ?></dd>

            <dt class="col-sm-3">Creado</dt>
            <dd class="col-sm-9"><?php echo $user['created_at']; ?></dd>

            <dt class="col-sm-3">Actualizado</dt>
            <dd class="col-sm-9"><?php echo $user['updated_at']; ?></dd>
        </dl>
    </div>
</div>
