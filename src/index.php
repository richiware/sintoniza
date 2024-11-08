<?php

require_once __DIR__ . '/inc/DB.php';
require_once __DIR__ . '/inc/API.php';
require_once __DIR__ . '/inc/GPodder.php';
require_once __DIR__ . '/inc/Feed.php';
require_once __DIR__ . '/config.php';

error_reporting(E_ALL);
$backtrace = null;

if (PHP_SAPI === 'cli-server' && file_exists(__DIR__ . $_SERVER['REQUEST_URI']) && !is_dir(__DIR__ . $_SERVER['REQUEST_URI'])) {
	return false;
}

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
	echo '<pre style="background: #fdd; padding: 20px; border: 5px solid darkred; position: absolute; top: 0; left: 0; right: 0; bottom: 0; overflow: auto; white-space: pre-wrap;"><h1>Internal error</h1>';

	error_log((string) $e);

	if (defined('DEBUG') && DEBUG) {
		echo $e;

		global $backtrace;
		$backtrace ??= debug_backtrace();

		error_log(print_r($backtrace, true));

		echo '<hr style="margin: 30px 0; border: none; border-top: 5px solid darkred; background: none;" />';
		print_r($backtrace);
	}
	else {
		echo 'An error happened and has been logged to logs/error.log<br />Enable DEBUG constant to see errors directly.';
	}

	echo '</pre>';
	exit;
});

// Fix issues with badly configured web servers
if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
	@list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
}

ini_set('error_log', __DIR__ . '/logs/error.log');

if (!defined('ENABLE_SUBSCRIPTIONS')) {
	define('ENABLE_SUBSCRIPTIONS', false);
}

if (!defined('DEBUG')) {
	define('DEBUG', null);
}

if (!defined('DISABLE_USER_METADATA_UPDATE')) {
	define('DISABLE_USER_METADATA_UPDATE', false);
}

//$db = new DB(DATA_ROOT . '/data.sqlite');
$db = new DB(DB_HOST, DB_NAME, DB_USER, DB_PASS);

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

function isAdmin(): bool {
    global $gpodder;
    return $gpodder->user && $gpodder->user->admin === 1;
}

function html_head($page_name = null, $logged = false) {
	if($page_name == null) {
		$title = TITLE;
	} else {
		$title = TITLE . ' | ' . $page_name;
	}

	if ($logged == false) {
		$menu = '<a href="login" class="btn btn-light me-2 d-flex align-items-center justify-content-center gap-2"><i class="bi bi-box-arrow-in-right"></i> Entrar</a>
		<a href="register" class="btn btn-warning d-flex align-items-center justify-content-center gap-2"><i class="bi bi-person-plus"></i> Registrar</a>';
	} else {
		$menu = '<a href="subscriptions" class="btn btn-primary me-2 d-flex align-items-center justify-content-center gap-2"><i class="bi bi-mic-fill"></i> Inscrições</a>
		<a href="config" class="btn btn-primary me-2 d-flex align-items-center justify-content-center gap-2"><i class="bi bi-nut"></i> Meus dados</a>
		<a href="logout" class="btn btn-danger me-2 d-flex align-items-center justify-content-center gap-2"><i class="bi bi-box-arrow-right"></i> Sair</a>';
	}

	$menu_admin = null;
	if(isAdmin()) {
		$menu_admin = '<li><a href="admin" class="nav-link px-2 text-white d-flex align-items-center justify-content-center gap-2"><i class="bi bi-shield-lock"></i> Administração</a></li>';
	}

	$description = 'Servidor de sincronização de podcast baseado no protocolo gPodder com suporte ao AntennaPod';

	echo '<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>' . htmlspecialchars($title) . '</title>
		<link href="//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
		<link rel="icon" type="image/png" href="/assets/favicon/favicon-96x96.png" sizes="96x96" />
		<link rel="icon" type="image/svg+xml" href="/assets/favicon/favicon.svg" />
		<link rel="shortcut icon" href="/assets/favicon/favicon.ico" />
		<link rel="apple-touch-icon" sizes="180x180" href="/assets/favicon/apple-touch-icon.png" />
		<meta name="apple-mobile-web-app-title" content="' . htmlspecialchars($title) . '" />
		<link rel="manifest" href="/assets/favicon/site.webmanifest" />
		<meta name="description" content="' . htmlspecialchars($description) . '" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="'.BASE_URL.'" />
		<meta property="og:title" content="' . htmlspecialchars($title) . ' - Sincronização de Podcasts" />
		<meta property="og:description" content="' . htmlspecialchars($description) . '" />
		<meta property="og:image" content="/assets/opengraph.png" />
	</head>
	<body class="bg-light">
		<header class="p-3 text-bg-dark">
			<div class="container">
				<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
					<a href="/" class="d-flex align-items-center me-lg-3 text-white text-decoration-none fs-1">
						<i class="bi bi-broadcast-pin"></i>
					</a>

					<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
						<li><a href="/" class="nav-link px-2 text-white d-flex align-items-center justify-content-center gap-2"><i class="bi bi-house"></i> Inicio</a></li>
						'.$menu_admin.'
					</ul>

					<div class="text-end d-flex align-items-center justify-content-center gap-2">
					' . $menu . '
					</div>
				</div>
			</div>
		</header>

		<div class="container py-5">
			<main>';
}

