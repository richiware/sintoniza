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
	<h1 class="p-0 m-0 fs-5">Internal error</h1>';
	
	error_log((string) $e);

	if (defined('DEBUG') && DEBUG == true) {
		echo $e;

		global $backtrace;
		$backtrace ??= debug_backtrace();

		error_log(print_r($backtrace, true));

		echo '<hr/>';
		print_r($backtrace);
	}
	else { ?>
		<?php echo __('An error happened and has been logged to logs/error.log'); ?></br>
		<?php echo __('Enable DEBUG constant to see errors directly'); ?>
	<?php }

	echo '</pre>';
	exit;
});
