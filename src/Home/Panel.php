<?php

declare(strict_types=1);

namespace Lex\Home;

/**
 * A panel is the seat a mediator or an arbitrator holds. One seat hears a claim,
 * two or three seats hear it together, and the panel needs an odd number so a
 * vote can settle.
 */
final class Panel
{
    /** @param list<string> $seats */
    public function __construct(
        public readonly string $handle,
        public readonly string $name,
        private array $seats,
        private readonly string $role,
    ) {
        if ($seats === []) {
            throw new \RuntimeException('a panel holds at least one seat');
        }
        if (count($seats) % 2 === 0) {
            throw new \RuntimeException('a panel holds an odd number of seats');
        }
    }

    /** @return list<string> */
    public function seats(): array
    {
        return $this->seats;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function add(string $seat): void
    {
        $this->seats[] = $seat;
    }

    public function sits(string $seat): bool
    {
        return in_array($seat, $this->seats, true);
    }

    public function majority(): int
    {
        return intdiv(count($this->seats), 2) + 1;
    }
}
