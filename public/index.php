<?php
require __DIR__ . '/../vendor/autoload.php';

use Memory\Player;
use Memory\Game;
use Memory\Config\Database;

try {
    $pdo = Database::getConnection();
    $dbStatus = 'OK';
} catch (Throwable $e) {
    $pdo = null;
    $dbStatus = 'Erreur DB: ' . $e->getMessage();
}

// Exemple minimal : créer une partie si la DB est accessible
if ($pdo) {
    $game = new Game(9); // 9 paires = 18 cartes
    $cards = $game->getCards();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Game - Accueil</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: url('assets/bg-lion.svg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 20px;
        }

        .nav-bar {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 15px 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .nav-bar a {
            color: #1e3a8a;
            text-decoration: none;
            padding: 12px 25px;
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            color: white;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-bar a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
        }

        .nav-bar a.active {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            color: #1e3a8a;
            font-size: 3em;
            margin-bottom: 20px;
        }

        .status {
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .status.ok {
            background: #d4edda;
            color: #155724;
        }

        .status.error {
            background: #f8d7da;
            color: #721c24;
        }

        .info {
            color: #666;
            margin-bottom: 20px;
            font-size: 1.1em;
        }

        .welcome-text {
            color: #3b82f6;
            font-size: 1.3em;
            margin: 30px 0;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .nav-bar {
                gap: 10px;
            }

            .nav-bar a {
                padding: 10px 15px;
                font-size: 0.9em;
            }
        }
    </style>
</head>

<body>
    <nav class="nav-bar">
        <a href="index.php" class="active">🏠 Accueil</a>
        <a href="play.php">🎮 Jouer</a>
        <a href="login.php">🔐 Connexion</a>
        <a href="register.php">📝 Inscription</a>
        <a href="Top.php">🏆 Classement</a>
    </nav>

    <div class="container">
        <h1>🎮 Memory Game</h1>

        <div class="status <?= $pdo ? 'ok' : 'error' ?>">
            DB: <?= htmlspecialchars($dbStatus) ?>
        </div>

        <?php if ($pdo): ?>
            <div class="info">
                Partie prête avec <?= count($cards) ?> cartes
            </div>
        <?php endif; ?>

        <div class="welcome-text">
            <p>Bienvenue sur Memory Game ! 🦁</p>
            <p>Testez votre mémoire en retrouvant les paires de cartes.</p>
            <p>Cliquez sur "Jouer" pour commencer une partie !</p>
        </div>
    </div>
</body>

</html>