# Flow

Central flow measure management and control system, built on Laravel Framework.

[![Test](https://github.com/ECFMP/flow/actions/workflows/test.yml/badge.svg)](https://github.com/ECFMP/flow/actions/workflows/test.yml)

## Logging In

This project uses VATSIM Connect for authentication.

Information about how to integrate with Connect, including development environment credentials may be found [here](https://github.com/vatsimnetwork/developer-info/wiki/Connect-Development-Environment).

The development site for Connect may be found [here](https://auth-dev.vatsim.net).

You just need to add your OAuth client id and secret to the `.env` file.

## Developing

A handy docker environment is provided via Laravel Sail. For more information about how to use this, please see the [documentation](https://laravel.com/docs/9.x/sail).

At a very high level, simply run `./vendor/bin/sail up -d` to start the containers in daemon mode. You can then run composer using `./vendor/bin/sail composer` or artisan using `./vendor/bin/sail artisan`.

## Running the Test Suite

Using sail, run `./vendor/bin/sail artisan test`

## Code Formatting

This project uses `pint` to enforce PSR-12 formatting. To check your code for formatting issues and fix them, just run `./vendor/bin/pint`.
