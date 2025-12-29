<?php

use App\Presentation\Http\Router;

// Carga de rutas
Router::loadRoutes();

// Rutas sin protección
Flight::route("GET /", function () {
    Flight::json([
        "success" => true,
        "data" => [
            "message" => "API sv-task - Backend",
            "version" => "1.0.0",
            "status" => "active",
            "endpoint" => [
              "GET /" => "Health check"
            ]
        ]
    ]);
});
