<?php

namespace App\Domain\Repositories;

interface TaskStatusRepositoryInterface
{
    public function findAll(): array;
}
