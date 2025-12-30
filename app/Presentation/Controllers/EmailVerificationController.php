<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\EmailVerificationUseCase;
use App\Application\Interfaces\RequestInterface;
use App\Application\Interfaces\ResponseInterface;

class EmailVerificationController
{
    private EmailVerificationUseCase $emailVerificationUseCase;
    private ResponseInterface $response;

    public function __construct(
        EmailVerificationUseCase $emailVerificationUseCase,
        ResponseInterface $response
    ) {
        $this->emailVerificationUseCase = $emailVerificationUseCase;
        $this->response = $response;
    }

    public function verifyEmail(RequestInterface $request): void
    {
        try {
            // Obtener el token del query parameter
            $token = $request->getParam('token');

            if (empty($token)) {
                $this->response->json([
                    'success' => false,
                    'message' => 'Token de verificación requerido',
                    'errors' => ['token' => ['El parámetro token es requerido']]
                ], 400);
                return;
            }

            // Ejecutar caso de uso de verificación
            $result = $this->emailVerificationUseCase->verifyEmail($token);

            // Determinar código de respuesta HTTP
            $statusCode = $result->isSuccess() ? 200 : 400;

            // Enviar respuesta
            $this->response->json($result->toArray(), $statusCode);

        } catch (\Exception $e) {
            // Log del error
            error_log("Error en verificación de email: " . $e->getMessage());

            $this->response->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'errors' => ['system' => ['Error procesando la verificación']]
            ], 500);
        }
    }
}