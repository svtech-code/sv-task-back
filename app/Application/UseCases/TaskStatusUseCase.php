<?php

namespace App\Application\UseCases;

use App\Infrastructure\Persistence\TaskStatusRepository;
use App\Application\DTOs\TaskStatusResponseDTO;

class TaskStatusUseCase
{
    private TaskStatusRepository $taskStatusRepository;

    public function __construct(TaskStatusRepository $taskStatusRepository)
    {
        $this->taskStatusRepository = $taskStatusRepository;
    }

    public function execute(): TaskStatusResponseDTO
    {
        try {
            $responseTaskStatus = $this->taskStatusRepository->findAll();

            $response = array_map(function ($taskStatus) {
                return $taskStatus->toArray();
            }, $responseTaskStatus);

            return new TaskStatusResponseDTO(
                true,
                'Estados de las tareas obtenidas de manera exitosa',
                ['taskStatus' => $response]
            );
        } catch (\Exception $e) {
            return new TaskStatusResponseDTO(
                false,
                'Error al obtener los estados de las tareas: ' . $e->getMessage(),
                null
            );
        }
    }
}
