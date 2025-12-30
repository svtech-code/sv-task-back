<?php

/**
 * Task Status Routes
 *
 * This file contains route definitions and does not follow PSR-4 namespace conventions
 * as it's not a class file but a configuration file for routes.
 */

Flight::route('GET /taskStatus', function () {
    $controller = Flight::taskStatusController();
    $controller->getAll();
});
