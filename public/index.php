<?php
require __DIR__ . '/../vendor/autoload.php';

use Memory\Player;
use Memory\Game;
use Memory\Config\Database;

session_start();

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
            font-family: 'Georgia', serif;
            background: url('assets/bg-souk.svg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 20px;
        }

        .nav-bar {
            background: linear-gradient(135deg, rgba(255, 248, 220, 0.98) 0%, rgba(255, 243, 205, 0.98) 100%);
            border-radius: 15px;
            padding: 15px 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(193, 120, 23, 0.3);
            border: 2px solid rgba(247, 147, 30, 0.3);
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .nav-bar a {
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            background: linear-gradient(135deg, #F7931E 0%, #C17817 100%);
            border-radius: 12px;
            font-weight: bold;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(193, 120, 23, 0.3);
        }

        .nav-bar a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(247, 147, 30, 0.5);
            background: linear-gradient(135deg, #FBB03B 0%, #F7931E 100%);
        }

        .nav-bar a.active {
            background: linear-gradient(135deg, #C17817 0%, #8B5A0F 100%);
            box-shadow: 0 4px 15px rgba(139, 90, 15, 0.4);
        }

        .container {
            background: linear-gradient(135deg, rgba(255, 248, 220, 0.98) 0%, rgba(255, 243, 205, 0.95) 100%);
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(193, 120, 23, 0.4);
            border: 3px solid rgba(247, 147, 30, 0.4);
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            color: #C17817;
            font-size: 3em;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(139, 90, 15, 0.2);
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
            color: #8B5A0F;
            margin-bottom: 20px;
            font-size: 1.1em;
        }

        .welcome-text {
            color: #C17817;
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
        <?php if (isset($_SESSION['username'])): ?>
            <a href="#" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                👤 <?= htmlspecialchars($_SESSION['username']) ?>
            </a>
            <a href="logout.php" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                🚪 Déconnexion
            </a>
        <?php endif; ?>
    </nav>

    <div class="container">
        <h1>🎮 Memory Game</h1>



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