<?php

use Dotenv\Dotenv;
use App\Application\UseCases\TaskStatusUseCase;
use App\Application\UseCases\UserRegistrationUseCase;
use App\Application\UseCases\EmailVerificationUseCase;
use App\Infrastructure\Persistence\Database;
use App\Infrastructure\Services\EmailService;
use App\Presentation\Http\FlightRequest;
use App\Presentation\Http\FlightResponse;
use App\Infrastructure\Persistence\TaskStatusRepository;
use App\Infrastructure\Persistence\UserRepository;
use App\Infrastructure\Persistence\UserEmailVerificationRepository;
use App\Presentation\Controllers\TaskStatusController;
use App\Presentation\Controllers\UserRegistrationController;
use App\Presentation\Controllers\EmailVerificationController;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$dotenv->required([
    'DB_DRIVER',
    'DB_HOST',
    'DB_PORT',
    'DB_NAME',
    'DB_USER',
    'DB_PASS',
    'JWT_SECRET',
    'JWT_ALGORITHM',
    'JWT_EXPIRATION'
]);

// Registro de servicios usando Flight DI Container
// Usamos nombres únicos para evitar conflictos con servicios internos de Flight

// Servicios de infraestructura
Flight::register('database', Database::class);
Flight::register('emailService', EmailService::class);
Flight::register('appRequest', FlightRequest::class);
Flight::register('appResponse', FlightResponse::class);

// Repositorios
Flight::register('taskStatusRepository', TaskStatusRepository::class, [Flight::database()]);
Flight::register('userRepository', UserRepository::class, [Flight::database()]);
Flight::register('userEmailVerificationRepository', UserEmailVerificationRepository::class, [Flight::database()]);

// Casos de uso
Flight::register('taskStatusUseCase', TaskStatusUseCase::class, [Flight::taskStatusRepository()]);
Flight::register('userRegistrationUseCase', UserRegistrationUseCase::class, [
    Flight::userRepository(),
    Flight::userEmailVerificationRepository(),
    Flight::emailService()
]);
Flight::register('emailVerificationUseCase', EmailVerificationUseCase::class, [
    Flight::userRepository(),
    Flight::userEmailVerificationRepository(),
    Flight::emailService()
]);

// Controladores
Flight::register('taskStatusController', TaskStatusController::class, [
    Flight::taskStatusUseCase(),
    Flight::appResponse()
]);
Flight::register('userRegistrationController', UserRegistrationController::class, [
    Flight::userRegistrationUseCase(),
    Flight::appResponse()
]);
Flight::register('emailVerificationController', EmailVerificationController::class, [
    Flight::emailVerificationUseCase(),
    Flight::appResponse()
]);
