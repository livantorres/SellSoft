<?php
declare(strict_types=1);

namespace SellSoft\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static $instance = null;
    private $pdo;
    private $queryCount = 0;

    private function __construct()
    {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone='-05:00'",
        ];
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $this->handleConnectionError($e);
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __clone() {}

    public function query(string $sql, array $params = []): PDOStatement
    {
        $this->queryCount++;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result !== false ? $result : null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert(string $sql, array $params = []): int
    {
        $this->query($sql, $params);
        return (int) $this->pdo->lastInsertId();
    }

    public function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

    public function beginTransaction(): void { $this->pdo->beginTransaction(); }
    public function commit(): void           { $this->pdo->commit(); }
    public function rollBack(): void         { if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); } }
    public function getPdo(): PDO            { return $this->pdo; }
    public function getQueryCount(): int     { return $this->queryCount; }

    private function handleConnectionError(PDOException $e): void
    {
        if (APP_DEBUG) {
            die('<h1>Database Connection Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
        }
        error_log('DB connection error: ' . $e->getMessage());
        die('Database connection error. Please contact the administrator.');
    }
}
