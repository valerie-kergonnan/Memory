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
        // Symboles originaux style graphique/vitesse
        $availableSymbols = ['◢', '◣', '◤', '◥', '▰', '▱', '◐', '◑', '◒', '◓', '◔', '◕'];

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

    public function getCards(): array
    {
        return $this->cards;
    }

    public function getCard(int $id): ?Card
    {
        return $this->cards[$id] ?? null;
    }
}
