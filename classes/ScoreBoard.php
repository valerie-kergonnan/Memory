<?php

namespace Memory;

use PDO;
use Memory\Config\Database;

class ScoreBoard
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    public function saveGame(int $userId, int $pairs, int $attempts, int $duration): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO games (user_id, pairs, attempts, duration) VALUES (:user_id, :pairs, :attempts, :duration)"
            );
            return $stmt->execute([
                'user_id' => $userId,
                'pairs' => $pairs,
                'attempts' => $attempts,
                'duration' => $duration
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function updateLeaderboard(int $userId, int $score, int $time): bool
    {
        try {
            // Vérifier si l'utilisateur existe déjà dans le leaderboard
            $stmt = $this->pdo->prepare(
                "SELECT id, best_score, best_time FROM leaderboard WHERE user_id = :user_id"
            );
            $stmt->execute(['user_id' => $userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                // Mettre à jour si le nouveau score/temps est meilleur
                if ($score > $row['best_score'] || $time < $row['best_time']) {
                    $stmt = $this->pdo->prepare(
                        "UPDATE leaderboard SET best_score = :score, best_time = :time, updated_at = NOW() WHERE user_id = :user_id"
                    );
                    return $stmt->execute(['score' => $score, 'time' => $time, 'user_id' => $userId]);
                }
            } else {
                // Insérer nouveau record
                $stmt = $this->pdo->prepare(
                    "INSERT INTO leaderboard (user_id, best_score, best_time) VALUES (:user_id, :score, :time)"
                );
                return $stmt->execute(['user_id' => $userId, 'score' => $score, 'time' => $time]);
            }
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function top(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.username, l.best_score, l.best_time 
             FROM leaderboard l 
             JOIN users u ON u.id = l.user_id 
             ORDER BY l.best_score DESC, l.best_time ASC 
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserStats(int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as total_games, AVG(attempts) as avg_attempts, MIN(duration) as best_time 
             FROM games 
             WHERE user_id = :user_id"
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
