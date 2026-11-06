<?php

declare(strict_types=1);

namespace Lex\Tests;

use Lex\Home\Claim;
use Lex\Home\Matter;
use Lex\Home\Panel;

$open = static function (): Matter {
    $claim = new Claim('c9', 'Atlas', 'Harbour', 'A late container', '2026-09-14');
    $claim->assign('panel-1');
    $claim->advance();
    $claim->advance();

    return new Matter('m9', $claim, new Panel('panel-1', 'Bench one', ['Ada'], 'arbitration'));
};

return [
    ['a bond takes a positive deposit only', static function () use ($open): void {
        $matter = $open();
        $matter->deposit(120000);
        try {
            $matter->deposit(0);
            Assert::true(false, 'a zero deposit landed');
        } catch (\RuntimeException $e) {
            Assert::contains('positive', $e->getMessage());
        }
        Assert::same(120000, $matter->bond());
    }],
    ['a bond splits into a fee of one part in thirty two and a refund', static function () use ($open): void {
        $matter = $open();
        $matter->deposit(480000);
        $matter->file('The respondent pays the invoice in full.');
        $split = $matter->settle();
        Assert::same(15000, $split['fee']);
        Assert::same(465000, $split['refund']);
        Assert::same(480000, $split['fee'] + $split['refund']);
    }],
    ['a bond settles only after the claim closes', static function () use ($open): void {
        $matter = $open();
        $matter->deposit(100000);
        try {
            $matter->settle();
            Assert::true(false, 'an open claim settled');
        } catch (\RuntimeException $e) {
            Assert::contains('closes', $e->getMessage());
        }
    }],
    ['a closed claim takes no deposit', static function () use ($open): void {
        $matter = $open();
        $matter->deposit(1000);
        $matter->file('The respondent pays.');
        try {
            $matter->deposit(1000);
            Assert::true(false, 'a closed matter took a deposit');
        } catch (\RuntimeException $e) {
            Assert::contains('over', $e->getMessage());
        }
    }],
    ['a filed ruling enters the record', static function () use ($open): void {
        $matter = $open();
        $matter->file('The respondent pays the invoice in full.');
        Assert::same(1, count($matter->rulings()));
    }],
    ['the age of a case counts whole days between two dates', static function () use ($open): void {
        $matter = $open();
        Assert::same(54, $matter->days('2026-11-07'));
        Assert::same(365, $matter->days('2027-09-14'));
    }],
];
