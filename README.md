# Zaptec Billing

# Oppsett

- Installer systemverktøy:
    - PHP: `brew install php`
    - Pakkebehandler: `brew install composer`
- Installer prosjekt-verktøy: `composer install`
- Rediger `.env` filen med brukernavn og passord til Zaptec Portal
- List alle tilgjengelige ladere: `bin/console app:chargers:list`
- List ut en oversikt over lade-sesjoner: `bin/console app:chargers:sessions d5530f90-6ed0-44ff-a9c0-XXXXXXXXXX`
- Inkluder pris, og stop tidspunkt: `bin/console app:chargers:sessions d5530f90-6ed0-44ff-a9c0-XXXXXXXXXX --include-price --end=2021-10-01`