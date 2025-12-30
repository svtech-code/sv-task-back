<?php

namespace App\Application\DTOs;

class UserRegistrationRequestDTO
{
    private string $fullName;
    private string $email;
    private string $password;

    public function __construct(string $fullName, string $email, string $password)
    {
        $this->fullName = $fullName;
        $this->email = $email;
        $this->password = $password;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}