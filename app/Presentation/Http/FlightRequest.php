<?php

namespace App\Presentation\Http;

use App\Application\Interfaces\RequestInterface;
use Flight;

class FlightRequest implements RequestInterface
{
    public function getData(): object
    {
        return Flight::request()->data;
    }

    public function getParam(string $name): ?string
    {
        return Flight::request()->query[$name] ?? null;
    }

    public function getQueryParams(): array
    {
        return Flight::request()->query->getData();
    }
}
