<?php
require __DIR__ . '/../vendor/autoload.php';

use Memory\Player;
use Memory\Config\Database;

$message = '';
$player = new Player();

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    die('Erreur DB: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        if ($player->login($username, $password)) {
            $message = '<div class="message success">✓ Connecté! Bienvenue ' . htmlspecialchars($player->getUsername()) . '</div>';
        } else {
            $message = '<div class="message error">✗ Username ou mot de passe incorrect.</div>';
        }
    } else {
        $message = '<div class="message error">✗ Username et mot de passe requis.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Game - Connexion</title>
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

        .page-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 120px);
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #1e3a8a;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input {
            padding: 14px;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            font-size: 1em;
        }

        input:focus {
            outline: none;
            border-color: #1e3a8a;
        }

        button {
            padding: 14px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s;
        }

        button:hover {
            transform: translateY(-2px);
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
        }

        .link-text {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .link-text a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: bold;
        }

        .link-text a:hover {
            text-decoration: underline;
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
        <a href="index.php">🏠 Accueil</a>
        <a href="play.php">🎮 Jouer</a>
        <a href="login.php" class="active">🔐 Connexion</a>
        <a href="register.php">📝 Inscription</a>
        <a href="Top.php">🏆 Classement</a>
    </nav>

    <div class="page-wrapper">
        <div class="container">
            <h1>🔐 Connexion</h1>

            <?= $message ?>

            <form method="post">
                <input name="username" placeholder="Username" required autofocus>
                <input name="password" type="password" placeholder="Mot de passe" required>
                <button type="submit">Se connecter</button>
            </form>

            <div class="link-text">
                Pas encore de compte ? <a href="register.php">Créer un compte</a>
            </div>
        </div>
    </div>
</body>

</html>