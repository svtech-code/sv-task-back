<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\UserEmailVerification;

interface UserEmailVerificationRepositoryInterface
{
    /**
     * Create a new email verification record
     *
     * @param UserEmailVerification $verification
     * @return UserEmailVerification
     */
    public function create(UserEmailVerification $verification): UserEmailVerification;

    /**
     * Find verification by token
     *
     * @param string $token
     * @return UserEmailVerification|null
     */
    public function findByToken(string $token): ?UserEmailVerification;

    /**
     * Mark verification as completed
     *
     * @param string $token
     * @return bool
     */
    public function markAsVerified(string $token): bool;

    /**
     * Delete expired verification tokens
     *
     * @return int Number of deleted records
     */
    public function deleteExpired(): int;
}