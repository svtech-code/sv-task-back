<?php

namespace App\Application\Interfaces;

interface ResponseInterface
{
    /**
     * Send a JSON response
     *
     * @param array $data The data to be encoded as JSON
     * @param int $statusCode The HTTP status code (default: 200)
     * @return void
     */
    public function json(array $data, int $statusCode = 200): void;
}
