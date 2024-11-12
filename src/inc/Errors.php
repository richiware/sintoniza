<?php
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

ini_set('error_log', __DIR__ . '/../logs/error.log');

set_exception_handler(function ($e) {
	@http_response_code(500);
	if (defined('DEBUG') && DEBUG == true) {
		error_log(__DIR__ . '/../logs/debug.log');
	} else {
		error_log(__DIR__ . '/../logs/error.log');
	}
	echo '<pre class="alert alert-danger">
	<h1>Internal error</h1>';

	if (defined('DEBUG') && DEBUG == true) {
		echo $e;

		global $backtrace;
		$backtrace ??= debug_backtrace();

		error_log(print_r($backtrace, true), 3, __DIR__ . '/../logs/debug.log');

		echo '<hr/>';
		print_r($backtrace);
	}
	else { ?>
		<?php echo __('errors.debug_log'); ?></br>
		<?php echo __('errors.debug_enable'); ?>
	<?php }

	echo '</pre>';
	exit;
});

// Fix issues with badly configured web servers
if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
	@list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
}
