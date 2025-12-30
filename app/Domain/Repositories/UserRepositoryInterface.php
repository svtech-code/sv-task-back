<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    /**
     * Find a user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Create a new user
     *
     * @param User $user
     * @return User
     */
    public function create(User $user): User;

    /**
     * Find a user by ID
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Update user verification status
     *
     * @param int $userId
     * @param bool $isVerified
     * @return bool
     */
    public function updateVerificationStatus(int $userId, bool $isVerified): bool;
}