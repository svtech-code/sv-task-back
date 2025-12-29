<?php

namespace App\Presentation\Http;

use App\Application\Interfaces\ResponseInterface;
use Flight;

class FlightResponse implements ResponseInterface
{
    public function json(array $data, int $statusCode = 200): void
    {
        Flight::response()->status($statusCode);
        Flight::response()->header('Content-Type', 'application/json');
        Flight::response()->write(json_encode($data));
        Flight::response()->send();
        exit;
    }
}
