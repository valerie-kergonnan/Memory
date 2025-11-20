<?php
require __DIR__ . '/../vendor/autoload.php';

use Memory\ScoreBoard;
use Memory\Config\Database;

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    die('Erreur DB: ' . $e->getMessage());
}

$sb = new ScoreBoard($pdo);
$top = $sb->top(10);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Game - Classement</title>
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
            max-width: 800px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #1e3a8a;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-size: 1.1em;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        tr:hover {
            background: rgba(59, 130, 246, 0.1);
        }

        .rank {
            font-weight: bold;
            color: #1e3a8a;
            font-size: 1.2em;
        }

        .rank-1 {
            color: #fbbf24;
        }

        .rank-2 {
            color: #9ca3af;
        }

        .rank-3 {
            color: #cd7f32;
        }

        .nav-links {
            text-align: center;
            margin-top: 20px;
        }

        .nav-links a {
            color: #1e3a8a;
            text-decoration: none;
            padding: 12px 25px;
            background: rgba(59, 130, 246, 0.2);
            border-radius: 8px;
            margin: 0 10px;
            transition: all 0.3s;
            display: inline-block;
        }

        .nav-links a:hover {
            background: rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <nav class="nav-bar">
        <a href="index.php">🏠 Accueil</a>
        <a href="play.php">🎮 Jouer</a>
        <a href="login.php">🔐 Connexion</a>
        <a href="register.php">📝 Inscription</a>
        <a href="Top.php" class="active">🏆 Classement</a>
    </nav>

    <div class="page-wrapper">
        <div class="container">
            <h1>🏆 Top Scores - Leaderboard</h1>

            <?php if (count($top) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Joueur</th>
                            <th>Meilleur Score</th>
                            <th>Meilleur Temps (sec)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rank = 1;
                        foreach ($top as $row):
                            $rankClass = $rank <= 3 ? "rank-$rank" : "";
                        ?>
                            <tr>
                                <td class="rank <?= $rankClass ?>"><?= $rank++ ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= (int)$row['best_score'] ?></td>
                                <td><?= (int)$row['best_time'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #666; font-size: 1.2em;">Aucun score enregistré pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>