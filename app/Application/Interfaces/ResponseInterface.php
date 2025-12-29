<?php

namespace App\Application\Interfaces;

interface ResponseInterface
{
    public function json(array $data, int $statusCode = 200): void;
}
