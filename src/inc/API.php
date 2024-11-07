<?php

class API
{
	protected ?string $method;
	protected ?stdClass $user;
	protected ?string $section;
	public ?string $url;
	public ?string $base_url;
	public ?string $base_path;
	protected ?string $path;
	protected ?string $format = null;
	protected DB $db;

	public function __construct(DB $db)
	{
		session_name('sessionid');
		$this->db = $db;
		$url = defined('BASE_URL') ? BASE_URL : null;
		$url ??= getenv('BASE_URL', true) ?: null;

		if (!$url) {
			if (!isset($_SERVER['SERVER_PORT'], $_SERVER['SERVER_NAME'], $_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT'])) {
				echo "Não é possível detectar automaticamente a URL do aplicativo. Defina a constante BASE_URL ou a variável de ambiente..\n";
				exit(1);
			}

			$url = 'http';

			if (!empty($_SERVER['HTTPS']) || $_SERVER['SERVER_PORT'] === 443) {
				$url .= 's';
			}

			$url .= '://' . $_SERVER['SERVER_NAME'];

			if (!in_array($_SERVER['SERVER_PORT'], [80, 443])) {
				$url .= ':' . $_SERVER['SERVER_PORT'];
			}

			$path = substr(dirname($_SERVER['SCRIPT_FILENAME']), strlen($_SERVER['DOCUMENT_ROOT']));
			$path = trim($path, '/');
			$url .= $path ? '/' . $path . '/' : '/';
		}

		$this->base_path = parse_url($url, PHP_URL_PATH) ?? '';
		$this->base_url = $url;
	}

	public function url(string $path = ''): string
	{
		return $this->base_url . $path;
	}

	public function debug(string $message, ...$params): void
	{
		if (!DEBUG) {
			return;
		}

		file_put_contents(DEBUG, date('Y-m-d H:i:s ') . vsprintf($message, $params) . PHP_EOL, FILE_APPEND);
	}

    public function queryWithData(string $sql, ...$params): array {
        $result = $this->db->iterate($sql, ...$params);
        $out = [];

        foreach ($result as $row) {
            // Mudança na manipulação de JSON pois MySQL já retorna como string
            if (isset($row->data) && is_string($row->data)) {
                $jsonData = json_decode($row->data, true, 512, JSON_THROW_ON_ERROR);
                $row = (object) array_merge($jsonData, (array) $row);
                unset($row->data);
            }
            $out[] = (array) $row;
        }

        return $out;
    }

	/**
	 * @throws JsonException
	 */
	public function error(int $code, string $message): void {
		$this->debug('RETURN: %d - %s', $code, $message);

		http_response_code($code);
		header('Content-Type: application/json', true);
		echo json_encode(compact('code', 'message'), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
		exit;
	}

	/**
	 * @throws JsonException
	 */
	public function requireMethod(string $method): void {
		if ($method !== $this->method) {
			$this->error(405, 'Invalid HTTP method: ' . $this->method);
		}
	}

	/**
	 * @throws JsonException
	 */
	public function validateURL(string $url): void {
		if (!preg_match('!^https?://[^/]+!', $url)) {
			$this->error(400, 'URL inválida: ' . $url);
		}
	}

	public function getDeviceID(string $deviceid, int $user_id) {
		if (isset($deviceid)) {
			$this->debug('Procurando o ID do dispositivo para deviceid: %s e usuário: %d', $deviceid, $user_id);
			$device_id = $this->db->firstColumn('SELECT id FROM devices WHERE deviceid = ? AND user = ?;', $deviceid, $user_id);
			$this->debug('ID do dispositivo encontrado: %s', $device_id ?? 'null');
			return $device_id;
		} else {
			$this->error(400, 'ID do dispositivo não registrado');
			return null;
		}
	}

	/**
	 * @throws JsonException
	 */
	public function getInput()
	{
		if ($this->format === 'txt') {
			return array_filter(file('php://input'), 'trim');
		}

		$input = file_get_contents('php://input');
		return json_decode($input, false, 512, JSON_THROW_ON_ERROR);
	}

	/**
	 * @see https://gpoddernet.readthedocs.io/en/latest/api/reference/auth.html
	 * @throws JsonException
	 */
	public function handleAuth(): void
	{
		$this->requireMethod('POST');

		strtok($this->path, '/');
		$action = strtok('');

		if ($action === 'logout') {
			$_SESSION = [];
			session_destroy();
			$this->error(200, 'Desconectado');
		}
		elseif ($action !== 'login') {
			$this->error(404, 'Ação de login desconhecida: ' . $action);
		}

		if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
			$this->error(401, 'Nenhum nome de usuário ou senha fornecidos');
		}

		$this->requireAuth();

		$this->error(200, 'Conectado!');
	}

	public function login()
	{
		$login = $_SERVER['PHP_AUTH_USER'];
		list($login) = explode('__', $login, 2);

		$user = $this->db->firstRow('SELECT id, password FROM users WHERE name = ?;', $login);

		if(!$user) {
			$this->error(401, 'Nome de usuário inválido');
		}

		if (!password_verify($_SERVER['PHP_AUTH_PW'], $user->password ?? '')) {
			$this->error(401, 'Nome de usuário/senha inválidos');
		}

		$this->debug('Usuário conectado: %s', $login);

		@session_start();
		$_SESSION['user'] = $user;
	}

	/**
	 * @throws JsonException
	 */
	public function requireAuth(?string $username = null): void
	{
		if (isset($this->user)) {
			return;
		}

		// For gPodder desktop
		if ($username && false !== strpos($username, '__')) {
			$gpodder = new GPodder($this->db);
			if (!$gpodder->validateToken($username)) {
				$this->error(401, 'Token gpodder inválido');
			}

			$this->user = $gpodder->user;
			return;
		}

		if (isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
			$this->login();
			$this->user = $_SESSION['user'];
			return;
		}

		if (empty($_COOKIE['sessionid'])) {
			$this->error(401, 'Cookie de sessão é necessário');
		}

		@session_start();

		if (empty($_SESSION['user'])) {
			$this->error(401, 'Cookie de ID de sessão expirado e nenhum cabeçalho de autorização foi fornecido');
		}

		if (!$this->db->firstColumn('SELECT 1 FROM users WHERE id = ?;', $_SESSION['user']->id)) {
			$this->error(401, 'O usuário não existe');
		}

		$this->user = $_SESSION['user'];
		$this->debug('ID do usuário do cookie: %s', $this->user->id);
	}

	/**
	 * @throws JsonException
	 */
	public function route()
	{
		switch ($this->section) {
			// Not implemented
			case 'tag':
			case 'tags':
			case 'data':
			case 'toplist':
			case 'suggestions':
			case 'favorites':
				return [];
			case 'devices':
				return $this->devices();
			case 'updates':
				return $this->updates();
			case 'subscriptions':
				return $this->subscriptions();
			case 'episodes':
				return $this->episodes();
			case 'settings':
			case 'lists':
			case 'sync-device':
				$this->error(503, 'Not implemented');
			default:
				return null;
		}
	}

	/**
	 * Map NextCloud endpoints to GPodder
	 * @see https://github.com/thrillfall/nextcloud-gpodder
	 * @throws JsonException
	 */
	public function handleNextCloud(): ?array
	{
		if ($this->url === 'index.php/login/v2') {
			$this->requireMethod('POST');

			$id = sha1(random_bytes(16));

			return [
				'poll' => [
					'token' => $id,
					'endpoint' => $this->url('index.php/login/v2/poll'),
				],
				'login' => $this->url('login?token=' . $id),
			];
		}

		if ($this->url === 'index.php/login/v2/poll') {
			$this->requireMethod('POST');

			if (empty($_POST['token']) || !ctype_alnum($_POST['token'])) {
				$this->error(400, 'Token inválido');
			}

			session_id($_POST['token']);
			session_start();

			if (empty($_SESSION['user']) || empty($_SESSION['app_password'])) {
				$this->error(404, 'Ainda não conectado, usando token: ' . $_POST['token']);
			}

			return [
				'server' => $this->url(),
				'loginName' => $_SESSION['user']->name,
				'appPassword' => $_SESSION['app_password'], // FIXME provide a real app-password here
			];
		}

		$nextcloud_path = 'index.php/apps/gpoddersync/';

		if (0 !== strpos($this->url, $nextcloud_path)) {
			return null;
		}

		if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
			$this->error(401, 'Nenhum nome de usuário ou senha fornecidos');
		}

		$this->debug('Compatibilidade com Nextcloud: %s / %s', $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

		$user = $this->db->firstRow('SELECT id, password FROM users WHERE name = ?;', $_SERVER['PHP_AUTH_USER']);

		if (!$user) {
			$this->error(401, 'Nome de usuário inválido');
		}

		// FIXME store a real app password instead of this hack
		$token = strtok($_SERVER['PHP_AUTH_PW'], ':');
		$password = strtok('');
		$app_password = sha1($user->password . $token);

		if ($app_password !== $password) {
			$this->error(401, 'Nome de usuário/senha inválidos');
		}

		$this->user = $_SESSION['user'] = $user;

		$path = substr($this->url, strlen($nextcloud_path));

		if ($path === 'subscriptions') {
			$this->url = 'api/2/subscriptions/current/default.json';
		}
		elseif ($path === 'subscription_change/create') {
			$this->url = 'api/2/subscriptions/current/default.json';
		}
		elseif ($path === 'episode_action' || $path === 'episode_action/create') {
			$this->url = 'api/2/episodes/current.json';
		}
		else {
			$this->error(404, 'Ponto de extremidade da API Nextcloud indefinido');
		}

		return null;
	}

	/**
	 * @throws JsonException
	 */
	public function handleRequest(): void
	{
		$this->method = $_SERVER['REQUEST_METHOD'] ?? null;
		$url = '/' . trim($_SERVER['REQUEST_URI'] ?? '', '/');
		$url = substr($url, strlen($this->base_path));
		$this->url = strtok($url, '?');

		$this->debug('Recebi uma solicitação %s em %s', $this->method, $this->url);

		$return = $this->handleNextCloud();

		if ($return) {
			echo json_encode($return, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
			exit;
		}

		if (!preg_match('!^(suggestions|subscriptions|toplist|api/2/(auth|subscriptions|devices|updates|episodes|favorites|settings|lists|sync-devices|tags?|data))/!', $this->url, $match)) {
			return;
		}

		$this->section = $match[2] ?? $match[1];
		$this->path = substr($this->url, strlen($match[0]));
		$username = null;

		if (preg_match('/\.(json|opml|txt|jsonp|xml)$/', $this->url, $match)) {
			$this->format = $match[1];
			$this->path = substr($this->path, 0, -strlen($match[0]));
		}

		if (!in_array($this->format, ['json', 'opml', 'txt'])) {
			$this->error(501, 'Formato de saída não implementado');
		}

		// For gPodder
		if (preg_match('!(\w+__\w{10})!i', $this->path, $match)) {
			$username = $match[1];
		}

		if ($this->section === 'auth') {
			$this->handleAuth();
			return;
		}

		$this->requireAuth($username);

		$return = $this->route();

		$this->debug("RETURN:\n%s", json_encode($return, JSON_PRETTY_PRINT));

		if ($this->format === 'opml') {
			if ($this->section !== 'subscriptions') {
				$this->error(501, 'Formato de saída não implementado');
			}

			header('Content-Type: text/x-opml; charset=utf-8');
			echo $this->opml($return);
		}
		else {
			header('Content-Type: application/json');

			if ($return !== null) {
				echo json_encode($return, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
			}
		}

		exit;
	}

	/**
	 * @throws JsonException
	 */
	public function devices(): array
	{
		if ($this->method === 'GET') {
			return $this->queryWithData('SELECT deviceid as id, user, deviceid, name, data FROM devices WHERE user = ?;', $this->user->id);
		}

		if ($this->method === 'POST') {
			$deviceid = explode('/', $this->path)[1] ?? null;

			if (!$deviceid || !preg_match('/^[\w.-]+$/', $deviceid)) {
				$this->error(400, 'ID do dispositivo inválido');
			}

			$json = $this->getInput();
			$json ??= [];
			$json->subscriptions = 0;

			$params = [
				'deviceid' => $deviceid,
				'data'     => json_encode($json),
				'name'     => $json->caption ?? null,
				'user'     => $this->user->id,
			];

			$this->db->upsert('devices', $params, ['deviceid', 'user']);
			$this->error(200, 'Dispositivo atualizado');
		}
		$this->error(400, 'Método de solicitação incorreto');
		exit;
	}

	/**
	 * @throws JsonException
	 */
    public function subscriptions()
    {
        $v2 = strpos($this->url, 'api/2/') !== false;

        // We don't care about deviceid yet (FIXME)
        $deviceid = explode('/', $this->path)[1] ?? null;

        if ($this->method === 'GET' && !$v2) {
            return $this->db->rowsFirstColumn('SELECT url FROM subscriptions WHERE user = ?;', $this->user->id);
        }

        if (!$deviceid || !preg_match('/^[\w.-]+$/', $deviceid)) {
            $this->error(400, 'ID do dispositivo inválido');
        }

        // Get Subscription Changes
        if ($v2 && $this->method === 'GET') {
            $timestamp = (int)($_GET['since'] ?? 0);

            return [
                'add' => $this->db->rowsFirstColumn('SELECT url FROM subscriptions WHERE user = ? AND deleted = 0 AND changed >= ?;', $this->user->id, $timestamp),
                'remove' => $this->db->rowsFirstColumn('SELECT url FROM subscriptions WHERE user = ? AND deleted = 1 AND changed >= ?;', $this->user->id, $timestamp),
                'update_urls' => [],
                'timestamp' => time(),
            ];
        }

        if ($this->method === 'PUT') {
            $lines = $this->getInput();

            if (!is_array($lines)) {
                $this->error(400, 'Entrada inválida: requer uma matriz com uma linha por feed');
            }

            try {
                $this->db->beginTransaction();
                $st = $this->db->prepare('INSERT IGNORE INTO subscriptions (user, url, changed) VALUES (:user, :url, :changed)');

                foreach ($lines as $url) {
                    $this->validateURL($url);

                    $st->execute([
                        ':url' => $url,
                        ':user' => $this->user->id,
                        ':changed' => time()
                    ]);
                }

                $this->db->commit();
                return null;
            }
            catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
        }

        if ($this->method === 'POST') {
            $input = $this->getInput();

            try {
                $this->db->beginTransaction();

                $ts = time();

                if (!empty($input->add) && is_array($input->add)) {
                    foreach ($input->add as $url) {
                        $this->validateURL($url);

                        $this->db->upsert('subscriptions', [
                            'user'    => $this->user->id,
                            'url'     => $url,
                            'changed' => $ts,
                            'deleted' => 0,
                        ], ['user', 'url']);
                    }
                }

                if (!empty($input->remove) && is_array($input->remove)) {
                    foreach ($input->remove as $url) {
                        $this->validateURL($url);

                        $this->db->upsert('subscriptions', [
                            'user'    => $this->user->id,
                            'url'     => $url,
                            'changed' => $ts,
                            'deleted' => 1,
                        ], ['user', 'url']);
                    }
                }

                $this->db->commit();
                return ['timestamp' => $ts, 'update_urls' => []];
            }
            catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
        }

        $this->error(501, 'Ainda não implementado');
    }

	/**
	 * @throws JsonException
	 */
	public function updates(): mixed
	{
		$this->error(501, 'Ainda não implementado');
		exit;
	}

	/**
	 * @throws JsonException
	 */
	public function episodes(): array
	{
		if ($this->method === 'GET') {
			$since = isset($_GET['since']) ? (int)$_GET['since'] : 0;
	
			return [
				'timestamp' => time(),
				'actions' => $this->queryWithData('SELECT e.url AS episode, e.action, e.data, s.url AS podcast,
					DATE_FORMAT(FROM_UNIXTIME(e.changed), "%Y-%m-%dT%H:%i:%sZ") AS timestamp
					FROM episodes_actions e
					INNER JOIN subscriptions s ON s.id = e.subscription
					WHERE e.user = ? AND e.changed >= ?;', 
					$this->user->id, 
					$since
				)
			];
		}
	
		$this->requireMethod('POST');
	
		$input = $this->getInput();
	
		if (!is_array($input)) {
			$this->error(400, 'Nenhuma matriz válida encontrada');
		}
	
		try {
			$this->db->beginTransaction();
	
			$timestamp = time();
			$st = $this->db->prepare('INSERT INTO episodes_actions 
				(user, subscription, url, episode, changed, action, data, device) 
				VALUES 
				(:user, :subscription, :url, :episode, :changed, :action, :data, :device)');
	
			foreach ($input as $action) {
				if (!isset($action->podcast, $action->action, $action->episode)) {
					$this->db->rollBack();
					$this->error(400, 'Missing required key in action');
				}
	
				$this->validateURL($action->podcast);
				$this->validateURL($action->episode);
	
				// Get subscription ID or create new subscription
				$subscription_id = $this->db->firstColumn('SELECT id FROM subscriptions WHERE url = ? AND user = ?;', 
					$action->podcast, 
					$this->user->id
				);
	
				if (!$subscription_id) {
					$this->db->simple('INSERT INTO subscriptions (user, url, changed) VALUES (?, ?, ?);', 
						$this->user->id, 
						$action->podcast, 
						$timestamp
					);
					$subscription_id = $this->db->lastInsertId();
				}
	
				// Get feed ID from subscription
				$feed_id = $this->db->firstColumn('SELECT feed FROM subscriptions WHERE id = ?', $subscription_id);
	
				// Try to get episode ID from episodes table
				$episode_id = null;
				if ($feed_id) {
					$episode_id = $this->db->firstColumn('SELECT id FROM episodes WHERE media_url = ? AND feed = ?',
						$action->episode,
						$feed_id
					);
				}
	
				// Get device ID if device is provided
				$device_id = null;
				if (!empty($action->device)) {
					$device_id = $this->getDeviceID($action->device, $this->user->id);
				}
	
				$st->bindValue(':user', $this->user->id);
				$st->bindValue(':subscription', $subscription_id);
				$st->bindValue(':url', $action->episode);
				$st->bindValue(':episode', $episode_id); // Pode ser null inicialmente
				$st->bindValue(':changed', !empty($action->timestamp) ? strtotime($action->timestamp) : $timestamp);
				$st->bindValue(':action', strtolower($action->action));
				$st->bindValue(':device', $device_id);
				unset($action->action, $action->episode, $action->podcast, $action->device);
				$st->bindValue(':data', json_encode($action, JSON_THROW_ON_ERROR));
				$st->execute();
			}
	
			$this->db->commit();
	
			return compact('timestamp') + ['update_urls' => []];
		}
		catch (Exception $e) {
			$this->db->rollBack();
			throw $e;
		}
	}

	public function opml(array $data): string
	{
		$out = '<?xml version="1.0" encoding="utf-8"?>';
		$out .= PHP_EOL . '<opml version="1.0"><head><title>My Feeds</title></head><body>';

		foreach ($data as $row) {
			$out .= PHP_EOL . sprintf('<outline type="rss" xmlUrl="%s" />',
					htmlspecialchars($row ?? '', ENT_XML1)
				);
		}

		$out .= PHP_EOL . '</body></opml>';
		return $out;
	}
}