function html_foot() {
	echo '</main>
		</div>

	<footer class="bg-secondary-subtle text-center py-3 mt-3 mt-md-5">
		<p class="m-0">Instância gerenciada e mantida por <a class="link-secondary " href="https://pcdomanual.com/" target="_blank">PC do Manual</a> do <a class="link-secondary " href="https://manualdousuario.net" target="_blank">Manual do Usuario</a>.</p>
		<p class="m-0">Com ❤️ por <a class="link-secondary " href="https://altendorfme.com/" target="_blank">altendorfme</a> · Versão '.VERSION.'</p>
	</footer>
	</body>
	</html> ';
}

function format_description(string $str): string {
	$str = str_replace('</p>', "\n\n", $str);
	$str = preg_replace_callback('!<a[^>]*href=(".*?"|\'.*?\'|\S+)[^>]*>(.*?)</a>!i', function ($match) {
		$url = trim($match[1], '"\'');
		if ($url === $match[2]) {
			return $match[1];
		}
		else {
			return '[' . $match[2] . '](' . $url . ')';
		}
	}, $str);
	$str = htmlspecialchars(strip_tags($str));
	$str = preg_replace("!(?:\r?\n){3,}!", "\n\n", $str);
	$str = preg_replace('!\[([^\]]+)\]\(([^\)]+)\)!', '<a href="$2">$1</a>', $str);
	$str = preg_replace(';(?<!")https?://[^<\s]+(?!");', '<a href="$0">$0</a>', $str);
	$str = nl2br($str);
	return $str;
}

