<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-body p-4">
                <?php if (!empty($_POST) && $_POST['admin_password'] !== ADMIN_PASSWORD): ?>
                    <div class="alert alert-danger">Senha inválida!</div>
                <?php endif ?>

                <form method="post" action="">
                    <h2 class="card-title text-center mb-4 fs-3">Administração</h2>
                    <div class="mb-3">
                        <label for="admin_password" class="form-label">Senha</label>
                        <input type="password" class="form-control" required 
                            name="admin_password" 
                            id="admin_password" 
                            autocomplete="current-password" />
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-shield-lock"></i> Entrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 