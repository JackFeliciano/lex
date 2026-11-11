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
        $LEDE = 'LEX is an online system that settles disputes between a claimant and a respondent, and anyone can bring a claim.';
        $out = $this->header($TITLE);
        $out .= '<h1>' . $TITLE . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h('A claim names the claimant, the respondent, the subject in one line and the day of filing. LEX assigns a panel with an odd number of seats. The panel meets both sides and looks for terms. If that fails, the same panel hears the case and decides. At closure, LEX records the ruling and the day.') . '</p>' . "\n";
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
            ['Filed', 'The claimant files a claim that names both sides, the subject in one line and the day of filing.'],
            ['Panel', 'LEX assigns the case to a panel with an odd number of seats, and the panel meets both sides to look for terms.'],
            ['Mediated', 'The panel looks for terms both sides accept.'],
            ['Arbitrated', 'The same panel hears the case and decides by vote.'],
            ['Closed', 'LEX records the ruling and the day the case closes.'],
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
