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
New-Item -ItemType File -Path database/database.sqlite -ErrorAction SilentlyContinue
php artisan migrate
```

Aktuell verwenden wir SQLite (`DB_CONNECTION=sqlite`); MariaDB ist später vorgesehen. Die lokale Datenbank liegt in `database/database.sqlite` und wird nicht eingecheckt. Die Anmeldung ist noch offen. Ein Frontend-Build ist für die API nicht erforderlich.

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

Der aktuell konfigurierte Guard heißt `web`. Verwende bei Rollen und Berechtigungen denselben Guard wie bei den Benutzern. Spatie regelt Berechtigungen; die Anmeldung wird separat ergänzt. Verfügbar sind die Middleware-Aliase `permission`, `role` und `role_or_permission`; für einzelne Rechte ist auch Laravels `can`-Middleware nutzbar.

Bei Änderungen über die Paketmethoden wird der Berechtigungscache automatisch aktualisiert. Bei manuellen Datenbankänderungen: `php artisan permission:cache-reset`.

Für den späteren Wechsel auf MariaDB `DB_CONNECTION=mariadb` sowie `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` und `DB_PASSWORD` lokal konfigurieren, dann `php artisan config:clear` und `php artisan migrate` ausführen. Bestehende SQLite-Daten werden dabei nicht übertragen.

## Prüfung

```powershell
php artisan test
php vendor/bin/pint --test
php artisan route:list --path=api
```

Tests verwenden SQLite im Arbeitsspeicher. API-Tests liegen in `tests/Feature/ApiTest.php`, Anwendungslogik in `app/`, API-Routen in `routes/api.php`.

## Konfiguration

Lokale Einstellungen stehen in `.env`, Standardwerte in `.env.example`. `.env` und `vendor/` sind vom Git-Tracking ausgeschlossen. Keine Zugangsdaten einchecken. Auf produktiven Systemen `APP_DEBUG=false` setzen.
