<?php $title = 'Editar Usuario'; ?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-user-edit me-2"></i>Editar Usuario</h1>
    <div>
        <a href="index.php?controller=user&action=index" class="btn btn-secondary">Volver</a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
                <li><?php echo $err; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?controller=user&action=update">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Usuario *</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                <div id="usernameFeedback" class="form-text text-danger d-none">El usuario ya existe</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre Completo</label>
                <input type="text" name="nombre_completo" class="form-control" value="<?php echo htmlspecialchars($user['nombre_completo'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nueva Contraseña (opcional)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary" type="submit">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
async function checkUnique(field, value, excludeId) {
    const params = new URLSearchParams({ field, value, exclude_id: excludeId });
    const res = await fetch('index.php?controller=user&action=checkUnique&' + params.toString());
    return await res.json();
}

document.getElementById('username').addEventListener('input', async function() {
    const v = this.value.trim();
    if (!v) return;
    const r = await checkUnique('username', v, <?php echo intval($user['id']); ?>);
    const fb = document.getElementById('usernameFeedback');
    if (!r.unique) {
        fb.classList.remove('d-none');
    } else {
        fb.classList.add('d-none');
    }
});
</script>
