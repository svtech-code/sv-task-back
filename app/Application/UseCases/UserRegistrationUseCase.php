<?php

namespace App\Application\UseCases;

use App\Domain\Entities\User;
use App\Domain\Entities\UserEmailVerification;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Repositories\UserEmailVerificationRepositoryInterface;
use App\Application\Interfaces\EmailServiceInterface;
use App\Application\DTOs\UserRegistrationRequestDTO;
use App\Application\DTOs\UserRegistrationResponseDTO;
use DateTime;

class UserRegistrationUseCase
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

    public function execute(UserRegistrationRequestDTO $request): UserRegistrationResponseDTO
    {
        try {
            // Validar entrada
            $validationErrors = $this->validateRequest($request);
            if (!empty($validationErrors)) {
                return new UserRegistrationResponseDTO(
                    false,
                    'Datos de entrada inválidos',
                    null,
                    $validationErrors
                );
            }

            // Verificar si el usuario ya existe
            $existingUser = $this->userRepository->findByEmail($request->getEmail());
            if ($existingUser !== null) {
                return new UserRegistrationResponseDTO(
                    false,
                    'Ya existe una cuenta con este correo electrónico',
                    null,
                    ['email' => ['El correo electrónico ya está registrado']]
                );
            }

            // Crear nuevo usuario
            $passwordHash = password_hash($request->getPassword(), PASSWORD_BCRYPT);
            $user = new User(
                null,
                $request->getFullName(),
                $request->getEmail(),
                $passwordHash,
                null,
                false, // is_verified = false
                1 // cod_user_status = 1 (activo)
            );

            $createdUser = $this->userRepository->create($user);

            // Generar token de verificación
            $token = $this->generateVerificationToken();
            $expiresAt = new DateTime('+24 hours'); // Token válido por 24 horas

            $verification = new UserEmailVerification(
                null,
                $createdUser->getCodUser(),
                $token,
                $expiresAt
            );

            $this->verificationRepository->create($verification);

            // Enviar email de verificación
            $emailSent = $this->emailService->sendVerificationEmail(
                $request->getEmail(),
                $request->getFullName(),
                $token
            );

            if (!$emailSent) {
                return new UserRegistrationResponseDTO(
                    false,
                    'Usuario creado pero no se pudo enviar el correo de verificación. Contacta soporte.',
                    ['user_id' => $createdUser->getCodUser()]
                );
            }

            return new UserRegistrationResponseDTO(
                true,
                'Usuario registrado exitosamente. Revisa tu correo para verificar tu cuenta.',
                [
                    'user_id' => $createdUser->getCodUser(),
                    'email' => $createdUser->getEmail(),
                    'verification_sent' => true
                ]
            );

        } catch (\Exception $e) {
            return new UserRegistrationResponseDTO(
                false,
                'Error interno del servidor. Inténtalo de nuevo.',
                null,
                ['system' => [$e->getMessage()]]
            );
        }
    }

    /**
     * Validate the registration request
     *
     * @param UserRegistrationRequestDTO $request
     * @return array<string,array<string>>
     */
    private function validateRequest(UserRegistrationRequestDTO $request): array
    {
        $errors = [];

        // Validar nombre
        if (empty(trim($request->getFullName()))) {
            $errors['full_name'][] = 'El nombre completo es requerido';
        } elseif (strlen($request->getFullName()) < 2) {
            $errors['full_name'][] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($request->getFullName()) > 120) {
            $errors['full_name'][] = 'El nombre no puede exceder 120 caracteres';
        }

        // Validar email
        if (empty(trim($request->getEmail()))) {
            $errors['email'][] = 'El correo electrónico es requerido';
        } elseif (!filter_var($request->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'El formato del correo electrónico es inválido';
        } elseif (strlen($request->getEmail()) > 150) {
            $errors['email'][] = 'El correo electrónico no puede exceder 150 caracteres';
        }

        // Validar password
        if (empty($request->getPassword())) {
            $errors['password'][] = 'La contraseña es requerida';
        } elseif (strlen($request->getPassword()) < 8) {
            $errors['password'][] = 'La contraseña debe tener al menos 8 caracteres';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $request->getPassword())) {
            $errors['password'][] = 'La contraseña debe contener al menos una mayúscula, una minúscula y un número';
        }

        return $errors;
    }

    /**
     * Generate a secure verification token
     *
     * @return string
     */
    private function generateVerificationToken(): string
    {
        return bin2hex(random_bytes(50)); // 100 caracteres hexadecimales
    }
}