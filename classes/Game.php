<?php

namespace Memory;

class Game
{
    private Board $board;
    private array $revealed = [];
    private int $attempts = 0;
    private bool $finished = false;

    public function __construct(int $pairs)
    {
        $this->board = new Board($pairs);
    }

    public function getCards(): array
    {
        return $this->board->getCards();
    }

    public function revealCard(int $id): void
    {
        if ($this->finished) return;

        $card = $this->board->getCard($id);
        if (!$card || $card->isMatched()) return;

        if (!in_array($id, $this->revealed)) {
            $this->revealed[] = $id;
        }

        if (count($this->revealed) === 2) {
            $this->checkMatch();
        }
    }

    private function checkMatch(): void
    {
        $this->attempts++;

        [$id1, $id2] = $this->revealed;

        $c1 = $this->board->getCard($id1);
        $c2 = $this->board->getCard($id2);

        if ($c1->getSymbol() === $c2->getSymbol()) {
            $c1->setMatched(true);
            $c2->setMatched(true);
        }

        $this->revealed = [];
        $this->finished = $this->isFinished();
    }

    public function isFinished(): bool
    {
        foreach ($this->board->getCards() as $card) {
            if (!$card->isMatched()) return false;
        }
        return true;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }
}
