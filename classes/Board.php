<?php

namespace Memory;

class Board
{
    private array $cards = [];

    public function __construct(int $pairs)
    {
        $this->generate($pairs);
    }

    private function generate(int $pairs): void
    {
        // Déterminer les symboles selon le nombre de paires (mode de difficulté)
        $availableSymbols = $this->getSymbolsForMode($pairs);

        $symbols = [];
        for ($i = 0; $i < $pairs; $i++) {
            $symbols[] = $availableSymbols[$i % count($availableSymbols)];
        }

        $symbols = array_merge($symbols, $symbols);
        shuffle($symbols);

        foreach ($symbols as $i => $symbol) {
            $this->cards[] = new Card($i, $symbol);
        }
    }

    private function getSymbolsForMode(int $pairs): array
    {
        // Mode facile (6 paires) - Souk aux Lanternes
        if ($pairs === 6) {
            return ['🫖', '🍵', '🪔', '🧿', '🕌', '🌙'];
        }

        // Mode moyen (9 paires) - Souk aux Lanternes
        if ($pairs === 9) {
            return ['🫖', '🍵', '🪔', '🧿', '🕌', '🌙', '🥿', '🏺', '🌴'];
        }

        // Mode difficile (12 paires) - Souk aux Lanternes
        if ($pairs === 12) {
            return ['🫖', '🍵', '🪔', '🧿', '🕌', '🌙', '🥿', '🏺', '🌴', '🐪', '⭐', '🪙'];
        }

        // Mode super difficile (15 paires) - Collection complète du souk
        return ['🫖', '🍵', '🪔', '🧿', '🕌', '🌙', '🥿', '🏺', '🌴', '🐪', '⭐', '🪙', '🎭', '🏜️', '🌺'];
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    public function getCard(int $id): ?Card
    {
        return $this->cards[$id] ?? null;
    }
}
