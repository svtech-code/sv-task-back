<?php

namespace App\Domain\Entities;

class TaskStatus
{
    private ?int $codTaskStatus;
    private string $desc_task_status;

    public function __construct(?int $codTaskStatus, string $desc_task_status)
    {
        $this->codTaskStatus = $codTaskStatus;
        $this->desc_task_status = $desc_task_status;
    }

    public function getCodTaskStatus(): ?int
    {
        return $this->codTaskStatus;
    }

    public function getDescTaskStatus(): string
    {
        return $this->desc_task_status;
    }

    /**
     * Convert the entity to an array representation
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'cod_task_status' => $this->codTaskStatus,
            'desc_task_status' => $this->desc_task_status
        ];
    }
}
