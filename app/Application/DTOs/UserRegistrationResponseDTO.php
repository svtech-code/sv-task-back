<?php

namespace App\Application\DTOs;

class UserRegistrationResponseDTO
{
    private bool $success;
    private string $message;
    private ?array $data;
    private ?array $errors;

    public function __construct(bool $success, string $message, ?array $data = null, ?array $errors = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
        $this->errors = $errors;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    /**
     * Convert the DTO to an array representation
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $response = [
            'success' => $this->success,
            'message' => $this->message
        ];

        if ($this->data !== null) {
            $response['data'] = $this->data;
        }

        if ($this->errors !== null) {
            $response['errors'] = $this->errors;
        }

        return $response;
    }
}