<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\UserRegistrationUseCase;
use App\Application\DTOs\UserRegistrationRequestDTO;
use App\Application\Interfaces\RequestInterface;
use App\Application\Interfaces\ResponseInterface;

class UserRegistrationController
{
    private UserRegistrationUseCase $userRegistrationUseCase;
    private ResponseInterface $response;

    public function __construct(
        UserRegistrationUseCase $userRegistrationUseCase,
        ResponseInterface $response
    ) {
        $this->userRegistrationUseCase = $userRegistrationUseCase;
        $this->response = $response;
    }

    public function register(RequestInterface $request): void
    {
        try {
            // Obtener los datos del request
            $data = $request->getData();

            // Validar que existan los campos requeridos
            if (!isset($data->full_name) || !isset($data->email) || !isset($data->password)) {
                $this->response->json([
                    'success' => false,
                    'message' => 'Campos requeridos faltantes',
                    'errors' => [
                        'fields' => ['full_name', 'email', 'password son requeridos']
                    ]
                ], 400);
                return;
            }

            // Crear DTO con los datos de entrada
            $registrationRequest = new UserRegistrationRequestDTO(
                trim((string)$data->full_name),
                trim(strtolower((string)$data->email)),
                (string)$data->password
            );

            // Ejecutar caso de uso
            $result = $this->userRegistrationUseCase->execute($registrationRequest);

            // Determinar código de respuesta HTTP
            $statusCode = $result->isSuccess() ? 201 : 400;

            // Enviar respuesta
            $this->response->json($result->toArray(), $statusCode);

        } catch (\Exception $e) {
            // Log del error
            error_log("Error en registro de usuario: " . $e->getMessage());

            $this->response->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'errors' => ['system' => ['Error procesando la solicitud']]
            ], 500);
        }
    }
}