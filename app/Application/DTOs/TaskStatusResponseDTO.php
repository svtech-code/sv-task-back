<?php

namespace App\Application\DTOs;

class TaskStatusResponseDTO
{
    private bool $success;
    private string $message;
    private ?array $data;

    public function __construct(bool $success, string $message, ?array $data)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
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

        return $response;
    }
}
