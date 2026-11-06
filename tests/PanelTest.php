<?php

declare(strict_types=1);

namespace Lex\Tests;

use Lex\Home\Panel;

return [
    ['a panel of three seats takes a majority of two', static function (): void {
        $panel = new Panel('p1', 'Bench one', ['Ada', 'Miro', 'Sefa'], 'mediation');
        Assert::same(3, count($panel->seats()));
        Assert::same(2, $panel->majority());
    }],
    ['a panel of one seat takes a majority of one', static function (): void {
        $panel = new Panel('p2', 'Bench two', ['Yusuf'], 'arbitration');
        Assert::same(1, $panel->majority());
    }],
    ['an even number of seats is refused', static function (): void {
        try {
            new Panel('p3', 'Bench three', ['Ada', 'Miro'], 'mediation');
            Assert::true(false, 'an even panel stood');
        } catch (\RuntimeException $e) {
            Assert::contains('odd', $e->getMessage());
        }
    }],
    ['a panel with no seat is refused', static function (): void {
        try {
            new Panel('p4', 'Bench four', [], 'mediation');
            Assert::true(false, 'an empty panel stood');
        } catch (\RuntimeException $e) {
            Assert::contains('one seat', $e->getMessage());
        }
    }],
    ['a seat added later stays odd or the panel refuses it', static function (): void {
        $panel = new Panel('p5', 'Bench five', ['Ada'], 'mediation');
        $panel->add('Miro');
        Assert::same(2, count($panel->seats()));
        Assert::true($panel->sits('Miro'));
        Assert::false($panel->sits('Sefa'));
    }],
];
