<?php
require __DIR__ . '/../vendor/autoload.php';

use Memory\Player;
use Memory\Config\Database;

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Game - Profil</title>
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
            max-width: 500px;
            width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #1e3a8a;
            text-align: center;
            margin-bottom: 30px;
        }

        h2 {
            color: #3b82f6;
            margin: 25px 0 15px;
            font-size: 1.3em;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input {
            padding: 12px;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            font-size: 1em;
        }

        input:focus {
            outline: none;
            border-color: #1e3a8a;
        }

        button {
            padding: 12px;
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

        .nav-links {
            text-align: center;
            margin-top: 30px;
        }

        .nav-links a {
            color: #1e3a8a;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(59, 130, 246, 0.2);
            border-radius: 5px;
            margin: 0 5px;
            transition: background 0.3s;
        }

        .nav-links a:hover {
            background: rgba(59, 130, 246, 0.3);
        }
    </style>
</head>

<body>
    <nav class="nav-bar">
        <a href="index.php">🏠 Accueil</a>
        <a href="play.php">🎮 Jouer</a>
        <a href="profile.php?action=login" class="active">🔐 Connexion</a>
        <a href="profile.php?action=register">📝 Inscription</a>
        <a href="Top.php">🏆 Classement</a>
    </nav>

    <div class="page-wrapper">
        <div class="container">
            <h1>👤 Profil Joueur</h1>
            <?php

            try {
                $pdo = Database::getConnection();
            } catch (Throwable $e) {
                die('Erreur DB: ' . $e->getMessage());
            }

            $message = '';
            $player = new Player();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? 'login';
                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';

                if ($action === 'register') {
                    $email = trim($_POST['email'] ?? '');
                    if ($username && $email && $password) {
                        if ($player->register($username, $email, $password)) {
                            $message = '<p style="color: green;">Compte créé avec succès! ID: ' . $player->getId() . '</p>';
                        } else {
                            $message = '<p style="color: red;">Erreur: Username ou email déjà utilisé.</p>';
                        }
                    } else {
                        $message = '<p style="color: red;">Tous les champs sont requis.</p>';
                    }
                } elseif ($action === 'login') {
                    if ($username && $password) {
                        if ($player->login($username, $password)) {
                            $message = '<p style="color: green;">Connecté! Bienvenue ' . htmlspecialchars($player->getUsername()) . ' (ID: ' . $player->getId() . ')</p>';
                        } else {
                            $message = '<p style="color: red;">Username ou mot de passe incorrect.</p>';
                        }
                    } else {
                        $message = '<p style="color: red;">Username et mot de passe requis.</p>';
                    }
                }
            }

            echo '<h1>Profil Joueur</h1>';
            echo $message;
            echo '<h2>Connexion</h2>';
            echo '<form method="post">';
            echo '<input type="hidden" name="action" value="login">';
            echo 'Username: <input name="username" required><br>';
            echo 'Password: <input name="password" type="password" required><br>';
            echo '<button type="submit">Se connecter</button>';
            echo '</form>';

            echo '<h2>Créer un compte</h2>';
            echo '<form method="post">';
            echo '<input type="hidden" name="action" value="register">';
            echo '<input name="username" placeholder="Username" required>';
            echo '<input name="email" type="email" placeholder="Email" required>';
            echo '<input name="password" type="password" placeholder="Mot de passe" required>';
            echo '<button type="submit">Créer</button>';
            echo '</form>';
            echo '</div></div></body></html>';
