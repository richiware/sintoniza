
<?php
if ($error) {
    printf('<div class="alert alert-danger" role="alert">%s</div>', htmlspecialchars($error));
}

if (isset($_GET['token'])) {
    printf('<div class="alert alert-warning" role="alert">Um aplicativo está solicitando acesso à sua conta.</div>');
}

echo '<div class="row justify-content-center">
<div class="col-md-6 col-lg-4">
<div class="card shadow">
<div class="card-body p-4">
<form method="post" action="">
<h2 class="card-title text-center mb-4">Entrar</h2>
<div class="mb-3">
<label for="login" class="form-label">Usuário</label>
<input type="text" class="form-control" required name="login" id="login" />
</div>
<div class="mb-3">
<label for="password" class="form-label">Senha</label>
<input type="password" class="form-control" required name="password" id="password" />
</div>
<div class="d-grid">
<button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
<i class="bi bi-box-arrow-in-right"></i> Entrar 
</button>
</div>
</form>
</div>
</div>
</div>
</div>';