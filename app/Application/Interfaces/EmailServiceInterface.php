<?php

namespace App\Application\Interfaces;

interface EmailServiceInterface
{
    /**
     * Send verification email
     *
     * @param string $email Recipient email
     * @param string $name Recipient name
     * @param string $token Verification token
     * @return bool
     */
    public function sendVerificationEmail(string $email, string $name, string $token): bool;

    /**
     * Send welcome email
     *
     * @param string $email Recipient email
     * @param string $name Recipient name
     * @return bool
     */
    public function sendWelcomeEmail(string $email, string $name): bool;
}