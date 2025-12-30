<?php
/**
 * User Registration Routes
 * 
 * This file contains route definitions for user registration and email verification
 * and does not follow PSR-4 namespace conventions as it's a configuration file.
 */

Flight::route('POST /register', function () {
    /** @var \App\Presentation\Controllers\UserRegistrationController $controller */
    $controller = Flight::userRegistrationController();
    /** @var \App\Presentation\Http\FlightRequest $request */
    $request = Flight::appRequest();
    $controller->register($request);
});

Flight::route('GET /verify-email', function () {
    /** @var \App\Presentation\Controllers\EmailVerificationController $controller */
    $controller = Flight::emailVerificationController();
    /** @var \App\Presentation\Http\FlightRequest $request */
    $request = Flight::appRequest();
    $controller->verifyEmail($request);
});