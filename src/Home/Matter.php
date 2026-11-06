<?php

declare(strict_types=1);

namespace Lex\Home;

/**
 * A matter ties a claim to the panel that hears it and to the money the parties
 * put up. The court holds the bond and keeps a fee when the case closes.
 */
final class Matter
{
    /** @param list<string> $rulings */
    public function __construct(
        public readonly string $handle,
        public readonly Claim $claim,
        public readonly Panel $panel,
        private int $bondCents = 0,
        private array $rulings = [],
    ) {
    }

    public function bond(): int
    {
        return $this->bondCents;
    }

    public function deposit(int $cents): void
    {
        if ($cents <= 0) {
            throw new \RuntimeException('a deposit carries a positive amount');
        }
        if (!$this->claim->open()) {
            throw new \RuntimeException('a claim that is over takes no deposit');
        }
        $this->bondCents += $cents;
    }

    public function file(string $ruling): void
    {
        $this->claim->close($ruling);
        $this->rulings[] = $ruling;
    }

    /** @return list<string> */
    public function rulings(): array
    {
        return $this->rulings;
    }

    /**
     * The court keeps a fee of one part in thirty two of the bond and returns the
     * rest to the parties. Both figures are whole cents, so no cent goes missing.
     */
    public function settle(): array
    {
        if ($this->claim->open()) {
            throw new \RuntimeException('a bond settles after the claim closes');
        }
        $fee = intdiv($this->bondCents, 32);

        return ['fee' => $fee, 'refund' => $this->bondCents - $fee];
    }

    /**
     * The age of a case in days, counted from the day it was filed to the day
     * given. The demo passes a fixed day so the page reads the same every time.
     */
    public function days(string $today): int
    {
        $a = new \DateTimeImmutable($this->claim->filedOn);
        $b = new \DateTimeImmutable($today);

        return (int) $a->diff($b)->days;
    }
}
