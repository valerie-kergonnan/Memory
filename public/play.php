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

// Gestion des modes de difficulté
$difficulty = $_GET['difficulty'] ?? 'moyen';
$pairs = 9; // Par défaut moyen
$gridColumns = 6;

switch ($difficulty) {
    case 'facile':
        $pairs = 6; // 12 cartes
        $gridColumns = 4;
        break;
    case 'moyen':
        $pairs = 9; // 18 cartes
        $gridColumns = 6;
        break;
    case 'difficile':
        $pairs = 12; // 24 cartes
        $gridColumns = 6;
        break;
    case 'super-difficile':
        $pairs = 15; // 30 cartes
        $gridColumns = 6;
        break;
}

try {
    $game = new Game($pairs);
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
            font-family: 'Georgia', serif;
            background: url('assets/bg-souk.svg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 20px;
        }

        .nav-bar {
            background: rgba(212, 165, 116, 0.95);
            border-radius: 15px;
            padding: 15px 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(139, 90, 8, 0.3);
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            border: 2px solid rgba(193, 120, 23, 0.5);
        }

        .nav-bar a {
            color: #3E2723;
            text-decoration: none;
            padding: 12px 25px;
            background: linear-gradient(135deg, #F7931E 0%, #FBB03B 100%);
            color: white;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(193, 120, 23, 0.3);
        }

        .nav-bar a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(247, 147, 30, 0.6);
        }

        .nav-bar a.active {
            background: linear-gradient(135deg, #C17817 0%, #D4A574 100%);
            box-shadow: 0 3px 10px rgba(193, 120, 23, 0.5);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #FFF8DC;
            margin-bottom: 30px;
            text-shadow: 3px 3px 6px rgba(139, 69, 19, 0.8), 0 0 20px rgba(255, 165, 0, 0.5);
            font-family: 'Georgia', serif;
            font-size: 3em;
        }

        .game-info {
            text-align: center;
            color: #FFF8DC;
            margin-bottom: 20px;
            font-size: 1.2em;
            text-shadow: 2px 2px 4px rgba(139, 69, 19, 0.7);
        }

        .difficulty-selector {
            text-align: center;
            margin-bottom: 25px;
        }

        .difficulty-btn {
            padding: 12px 30px;
            margin: 0 10px;
            background: rgba(255, 255, 255, 0.9);
            border: 3px solid transparent;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            color: #1e3a8a;
        }

        .difficulty-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }

        .difficulty-btn.active {
            background: linear-gradient(135deg, #2d5f4d 0%, #8b4a4a 100%);
            color: white;
            border-color: white;
        }

        .difficulty-btn.facile {
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            color: white;
        }

        .difficulty-btn.moyen {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
        }

        .difficulty-btn.difficile {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .difficulty-btn.super-difficile {
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
            color: white;
            border: 2px solid #fbbf24;
            box-shadow: 0 0 15px rgba(251, 191, 36, 0.5);
            animation: superDifficilePulse 2s ease-in-out infinite;
        }

        @keyframes superDifficilePulse {

            0%,
            100% {
                box-shadow: 0 0 15px rgba(251, 191, 36, 0.5);
            }

            50% {
                box-shadow: 0 0 25px rgba(251, 191, 36, 0.8);
            }
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(<?= $gridColumns ?>, 1fr);
            gap: 10px;
            max-width: 700px;
            margin: 0 auto;
        }

        .card {
            aspect-ratio: 1;
            perspective: 1000px;
            cursor: pointer;
            width: 100px;
            height: 100px;
            max-width: 100px;
            max-height: 100px;
        }

        .card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform 0.6s;
            transform-style: preserve-3d;
            transform-origin: center center;
        }

        .card.flipped .card-inner {
            transform: rotateY(180deg);
        }

        .card-face {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2em;
            font-weight: bold;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        }

        .card-back {
            background: linear-gradient(135deg, #C17817 0%, #E8A735 50%, #D4712B 100%);
            color: white;
            background-size: cover;
            background-position: center;
            border: 3px solid rgba(139, 90, 8, 0.8);
            box-shadow: 0 5px 15px rgba(193, 120, 23, 0.5), inset 0 2px 10px rgba(255, 200, 100, 0.4);
            position: relative;
        }

        .card-back::after {
            content: '';
            position: absolute;
            top: 10%;
            left: 10%;
            right: 10%;
            bottom: 10%;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.3) 0%, transparent 70%);
            border-radius: 4px;
            border: 2px solid rgba(255, 200, 100, 0.4);
        }

        .card-front {
            background: linear-gradient(135deg, #FFF8DC 0%, #FAEBD7 100%);
            color: #8B4513;
            transform: rotateY(180deg);
            border: 3px solid rgba(193, 120, 23, 0.5);
            box-shadow: 0 5px 15px rgba(139, 90, 8, 0.3);
            font-size: 3em;
        }

        .card.matched .card-face {
            background: linear-gradient(135deg, #F7931E 0%, #FBB03B 50%, #FFD700 100%);
            color: white;
            font-size: 3.2em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.8), 0 5px 15px rgba(247, 147, 30, 0.6);
            animation: lanternGlow 1.5s ease-in-out infinite;
        }

        @keyframes lanternGlow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(255, 215, 0, 0.8), 0 5px 15px rgba(247, 147, 30, 0.6);
            }

            50% {
                box-shadow: 0 0 30px rgba(255, 215, 0, 1), 0 5px 25px rgba(247, 147, 30, 0.8);
            }
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

        /* Modal de victoire */
        .victory-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .victory-modal.show {
            display: flex;
        }

        .victory-content {
            background: linear-gradient(135deg, #2d5f4d 0%, #e8ebe9 50%, #8b4a4a 100%);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .victory-content h2 {
            color: #FFF8DC;
            font-size: 2.8em;
            margin-bottom: 20px;
            text-shadow: 3px 3px 6px rgba(139, 69, 19, 0.8), 0 0 30px rgba(255, 215, 0, 0.8);
            font-family: 'Georgia', serif;
        }

        .victory-content p {
            color: #FFF8DC;
            font-size: 1.3em;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(139, 69, 19, 0.7);
        }

        .victory-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .victory-btn {
            padding: 15px 30px;
            border: 2px solid rgba(193, 120, 23, 0.5);
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .victory-btn.restart {
            background: linear-gradient(135deg, #F7931E 0%, #FBB03B 100%);
            color: white;
        }

        .victory-btn.home {
            background: linear-gradient(135deg, #C17817 0%, #D4A574 100%);
            color: white;
        }

        .victory-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(255, 165, 0, 0.6);
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
        <h1>🏮 Souk aux Lanternes 🏮</h1>

        <div class="difficulty-selector">
            <a href="play.php?difficulty=facile" class="difficulty-btn facile <?= $difficulty === 'facile' ? 'active' : '' ?>">
                🕌 Facile (12 cartes)
            </a>
            <a href="play.php?difficulty=moyen" class="difficulty-btn moyen <?= $difficulty === 'moyen' ? 'active' : '' ?>">
                🏺 Moyen (18 cartes)
            </a>
            <a href="play.php?difficulty=difficile" class="difficulty-btn difficile <?= $difficulty === 'difficile' ? 'active' : '' ?>">
                🐪 Difficile (24 cartes)
            </a>
            <a href="play.php?difficulty=super-difficile" class="difficulty-btn super-difficile <?= $difficulty === 'super-difficile' ? 'active' : '' ?>">
                🔥 Super Difficile (30 cartes)
            </a>
        </div>

        <div class="game-info">
            <p>Mode: <strong><?= ucfirst($difficulty) ?></strong> | Tentatives: <span id="attempts">0</span> | Cartes: <?= count($cards) ?></p>
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

        // Fonction pour jouer un tintement de clochette métallique (bonne réponse)
        function playSuccessSound() {
            // Tintement de clochette style lanterne
            const bellFreqs = [1046.5, 1318.5, 1568, 2093]; // C6, E6, G6, C7 - gamme pentatonique

            bellFreqs.forEach((freq, index) => {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();

                osc.connect(gain);
                gain.connect(audioCtx.destination);

                osc.frequency.value = freq;
                osc.type = 'sine';

                const startTime = audioCtx.currentTime + (index * 0.08);
                gain.gain.setValueAtTime(0, startTime);
                gain.gain.linearRampToValueAtTime(0.12, startTime + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.01, startTime + 1.2);

                osc.start(startTime);
                osc.stop(startTime + 1.2);
            });

            // Résonance métallique
            const shimmer = audioCtx.createOscillator();
            const shimmerGain = audioCtx.createGain();

            shimmer.connect(shimmerGain);
            shimmerGain.connect(audioCtx.destination);

            shimmer.frequency.value = 3000;
            shimmer.type = 'triangle';

            const startTime = audioCtx.currentTime + 0.1;
            shimmerGain.gain.setValueAtTime(0.03, startTime);
            shimmerGain.gain.exponentialRampToValueAtTime(0.01, startTime + 0.8);

            shimmer.start(startTime);
            shimmer.stop(startTime + 0.8);
        }

        // Fonction pour jouer une note douce descendante (erreur)
        function playErrorSound() {
            // Note descendante douce style oud
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            osc.frequency.setValueAtTime(440, audioCtx.currentTime); // La
            osc.frequency.exponentialRampToValueAtTime(330, audioCtx.currentTime + 0.4); // Mi
            osc.type = 'triangle';

            gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);

            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.4);
        }

        // Fonction pour jouer une célébration orientale (victoire)
        function playVictorySound() {
            // Mélodie de piano oriental ascendante (gamme Hijaz simplifiée)
            const melodyNotes = [{
                    freq: 440,
                    time: 0
                }, // La
                {
                    freq: 493.88,
                    time: 0.15
                }, // Si
                {
                    freq: 554.37,
                    time: 0.3
                }, // Do#
                {
                    freq: 587.33,
                    time: 0.45
                }, // Ré
                {
                    freq: 659.25,
                    time: 0.6
                }, // Mi
                {
                    freq: 739.99,
                    time: 0.75
                }, // Fa#
                {
                    freq: 880,
                    time: 0.9
                } // La (octave)
            ];

            melodyNotes.forEach(note => {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();

                osc.connect(gain);
                gain.connect(audioCtx.destination);

                osc.frequency.value = note.freq;
                osc.type = 'sine';

                const startTime = audioCtx.currentTime + note.time;
                gain.gain.setValueAtTime(0.12, startTime);
                gain.gain.exponentialRampToValueAtTime(0.01, startTime + 0.5);

                osc.start(startTime);
                osc.stop(startTime + 0.5);
            });

            // Tintements de lanternes multiples (comme des clochettes)
            for (let i = 0; i < 8; i++) {
                const bell = audioCtx.createOscillator();
                const bellGain = audioCtx.createGain();

                bell.connect(bellGain);
                bellGain.connect(audioCtx.destination);

                const frequencies = [1046.5, 1318.5, 1568, 2093];
                bell.frequency.value = frequencies[Math.floor(Math.random() * frequencies.length)];
                bell.type = 'sine';

                const startTime = audioCtx.currentTime + 0.8 + (i * 0.08);
                bellGain.gain.setValueAtTime(0.08, startTime);
                bellGain.gain.exponentialRampToValueAtTime(0.01, startTime + 1);

                bell.start(startTime);
                bell.stop(startTime + 1);
            }
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
                                showVictoryModal(attempts);
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

        // Fonction pour afficher le modal de victoire
        function showVictoryModal(attempts) {
            const modal = document.getElementById('victoryModal');
            document.getElementById('finalAttempts').textContent = attempts;
            modal.classList.add('show');
        }

        // Fonction pour nouvelle partie
        function restartGame() {
            window.location.href = 'play.php?difficulty=<?= $difficulty ?>';
        }

        // Musique d'ambiance orientale
        const backgroundMusic = new Audio('assets/souk-ambiance.mp3');
        backgroundMusic.loop = true;
        backgroundMusic.volume = 0.4;



        // Démarrer la musique au premier clic (nécessaire pour les navigateurs modernes)
        document.addEventListener('click', function startMusic() {
            backgroundMusic.play().then(() => {
                console.log('🎵 Musique démarrée');
            }).catch(e => {
                console.error('❌ Erreur lecture audio:', e);
            });
            document.removeEventListener('click', startMusic);
        }, {
            once: true
        });

        // Arrêter la musique en quittant la page
        window.addEventListener('beforeunload', () => {
            backgroundMusic.pause();
        });
    </script>

    <!-- Modal de victoire -->
    <div id="victoryModal" class="victory-modal">
        <div class="victory-content">
            <h2>🏮 Mabrouk! Félicitations! 🏮</h2>
            <p>✨ Les lanternes du souk brillent pour vous! ✨</p>
            <p>Vous avez terminé en <strong id="finalAttempts">0</strong> tentatives!</p>
            <p>Mode: <strong><?= ucfirst($difficulty) ?></strong></p>
            <div class="victory-buttons">
                <button class="victory-btn restart" onclick="restartGame()">
                    🔄 Nouvelle Partie
                </button>
                <a href="index.php" class="victory-btn home">
                    🏠 Retour au Souk
                </a>
            </div>
        </div>
    </div>
</body>

</html>