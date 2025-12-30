<?php

namespace App\Domain\Entities;

use DateTime;

class User
{
    private ?int $codUser;
    private string $fullName;
    private string $email;
    private ?string $passwordHash;
    private ?string $avatarUrl;
    private bool $isVerified;
    private int $codUserStatus;
    private ?DateTime $lastLoginAt;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private ?DateTime $deletedAt;

    public function __construct(
        ?int $codUser,
        string $fullName,
        string $email,
        ?string $passwordHash = null,
        ?string $avatarUrl = null,
        bool $isVerified = false,
        int $codUserStatus = 1, // Asumiendo que 1 es "activo"
        ?DateTime $lastLoginAt = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null,
        ?DateTime $deletedAt = null
    ) {
        $this->codUser = $codUser;
        $this->fullName = $fullName;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->avatarUrl = $avatarUrl;
        $this->isVerified = $isVerified;
        $this->codUserStatus = $codUserStatus;
        $this->lastLoginAt = $lastLoginAt;
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();
        $this->deletedAt = $deletedAt;
    }

    public function getCodUser(): ?int
    {
        return $this->codUser;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): void
    {
        $this->isVerified = $isVerified;
        $this->updatedAt = new DateTime();
    }

    public function getCodUserStatus(): int
    {
        return $this->codUserStatus;
    }

    public function getLastLoginAt(): ?DateTime
    {
        return $this->lastLoginAt;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    /**
     * Convert the entity to an array representation
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'cod_user' => $this->codUser,
            'full_name' => $this->fullName,
            'email' => $this->email,
            'avatar_url' => $this->avatarUrl,
            'is_verified' => $this->isVerified,
            'cod_user_status' => $this->codUserStatus,
            'last_login_at' => $this->lastLoginAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s')
        ];
    }
}