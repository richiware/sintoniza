<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

return [
    'database' => [
        'host' => $_ENV['DB_HOST'],
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD'],
        'dbname' => $_ENV['DB_NAME'],
        'charset' => 'utf8mb4'
    ],
    'admin_password' => $_ENV['ADMIN_PASSWORD'],
    'site' => [
        'url' => $_ENV['SITE_URL'],
        'name' => $_ENV['SITE_NAME'],
        'enable_subscription' => isset($_ENV['ENABLE_SUBSCRIPTION']) ? $_ENV['ENABLE_SUBSCRIPTION'] : false,
        'disable_user_metadata_update' => isset($_ENV['DISABLE_USER_METADATA_UPDATE']) ? $_ENV['DISABLE_USER_METADATA_UPDATE'] : false,
        'debug' => isset($_ENV['DEBUG']) ? $_ENV['DEBUG'] : false
    ]
];
