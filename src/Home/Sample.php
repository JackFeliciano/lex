<?php

declare(strict_types=1);

namespace Lex\Home;

/**
 * The records the demo shows. They stand in for a registry service and they keep
 * the page honest: every figure on the page comes out of these objects.
 */
final class Sample
{
    /** @var array<string, Matter> */
    private array $matters = [];

    public function __construct()
    {
        $panel = new Panel('panel-mediation-1', 'Mediation bench one', ['Ada Rune', 'Miro Fell', 'Sefa Oyelaran'], 'mediation');

        $one = new Claim(
            'claim-2026-0041',
            'Atlas Freight',
            'Harbour Line',
            'Late delivery of a sealed container',
            '2026-09-14',
        );
        $one->assign($panel->handle);
        $one->advance();
        $one->advance();
        $matter = new Matter('matter-0041', $one, $panel, 480000);
        $matter->deposit(120000);
        $this->matters[$matter->handle] = $matter;

        $two = new Claim(
            'claim-2026-0042',
            'Noma Studio',
            'Bright Field Media',
            'Use of a photograph beyond the licence term',
            '2026-10-02',
        );
        $bench = new Panel('panel-arbitration-2', 'Arbitration bench two', ['Yusuf Demir'], 'arbitration');
        $two->assign($bench->handle);
        $two->advance();
        $three = new Matter('matter-0042', $two, $bench, 95000);
        $this->matters[$three->handle] = $three;

        $four = new Claim(
            'claim-2026-0043',
            'Kestrel Works',
            'Vine & Vale',
            'Unpaid invoice 1187 for six weeks of fitting',
            '2026-10-21',
        );
        $five = new Matter('matter-0043', $four, new Panel('panel-filing-3', 'Filing bench three', ['Ines Barros'], 'mediation'), 210000);
        $this->matters[$five->handle] = $five;
    }

    /** @return list<Matter> */
    public function matters(): array
    {
        return array_values($this->matters);
    }

    public function count(): int
    {
        return count($this->matters);
    }

    public function fee(): int
    {
        $sum = 0;
        foreach ($this->matters as $matter) {
            $sum += $matter->bond();
        }

        return intdiv($sum, 32);
    }
}
