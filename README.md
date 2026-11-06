# LEX

## What the project is

LEX is the legal system of the InFolk Community. It runs mediation and arbitration over a distributed network.

The project models a claim through five steps:

Filed
Assigned to a panel
Mediated
Arbitrated
Closed

Each claim names a claimant, a respondent, a subject in one line and the day it was filed. A panel holds an odd number of seats so a vote settles the matter.

When a case closes, LEX records the ruling and the day. The court holds a bond that the parties put up and keeps a fee of one part in thirty two when the case closes.

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
