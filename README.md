# LaravelAPI

Eine minimale API-Basis mit Laravel 13 und PHP ab 8.3.

Arbeitsschritte, Entscheidungen, Prüfergebnisse und offene Aufgaben werden in [PROJEKTDOKUMENTATION.md](PROJEKTDOKUMENTATION.md) fortlaufend festgehalten.

Windows-Eignung und Alternativen für Sicherungen: [BACKUP_PRUEFUNG.md](BACKUP_PRUEFUNG.md).

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
New-Item -ItemType File -Path database/database.sqlite -ErrorAction SilentlyContinue
php artisan migrate
```

Aktuell verwenden wir SQLite (`DB_CONNECTION=sqlite`); MariaDB ist später vorgesehen. Die lokale Datenbank liegt in `database/database.sqlite` und wird nicht eingecheckt. Die Runner UI verwendet eine Browser-Anmeldung. Ein Frontend-Build ist für die API nicht erforderlich.

## Rollen und Berechtigungen

Spatie Laravel Permission ist im User-Modell integriert. Konfiguration: `config/permission.php`. Die Migration erstellt Rollen, Berechtigungen und deren Zuordnungstabellen; fachliche Rollen und Rechte werden noch nicht angelegt.

Beispiel für die spätere Verwendung in PHP:

```php
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$permission = Permission::findOrCreate('users.view', 'web');
$role = Role::findOrCreate('reader', 'web');
$role->givePermissionTo($permission);
$user->assignRole($role);
$user->can('users.view');
```

Der aktuell konfigurierte Guard heißt `web`. Verwende bei Rollen und Berechtigungen denselben Guard wie bei den Benutzern. Verfügbar sind die Middleware-Aliase `permission`, `role` und `role_or_permission`; für einzelne Rechte ist auch Laravels `can`-Middleware nutzbar.

Bei Änderungen über die Paketmethoden wird der Berechtigungscache automatisch aktualisiert. Bei manuellen Datenbankänderungen: `php artisan permission:cache-reset`.

Für den späteren Wechsel auf MariaDB `DB_CONNECTION=mariadb` sowie `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` und `DB_PASSWORD` lokal konfigurieren, dann `php artisan config:clear` und `php artisan migrate` ausführen. Bestehende SQLite-Daten werden dabei nicht übertragen.

## Artisan Runner UI

`codesquirrel/artisan-runner-ui` ist installiert. Die Konfiguration liegt in `config/artisan-runner-ui.php`. Lokal ist die UI aktiviert. Neue Checkouts starten mit `ARTISAN_RUNNER_UI_ENABLED=false`; zum Aktivieren den Wert in `.env` auf `true` setzen und `php artisan config:clear` ausführen.

Die Oberfläche liegt unter `/artisan-runner-ui`. Anmeldung: `/login`; Konto und Abmeldung: `/runner`. Die Middleware `web`, `auth:web` und `can:run-artisan-runner-ui` schützt sowohl die Oberfläche als auch die Befehlsausführung. Die Paketkonfiguration erlaubt ausschließlich die Umgebungen `local`, `qa`, `staging` und `uat`.

Die Listen `allowed` und `denied` steuern verfügbare Befehle. Die Oberfläche benötigt keinen Frontend-Build und keine zusätzliche Migration. Die veröffentlichte Blade-Ansicht enthält einen Link zum Konto; bei Paketupdates Änderungen an der Originalansicht prüfen.

## Zugangsbenutzer anlegen

```powershell
php artisan runner:create-user admin@example.com --name="Administrator"
```

Der Befehl fragt das Passwort verdeckt inklusive Bestätigung ab (mindestens 12 Zeichen), speichert es gehasht und vergibt ausschließlich die Berechtigung `run-artisan-runner-ui` für den Guard `web`. Bereits vorhandene Benutzer werden nicht geändert. Es gibt keine öffentliche Registrierung und keine automatisch erzeugten Standardzugänge. Der Befehl ist in der Runner UI gesperrt.

Bestehenden Benutzern kann die Berechtigung über Spatie vergeben oder entzogen werden:

```php
$permission = Permission::findOrCreate('run-artisan-runner-ui', 'web');
$user->givePermissionTo($permission);
$user->revokePermissionTo($permission);
```

Die Anmeldung verwendet Sessions, CSRF-Schutz und begrenzte Anmeldeversuche. Beim Anmelden wird die Session-ID erneuert; beim Abmelden werden Session und CSRF-Token erneuert. Eine API-Token-Anmeldung ist noch nicht eingerichtet.

## Laravel Telescope

Telescope ist als Entwicklungsabhängigkeit installiert und wird ausschließlich in `APP_ENV=local` registriert. Lokal ist das Dashboard unter `http://127.0.0.1:8000/telescope` ohne Anmeldung erreichbar. In anderen Umgebungen werden keine Telescope-Routen registriert; `composer install --no-dev` ist weiterhin möglich.

