<?php

namespace Memory;

class Card
{
    private int $id;
    private string $symbol;
    private bool $matched = false;

    public function __construct(int $id, string $symbol)
    {
        $this->id = $id;
        $this->symbol = $symbol;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function isMatched(): bool
    {
        return $this->matched;
    }

    public function setMatched(bool $matched): void
    {
        $this->matched = $matched;
    }
}
