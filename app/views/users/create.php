<?php $title = 'Nuevo Usuario'; ?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-user-plus me-2"></i>Nuevo Usuario</h1>
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
        <form method="POST" action="index.php?controller=user&action=store">
            <div class="mb-3">
                <label class="form-label">Usuario *</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($old['username'] ?? ''); ?>" required>
                <div id="usernameFeedback" class="form-text text-danger d-none">El usuario ya existe</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre Completo</label>
                <input type="text" name="nombre_completo" class="form-control" value="<?php echo htmlspecialchars($old['nombre_completo'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña *</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary" type="submit">Crear Usuario</button>
            </div>
        </form>
    </div>
</div>

<script>
// Validación AJAX de unicidad
async function checkUnique(field, value) {
    const params = new URLSearchParams({ field, value });
    const res = await fetch('index.php?controller=user&action=checkUnique&' + params.toString());
    return await res.json();
}

document.getElementById('username').addEventListener('input', async function() {
    const v = this.value.trim();
    if (!v) return;
    const r = await checkUnique('username', v);
    const fb = document.getElementById('usernameFeedback');
    if (!r.unique) {
        fb.classList.remove('d-none');
    } else {
        fb.classList.add('d-none');
    }
});
</script>
