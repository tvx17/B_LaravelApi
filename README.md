# LaravelAPI

Eine minimale API-Basis mit Laravel 13 und PHP ab 8.3.

## Lokal starten

Abhängigkeiten und lokaler Anwendungsschlüssel sind bereits eingerichtet.

```powershell
php artisan serve
```

Erreichbar unter `http://127.0.0.1:8000`:

- `GET /`: Anwendungsname und Status als JSON.
- `GET /api/status`: `{"status":"ok"}` als JSON.
- `GET /up`: Laravel-Healthcheck.

Unbekannte API-Routen liefern HTTP 404 als JSON, auch ohne Accept-Header. Die Status-Endpunkte benötigen keine Datenbank.

## Neuer Checkout

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Datenbankwahl und Authentifizierung sind noch offen. Die Vorlage verwendet vorläufig SQLite als Konfigurationswert; Migrationen wurden nicht ausgeführt. Vor `php artisan migrate` die gewünschte Datenbank in `.env` konfigurieren. Ein Frontend-Build ist für die API nicht erforderlich.

## Prüfung

```powershell
php artisan test
php vendor/bin/pint --test
php artisan route:list --path=api
```

Tests verwenden SQLite im Arbeitsspeicher. API-Tests liegen in `tests/Feature/ApiTest.php`, Anwendungslogik in `app/`, API-Routen in `routes/api.php`.

## Konfiguration

Lokale Einstellungen stehen in `.env`, Standardwerte in `.env.example`. `.env` und `vendor/` sind vom Git-Tracking ausgeschlossen. Keine Zugangsdaten einchecken. Auf produktiven Systemen `APP_DEBUG=false` setzen.
