<?php

namespace Memory;

use PDO;
use Memory\Config\Database;

class Player
{
    private PDO $pdo;
    private ?int $id = null;
    private string $username;
    private string $email;
    private string $passwordHash;

    public function __construct(?PDO $pdo = null, string $username = '', string $email = '', string $password = '')
    {
        $this->pdo = $pdo ?? Database::getConnection();
        $this->username = $username;
        $this->email = $email;
        $this->passwordHash = $password ? password_hash($password, PASSWORD_BCRYPT) : '';

        if ($username) {
            $stmt = $this->pdo->prepare("SELECT id, email FROM users WHERE username = :username");
            $stmt->execute(['username' => $this->username]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $this->id = (int)$row['id'];
                $this->email = $row['email'];
            }
        }
    }

    public function register(string $username, string $email, string $password): bool
    {
        try {
            $this->username = $username;
            $this->email = $email;
            $this->passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $this->pdo->prepare(
                "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)"
            );
            $stmt->execute([
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->passwordHash
            ]);
            $this->id = (int)$this->pdo->lastInsertId();
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function login(string $username, string $password): bool
    {
        $stmt = $this->pdo->prepare("SELECT id, email, password FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password'])) {
            $this->id = (int)$row['id'];
            $this->username = $username;
            $this->email = $row['email'];
            return true;
        }
        return false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
