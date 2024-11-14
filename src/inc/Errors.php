<?php
// Errors log
error_reporting(E_ALL);

$backtrace = null;

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
	else { ?>
		<?php echo __('errors.debug_log'); ?></br>
		<?php echo __('errors.debug_enable'); ?>
	<?php }

	echo '</pre>';
	exit;
});