if ($api->url === 'logout') {
	$gpodder->logout();
	header('Location: ./');
	exit;
}
elseif ($gpodder->user && $api->url === 'admin' && isAdmin()) {
	html_head('Administração', $gpodder->isLogged());

	// Handle delete user action
	if (isset($_POST['delete_user'])) {
		$user_id = (int)$_POST['delete_user'];
		$db->simple('DELETE FROM users WHERE id = ?', $user_id);
		echo '<div class="alert alert-success" role="alert">Usuário deletado com sucesso!</div>';
	}

	// Handle new user registration from admin
	if (isset($_POST['new_username'], $_POST['new_password'])) {
		if ($error = $gpodder->subscribe($_POST['new_username'], $_POST['new_password'])) {
			printf('<div class="alert alert-danger" role="alert">%s</div>', htmlspecialchars($error));
		} else {
			echo '<div class="alert alert-success" role="alert">Usuário registrado com sucesso!</div>';
		}
	}

	echo '<h2>Administrativo</h2>';

	// Add new user form
	echo '<div class="card mb-4">
		<div class="card-body">
			<h3 class="card-title">Adicionar Novo Usuário</h3>
			<form method="post" action="" class="row g-3">
				<div class="col-md-5">
					<label for="new_username" class="form-label">Usuário</label>
					<input type="text" class="form-control" name="new_username" id="new_username" required>
				</div>
				<div class="col-md-5">
					<label for="new_password" class="form-label">Senha</label>
					<input type="password" class="form-control" name="new_password" id="new_password" required minlength="8">
				</div>
				<div class="col-md-2 d-flex align-items-end">
					<button type="submit" class="btn btn-primary w-100">
						<i class="bi bi-person-plus"></i> Adicionar
					</button>
				</div>
			</form>
		</div>
	</div>';

	// Users list
	echo '<div class="card">
		<div class="card-body">
			<h3 class="card-title">Lista de Usuários</h3>
			<table class="table table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Usuário</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>';

		$users = $db->all('SELECT id, name FROM users ORDER BY id DESC');
		foreach ($users as $user) {
			printf('<tr>
				<td>%d</td>
				<td>%s</td>
				<td>
					<form method="post" action="" class="d-inline" onsubmit="return confirm(\'Tem certeza que deseja deletar este usuário?\');">
						<input type="hidden" name="delete_user" value="%d">
						<button type="submit" class="btn btn-danger btn-sm">
							<i class="bi bi-trash"></i> Deletar
						</button>
					</form>
				</td>
			</tr>',
				$user->id,
				htmlspecialchars($user->name),
				$user->id
			);
		}

		echo '</tbody></table>
			</div>
		</div>';

	html_foot();
}
elseif ($gpodder->user && $api->url === 'subscriptions') {
	html_head('Inscrições', $gpodder->isLogged());

	?>
		<style>
			audio {
				border: 1px solid #000;
				border-radius: 20px;
				height: 31px;
				width: 320px;
			}
		</style>
	<?php

	if (isset($_POST['update']) && !DISABLE_USER_METADATA_UPDATE) {
		echo '<p><a href="./subscriptions" class="btn btn-danger" aria-label="Voltar">Voltar</a></p>';
		$gpodder->updateAllFeeds();
		exit;
	}
	elseif (isset($_GET['id'])) {
		echo '<p>
			<a href="./subscriptions"class="btn btn-danger" aria-label="Voltar">Voltar</a>
		</p>';

		$feed = $gpodder->getFeedForSubscription((int)$_GET['id']);

		if (isset($feed->url, $feed->title, $feed->image_url, $feed->description)) {
			printf('<div class="row"><div class="d-flex align-items-center"><div class="pe-3"><img class="rounded" width="150" height="150" src="%s"></div><div><h2 class="fs-3"><a href="%s" target="_blank">%s</a></span></h2><p>%s</p></div></div></div>',
				$feed->image_url,	
				htmlspecialchars($feed->url),
				htmlspecialchars($feed->title),
				format_description($feed->description)
			);

			echo '<div class="alert alert-warning mt-3" role="alert">
			Os títulos dos episódios podem estar faltando devido a rastreadores/anúncios usados ​​por alguns provedores de podcast.<br/>
			Dar o play neste ambiente não irá contabilizar para sincronização.
			</div>';
		}
		else {
			echo '<div class="alert alert-warning mt-3" role="alert">Nenhuma informação disponível neste feed.</div>';
		}

		echo '<table class="table table-striped"><thead><tr><th scope="col"></th><th style="width: 80px;" scope="col"></th><th>Informações</th><th scope="col"></td><th scope="col"></td></tr></thead><tbody>';

		foreach ($gpodder->listActions((int)$_GET['id']) as $row) {
			$url = strtok(basename($row->url), '?');
			strtok('');
			$title = $row->title ?? $url;
			$image_url = !empty($row->image_url) ? '<img class="rounded" src="'.$row->image_url.'" width="80" height="80" />' : '' ;
		
			if($row->action == 'play') {
				$action = '<div class="badge text-bg-success rounded-pill"><i class="bi bi-play"></i> Tocado</div>';
			} else if($row->action == 'download') {
				$action = '<div class="badge text-bg-primary rounded-pill"><i class="bi bi-download"></i> Baixado</div>';
			} else if($row->action == 'delete') {
				$action = '<div class="badge text-bg-danger rounded-pill"><i class="bi bi-trash-fill"></i> Deletado</div>';
			} else {
				$action = '<div class="badge text-bg-secondary rounded-pill"><i class="bi bi-motherboard"></i> Indisponivel</div>';
			}

			$device_name = $row->device_name ? 'em <div class="badge text-bg-primary rounded-pill">'.$row->device_name.'</div>' : '<div class="badge text-bg-secondary rounded-pill"><i class="bi bi-motherboard"></i> Indisponivel</div>';
			$duration = gmdate("H:i:s", $row->duration);

			printf('<tr><td>%s<br/>%s<br/>em <small><time datetime="%s">%s</time></small></td><td>%s</td><td><a href="%s" target="_blank">%s</a><br/>Duração: %s</td><td><a href="%s" target="_blank" class="btn btn-sm btn-secondary"><i class="bi bi-cloud-arrow-down-fill"></i> Download</a></td></tr>',
				$action,
				$device_name,
				date(DATE_ISO8601, $row->changed),
				date('d/m/Y \à\s H:i', $row->changed),
				$image_url,
				$row->episode_url,
				htmlspecialchars($title),
				$duration,
				htmlspecialchars($row->url),
			);
		}
	}
	else {
		printf('<form method="post" action=""><div class="flex-wrap d-flex gap-2 pb-4">
			<a href="./" class="btn btn-danger" aria-label="Voltar">Voltar</a>
			<a href="./subscriptions/%s.opml" target="_blank" class="btn btn-secondary">Feed OPML</a>
			%s
		</div></form>',
			htmlspecialchars($gpodder->user->name),
			DISABLE_USER_METADATA_UPDATE ? '' : '<button type="submit" class="btn btn-info" name="update" value=1>Atualizar todos os metadados dos feeds</button>',
		);

		echo '<table class="table table-striped"><thead><tr><th scope="col">Podcast URL</th><th scope="col">Última ação</th><th scope="col">Ações</th></tr></thead><tbody>';

		foreach ($gpodder->listActiveSubscriptions() as $row) {
			$title = $row->title ?? str_replace(['http://', 'https://'], '', $row->url);
			printf('<tr><th scope="row"><a href="?id=%d">%s</a></th><td><time datetime="%s">%s</time></td><td>%d</td></tr>',
				$row->id,
				htmlspecialchars($title),
				date(DATE_ISO8601, $row->last_change),
				date('d/m/Y H:i', $row->last_change),
				$row->count
			);
		}
	}

	echo '</tbody></table>';
	html_foot();
}
elseif ($gpodder->user && $api->url === 'config') {
	html_head('Painel', $gpodder->isLogged());

	if (isset($_GET['oktoken'])) {
		echo '<div class="alert alert-success" role="alert">Você está logado, pode fechar isso e voltar para o aplicativo.</div>';
	}

	?>
	<div class="row justify-content-center">
		<div class="col-md-6 col-lg-4">
			<div class="card shadow">
				<div class="card-body p-4">
					<form method="post" action="/config">
						<h3 class="card-title text-center mb-4">Alterar Senha</h3>
						<?php
							if (isset($_POST['change_password'])) {
								if ($_POST['new_password'] !== $_POST['confirm_password']) {
									echo '<div class="alert alert-danger" role="alert">As novas senhas não coincidem.</div>';
								}
								else {
									$result = $gpodder->changePassword($_POST['current_password'], $_POST['new_password']);
									if ($result === null) {
										echo '<div class="alert alert-success" role="alert">Senha alterada com sucesso!</div>';
									}
									else {
										printf('<div class="alert alert-danger" role="alert">%s</div>', htmlspecialchars($result));
									}
								}
							}
						?>
						<div class="mb-3">
							<label for="current_password" class="form-label">Senha atual:</label>
							<input type="password" class="form-control" required name="current_password" id="current_password" />
						</div>
						<div class="mb-3">
							<label for="new_password" class="form-label">Nova Senha (mínimo 8 caracteres):</label>
							<input type="password" class="form-control" required name="new_password" id="new_password" minlength="8" />
						</div>
						<div class="mb-3">
							<label for="confirm_password" class="form-label">Confirmar nova Senha:</label>
							<input type="password" class="form-control" required name="confirm_password" id="confirm_password" minlength="8" />
						</div>
						<div class="d-grid">
							<button type="submit" name="change_password" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
								<i class="bi bi-key"></i> Alterar
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?php

	html_foot();
}
elseif ($gpodder->user) {
	html_head('Painel', $gpodder->isLogged());

	if (isset($_GET['oktoken'])) {
		echo '<div class="alert alert-success" role="alert">Você está logado, pode fechar isso e voltar para o aplicativo.</div>';
	}

	echo '<div class="text-center mb-4">';
	printf('<h2 class="mb-3">Olá, <strong>%s</strong>!</h2>', $gpodder->user->name);
	echo '<div class="alert alert-warning" role="alert">';
	printf('Usuário secreto do GPodder: <strong>%s</strong>', $gpodder->getUserToken());
	echo '<small class="d-block">(Use este nome de usuário no <i>GPodder Desktop</i>, pois ele não suporta senhas)</small>';
	echo '</div>';
	echo '</div>';

	html_foot();
}
elseif ($api->url === 'login') {
	$error = $gpodder->login();

	if ($gpodder->isLogged()) {
		$token = isset($_GET['token']) ? '?oktoken' : '';
		header('Location: ./' . $token);
		exit;
	}

	html_head('Entrar');

	if ($error) {
		printf('<div class="alert alert-danger" role="alert">%s</div>', htmlspecialchars($error));
	}

	if (isset($_GET['token'])) {
		printf('<div class="alert alert-warning" role="alert">Um aplicativo está solicitando acesso à sua conta.</div>');
	}

	echo '<div class="row justify-content-center">
	<div class="col-md-6 col-lg-4">
	<div class="card shadow">
	<div class="card-body p-4">
	<form method="post" action="">
	<h2 class="card-title text-center mb-4">Entrar</h2>
	<div class="mb-3">
	<label for="login" class="form-label">Usuário</label>
	<input type="text" class="form-control" required name="login" id="login" />
	</div>
	<div class="mb-3">
	<label for="password" class="form-label">Senha</label>
	<input type="password" class="form-control" required name="password" id="password" />
	</div>
	<div class="d-grid">
	<button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
	<i class="bi bi-box-arrow-in-right"></i> Entrar 
	</button>
	</div>
	</form>
	</div>
	</div>
	</div>
	</div>';

	html_foot();
}
elseif ($api->url === 'register' && !$gpodder->canSubscribe()) {
	html_head('Registrar');
	echo '<div class="alert alert-success" role="alert">As assinaturas estão desabilitadas.</div>';
	html_foot();
}
elseif ($api->url === 'register') {
	html_head('Registrar');

	if (!empty($_POST)) {
		if (!$gpodder->checkCaptcha($_POST['captcha'] ?? '', $_POST['cc'] ?? '')) {
			echo '<div class="alert alert-danger" role="alert">Invalid captcha.</div>';
		}
		elseif ($error = $gpodder->subscribe($_POST['username'] ?? '', $_POST['password'] ?? '')) {
			printf('<div class="alert alert-danger" role="alert">%s</div>', htmlspecialchars($error));
		}
		else {
			echo '<div class="alert alert-success" role="alert">Your account is registered.</div>';
			echo '<p><a href="login" class="btn btn-light me-2 d-flex align-items-center justify-content-center gap-2"><i class="bi bi-box-arrow-in-right"></i> Entrar</a></p>';
		}
	}

	echo '<div class="row justify-content-center">
	<div class="col-md-6 col-lg-4">
	<div class="card shadow">
	<div class="card-body p-4">
	<form method="post" action="">
	<h2 class="card-title text-center mb-4">Registrar</h2>
	<div class="mb-3">
	<label for="username" class="form-label">Usuário</label>
	<input type="text" class="form-control" name="username" required id="username" />
	</div>
	<div class="mb-3">
	<label for="password" class="form-label">Senha (minimo de 8 caracteres)</label>
	<input type="password" class="form-control" minlength="8" required name="password" id="password" />
	</div>
	<div class="mb-3">
	<label class="form-label">Captcha</label>
	<div class="alert alert-info">
	Preencha com seguinte número: '.$gpodder->generateCaptcha().'
	</div>
	<input type="text" class="form-control" name="captcha" required id="captcha" />
	</div>
	<div class="d-grid">
	<button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
	<i class="bi bi-person-plus"></i> Registrar
	</button>
	</div>
	</form>
	</div>
	</div>
	</div>
	</div>';

	html_foot();
}
else {
	html_head();

	echo '<div>
    <p>Este é um servidor de sincronização de podcast baseado no "protocolo" gPodder.</p>
    <p>Esse projeto é um fork do <a href="https://github.com/kd2org/opodsync" target="_blank">oPodSync</a></p>
	<p>Projeto publicado no Github <a href="https://github.com/manualdousuario/sintoniza/" target="_blank">Sintoniza</a></p>

    <h3>Aplicativos testados</h3>
    <ul>
        <li>
			<a target="_blank" href="https://github.com/AntennaPod/AntennaPod">AntennaPod</a> 3.5.0 - Android
			<div class="d-block mt-2"><video class="img-thumbnail" autoplay loop muted><source src="https://github.com/manualdousuario/sintoniza/blob/main/assets/antennapod_350.mp4?raw=true" type="video/mp4" /></video></div>
		</li>
		<li>
			<a target="_blank" href="https://invent.kde.org/multimedia/kasts">Kasts</a> 21.88 - <a target="_blank" href="https://cdn.kde.org/ci-builds/multimedia/kasts/">Windows</a>/Android/Linux (Funciona sincronização entre devices)
		</li>
		<li>
			<a target="_blank" href="https://gpodder.github.io/">gPodder</a> 3.11.4 - Windows/macOS/Linux/BSD
		</li>
    </ul>
	</div>';

	html_foot();
}
