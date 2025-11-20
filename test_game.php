<?php
require 'vendor/autoload.php';

use Memory\Game;
use Memory\Config\Database;

try {
    echo "Test 1: Connexion DB...\n";
    $pdo = Database::getConnection();
    echo "✓ DB connectée\n\n";

    echo "Test 2: Création Game...\n";
    $game = new Game(6);
    echo "✓ Game créé\n\n";

    echo "Test 3: Récupération cartes...\n";
    $cards = $game->getCards();
    echo "✓ Nombre de cartes: " . count($cards) . "\n\n";

    echo "Test 4: Affichage cartes...\n";
    foreach ($cards as $card) {
        echo "- ID: " . $card->getId() . " | Symbole: " . $card->getSymbol() . "\n";
    }

    echo "\n✓ Tous les tests réussis!\n";
} catch (Throwable $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
