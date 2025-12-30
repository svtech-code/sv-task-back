<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Repositories\UserEmailVerificationRepositoryInterface;
use App\Application\Interfaces\EmailServiceInterface;
use App\Application\DTOs\TaskStatusResponseDTO;

class EmailVerificationUseCase
{
    private UserRepositoryInterface $userRepository;
    private UserEmailVerificationRepositoryInterface $verificationRepository;
    private EmailServiceInterface $emailService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserEmailVerificationRepositoryInterface $verificationRepository,
        EmailServiceInterface $emailService
    ) {
        $this->userRepository = $userRepository;
        $this->verificationRepository = $verificationRepository;
        $this->emailService = $emailService;
    }

    public function verifyEmail(string $token): TaskStatusResponseDTO
    {
        try {
            // Buscar el token de verificación
            $verification = $this->verificationRepository->findByToken($token);

            if (!$verification) {
                return new TaskStatusResponseDTO(
                    false,
                    'Token de verificación inválido o no encontrado',
                    null
                );
            }

            // Verificar si ya fue verificado
            if ($verification->isVerified()) {
                return new TaskStatusResponseDTO(
                    false,
                    'Este correo ya ha sido verificado anteriormente',
                    ['already_verified' => true]
                );
            }

            // Verificar si el token ha expirado
            if ($verification->isExpired()) {
                return new TaskStatusResponseDTO(
                    false,
                    'El token de verificación ha expirado. Solicita un nuevo token',
                    ['expired' => true]
                );
            }

            // Buscar el usuario
            $user = $this->userRepository->findById($verification->getCodUser());
            if (!$user) {
                return new TaskStatusResponseDTO(
                    false,
                    'Usuario no encontrado',
                    null
                );
            }

            // Marcar como verificado en la tabla de verificaciones
            $verificationUpdated = $this->verificationRepository->markAsVerified($token);
            if (!$verificationUpdated) {
                return new TaskStatusResponseDTO(
                    false,
                    'Error al procesar la verificación',
                    null
                );
            }

            // Actualizar el estado de verificación del usuario
            $userUpdated = $this->userRepository->updateVerificationStatus(
                $user->getCodUser(),
                true
            );

            if (!$userUpdated) {
                return new TaskStatusResponseDTO(
                    false,
                    'Error al actualizar el estado del usuario',
                    null
                );
            }

            // Enviar email de bienvenida
            $welcomeEmailSent = $this->emailService->sendWelcomeEmail(
                $user->getEmail(),
                $user->getFullName()
            );

            return new TaskStatusResponseDTO(
                true,
                'Correo verificado exitosamente. ¡Bienvenido!',
                [
                    'user_id' => $user->getCodUser(),
                    'email' => $user->getEmail(),
                    'verified_at' => date('Y-m-d H:i:s'),
                    'welcome_email_sent' => $welcomeEmailSent
                ]
            );

        } catch (\Exception $e) {
            return new TaskStatusResponseDTO(
                false,
                'Error interno del servidor durante la verificación',
                ['error' => $e->getMessage()]
            );
        }
    }
}