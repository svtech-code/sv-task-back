<?php

use Dotenv\Dotenv;
use App\Application\UseCases\TaskStatusUseCase;
use App\Infrastructure\Persistence\Database;
use App\Presentation\Http\FlightRequest;
use App\Presentation\Http\FlightResponse;
use App\Infrastructure\Persistence\TaskStatusRepository;
use App\Presentation\Controllers\TaskStatusController;

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

$container = [];

$container['database'] = function () {
    return new Database();
};

$container['request'] = function () {
    return new FlightRequest();
};

$container['response'] = function () {
    return new FlightResponse();
};


$container['taskStatusRepository'] = function () use ($container) {
    return new TaskStatusRepository($container['database']());
};

$container['taskStatusUseCase'] = function () use ($container) {
    return new TaskStatusUseCase($container['taskStatusRepository']());
};

$container['taskStatusController'] = function () use ($container) {
    return new TaskStatusController(
        $container['taskStatusUseCase'](),
        $container['response']()
    );
};

Flight::map('taskStatusController', $container['taskStatusController']);

return $container;
