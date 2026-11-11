# LEX

## What the project is

LEX is an online Alternative Dispute Resolution environment and system. It settles disputes between a claimant and a respondent, and anyone can bring a claim. A panel meets both sides and looks for terms, and if that fails, the same panel hears the case and decides. When a case closes, LEX records the ruling and the day. LEX stands on its own. It belongs to no community, no organisation and no single country.

A claim names the claimant, the respondent, the subject in one line and the day of filing. LEX assigns a panel with an odd number of seats. The panel meets both sides and looks for terms. If that fails, the same panel hears the case and decides. At closure, LEX records the ruling and the day.

A case passes five steps:

Filed
Panel
Mediated
Arbitrated
Closed

Each party puts up a bond. When a case closes, the court keeps one part in thirty two as its fee and returns the rest.

The repository contains a plain PHP 8.2 implementation. It requires no Composer and uses no packages.

## How to run it

The demo uses PHP's built-in web server.

Start the demo with:

```bash
php -S 127.0.0.1:8101 -t showroom showroom/index.php
```


Open the local server in a browser at http://127.0.0.1:8101/.

The application supports a URL prefix through the LEX_BASE environment variable. Set this value to the prefix that the proxy passes to the application. The default value is /.

Run the test suite from the repository root:

```bash
php tests/run.php

## What the routes answer

The application provides two routes.

/ renders the register of matters and the table of five steps. The register contains three sample matters.
/health answers with the matter count and the court fee in cents.

The health route provides a compact response about the current matter register and court fee.

## How the layout is arranged

The repository separates the model, application, demo entry point, tests and deployment files.

src/Home/ holds the model. Its classes are Claim, Panel, Matter and Sample.
src/App/ holds the router, front controller and HTML.
showroom/ holds the demo entry file and stylesheet.
tests/ holds the test suite and its runner.
deploy/ holds the systemd unit and nginx location block.

This layout keeps the model separate from the application entry points, presentation and deployment configuration.

## The state of the suite

The repository includes a hand-written test runner with 17 checks.

Run all checks with:

```bash
php tests/run.php


The suite uses no external test packages. The runner executes directly with PHP 8.2.

## Licence

The repository uses the Mozilla Public License 2.0 (MPL 2.0). You may use, modify and distribute the code. You must publish changes to files covered by the licence.
