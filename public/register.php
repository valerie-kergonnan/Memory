<?php
require __DIR__ . '/../vendor/autoload.php';

use Memory\Player;
use Memory\Config\Database;

session_start();

$message = '';
$player = new Player();

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    die('Erreur DB: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $email && $password) {
        if ($player->register($username, $email, $password)) {
            $message = '<div class="message success">✓ Compte créé avec succès! ID: ' . $player->getId() . '</div>';
        } else {
            $message = '<div class="message error">✗ Erreur: Username ou email déjà utilisé.</div>';
        }
    } else {
        $message = '<div class="message error">✗ Tous les champs sont requis.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Game - Inscription</title>
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

        .page-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 120px);
        }

        .container {
            background: linear-gradient(135deg, rgba(255, 248, 220, 0.98) 0%, rgba(255, 243, 205, 0.95) 100%);
            border-radius: 20px;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(193, 120, 23, 0.4);
            border: 3px solid rgba(247, 147, 30, 0.4);
        }

        h1 {
            color: #C17817;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
            text-shadow: 2px 2px 4px rgba(139, 90, 15, 0.2);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input {
            padding: 14px;
            border: 2px solid #F7931E;
            border-radius: 8px;
            font-size: 1em;
            background: rgba(255, 255, 255, 0.9);
        }

        input:focus {
            outline: none;
            border-color: #C17817;
            box-shadow: 0 0 10px rgba(247, 147, 30, 0.3);
        }

        button {
            padding: 14px;
            background: linear-gradient(135deg, #C17817 0%, #F7931E 50%, #FBB03B 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(193, 120, 23, 0.3);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(247, 147, 30, 0.5);
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
            color: #F7931E;
            text-decoration: none;
            font-weight: bold;
        }

        .link-text a:hover {
            color: #C17817;
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
        <a href="login.php">🔐 Connexion</a>
        <a href="register.php" class="active">📝 Inscription</a>
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

    <div class="page-wrapper">
        <div class="container">
            <h1>📝 Inscription</h1>

            <?= $message ?>

            <form method="post">
                <input name="username" placeholder="Username" required autofocus>
                <input name="email" type="email" placeholder="Email" required>
                <input name="password" type="password" placeholder="Mot de passe" required>
                <button type="submit">Créer mon compte</button>
            </form>

            <div class="link-text">
                Déjà un compte ? <a href="login.php">Se connecter</a>
            </div>
        </div>
    </div>
</body>

</html>