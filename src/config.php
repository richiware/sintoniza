<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Define
$version = "1.8.3";
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
require_once __DIR__ . '/inc/DB.php';
require_once __DIR__ . '/inc/API.php';
require_once __DIR__ . '/inc/GPodder.php';
require_once __DIR__ . '/inc/Feed.php';
require_once __DIR__ . '/inc/Language.php';

Language::getInstance();

// Templates
require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/templates/footer.php';

// Errors log
error_reporting(E_ALL);

set_error_handler(static function ($severity, $message, $file, $line) {
	if (!(error_reporting() & $severity)) {
		// Don't report this error (for example @unlink)
		return;
	}

	global $backtrace;
	$backtrace = debug_backtrace();

	throw new \ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function ($e) {
	@http_response_code(500);
	error_log((string)$e);
	echo '<pre class="alert alert-danger">
	<h1>Internal error</h1>';

	error_log((string) $e);

	if (defined('DEBUG') && DEBUG) {
		echo $e;

		global $backtrace;
		$backtrace ??= debug_backtrace();

		error_log(print_r($backtrace, true));

		echo '<hr/>';
		print_r($backtrace);
	}
	else {
		echo 'An error happened and has been logged to logs/error.log<br />Enable DEBUG constant to see errors directly.';
	}

	echo '</pre>';
	exit;
});

ini_set('error_log', __DIR__ . '/logs/error.log');

// Fix issues with badly configured web servers
if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
	@list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
}

