<?php

/**
 * Configuration de la connexion à la base de données
 */

namespace Memory\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private const HOST = '127.0.0.1';
    private const DB_NAME = 'memory-game';
    private const USERNAME = 'root';
    private const PASSWORD = '';
    private const CHARSET = 'utf8mb4';

    /**
     * Obtenir une instance PDO singleton
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    self::HOST,
                    self::DB_NAME,
                    self::CHARSET
                );

                self::$instance = new PDO($dsn, self::USERNAME, self::PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                throw new PDOException('Erreur de connexion à la base de données: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    /**
     * Réinitialiser la connexion (utile pour les tests)
     */
    public static function resetConnection(): void
    {
        self::$instance = null;
    }
}
