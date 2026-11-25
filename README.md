# recht-zeitig

Demo- und Testanwendung für [`teris-dev/erecht24-bundle`](https://github.com/teris-dev/erecht24-bundle):
Impressum und Datenschutzerklärung (DE/EN) kommen per Push & Pull aus dem eRecht24 Projekt-Manager.
Single-Modus, ohne Datenbank.

| Seite | Pfad |
|---|---|
| Start mit Status (Stand der Texte, Push-URL) | `/` |
| Impressum / Imprint | `/impressum`, `/en/imprint` |
| Datenschutz / Privacy Policy | `/datenschutz`, `/en/privacy-policy` |
| eRecht24-Push-Endpunkt | `/erecht24/push` |
| Health-Check (sym-ops) | `/healthz` |

## Lokal

```bash
composer install
symfony serve            # oder: php -S 127.0.0.1:8000 -t public
```

In `.env` ist der öffentliche Demo-API-Key aus der eRecht24-Doku eingetragen. Damit
funktioniert das Abholen der Texte, die Push-Registrierung aber nicht: Für den
Demo-Key sind bei eRecht24 bereits 3 Clients angemeldet, mehr erlaubt die API nicht.

## Auf sym (recht-zeitig.labs.teris.de)

Voraussetzung: DNS von `recht-zeitig.labs.teris.de` zeigt auf sym (213.160.77.208 / 2a00:17d8:100::4bc1).

```bash
sudo -u deploy git clone git@github-recht-zeitig:teris-dev/recht-zeitig.git /srv/recht-zeitig/repo
echo 'BRANCH=main' | sudo tee /srv/recht-zeitig/site.conf
sudo site recht-zeitig provision
sudoedit /srv/recht-zeitig/shared/.env.local   # siehe unten
sudo site recht-zeitig cert
sudo site recht-zeitig deploy
```

`shared/.env.local`:

```dotenv
APP_ENV=prod
APP_SECRET=<zufällig>
DEFAULT_URI=https://recht-zeitig.labs.teris.de
ERECHT24_API_KEY=<API-Key des eRecht24-Projekts „recht-zeitig“>
```

Push-Endpunkt anmelden und testen:

```bash
sudo site recht-zeitig console erecht24:client:register
# → ERECHT24_CLIENT_ID / ERECHT24_CLIENT_SECRET in shared/.env.local ergänzen, erneut deployen
sudo site recht-zeitig console erecht24:client:test-push --type=ping
sudo site recht-zeitig console erecht24:client:test-push --type=imprint
```

Danach im eRecht24 Projekt-Manager einen Text ändern, auf „Synchronisieren“ klicken
und auf der Startseite den neuen Stand prüfen.

## Automatische Updates

- **Bundle:** `.github/workflows/update-bundle.yml` prüft stündlich, ob es einen neuen Stand von
  `teris-dev/erecht24-bundle` (main) gibt, aktualisiert `composer.lock`, führt `bin/smoke-test`
  aus und pusht nur bei Erfolg auf `main`.
- **Übrige Abhängigkeiten:** Dependabot (täglich), Patch/Minor werden nach grünem CI automatisch gemergt.
- **Deploy:** sym (`AUTODEPLOY=1`) deployt jeden neuen Commit auf `main` stündlich um :17.
  Scheitert der Health-Check, bleibt das vorherige Release aktiv.

`bin/smoke-test` startet die App lokal und prüft alle Seiten und den Push-Endpunkt.
