<?php

namespace App\Presentation\Http;

class Router
{
    public static function loadRoutes(): void
    {
        $routesPath = __DIR__ . "/Routes";

        if (file_exists("$routesPath/task-status.php")) {
            require_once "$routesPath/task-status.php";
        }

        if (file_exists("$routesPath/user-registration.php")) {
            require_once "$routesPath/user-registration.php";
        }
    }
}
