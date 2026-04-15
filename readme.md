# Readme

Personal website https://justintallant.com

## Run
1. Clone this repo
1. Clone skimpy engine into the same directory as this repository
    1. `git clone git@github.com:skimpy/engine.git`
    1. This allows me to edit the engine while I work on my site
    1. See composer.json for more info on how this works.
1. `cp .env.example .env`
1. Edit the env values
1. `composer install`
1. `php -S localhost:4000 -t public`
1. `npm run watch`

## Deploy

Requires SSH key access to the production server.

```
./deploy.sh
```