Konfiguration: `config/telescope.php`; Filter und Maskierung: `app/Providers/TelescopeServiceProvider.php`. `TELESCOPE_ENABLED=false` deaktiviert lokal Aufzeichnung und Dashboard. Nach einer Änderung gegebenenfalls `php artisan config:clear` ausführen.

Requests, Fehler, Datenbankabfragen und weitere Ereignisse werden in der konfigurierten Datenbank gespeichert, aktuell SQLite. Request-Passwörter, Cookies, Authorization- und CSRF-Werte werden auch lokal maskiert. Die mitgelieferten Dashboard-Assets benötigen keinen eigenen Frontend-Build.

Alte Einträge bei Bedarf entfernen:

```powershell
php artisan telescope:prune --hours=48
```

Der Befehl entfernt Aufzeichnungen, die älter als 48 Stunden sind. Eine automatische Bereinigung ist noch nicht eingerichtet. Bei einem neuen Checkout legt `php artisan migrate` auch die Telescope-Tabellen an.

## Socialite

Laravel Socialite ist als reguläre Abhängigkeit installiert und wird automatisch von Laravel registriert. Einrichtung nach der [offiziellen Dokumentation](https://laravel.com/framework/docs/socialite).

Ein OAuth-Anbieter und Login-/Callback-Routen sind noch nicht eingerichtet. Nach Auswahl des Anbieters dessen `client_id`, `client_secret` und `redirect` in `config/services.php` mit Werten aus `.env` konfigurieren; Platzhalter in `.env.example` dokumentieren. Die Callback-URL muss mit der beim Anbieter registrierten URL übereinstimmen. Zugangsdaten ausschließlich lokal hinterlegen.

Vor Umsetzung festlegen, ob Socialite die Browser-Anmeldung oder die API-Anmeldung ergänzen soll und wie externe Konten mit Benutzern verknüpft werden. Socialite allein stellt keine API-Tokens aus und vergibt keine Runner-Berechtigung.

## Backups mit Kopia

KopiaUI ist lokal unter Windows installiert; Sicherung und Wiederherstellung mit temporären Dateien wurden geprüft. Ziel, Umfang und automatische Datenbank-Vorbereitung stehen noch aus. Details: [Kopia-Einrichtung](KOPIA_SETUP.md) und [Windows-Backup-Prüfung](BACKUP_PRUEFUNG.md).

## Prüfung

```powershell
php artisan test
php vendor/bin/pint --test
php artisan route:list --path=api
```

Tests verwenden SQLite im Arbeitsspeicher. API-Tests liegen in `tests/Feature/ApiTest.php`, Anwendungslogik in `app/`, API-Routen in `routes/api.php`.

## Konfiguration

Lokale Einstellungen stehen in `.env`, Standardwerte in `.env.example`. `.env` und `vendor/` sind vom Git-Tracking ausgeschlossen. Keine Zugangsdaten einchecken. Auf produktiven Systemen `APP_DEBUG=false` setzen.
