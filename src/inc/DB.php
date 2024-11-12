<?php

class DB extends PDO
{
    protected array $statements = [];

    public function __construct(string $host, string $dbname, string $user, string $password)
    {
        $dsn = sprintf('mysql:host=%s;charset=utf8mb4', $host);
        
        // Primeiro conecta sem selecionar o banco de dados
        parent::__construct($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_FOUND_ROWS => true
        ]);

        $this->exec('SET time_zone = "+00:00"');
        
        // Verifica se o banco de dados existe
        try {
            $this->exec(sprintf('USE `%s`', $dbname));
        }
        catch (PDOException $e) {
            if ($e->getCode() == 1049) { // Database doesn't exist
                $this->createDatabase($dbname);
            } else {
                throw $e;
            }
        }

        // Verifica se as tabelas existem
        if (!$this->checkTablesExist()) {
            $this->installSchema();
        }
    }

    protected function createDatabase(string $dbname): void
    {
        $this->exec(sprintf('CREATE DATABASE IF NOT EXISTS `%s` 
            DEFAULT CHARACTER SET utf8mb4 
            DEFAULT COLLATE utf8mb4_general_ci', $dbname));
        $this->exec(sprintf('USE `%s`', $dbname));
    }

    protected function checkTablesExist(): bool
    {
        $requiredTables = ['users', 'devices', 'episodes', 'episodes_actions', 'feeds', 'subscriptions'];
        $existingTables = $this->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        return count(array_intersect($requiredTables, $existingTables)) === count($requiredTables);
    }

    protected function installSchema(): void
    {
        $sqlFile = __DIR__ . '/mysql.sql';
        
        if (!file_exists($sqlFile)) {
            throw new RuntimeException(__('db.schema_not_found'));
        }

        $sql = file_get_contents($sqlFile);
        
        $commands = array_filter(
            array_map(
                'trim',
                preg_split("/;[\r\n]+/", $sql)
            )
        );

        $this->exec('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($commands as $command) {
            if (empty($command)) continue;
            
            if (preg_match('/^(\/\*|SET|--)/i', trim($command))) {
                continue;
            }

            try {
                $this->exec($command);
            }
            catch (PDOException $e) {
                $this->exec('SET FOREIGN_KEY_CHECKS = 1');
                throw new RuntimeException(
                    sprintf("Error: %s\n - %s", 
                        $e->getMessage(), 
                        $command
                    )
                );
            }
        }

        $this->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function upsert(string $table, array $params, array $conflict_columns): ?PDOStatement
    {
        $columns = array_keys($params);
        $placeholders = array_map(fn($col) => ":$col", $columns);
        $updates = array_map(fn($col) => "$col = VALUES($col)", $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders),
            implode(', ', $updates)
        );

        return $this->simple($sql, $params);
    }

    public function prepare2(string $sql, ...$params): PDOStatement
    {
        $hash = md5($sql);

        if (!array_key_exists($hash, $this->statements)) {
            $st = $this->statements[$hash] = $this->prepare($sql);
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

    public function firstRow(string $sql, ...$params): ?stdClass
    {
        $st = $this->simple($sql, ...$params);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function firstColumn(string $sql, ...$params)
    {
        $st = $this->simple($sql, ...$params);
        return $st->fetchColumn() ?: null;
    }

    public function rowsFirstColumn(string $sql, ...$params): array
    {
        $st = $this->simple($sql, ...$params);
        return $st->fetchAll(PDO::FETCH_COLUMN);
    }

    public function iterate(string $sql, ...$params): Generator
    {
        $st = $this->simple($sql, ...$params);
        
        while ($row = $st->fetch()) {
            yield $row;
        }
    }

    public function all(string $sql, ...$params): array
    {
        return iterator_to_array($this->iterate($sql, ...$params));
    }
}
