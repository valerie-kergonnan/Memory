<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use Memory\Game;
use Memory\Config\Database;

session_start();

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    die('<h1>Erreur de connexion DB</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>');
}

try {
    // Crée une partie et affiche les cartes
    $game = new Game(9); // 9 paires = 18 cartes
    $cards = $game->getCards();
} catch (Throwable $e) {
    die('<h1>Erreur de création du jeu</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>');
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Game - Jouer</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .game-info {
            text-align: center;
            color: white;
            margin-bottom: 20px;
            font-size: 1.2em;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 15px;
            max-width: 900px;
            margin: 0 auto;
        }

        .card {
            aspect-ratio: 1;
            perspective: 1000px;
            cursor: pointer;
        }

        .card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }

        .card.flipped .card-inner {
            transform: rotateY(180deg);
        }

        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5em;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .card-back {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .card-back::before {
            content: "?";
        }

        .card-front {
            background: white;
            color: #667eea;
            transform: rotateY(180deg);
        }

        .card.matched .card-face {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .nav-links {
            text-align: center;
            margin-top: 30px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            margin: 0 10px;
            transition: background 0.3s;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .cards-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
            }
        }

        @media (max-width: 480px) {
            .cards-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }
        }
    </style>
</head>

<body>
    <nav class="nav-bar">
        <a href="index.php">🏠 Accueil</a>
        <a href="play.php" class="active">🎮 Jouer</a>
        <a href="login.php">🔐 Connexion</a>
        <a href="register.php">📝 Inscription</a>
        <a href="Top.php">🏆 Classement</a>
    </nav>

    <div class="container">
        <h1>🎮 Memory Game</h1>

        <div class="game-info">
            <p>Tentatives: <span id="attempts">0</span> | Cartes: <?= count($cards) ?></p>
        </div>

        <div class="cards-grid">
            <?php foreach ($cards as $card): ?>
                <div class="card" data-id="<?= $card->getId() ?>" data-symbol="<?= htmlspecialchars($card->getSymbol()) ?>">
                    <div class="card-inner">
                        <div class="card-face card-back"></div>
                        <div class="card-face card-front"><?= htmlspecialchars($card->getSymbol()) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        let flippedCards = [];
        let matchedPairs = 0;
        let attempts = 0;
        let canFlip = true;

        const cards = document.querySelectorAll('.card');
        const attemptsDisplay = document.getElementById('attempts');

        // Créer des contextes audio pour les sons
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        const audioCtx = new AudioContext();

        // Fonction pour jouer un son de succès (bonne réponse)
        function playSuccessSound() {
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            oscillator.frequency.value = 523.25; // Note Do
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);

            oscillator.start(audioCtx.currentTime);
            oscillator.stop(audioCtx.currentTime + 0.5);

            // Jouer une deuxième note pour l'harmonie
            const oscillator2 = audioCtx.createOscillator();
            const gainNode2 = audioCtx.createGain();

            oscillator2.connect(gainNode2);
            gainNode2.connect(audioCtx.destination);

            oscillator2.frequency.value = 659.25; // Note Mi
            oscillator2.type = 'sine';

            gainNode2.gain.setValueAtTime(0.2, audioCtx.currentTime);
            gainNode2.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);

            oscillator2.start(audioCtx.currentTime + 0.1);
            oscillator2.stop(audioCtx.currentTime + 0.6);
        }

        // Fonction pour jouer un son d'erreur (mauvaise réponse)
        function playErrorSound() {
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            oscillator.frequency.value = 200; // Note basse
            oscillator.type = 'sawtooth';

            gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);

            oscillator.start(audioCtx.currentTime);
            oscillator.stop(audioCtx.currentTime + 0.3);
        }

        // Fonction pour jouer le son de victoire
        function playVictorySound() {
            const notes = [523.25, 587.33, 659.25, 783.99]; // Do Mi Sol Si
            notes.forEach((freq, index) => {
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);

                oscillator.frequency.value = freq;
                oscillator.type = 'sine';

                const startTime = audioCtx.currentTime + (index * 0.15);
                gainNode.gain.setValueAtTime(0.3, startTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, startTime + 0.4);

                oscillator.start(startTime);
                oscillator.stop(startTime + 0.4);
            });
        }

        cards.forEach(card => {
            card.addEventListener('click', flipCard);
        });

        function flipCard() {
            if (!canFlip) return;
            if (this.classList.contains('flipped') || this.classList.contains('matched')) return;

            this.classList.add('flipped');
            flippedCards.push(this);

            if (flippedCards.length === 2) {
                canFlip = false;
                attempts++;
                attemptsDisplay.textContent = attempts;
                checkMatch();
            }
        }

        function checkMatch() {
            const [card1, card2] = flippedCards;
            const symbol1 = card1.dataset.symbol;
            const symbol2 = card2.dataset.symbol;

            if (symbol1 === symbol2) {
                // Match trouvé - Son de succès
                playSuccessSound();

                setTimeout(() => {
                    card1.classList.add('matched');
                    card2.classList.add('matched');
                    matchedPairs++;

                    if (matchedPairs === <?= count($cards) / 2 ?>) {
                        setTimeout(() => {
                            playVictorySound();
                            setTimeout(() => {
                                alert(`🎉 Bravo! Vous avez terminé en ${attempts} tentatives!`);
                            }, 800);
                        }, 300);
                    }

                    flippedCards = [];
                    canFlip = true;
                }, 500);
            } else {
                // Pas de match - Son d'erreur
                playErrorSound();

                setTimeout(() => {
                    card1.classList.remove('flipped');
                    card2.classList.remove('flipped');
                    flippedCards = [];
                    canFlip = true;
                }, 1000);
            }
        }
    </script>
</body>

</html>