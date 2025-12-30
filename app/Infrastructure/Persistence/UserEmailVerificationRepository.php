<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\UserEmailVerification;
use App\Domain\Repositories\UserEmailVerificationRepositoryInterface;
use App\Application\Interfaces\DatabaseInterface;
use PDO;
use DateTime;

class UserEmailVerificationRepository implements UserEmailVerificationRepositoryInterface
{
    private DatabaseInterface $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function create(UserEmailVerification $verification): UserEmailVerification
    {
        try {
            $conn = $this->database->getConnection();
            $query = "
                INSERT INTO user_email_verification (cod_user, token, expires_at, created_at) 
                VALUES (:cod_user, :token, :expires_at, :created_at)
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindValue(':cod_user', $verification->getCodUser());
            $stmt->bindValue(':token', $verification->getToken());
            $stmt->bindValue(':expires_at', $verification->getExpiresAt()->format('Y-m-d H:i:s'));
            $stmt->bindValue(':created_at', $verification->getCreatedAt()->format('Y-m-d H:i:s'));

            $stmt->execute();

            $verificationId = $conn->lastInsertId();

            // Devolver la verificación con el ID asignado
            return $this->findById((int)$verificationId);
        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al crear verificación de email: " . $e->getMessage());
        }
    }

    public function findByToken(string $token): ?UserEmailVerification
    {
        try {
            $conn = $this->database->getConnection();
            $query = "
                SELECT cod_verification, cod_user, token, expires_at, verified_at, created_at 
                FROM user_email_verification 
                WHERE token = :token
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? $this->mapToEntity($row) : null;
        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al buscar verificación por token: " . $e->getMessage());
        }
    }

    public function markAsVerified(string $token): bool
    {
        try {
            $conn = $this->database->getConnection();
            $query = "
                UPDATE user_email_verification 
                SET verified_at = :verified_at 
                WHERE token = :token AND verified_at IS NULL
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindValue(':verified_at', (new DateTime())->format('Y-m-d H:i:s'));
            $stmt->bindValue(':token', $token);

            return $stmt->execute() && $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al marcar verificación como completada: " . $e->getMessage());
        }
    }

    public function deleteExpired(): int
    {
        try {
            $conn = $this->database->getConnection();
            $query = "
                DELETE FROM user_email_verification 
                WHERE expires_at < :now AND verified_at IS NULL
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindValue(':now', (new DateTime())->format('Y-m-d H:i:s'));
            $stmt->execute();

            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al eliminar tokens expirados: " . $e->getMessage());
        }
    }

    private function findById(int $id): UserEmailVerification
    {
        try {
            $conn = $this->database->getConnection();
            $query = "
                SELECT cod_verification, cod_user, token, expires_at, verified_at, created_at 
                FROM user_email_verification 
                WHERE cod_verification = :id
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $this->mapToEntity($row);
        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al buscar verificación por ID: " . $e->getMessage());
        }
    }

    /**
     * Map database row data to UserEmailVerification entity
     *
     * @param array<string,mixed> $row Database row data
     * @return UserEmailVerification
     */
    private function mapToEntity(array $row): UserEmailVerification
    {
        return new UserEmailVerification(
            $row['cod_verification'],
            $row['cod_user'],
            $row['token'],
            new DateTime($row['expires_at']),
            $row['verified_at'] ? new DateTime($row['verified_at']) : null,
            new DateTime($row['created_at'])
        );
    }
}
