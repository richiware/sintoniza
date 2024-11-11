<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Define
$version = "1.9";
define("VERSION", $version);
define("DB_HOST", isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'localhost');
define("DB_USER", isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : 'root');
define("DB_PASS", isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : '');
define("DB_NAME", isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : 'gpodder');
define("BASE_URL", isset($_ENV['BASE_URL']) ? $_ENV['BASE_URL'] : '');
define("TITLE", isset($_ENV['TITLE']) ? $_ENV['TITLE'] : 'Awesome gPodder');
define("ENABLE_SUBSCRIPTIONS", isset($_ENV['ENABLE_SUBSCRIPTIONS']) ? filter_var($_ENV['ENABLE_SUBSCRIPTIONS'], FILTER_VALIDATE_BOOLEAN) : false);
define("DEBUG", isset($_ENV['DEBUG']) && $_ENV['DEBUG'] == true ? __DIR__ . '/logs/debug.log' : null);
define("DISABLE_USER_METADATA_UPDATE", isset($_ENV['DISABLE_USER_METADATA_UPDATE']) ? filter_var($_ENV['DISABLE_USER_METADATA_UPDATE'], FILTER_VALIDATE_BOOLEAN) : false);

// Functions and classes
require_once __DIR__ . '/inc/Errors.php';
require_once __DIR__ . '/inc/DB.php';
require_once __DIR__ . '/inc/API.php';
require_once __DIR__ . '/inc/GPodder.php';
require_once __DIR__ . '/inc/Feed.php';
require_once __DIR__ . '/inc/Language.php';

Language::getInstance();

// Templates
require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/templates/footer.php';