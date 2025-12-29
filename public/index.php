<?php

// Carga de las dependencias
require_once __DIR__ . "/../vendor/autoload.php";

// Configuración para las cors
require_once __DIR__ . "/../config/cors-control.php";

// Configuraciones del sistema
require_once __DIR__ . "/../config/config.php";

// Configuracion de las rutas
require_once __DIR__ . "/../routes/api.php";

Flight::start();
