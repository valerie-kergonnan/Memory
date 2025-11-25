<?php
require __DIR__ . '/../vendor/autoload.php';

use Memory\Config\Database;

session_start();

$message = '';

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    die('Erreur DB: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email) {
        // Vérifier si l'email existe
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Générer un token de réinitialisation
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Stocker le token dans la base de données
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expiry) VALUES (:user_id, :token, :expiry)");
            $stmt->execute([
                'user_id' => $user['id'],
                'token' => $token,
                'expiry' => $expiry
            ]);

            $message = '<div class="message success">✓ Un lien de réinitialisation a été envoyé (simulation). <br><a href="password_resets.php?token=' . $token . '" style="color: #155724;">Cliquez ici pour réinitialiser</a></div>';
        } else {
            $message = '<div class="message error">✗ Aucun compte associé à cet email.</div>';
        }
    } else {
        $message = '<div class="message error">✗ Veuillez entrer votre email.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Game - Mot de passe oublié</title>
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
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
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
            margin-bottom: 20px;
            font-size: 2em;
            text-shadow: 2px 2px 4px rgba(139, 90, 15, 0.2);
        }

        .info-text {
            color: #8B5A0F;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.6;
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

    <div class="page-wrapper">
        <div class="container">
            <h1>🔑 Mot de passe oublié</h1>

            <p class="info-text">
                Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
            </p>

            <?= $message ?>

            <form method="post">
                <input name="email" type="email" placeholder="Votre email" required autofocus>
                <button type="submit">Envoyer le lien</button>
            </form>

            <div class="link-text">
                <a href="login.php">← Retour à la connexion</a>
            </div>
        </div>
    </div>
</body>

</html>