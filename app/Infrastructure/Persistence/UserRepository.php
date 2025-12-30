<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Application\Interfaces\DatabaseInterface;
use PDO;
use DateTime;

class UserRepository implements UserRepositoryInterface
{
    private DatabaseInterface $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    public function findByEmail(string $email): ?User
    {
        try {
            $conn = $this->database->getConnection();
            $query = "
                SELECT cod_user, full_name, email, password_hash, avatar_url, 
                       is_verified, cod_user_status, last_login_at, created_at, 
                       updated_at, deleted_at 
                FROM users 
                WHERE email = :email AND deleted_at IS NULL
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? $this->mapToEntity($row) : null;
        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al buscar usuario por email: " . $e->getMessage());
        }
    }

    public function create(User $user): User
    {
        try {
            $conn = $this->database->getConnection();
            $query = "
                INSERT INTO users (full_name, email, password_hash, avatar_url, 
                                 is_verified, cod_user_status, created_at, updated_at) 
                VALUES (:full_name, :email, :password_hash, :avatar_url, 
                        :is_verified, :cod_user_status, :created_at, :updated_at)
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindValue(':full_name', $user->getFullName());
            $stmt->bindValue(':email', $user->getEmail());
            $stmt->bindValue(':password_hash', $user->getPasswordHash());
            $stmt->bindValue(':avatar_url', $user->getAvatarUrl());
            $stmt->bindValue(':is_verified', $user->isVerified(), PDO::PARAM_BOOL);
            $stmt->bindValue(':cod_user_status', $user->getCodUserStatus());
            $stmt->bindValue(':created_at', $user->getCreatedAt()->format('Y-m-d H:i:s'));
            $stmt->bindValue(':updated_at', $user->getUpdatedAt()->format('Y-m-d H:i:s'));

            $stmt->execute();
            
            $userId = $conn->lastInsertId();
            
            // Devolver el usuario con el ID asignado
            return $this->findById((int)$userId);
        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al crear usuario: " . $e->getMessage());
        }
    }

    public function findById(int $id): ?User
    {
        try {
            $conn = $this->database->getConnection();
            $query = "
                SELECT cod_user, full_name, email, password_hash, avatar_url, 
                       is_verified, cod_user_status, last_login_at, created_at, 
                       updated_at, deleted_at 
                FROM users 
                WHERE cod_user = :id AND deleted_at IS NULL
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? $this->mapToEntity($row) : null;
        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al buscar usuario por ID: " . $e->getMessage());
        }
    }

    public function updateVerificationStatus(int $userId, bool $isVerified): bool
    {
        try {
            $conn = $this->database->getConnection();
            $query = "
                UPDATE users 
                SET is_verified = :is_verified, updated_at = :updated_at 
                WHERE cod_user = :user_id AND deleted_at IS NULL
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindValue(':is_verified', $isVerified, PDO::PARAM_BOOL);
            $stmt->bindValue(':updated_at', (new DateTime())->format('Y-m-d H:i:s'));
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            throw new \RuntimeException("Error al actualizar estado de verificación: " . $e->getMessage());
        }
    }

    /**
     * Map database row data to User entity
     *
     * @param array<string,mixed> $row Database row data
     * @return User
     */
    private function mapToEntity(array $row): User
    {
        return new User(
            $row['cod_user'],
            $row['full_name'],
            $row['email'],
            $row['password_hash'],
            $row['avatar_url'],
            (bool)$row['is_verified'],
            $row['cod_user_status'],
            $row['last_login_at'] ? new DateTime($row['last_login_at']) : null,
            new DateTime($row['created_at']),
            new DateTime($row['updated_at']),
            $row['deleted_at'] ? new DateTime($row['deleted_at']) : null
        );
    }
}