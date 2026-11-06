<?php

declare(strict_types=1);

namespace Lex\Tests;

use Lex\Home\Claim;

return [
    ['a filed claim takes a panel before it advances', static function (): void {
        $claim = new Claim('c1', 'A', 'B', 'A late delivery', '2026-09-14');
        Assert::same(Claim::FILED, $claim->status());
        try {
            $claim->advance();
            Assert::true(false, 'a claim advanced with no panel');
        } catch (\RuntimeException $e) {
            Assert::contains('panel', $e->getMessage());
        }
    }],
    ['a claim walks filed to mediated to arbitrated to closed', static function (): void {
        $claim = new Claim('c2', 'A', 'B', 'A late delivery', '2026-09-14');
        $claim->assign('panel-1');
        $claim->advance();
        Assert::same(Claim::MEDIATED, $claim->status());
        $claim->advance();
        Assert::same(Claim::ARBITRATED, $claim->status());
        $claim->close('The respondent pays the invoice in full.');
        Assert::same(Claim::CLOSED, $claim->status());
        Assert::false($claim->open());
    }],
    ['only an arbitrated claim closes', static function (): void {
        $claim = new Claim('c3', 'A', 'B', 'A late delivery', '2026-09-14');
        $claim->assign('panel-1');
        $claim->advance();
        try {
            $claim->close('A ruling with no hearing behind it.');
            Assert::true(false, 'a mediated claim closed');
        } catch (\RuntimeException $e) {
            Assert::contains('arbitrated', $e->getMessage());
        }
    }],
    ['a closed claim takes no panel and no second close', static function (): void {
        $claim = new Claim('c4', 'A', 'B', 'A late delivery', '2026-09-14');
        $claim->assign('panel-1');
        $claim->advance();
        $claim->advance();
        $claim->close('The respondent pays.');
        try {
            $claim->assign('panel-2');
            Assert::true(false, 'a closed claim took a panel');
        } catch (\RuntimeException $e) {
            Assert::contains('over', $e->getMessage());
        }
    }],
    ['a withdrawn claim is over', static function (): void {
        $claim = new Claim('c5', 'A', 'B', 'A late delivery', '2026-09-14');
        $claim->withdraw();
        Assert::same(Claim::WITHDRAWN, $claim->status());
        Assert::false($claim->open());
    },],
    ['a claim at a state with no next step refuses to advance', static function (): void {
        $claim = new Claim('c6', 'A', 'B', 'A late delivery', '2026-09-14');
        $claim->assign('panel-1');
        $claim->advance();
        $claim->advance();
        $claim->close('The respondent pays.');
        try {
            $claim->advance();
            Assert::true(false, 'a closed claim advanced');
        } catch (\RuntimeException $e) {
            Assert::contains('no next step', $e->getMessage());
        }
    }],
];
