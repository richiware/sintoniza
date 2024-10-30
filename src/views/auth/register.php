<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-body p-4">
                <form method="post" action="">
                    <h2 class="card-title text-center mb-4">Registrar</h2>
                    <?php if ($error): ?>
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                    <?php endif ?>
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuário</label>
                        <input type="text" class="form-control" name="username" required id="username" />
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha (minimo de 8 caracteres)</label>
                        <input type="password" class="form-control" minlength="8" required name="password" id="password" />
                    </div>
                    <?php if (!isAdmin()): ?>
                    <div class="mb-3">
                        <label class="form-label">Captcha</label>
                        <div class="alert alert-info">
                            Preencha com seguinte número: <?= $gpodder->generateCaptcha() ?>
                        </div>
                        <input type="text" class="form-control" name="captcha" required id="captcha" />
                    </div>
                    <?php endif ?>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-person-plus"></i> Registrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 