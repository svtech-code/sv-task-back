<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\TaskStatusUseCase;
use App\Application\Interfaces\ResponseInterface;

class TaskStatusController
{
    private TaskStatusUseCase $taskStatusUseCase;
    private ResponseInterface $response;

    public function __construct(TaskStatusUseCase $taskStatusUseCase, ResponseInterface $response)
    {
        $this->taskStatusUseCase = $taskStatusUseCase;
        $this->response = $response;
    }

    public function getAll(): void
    {
        try {
            $result = $this->taskStatusUseCase->execute();
            $responseArray = $result->toArray();

            if ($responseArray['success']) {
                $this->response->json($responseArray, 200);
            } else {
                $this->response->json($responseArray, 500);
            }
        } catch (\Exception $e) {
            $this->response->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}
