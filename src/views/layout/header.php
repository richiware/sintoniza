<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($title) ?></title>
    <link href="//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <header class="p-3 text-bg-dark">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="/" class="d-flex align-items-center me-lg-2 text-white text-decoration-none fs-2">
                    <i class="bi bi-broadcast-pin"></i>
                </a>

                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="/" class="nav-link px-2 text-white d-flex align-items-center justify-content-center gap-2"><i class="bi bi-house"></i> Inicio</a></li>
                    <li><a href="admin" class="nav-link px-2 text-white d-flex align-items-center justify-content-center gap-2"><i class="bi bi-shield-lock"></i> Administração</a></li>
                </ul>

                <div class="text-end d-flex align-items-center justify-content-center gap-2">
                    <?php if ($gpodder->user || isAdmin()) { ?>
                        <a href="logout" class="btn btn-danger me-2 d-flex align-items-center justify-content-center gap-2"><i class="bi bi-box-arrow-right"></i> Sair</a>
                    <?php } else { ?>
                        <a href="login" class="btn btn-light me-2 d-flex align-items-center justify-content-center gap-2"><i class="bi bi-box-arrow-in-right"></i> Entrar</a>
                        <a href="register" class="btn btn-warning d-flex align-items-center justify-content-center gap-2"><i class="bi bi-person-plus"></i> Registrar</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </header>

    <div class="container py-5">
        <main>