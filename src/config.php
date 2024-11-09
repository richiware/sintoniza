<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define("VERSION", "1.6.1");

define("DB_HOST", isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'localhost');

define("DB_USER", isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : 'root');

define("DB_PASS", isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : '');

define("DB_NAME", isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : 'gpodder');

define("BASE_URL", isset($_ENV['BASE_URL']) ? $_ENV['BASE_URL'] : '');

define("TITLE", isset($_ENV['TITLE']) ? $_ENV['TITLE'] : 'Awesome gPodder');

define("ENABLE_SUBSCRIPTIONS", isset($_ENV['ENABLE_SUBSCRIPTIONS']) ? filter_var($_ENV['ENABLE_SUBSCRIPTIONS'], FILTER_VALIDATE_BOOLEAN) : false);

define("DEBUG", isset($_ENV['DEBUG']) && $_ENV['DEBUG'] == true ? __DIR__ . '/logs/debug.log' : null);

define("DISABLE_USER_METADATA_UPDATE", isset($_ENV['DISABLE_USER_METADATA_UPDATE']) ? filter_var($_ENV['DISABLE_USER_METADATA_UPDATE'], FILTER_VALIDATE_BOOLEAN) : false);