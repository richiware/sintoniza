<?php

class DB {
	protected $pdo;
	protected $statements = [];

	public function __construct(array $config)
	{
		$dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', 
			$config['host'] ?? 'localhost',
			$config['database']
		);

		$this->pdo = new PDO($dsn, 
			$config['username'] ?? 'root',
			$config['password'] ?? '',
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
			]
		);
	}

	public function upsert(string $table, array $params, array $conflict_columns): ?PDOStatement
	{
		$columns = array_keys($params);
		$values = array_map(fn($col) => ":$col", $columns);
		$updates = array_map(fn($col) => "$col = VALUES($col)", $columns);

		$sql = sprintf(
			'INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s',
			$table,
			implode(', ', $columns),
			implode(', ', $values),
			implode(', ', $updates)
		);

		return $this->simple($sql, $params);
	}

	public function prepare2(string $sql, ...$params): PDOStatement
	{
		$hash = md5($sql);

		if (!array_key_exists($hash, $this->statements)) {
			$st = $this->statements[$hash] = $this->pdo->prepare($sql);
		}
		else {
			$st = $this->statements[$hash];
		}

		if (isset($params[0]) && is_array($params[0])) {
			$params = $params[0];
		}

		foreach ($params as $key => $value) {
			if (is_int($key)) {
				$st->bindValue($key + 1, $value);
			}
			else {
				$st->bindValue(':' . $key, $value);
			}
		}

		return $st;
	}

	public function simple(string $sql, ...$params): ?PDOStatement
	{
		$st = $this->prepare2($sql, ...$params);
		$st->execute();
		return $st;
	}

	public function firstRow(string $sql, ...$params): ?\stdClass
	{
		$row = $this->simple($sql, ...$params)->fetch();
		return $row ? (object) $row : null;
	}

	public function firstColumn(string $sql, ...$params)
	{
		return $this->simple($sql, ...$params)->fetchColumn();
	}

	public function rowsFirstColumn(string $sql, ...$params): array
	{
		$res = $this->simple($sql, ...$params);
		$out = [];

		while ($row = $res->fetchColumn()) {
			$out[] = $row;
		}

		return $out;
	}

	public function iterate(string $sql, ...$params): \Generator
	{
		$res = $this->simple($sql, ...$params);

		while ($row = $res->fetch()) {
			yield (object) $row;
		}
	}

	public function all(string $sql, ...$params): array
	{
		return iterator_to_array($this->iterate($sql, ...$params));
	}

	public function beginTransaction(): bool
	{
		return $this->pdo->beginTransaction();
	}

	public function commit(): bool
	{
		return $this->pdo->commit();
	}

	public function rollBack(): bool
	{
		return $this->pdo->rollBack();
	}

	public function lastInsertId(): string
	{
		return $this->pdo->lastInsertId();
	}

	public function exec(string $sql): int|false
	{
		return $this->pdo->exec($sql);
	}
}
