<?php
$app_config = require __DIR__ . '/config.php';

if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
	@list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
}

if (!defined('BASE_URL')) {
	define('BASE_URL', $app_config['site']['url']);
}

if (!defined('TITLE')) {
	define('TITLE', $app_config['site']['name']);
}

if (!defined('ENABLE_SUBSCRIPTIONS')) {
	define('ENABLE_SUBSCRIPTIONS', $app_config['site']['enable_subscriptions']);
}

if (!defined('ADMIN_PASSWORD')) {
	define('ADMIN_PASSWORD', $app_config['admin_password']);
}

if (!defined('DISABLE_USER_METADATA_UPDATE')) {
	define('DISABLE_USER_METADATA_UPDATE', $app_config['site']['disable_user_metadata_update']);
}

if (!defined('DEBUG')) {
	define('DEBUG', $app_config['site']['debug']);
}

require_once __DIR__ . '/inc/DB.php';
require_once __DIR__ . '/inc/API.php';
require_once __DIR__ . '/inc/GPodder.php';
require_once __DIR__ . '/inc/Feed.php';
require_once __DIR__ . '/inc/functions.php';

$db = new DB([
	'host' => $app_config['database']['host'],
	'database' => $app_config['database']['dbname'],
	'username' => $app_config['database']['username'],
	'password' => $app_config['database']['password']
]);

$api = new API($db);

try {
	if ($api->handleRequest()) {
		return;
	}
} catch (JsonException $e) {
	return;
}

$gpodder = new GPodder($db);

if (PHP_SAPI === 'cli') {
	$gpodder->updateAllFeeds(true);
	exit(0);
}

switch ($api->url) {
	case 'login':
		$error = $gpodder->login();

		if ($gpodder->isLogged()) {
			redirect('./' . (isset($_GET['token']) ? '?oktoken' : ''));
		}

		view('auth/login', [
			'title' => 'Entrar',
			'error' => $error,
			'gpodder' => $gpodder
		]);
		break;

	case 'register':
		if ($gpodder->user) {
			redirect('./');
		}

		$canRegister = $gpodder->canSubscribe() || isAdmin();

		if (!$canRegister) {
			view('auth/register_disabled', [
				'title' => 'Registro desabilitado',
				'gpodder' => $gpodder
			]);
			break;
		}

		$error = null;
		if (!empty($_POST)) {
			if (!isAdmin() && !$gpodder->checkCaptcha($_POST['captcha'] ?? '', $_POST['cc'] ?? '')) {
				$error = 'Invalid captcha.';
			} else {
				$error = $gpodder->subscribe(
					$_POST['username'] ?? '',
					$_POST['password'] ?? '',
					isAdmin()
				);

				if (!$error) {
					view('auth/register_success', [
						'title' => 'Account Created',
						'gpodder' => $gpodder
					]);
					break;
				}
			}
		}

		view('auth/register', [
			'title' => 'Registrar',
			'error' => $error,
			'gpodder' => $gpodder
		]);
		break;

	case 'admin':
		if (!empty($_POST['admin_password'])) {
			if ($_POST['admin_password'] === ADMIN_PASSWORD) {
				$_SESSION['is_admin'] = true;
			}
		}

		if (!isAdmin()) {
			view('admin/login', [
				'title' => 'Administração',
				'gpodder' => $gpodder
			]);
			break;
		}

		if (!empty($_POST['action'])) {
			switch ($_POST['action']) {
				case 'toggle_user_status':
					if (!empty($_POST['user_id'])) {
						$db->simple(
							'UPDATE users SET active = NOT active WHERE id = ?',
							$_POST['user_id']
						);
					}
					break;

				case 'delete_user':
					if (!empty($_POST['user_id'])) {
						$db->beginTransaction();
						try {
							$db->simple('DELETE FROM episodes_actions WHERE user = ?', $_POST['user_id']);
							$db->simple('DELETE FROM subscriptions WHERE user = ?', $_POST['user_id']);
							$db->simple('DELETE FROM users WHERE id = ?', $_POST['user_id']);
							$db->commit();
						} catch (Exception $e) {
							$db->rollBack();
						}
					}
					break;
			}
		}

		$users = $db->all('SELECT u.*, 
			COUNT(DISTINCT s.id) as subscription_count,
			MAX(ea.changed) as last_activity
			FROM users u 
			LEFT JOIN subscriptions s ON s.user = u.id AND s.deleted = 0
			LEFT JOIN episodes_actions ea ON ea.user = u.id
			GROUP BY u.id
			ORDER BY u.name');

		view('admin/users', [
			'title' => 'Administração',
			'users' => $users,
			'gpodder' => $gpodder
		]);
		break;

	case 'subscriptions':
		if (!$gpodder->user) {
			redirect('login');
		}

		if (isset($_GET['id'])) {
			$subscription = $db->firstRow(
				'SELECT * FROM subscriptions WHERE id = ? AND user = ?',
				$_GET['id'],
				$gpodder->user->id
			);

			if (!$subscription) {
				redirect('subscriptions');
			}

			view('subscriptions/view', [
				'title' => $feed,
				'subscription' => $subscription,
				'feed' => $feed,
				'episodes' => $episodes,
				'gpodder' => $gpodder
			]);
		} else {
			$subscriptions = $gpodder->listActiveSubscriptions();
			view('home/user', [
				'title' => 'Olá, ' . $gpodder->user->name,
				'user' => $gpodder->user,
				'subscriptions' => $subscriptions,
				'subscription_count' => $gpodder->countActiveSubscriptions(),
				'gpodder' => $gpodder
			]);
		}
		break;

	case 'logout':
		$gpodder->logout();
		session_destroy();
		session_start();
		redirect('./');
		break;

	default:
		if ($gpodder->user) {
			$subscriptions = $gpodder->listActiveSubscriptions();
			view('home/user', [
				'title' => 'Olá, ' . $gpodder->user->name,
				'user' => $gpodder->user,
				'subscriptions' => $subscriptions,
				'subscription_count' => $gpodder->countActiveSubscriptions(),
				'gpodder' => $gpodder
			]);
		} else {
			view('home/guest', ['gpodder' => $gpodder]);
		}
		break;
}
