<?php

if (!empty($_POST)) {
    if (!$gpodder->checkCaptcha($_POST['captcha'] ?? '', $_POST['cc'] ?? '')) {
        echo '<div class="alert alert-danger" role="alert">'.__('messages.invalid_captcha').'</div>';
    }
    elseif ($error = $gpodder->subscribe($_POST['username'] ?? '', $_POST['password'] ?? '', $_POST['email'] ?? '')) {
        printf('<div class="alert alert-danger" role="alert">%s</div>', htmlspecialchars($error));
    }
    else {
        echo '<div class="alert alert-success" role="alert">'.__('admin.user_registered').'</div>';
        echo '<p><a href="login" class="btn btn-light me-2 d-flex align-items-center justify-content-center gap-2"><i class="bi bi-box-arrow-in-right"></i> '.__('general.login').'</a></p>';
    }
}

echo '<div class="row justify-content-center">
<div class="col-md-6 col-lg-4">
<div class="card shadow">
<div class="card-body p-4">
<form method="post" action="">
<h2 class="card-title text-center mb-4">'.__('general.register').'</h2>
<div class="mb-3">
<label for="username" class="form-label">'.__('general.username').'</label>
<input type="text" class="form-control" name="username" required id="username" />
</div>
<div class="mb-3">
<label for="password" class="form-label">'.__('general.min_password_length').'</label>
<input type="password" class="form-control" minlength="8" required name="password" id="password" />
</div>
<div class="mb-3">
<label for="email" class="form-label">'.__('general.email').'</label>
<input type="email" class="form-control" minlength="8" required name="email" id="email" />
</div>
<div class="mb-3">
<label class="form-label">Captcha</label>
<div class="alert alert-info">
'.__('messages.fill_captcha').' '.$gpodder->generateCaptcha().'
</div>
<input type="text" class="form-control" name="captcha" required id="captcha" />
</div>
<div class="d-grid">
<button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
<i class="bi bi-person-plus"></i> '.__('general.register').'
</button>
</div>
</form>
</div>
</div>
</div>
</div>';
