<?php

namespace App\Infrastructure\Persistence;

use App\Application\Interfaces\DatabaseInterface;
use PDO;
use PDOException;

class Database implements DatabaseInterface
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = $this->createConnection();
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    private function createConnection(): PDO
    {
        try {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $dbName = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'] ?? 'root';
            $password = $_ENV['DB_PASS'] ?? '';
            $driver = $_ENV['DB_DRIVER'] ?? 'mysql';
            $port = (int)($_ENV['DB_PORT'] ?? 3306);

            if (empty($dbName)) {
                throw new \RuntimeException("Variable DB_NAME es requerida");
            }

            // Construir DSN según el driver
            if ($driver === 'pgsql') {
                $dsn = "pgsql:host={$host};port={$port};dbname={$dbName}";
            } else {
                $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
            }

            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(PDO::ATTR_PERSISTENT, false);

            return $pdo;
        } catch (PDOException $e) {
            throw new \RuntimeException("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollback(): bool
    {
        return $this->connection->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->connection->inTransaction();
    }
}
