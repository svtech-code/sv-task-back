<?php

namespace App\Domain\Entities;

use DateTime;

class UserEmailVerification
{
    private ?int $codVerification;
    private int $codUser;
    private string $token;
    private DateTime $expiresAt;
    private ?DateTime $verifiedAt;
    private DateTime $createdAt;

    public function __construct(
        ?int $codVerification,
        int $codUser,
        string $token,
        DateTime $expiresAt,
        ?DateTime $verifiedAt = null,
        ?DateTime $createdAt = null
    ) {
        $this->codVerification = $codVerification;
        $this->codUser = $codUser;
        $this->token = $token;
        $this->expiresAt = $expiresAt;
        $this->verifiedAt = $verifiedAt;
        $this->createdAt = $createdAt ?? new DateTime();
    }

    public function getCodVerification(): ?int
    {
        return $this->codVerification;
    }

    public function getCodUser(): int
    {
        return $this->codUser;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    public function getVerifiedAt(): ?DateTime
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(DateTime $verifiedAt): void
    {
        $this->verifiedAt = $verifiedAt;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function isExpired(): bool
    {
        return new DateTime() > $this->expiresAt;
    }

    public function isVerified(): bool
    {
        return $this->verifiedAt !== null;
    }

    /**
     * Convert the entity to an array representation
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'cod_verification' => $this->codVerification,
            'cod_user' => $this->codUser,
            'token' => $this->token,
            'expires_at' => $this->expiresAt->format('Y-m-d H:i:s'),
            'verified_at' => $this->verifiedAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }
}