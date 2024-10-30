<?php

function view(string $name, array $data = []): void
{
    
    extract($data);

    if (!isset($title)) {
        $title = defined('TITLE') ? TITLE : 'oPodSync';
    } else {
        $title = TITLE .' | '. $title;
    }

    require __DIR__ . '/../views/layout/header.php';

    require __DIR__ . '/../views/' . $name . '.php';

    require __DIR__ . '/../views/layout/footer.php';
}

function isAdmin(): bool
{
    return !empty($_SESSION['is_admin']);
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
} 