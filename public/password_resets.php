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
    $token = $_GET['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($password && $confirmPassword && $token) {
        if ($password === $confirmPassword) {
            // Vérifier le token
            $stmt = $pdo->prepare("SELECT user_id, expiry FROM password_resets WHERE token = :token");
            $stmt->execute(['token' => $token]);
            $reset = $stmt->fetch(PDO::FETCH_ASSOC);

            // Vérifier si le token existe et n'est pas expiré
            if ($reset && strtotime($reset['expiry']) > time()) {
                // Mettre à jour le mot de passe
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
                $stmt->execute(['password' => $hash, 'id' => $reset['user_id']]);

                // Supprimer le token utilisé
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
                $stmt->execute(['token' => $token]);

                $message = '<div class="message success">✓ Mot de passe réinitialisé avec succès! <a href="login.php" style="color: #155724;">Se connecter</a></div>';
            } else {
                $message = '<div class="message error">✗ Token invalide ou expiré.</div>';
            }
        } else {
            $message = '<div class="message error">✗ Les mots de passe ne correspondent pas.</div>';
        }
    } else {
        $message = '<div class="message error">✗ Tous les champs sont requis.</div>';
    }
}

$token = $_GET['token'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Game - Réinitialiser le mot de passe</title>
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
    </style>
</head>

<body>
    <nav class="nav-bar">
        <a href="index.php">🏠 Accueil</a>
        <a href="play.php">🎮 Jouer</a>
        <a href="login.php">🔐 Connexion</a>
        <a href="register.php">📝 Inscription</a>
        <a href="Top.php">🏆 Classement</a>
    </nav>

    <div class="page-wrapper">
        <div class="container">
            <h1>🔒 Nouveau mot de passe</h1>

            <?php echo $message; ?>

            <form method="post" action="password_resets.php?token=<?= htmlspecialchars($token) ?>">
                <input name="password" type="password" placeholder="Nouveau mot de passe" required autofocus>
                <input name="confirm_password" type="password" placeholder="Confirmer le mot de passe" required>
                <button type="submit">Réinitialiser</button>
            </form>

            <div class="link-text">
                <a href="login.php">← Retour à la connexion</a>
            </div>
        </div>
    </div>
</body>

</html>