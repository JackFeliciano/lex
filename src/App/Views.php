<?php

declare(strict_types=1);

namespace Lex\App;

use Lex\Home\Claim;
use Lex\Home\Matter;
use Lex\Home\Sample;

/**
 * The HTML. Every string that reaches the page goes through h(), so a party name
 * with an ampersand in it cannot break the markup.
 */
final class Views
{
    public function __construct(private readonly string $base)
    {
    }

    private function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function header(string $title): string
    {
        $base = $this->h($this->base);

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$this->h($title)}</title>
<base href="{$base}">
<link rel="stylesheet" href="assets/site.css">
</head>
<body>
<main>
HTML;
    }

    public function footer(): string
    {
        return "</main>\n</body>\n</html>\n";
    }

    /**
     * The money the court holds is written in whole units with the cents after a
     * comma, so a reader can add the column up by eye.
     */
    private function money(int $cents): string
    {
        return number_format(intdiv($cents, 100), 0, ',', '.') . ',' . sprintf('%02d', $cents % 100);
    }

    public function front(Sample $sample, string $today): string
    {
        $TITLE = 'LEX';
        $LEDE = 'LEX runs mediation and arbitration across a distributed network. It serves as the legal system of the InFolk Community.';
        $out = $this->header($TITLE);
        $out .= '<h1>' . $TITLE . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h('Visitors can explore how LEX handles a claim from filing to closure. Each claim records the claimant, respondent, subject and filing day. A panel holds an odd number of seats so its vote settles the matter. The process follows five steps, from filing through assignment, mediation and arbitration to closure. When a case closes, LEX records its ruling and the day. The court also holds a bond from the parties and keeps one part in thirty two as its fee.') . '</p>' . "\n";
        $out .= '<p class="stage">Matters open: ' . $sample->count() . '. Court fee on the sample bond: ' . $this->money($sample->fee()) . '.</p>' . "\n";
        $out .= '<h2>The register</h2>' . "\n";
        $out .= '<div class="cards">' . "\n";
        foreach ($sample->matters() as $matter) {
            $claim = $matter->claim;
            $out .= '<article class="card">' . "\n";
            $out .= '<h3>' . $this->h($claim->handle) . '</h3>' . "\n";
            $out .= '<p>' . $this->h($claim->subject) . '</p>' . "\n";
            $out .= '<dl>' . "\n";
            $out .= '<dt>Claimant</dt><dd>' . $this->h($claim->claimant) . '</dd>' . "\n";
            $out .= '<dt>Respondent</dt><dd>' . $this->h($claim->respondent) . '</dd>' . "\n";
            $out .= '<dt>Filed</dt><dd>' . $this->h($claim->filedOn) . '</dd>' . "\n";
            $out .= '<dt>Panel</dt><dd>' . $this->h((string) $claim->panel()) . '</dd>' . "\n";
            $out .= '<dt>Bond</dt><dd>' . $this->money($matter->bond()) . '</dd>' . "\n";
            $out .= '<dt>Standing</dt><dd>' . $this->badge($claim->status()) . '</dd>' . "\n";
            $out .= '</dl>' . "\n";
            $out .= '</article>' . "\n";
        }
        $out .= '</div>' . "\n";

        $out .= '<h2>The steps a case takes</h2>' . "\n";
        $out .= '<table class="tiers"><thead><tr><th>Step</th><th>What has to happen</th></tr></thead><tbody>' . "\n";
        $steps = [
            ['Filed', 'A claimant names a respondent, states the subject in one line and puts up a bond.'],
            ['Panel', 'The court assigns a panel. A panel holds an odd number of seats so a vote settles.'],
            ['Mediated', 'The panel meets both sides and looks for terms.'],
            ['Arbitrated', 'The panel that failed to settle the case hears it and decides.'],
            ['Closed', 'The ruling enters the record with the day it closed and the bond settles.'],
        ];
        foreach ($steps as [$step, $line]) {
            $out .= '<tr><td><strong>' . $this->h($step) . '</strong></td><td>' . $this->h($line) . '</td></tr>' . "\n";
        }
        $out .= '</tbody></table>' . "\n";
        $out .= '<p class="stage">The register reads as of ' . $this->h($today) . '.</p>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    private function badge(string $status): string
    {
        $on = in_array($status, [Claim::FILED, Claim::MEDIATED, Claim::ARBITRATED], true);

        return '<span class="badge ' . ($on ? 'on' : 'off') . '">' . $this->h($status) . '</span>';
    }

    public function notFound(string $path): string
    {
        $out = $this->header('Not found');
        $out .= '<h1>Not found</h1>' . "\n";
        $out .= '<p class="stage">' . $this->h($path) . ' is not a route of this project.</p>' . "\n";
        $out .= $this->footer();

        return $out;
    }
}
