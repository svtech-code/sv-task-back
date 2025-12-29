<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\TaskStatus;
use App\Domain\Repositories\TaskStatusRepositoryInterface;
use App\Application\Interfaces\DatabaseInterface;
use PDO;
use PDOException;
use RuntimeException;

class TaskStatusRepository implements TaskStatusRepositoryInterface
{
    private DatabaseInterface $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findAll(): array
    {
        try {
            $conn = $this->database->getConnection();
            $query = "select cod_task_status, desc_task_status from task_status order by cod_task_status asc;";

            $stmt = $conn->prepare($query);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(function ($row) {
                return $this->mapToEntity($row);
            }, $rows);
        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al obtener estados de las tareas: " . $e->getMessage());
        }
    }

    private function mapToEntity(array $row): TaskStatus
    {
        return new TaskStatus(
            $row['cod_task_status'],
            $row['desc_task_status']
        );
    }
}
