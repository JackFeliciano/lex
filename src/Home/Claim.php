<?php

declare(strict_types=1);

namespace Lex\Home;

/**
 * A claim is what one party says another party did. It holds the two sides, the
 * subject in one line, the date it was filed and where it stands in the process.
 */
final class Claim
{
    public const FILED = 'filed';
    public const MEDIATED = 'mediated';
    public const ARBITRATED = 'arbitrated';
    public const CLOSED = 'closed';
    public const WITHDRAWN = 'withdrawn';

    public function __construct(
        public readonly string $handle,
        public readonly string $claimant,
        public readonly string $respondent,
        public readonly string $subject,
        public readonly string $filedOn,
        private string $status = self::FILED,
        private ?string $panel = null,
    ) {
    }

    public function status(): string
    {
        return $this->status;
    }

    public function panel(): ?string
    {
        return $this->panel;
    }

    /**
     * A claim takes a panel before anyone mediates it, and it takes that panel
     * again before anyone arbitrates it. A closed or withdrawn claim holds no
     * next step.
     */
    public function assign(string $panel): void
    {
        if ($this->status === self::CLOSED || $this->status === self::WITHDRAWN) {
            throw new \RuntimeException('a claim that is over takes no panel');
        }
        $this->panel = $panel;
    }

    public function advance(): void
    {
        if ($this->panel === null) {
            throw new \RuntimeException('a claim advances only after a panel takes it');
        }
        $this->status = match ($this->status) {
            self::FILED => self::MEDIATED,
            self::MEDIATED => self::ARBITRATED,
            default => throw new \RuntimeException('a claim at ' . $this->status . ' has no next step'),
        };
    }

    public function close(string $ruling): void
    {
        if ($ruling === '') {
            throw new \RuntimeException('a claim closes on a ruling with text in it');
        }
        if ($this->status !== self::ARBITRATED) {
            throw new \RuntimeException('only an arbitrated claim closes');
        }
        $this->status = self::CLOSED;
    }

    public function withdraw(): void
    {
        if ($this->status === self::CLOSED) {
            throw new \RuntimeException('a closed claim stays closed');
        }
        $this->status = self::WITHDRAWN;
    }

    public function open(): bool
    {
        return $this->status !== self::CLOSED && $this->status !== self::WITHDRAWN;
    }
}
