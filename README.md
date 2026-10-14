# LaravelAPI

Eine minimale API-Basis mit Laravel 13 und PHP ab 8.3.

Alle aktuellen Einstellungen, installierten Erweiterungen, URLs und Befehle: [Laravel-Handbuch](LARAVEL_HANDBUCH.md). Vollständige Referenzen: [Konfiguration](docs/LARAVEL_KONFIGURATION.md) und [Composer-Pakete](docs/COMPOSER_PAKETE.md).

Arbeitsschritte, Entscheidungen, Prüfergebnisse und offene Aufgaben werden in [PROJEKTDOKUMENTATION.md](PROJEKTDOKUMENTATION.md) fortlaufend festgehalten.

Windows-Eignung und Alternativen für Sicherungen: [BACKUP_PRUEFUNG.md](BACKUP_PRUEFUNG.md).

## Codex-Sitzung wieder aufnehmen

```powershell
.\Resume-Sitzung.ps1
```

Das Skript nimmt diese Sitzung anhand ihrer gespeicherten ID im Projektverzeichnis wieder auf. Codex CLI muss im PATH verfügbar sein; der Verlauf muss im gleichen lokalen Codex-Profil erhalten sein. Mit `-Auswahl` stattdessen eine gespeicherte Projektsitzung auswählen, mit `-SessionId ID` eine andere Sitzung öffnen. `-WhatIf` zeigt den vorgesehenen Aufruf ohne Codex zu starten. Das Skript startet keine Laravel-Dienste.

Grundlage: [offizielle Codex-Befehlsreferenz](https://learn.chatgpt.com/docs/developer-commands?surface=cli).

## Lokal starten

Abhängigkeiten und lokaler Anwendungsschlüssel sind bereits eingerichtet.

```powershell
php artisan serve
```

Erreichbar unter `http://127.0.0.1:8000`:

- `GET /`: Anwendungsname und Status im gemeinsamen API-Antwortformat.
- `GET /api/status`: Status im gemeinsamen API-Antwortformat.
- `GET /up`: Laravel-Healthcheck.

Unbekannte API-Routen liefern HTTP 404 als JSON, auch ohne Accept-Header. Die Status-Endpunkte benötigen keine Datenbank.

## Neuer Checkout

```powershell
composer setup
```

Der interaktive Assistent funktioniert unter Windows, Linux und macOS mit PHP ab 8.3 und Composer im PATH. Alternativ: `php scripts/Setup.php`. Jeder Einrichtungsschritt benötigt eine Bestätigung; Enter bedeutet Nein. Einzelheiten und Voraussetzungen: [Setup-Anleitung](docs/SETUP.md).

Er installiert die gesperrten Composer-Abhängigkeiten, erstellt bei Bedarf `.env` aus der sicheren Vorlage und einen neuen Anwendungsschlüssel. Anschließend wählt man SQLite, MariaDB oder MySQL sowie eine vorhandene oder neue Datenbank, prüft die Verbindung und bestätigt deren Übernahme. Danach folgen API-/Frontend-Adresse, CORS, Migrationen, Paketregistrierung/Filament-Assets, ein optionaler gemeinsamer Administrator, Runner-Aktivierung, Storage-Link und Brain-Scan. Zum Abschluss werden URLs und der Serverbefehl ausgegeben. Bestehende Schlüssel, Konten und nicht ausgewählte Einstellungen bleiben erhalten.

Aktuell verwenden wir SQLite (`DB_CONNECTION=sqlite`); MariaDB ist später vorgesehen. Die lokale Datenbank wird nicht eingecheckt. Ein Frontend-Build ist für die API nicht erforderlich.

### Einrichtung im Browser

Alternativ steht unter `/setup.php` ein grafischer Assistent mit denselben Einrichtungsschritten bereit, zum Beispiel `http://localhost:8000/setup.php`. Er funktioniert auch vor der Laravel-Installation, sofern ein PHP-Webserver bereits `public/` ausliefert.

Die Seite ist standardmäßig gesperrt. Zum Freigeben mit Editor oder Hosting-Dateimanager im Projektverzeichnis die Datei `.setup-access` anlegen und einen eigenen zufälligen Zugangsschlüssel mit mindestens 32 Zeichen eintragen. Diesen auf der Setup-Seite eingeben. Jede Änderung wird einzeln bestätigt; der Abschluss sperrt die Seite wieder. Außerhalb von localhost ist HTTPS erforderlich.

Zu Beginn stehen English (Standard), Deutsch, Français, Italiano und Español zur Auswahl. Zum Ausführen eines Schritts genügt die Schaltfläche; zusätzliche Bestätigungshaken entfallen. Abschlusslinks verwenden den aktuellen Aufrufhost samt Schema und Port, beispielsweise `http://localhost:8090/filament/login`.

Es ist kein eigener Terminalzugang nötig. PHP-CLI, Composer und erlaubte Prozessausführung müssen jedoch auf dem Server vorhanden sein. Voraussetzungen, Grenzen und Freigabe: [Browser-Setup](docs/SETUP.md#einrichtung-im-browser).

Mit JavaScript zeigt die Oberfläche laufende Teilbefehle, Laufzeit und regelmäßige Aktivitätsmeldungen. Ausbleibende Statusmeldungen werden kenntlich gemacht; bei Verbindungsabbruch nicht erneut starten, da die Aufgabe serverseitig weiterlaufen kann. Auch `composer setup` meldet Start, Dauer und Abschluss längerer Unterbefehle.

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

`codesquirrel/artisan-runner-ui` ist installiert. Die Konfiguration liegt in `config/artisan-runner-ui.php`. Lokal und in der Projektvorlage ist die UI aktiviert (`ARTISAN_RUNNER_UI_ENABLED=true`); Zugriff erfordert Anmeldung und das Runner-Recht. Zum Deaktivieren den Wert in `.env` auf `false` setzen und `php artisan config:clear` ausführen.

Die Oberfläche liegt unter `/artisan`, Befehlsausführung unter `/artisan/run`. Anmeldung: `/artisan/login`; Konto: `/artisan/account`; Abmeldung: `/artisan/logout`. Der Präfix ist fest in der Paketkonfiguration gesetzt und wird nicht durch `.env` überschrieben. Die Middleware `web`, `auth:web` und `can:run-artisan-runner-ui` schützt sowohl die Oberfläche als auch die Befehlsausführung. Die Paketkonfiguration erlaubt ausschließlich die Umgebungen `local`, `qa`, `staging` und `uat`.

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

## Laravel Brain

`laramint/laravel-brain` ist als Entwicklungsabhängigkeit installiert. Lokal den Architekturgraphen erzeugen und danach im Browser öffnen:

```powershell
php artisan brain:scan --no-interaction
php artisan serve
```

Oberfläche: `http://localhost:8000/_laravel-brain`. Nach Codeänderungen erneut scannen. Graphdateien liegen in `storage/app/laravel-brain/` und sind von Git ausgeschlossen. Die Paketoberfläche und Befehle werden nur bei `APP_ENV=local` registriert; bei `composer install --no-dev` wird das Paket nicht installiert.

Der erste Scan wurde unter Windows erfolgreich ausgeführt. Brain erzeugt keine Upload- oder fachliche Admin-Oberfläche. Die automatisch installierte Abhängigkeit `laramint/laravel-stress` wurde nicht für Lasttests verwendet. Die Funktion zur Generierung von KI-Regeldateien nicht auf das vorhandene `AGENTS.md` anwenden, solange dessen Nichtändern-Vorgabe gilt. [Paketdokumentation](https://github.com/laramint/laravel-brain).

## Filament Admin-Panel

Filament 5 ist unter `http://127.0.0.1:8000/filament` erreichbar, Anmeldung unter `/filament/login`. Panel-Konfiguration: `app/Providers/Filament/AdminPanelProvider.php`. Das Panel nutzt den vorhandenen `web`-Guard und benötigt in jeder Umgebung die Spatie-Berechtigung `access-admin-panel`.

Ein Konto lokal interaktiv anlegen und anschließend gezielt freischalten:

```powershell
php artisan make:filament-user
php artisan admin:grant-access admin@example.com
```

Alternativ nur den zweiten Befehl für einen bestehenden Benutzer ausführen. Kein Standardkonto wurde angelegt. Runner-Zugriff und Admin-Zugriff sind getrennte Berechtigungen. Die Befehle zur Kontoanlage und Admin-Freischaltung sind in der Runner UI gesperrt. Zugriff über Spaties `revokePermissionTo('access-admin-panel')` entziehen.

Das Panel enthält zunächst nur das Dashboard; keine fachlichen Ressourcen oder öffentliche Registrierung. Bei neuen Checkouts werden die mitgelieferten Assets durch den Composer-Hook `php artisan filament:upgrade` veröffentlicht. Für das Standardpanel ist kein eigener Node-/Tailwind-Build nötig. Nach Paketupdates bei Bedarf denselben Befehl ausführen. [Offizielle Installation](https://filamentphp.com/docs/5.x/introduction/installation).

## Socialite

Laravel Socialite ist als reguläre Abhängigkeit installiert und wird automatisch von Laravel registriert. Einrichtung nach der [offiziellen Dokumentation](https://laravel.com/framework/docs/socialite).

Ein OAuth-Anbieter und Login-/Callback-Routen sind noch nicht eingerichtet. Nach Auswahl des Anbieters dessen `client_id`, `client_secret` und `redirect` in `config/services.php` mit Werten aus `.env` konfigurieren; Platzhalter in `.env.example` dokumentieren. Die Callback-URL muss mit der beim Anbieter registrierten URL übereinstimmen. Zugangsdaten ausschließlich lokal hinterlegen.

Vor Umsetzung festlegen, ob Socialite die Browser-Anmeldung oder die API-Anmeldung ergänzen soll und wie externe Konten mit Benutzern verknüpft werden. Socialite allein stellt keine API-Tokens aus und vergibt keine Runner-Berechtigung.

## Spatie Media Library

Die kostenlose Spatie Media Library ist als Grundlage installiert. Konfiguration: `config/media-library.php`; die veröffentlichte Migration erstellt die Tabelle `media`. Bei einem neuen Checkout `php artisan migrate` ausführen.

Die Standardablage ist die Disk `public` unter `storage/app/public`; `MEDIA_DISK` kann eine andere Disk aus `config/filesystems.php` wählen. Die Dateigrößengrenze beträgt standardmäßig 10 MB. Für öffentliche URLs erst bei Bedarf `php artisan storage:link` ausführen; unter Windows können dafür entsprechende Rechte erforderlich sein.

Noch kein Anwendungsmodell und keine Upload-Route angebunden. Das gewünschte Modell muss `Spatie\MediaLibrary\HasMedia` implementieren und `Spatie\MediaLibrary\InteractsWithMedia` verwenden. Vor API-Uploads Dateitypen, Größen, Berechtigungen und öffentliche oder private Ablage festlegen.

GD ist als Bildtreiber konfiguriert und lokal vorhanden. Konvertierungen laufen standardmäßig über die Datenbank-Queue (`php artisan queue:work`). PDF-/SVG-Vorschaubilder benötigen zusätzliche Werkzeuge, Video-Vorschaubilder FFmpeg; deren Windows-Installation und Pfade erst vor Nutzung prüfen. Es wurden keine Konvertierungen eingerichtet. [Offizielle Anforderungen](https://spatie.be/docs/laravel-medialibrary/v11/requirements).

Kopia muss später sowohl die zugehörigen Dateien als auch die Datenbank sichern; die Tabelle `media` allein enthält nicht die Dateien.

## Backups mit Kopia

KopiaUI ist lokal unter Windows installiert; Sicherung und Wiederherstellung mit temporären Dateien wurden geprüft. Ziel, Umfang und automatische Datenbank-Vorbereitung stehen noch aus. Details: [Kopia-Einrichtung](KOPIA_SETUP.md) und [Windows-Backup-Prüfung](BACKUP_PRUEFUNG.md).

## Prüfung

```powershell
php artisan test
php vendor/bin/pint --test
php artisan route:list --path=api
```

Tests verwenden SQLite im Arbeitsspeicher. API-Tests liegen in `tests/Feature/ApiTest.php`, Anwendungslogik in `app/`, API-Routen in `routes/api.php`.

## CORS und Frontend-Cookies

`config/cors.php` erlaubt `http://localhost:9200` für `/api/*` und den vorbereiteten Pfad `/sanctum/csrf-cookie`. Cookies sind über `supports_credentials=true` erlaubt. Andere Origins werden nicht freigegeben; Filament und Runner sind nicht in den CORS-Pfaden enthalten.

`CORS_ALLOWED_ORIGINS` überschreibt die erlaubten Origins als kommagetrennte Liste vollständiger URLs einschließlich Schema und Port. Der Standard gilt auch ohne Eintrag in der lokalen `.env`. Nach Änderungen `php artisan config:clear` ausführen. Bei Cookie-Nutzung keine Wildcard-Origin konfigurieren.

Frontend-Aufrufe benötigen z. B. `fetch(url, { credentials: 'include' })` beziehungsweise bei Axios `withCredentials: true` und für den späteren CSRF-Ablauf `withXSRFToken: true`. Frontend und API lokal beide mit `localhost` betreiben, beispielsweise Frontend `http://localhost:9200`, API `http://localhost:8000`; nicht mit `127.0.0.1` mischen. Die vorhandenen hostgebundenen Session-Cookies mit SameSite `lax` passen zu diesen HTTP-Adressen auf demselben Host.

Sanctum ist für die SPA-Session-Anmeldung eingerichtet; siehe den folgenden Ablauf. Grundlage: [Laravel Sanctum SPA-Konfiguration](https://laravel.com/framework/docs/sanctum#spa-authentication).

## API-Anmeldung mit Sanctum

Sanctum 4.3.3 verwendet Cookies und den vorhandenen `web`-Guard. `config/sanctum.php` vertraut standardmäßig `localhost:9200`; `SANCTUM_STATEFUL_DOMAINS` kann die kommagetrennte Host-/Port-Liste ohne Schema überschreiben. CORS-Origins enthalten dagegen das Schema.

Fortify 1.41.0 übernimmt die Login-Pipeline: Zugangsdaten prüfen und Session erneuern. Konfiguration: `config/fortify.php`; Integration: `app/Providers/FortifyServiceProvider.php`. Die eigenen Controller delegieren weiterhin an Actions. Login-/Logout-Responses unter `app/Http/Responses/Fortify/` verwenden das gemeinsame API-Format. Optionales `remember` beim Login muss ein Boolean sein.

Automatische Fortify-Routen sind mit `Fortify::ignoreRoutes()` deaktiviert, damit `/api/login`, `/api/logout`, `/artisan` und `/filament` ihre bestehenden Zuständigkeiten behalten. Fortify ist ohne eigene Views eingerichtet. Registrierung, Passwort-Reset, Profil-/Passwortänderung, Zwei-Faktor-Anmeldung und Passkeys sind deaktiviert. Das Aktivieren eines Features allein genügt bei dieser Integration nicht: dazu passende eigene Routen, Actions, Responses und gegebenenfalls Migrationen ergänzen. [Offizielle Fortify-Dokumentation](https://laravel.com/framework/docs/13.x/fortify).

| Aufruf | Zweck | Erfolgscode |
| --- | --- | --- |
| `GET /sanctum/csrf-cookie` | Session und XSRF-Cookie initialisieren | HTTP 204, kein JSON-Body |
| `POST /api/login` | Mit `email` und `password` anmelden | `05001` |
| `GET /api/user` | Angemeldeten Benutzer abrufen | `05004` |
| `POST /api/logout` | Session beenden | `05003` |
| `GET /api/email/verify` | Verifizierungsstatus des angemeldeten Benutzers | `05005` |
| `POST /api/email/verification-notification` | Bestätigungslink senden/erneut senden | `05006`, bereits bestätigt: `05008` |
| `GET /api/email/verify/{id}/{hash}?expires=...&signature=...` | Signierten Link bestätigen | `05007`, bereits bestätigt: `05008` |

Beispiel im Frontend mit Axios:

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000',
  withCredentials: true,
  withXSRFToken: true,
  headers: { Accept: 'application/json' },
});

await api.get('/sanctum/csrf-cookie');
await api.post('/api/login', { email, password });
const { data } = await api.get('/api/user');
await api.post('/api/logout');
```

Axios ist ein Beispiel für das separate Frontend, keine hier installierte Abhängigkeit. Bei `fetch` Cookies mit `credentials: 'include'` mitsenden und den URL-dekodierten Wert des Cookies `XSRF-TOKEN` als `X-XSRF-TOKEN` bei POST-Anfragen übertragen. Origin/Referer muss zur freigegebenen SPA passen.

Falsche Zugangsdaten liefern `05002`/HTTP 401, Validierungsfehler `00422`/422, fehlende Anmeldung `00401`/401 und fehlender/ungültiger CSRF-Token `00419`/419. Login ist gedrosselt. Nach 419 den CSRF-Ablauf erneut starten, nach 401 erneut anmelden.

Ein vorhandenes Benutzerkonto wird benötigt; keine öffentliche Registrierung und keine automatisch vergebenen Admin-/Runner-Rechte. Sanctum ist hier ausschließlich für Sessions eingerichtet, ohne Token-Erzeugung oder Token-Migration. Browser-Panels und API teilen den `web`-Guard: API-Logout beendet daher auch die gemeinsame Browser-Session. `/filament` und `/artisan` bleiben getrennte Routenbereiche.

## E-Mail-Verifizierung

Native Laravel-Verifizierung ist eingerichtet. `User` implementiert `MustVerifyEmail`; der vorhandene Zeitstempel `email_verified_at` speichert den Zustand. Zusätzliche Pakete oder Migrationen werden nicht benötigt. Die drei Controller-Methoden delegieren an Actions unter `app/Actions/Users/`.

Nach Session-Anmeldung `POST /api/email/verification-notification` mit Cookies/CSRF aufrufen. Ein erfolgreicher Aufruf erzeugt die native Notification; er garantiert keine Zustellung in das Postfach. Mit `MAIL_MAILER=log` steht die Mail nur im Laravel-Log. Für echte Zustellung einen Maildienst in `.env` konfigurieren, beispielsweise:

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.example.org
MAIL_PORT=587
MAIL_USERNAME=your-mail-user
MAIL_PASSWORD="your-mail-password"
MAIL_FROM_ADDRESS="noreply@example.org"
MAIL_FROM_NAME="Your Application"
AUTH_VERIFICATION_EXPIRE=60
```

Werte an den Anbieter anpassen; für implizites TLS gegebenenfalls `MAIL_SCHEME=smtps` und den dazugehörigen Port verwenden. Danach `php artisan config:clear`. `APP_URL` muss die tatsächliche öffentliche API-Adresse sein, insbesondere für Links aus CLI-/Hintergrundprozessen. `.env` und Zugangsdaten nicht einchecken.

Den vollständigen Link aus der Mail unverändert öffnen: er enthält Signatur und Ablaufzeit, standardmäßig 60 Minuten. Es muss derselbe Benutzer angemeldet sein. Bei HTTP 401 zuerst anmelden und anschließend den ursprünglichen Link erneut öffnen; abgelaufene oder manipulierte Links liefern `00403`/HTTP 403. Der Link-Endpunkt nutzt Web-Session-Middleware, damit auch ein Browseraufruf aus einer Mail ohne SPA-Origin funktioniert, und antwortet weiterhin als JSON.

Versand ist auf sechs Anfragen pro Minute/Konto und 30 pro Minute/IP begrenzt; darüber `00429`/429 mit `Retry-After`. Bestehende Benutzer werden nicht automatisch bestätigt. Login, Filament und Runner erhalten keine zusätzliche Verifizierungspflicht. Später fachlich passende Routen mit `verified` schützen; bei künftigen E-Mail-Änderungen den Bestätigungsstatus zurücksetzen. Öffentliche Registrierung bleibt deaktiviert; ein künftig ausgelöstes `Registered`-Event versendet die native Notification.

Browser- und CLI-Assistent zeigen einen eigenen Hinweisschritt zu Mailzustellung, Linkdauer und API-Adresse. Dabei werden weder Testmails versandt noch Mailzugänge oder Benutzerzustände geändert. Paketvergleich: [Prüfung](docs/EMAIL_VERIFIZIERUNG_PRUEFUNG.md).

## Automatische Slugs für Eloquent-Modelle

`cviebrock/eloquent-sluggable` ist für Laravel 13 installiert. Es erzeugt URL-taugliche Kennungen aus Modellattributen und ergänzt bei Kollisionen einen Suffix, beispielsweise `laravel-api` und `laravel-api-2`. Konfiguration: `config/sluggable.php`. Es gibt keine eigene Oberfläche oder zusätzliche Paketroute.

Für ein künftiges Modell mit einer `title`- und `slug`-Spalte:

```php
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Sluggable;

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'title', 'maxLength' => 255]];
    }
}
```

Beim Speichern des Modells wird der Slug erzeugt. Die Datenbankspalte muss vorher per eigener Ressourcenmigration angelegt werden, etwa `$table->string('slug', 255)->unique()`. Bei bestehenden Datensätzen zunächst eine Backfill-Strategie festlegen. Der Unique-Index sichert auch parallele Schreibzugriffe ab; mögliche Constraint-Verletzungen müssen die jeweiligen Actions behandeln.

Standardmäßig bleiben vorhandene Slugs bei Titeländerungen erhalten (`onUpdate=false`); `unique=true` und `firstUniqueSuffix=2`. Quellfeld, maximale Länge und gegebenenfalls reservierte Namen pro Modell bestimmen. Eine Route kann später `{post:slug}` verwenden; der Slug ersetzt keine Autorisierungsprüfung.

Der Installationsassistent installiert und registriert das Paket bereits über seine Composer-/Paketschritte. Keine zusätzliche `.env`-Einstellung oder Paketmigration erforderlich. Noch kein Anwendungsmodell nutzt den Trait. Die benötigten `mbstring`-Funktionen werden hier durch den vorhandenen Symfony-Polyfill bereitgestellt; Windows-Nutzung ist geprüft. [Paketdokumentation](https://github.com/cviebrock/eloquent-sluggable).

## Migrationen aus vorhandenen Datenbanken erzeugen

Kitloong Laravel Migrations Generator 7.4.0 ist als Entwicklungsabhängigkeit installiert. Er erzeugt Laravel-Migrationen aus einem vorhandenen Datenbankschema einschließlich Indizes und Fremdschlüsseln. SQLite sowie MariaDB/MySQL werden unterstützt; Windows benötigt dafür keine Linux-Erweiterung, sondern den passenden PDO-Treiber.

```powershell
php artisan help migrate:generate
php artisan migrate:generate --connection=sqlite --tables=users --path=database/schema-export --skip-log
```

Für MariaDB/MySQL `--connection=mariadb` bzw. `--connection=mysql` verwenden und die entsprechende Verbindung vorher in Laravel konfigurieren. Ohne `--tables` werden alle berücksichtigten Tabellen exportiert. Zusammengehörige Tabellen für Fremdschlüssel gemeinsam auswählen.

Das Beispiel schreibt in einen separaten Ordner und verändert durch `--skip-log` die Migrationstabelle nicht. Ohne `--path` ist `database/migrations` das Ziel; vorhandene Projektmigrationen deshalb nicht ungeprüft durch weitere Tabellenanlagen ergänzen. Generierte Dateien vor Übernahme und Ausführung auf einer separaten Datenbank prüfen. SQLite-Fremdschlüssel in nachträglichen `ALTER TABLE`-Migrationen sind eingeschränkt; beim späteren Wechsel zu MariaDB auch Datentypen, Indizes und Constraints prüfen. Das Werkzeug exportiert keine Datensätze und überträgt keine Daten zwischen Datenbanken.

Der CLI- und Browser-Installationsassistent installiert und registriert das Paket über die bestehenden Composer-Schritte. Kein zusätzlicher Benutzer, keine Paketmigration und kein automatischer Schemaexport nötig. Bei `composer install --no-dev` steht der Befehl nicht zur Verfügung. Paketdefaults werden automatisch als `migrations-generator` geladen; keine eigene Konfigurationsdatei nötig. `migrate:generate` ist im Artisan Runner gesperrt, da es Quellcodedateien und ohne `--skip-log` auch den Migrationsstand verändern kann. [Paketdokumentation](https://github.com/kitloong/laravel-migrations-generator).

## IDE-Unterstützung für Laravel

Laravel IDE Helper 3.7.0 ist als Entwicklungsabhängigkeit installiert. Die generierten PHPDoc-Dateien verbessern die Vervollständigung für Facades, Eloquent-Attribute, Beziehungen und Builder; `ide-helper:meta` ergänzt PhpStorm-Containerinformationen. Keine eigene URL oder API-Funktion. Konfiguration: `config/ide-helper.php`.

Nach Einrichtung der lokalen Datenbank und Migrationen ausführen:

```powershell
php artisan ide-helper:generate --no-interaction
php artisan ide-helper:models --nowrite --no-interaction
php -d memory_limit=512M artisan ide-helper:meta --no-interaction
```

Die PhpStorm-Generierung benötigt hier mehr als die lokalen 128 MB PHP-Speicher. `-d memory_limit=512M` erhöht das Limit nur für diesen Aufruf.

Die Dateien `_ide_helper.php`, `_ide_helper_models.php` und `.phpstorm.meta.php` werden lokal erzeugt und von Git ignoriert. Die IDE muss sie indexieren. `--nowrite` lässt die Modelldateien unverändert; `--write`, `--write-mixin` und `--reset` nur bei bewusst gewünschter Änderung verwenden. Bei doppelten Klassendeklarationen in der IDE die Modell-Helper-Konfiguration bzw. den Index prüfen. Nach Schema-, Modell- oder Paketänderungen erneut generieren; Modellgenerierung braucht eine erreichbare, migrierte Datenbank. Drittanbieter-Beziehungen und Makros können zusätzliche Konfiguration benötigen.

CLI- und Browser-Setup installieren und registrieren IDE Helper bereits über ihre bestehenden Composer-/Paketschritte. Keine zusätzliche Paketmigration oder Benutzeranlage nötig. IDE-Dateien werden nach dem Setup bei Bedarf lokal mit den obigen Befehlen erzeugt; keine ungefragte Generierung im Assistenten oder bei Migrationen (`post_migrate=[]`). Mit `composer install --no-dev` ist das Werkzeug nicht verfügbar. Die Befehle sind im Artisan Runner gesperrt, da sie Dateien schreiben und je nach Optionen Modelle ändern können. [Paketdokumentation](https://github.com/barryvdh/laravel-ide-helper).

## API-Dokumentation mit Scramble

Scramble 0.13.47 dokumentiert die bestehenden API-Endpunkte einschließlich des Status unter `/` und der signierten E-Mail-Verifizierung. Aufruf unter derselben Basisadresse wie die Anwendung:

- `/docs/api`: interaktive Dokumentation.
- `/docs/api.json`: OpenAPI-Spezifikation als JSON.

Beide Routen sind standardmäßig ausschließlich in `APP_ENV=local` zugänglich; in anderen Umgebungen antworten sie mit 403. Filament und Artisan bleiben unter ihren eigenen Pfaden. Die Dokumentationsoberfläche lädt ihre Bibliothek vom CDN und benötigt dafür Internetzugang.

Konfiguration: `config/scramble.php`, Registrierung: `app/Providers/ScrambleServiceProvider.php`. API-Serveradressen folgen im Browser dem Aufrufhost; CLI-Exporte verwenden die konfigurierte `APP_URL`. Das dokumentierte Antwortschema enthält fünfstellige String-Enum-Codes, `status`, `data` und `responseStatus`. `app/Support/Documentation/ApiOperationTransformer.php` ergänzt Angaben, die Scramble durch Fortify-/Action-Delegation und zentrale Fehlerbehandlung nicht vollständig erkennt. Bei neuen Operationen oder geänderten Codes diese Ergänzung prüfen; andere Routen werden weiterhin automatisch analysiert. Passwörter/remember_token sind keine Antwortfelder.

Cookie-Anmeldung: zunächst `/sanctum/csrf-cookie` aufrufen, danach `/api/login` mit Cookies und dem URL-dekodierten XSRF-TOKEN als `X-XSRF-TOKEN` senden. In der UI müssen Cookies bereits vorhanden sein und bei Schreibzugriffen der CSRF-Header angegeben werden. Signierte Verifizierungslinks mitsamt `expires`/`signature` aus der E-Mail verwenden; kein beliebiger Link kann dort erzeugt werden. Die Dokumentation ersetzt keine Anmeldung oder Berechtigungsprüfung. CLI-/Browser-Setup installiert und registriert das Paket bereits; Abschlussübersichten enthalten die Dokumentationsadressen. Keine zusätzliche Migration oder Benutzeranlage nötig.

```powershell
php artisan scramble:export
php artisan scramble:cache
php artisan scramble:clear
```

Exportziel: `storage/app/private/openapi.json`, Git-ignoriert und nicht öffentlich abgelegt. Cache nach API-Änderungen leeren oder neu erzeugen. Bei per CLI erzeugtem Dokumentationscache stammen auch die gespeicherten Serveradressen aus `APP_URL`; diese vor dem Cachen auf die tatsächliche Basisadresse setzen. Scramble-Befehle sind im Artisan Runner gesperrt, weil sie Dateien beziehungsweise Cache schreiben können. Keine API-Platform-Installation oder Änderung der API-Laufzeit erforderlich. [Scramble-Dokumentation](https://scramble.dedoc.co/installation).

## API-Antwortformat

Eigene API-Actions verwenden den Trait `App\Traits\ApiResponse`. Jede Antwort enthält `code` (fünfstelliger String), `status` (Boolean), `data` und `responseStatus` (zusätzlich zum tatsächlichen HTTP-Status).

```php
return $this->success(UserResponseCode::LoginSuccessful, $data);
return $this->error(UserResponseCode::CredentialsError, null, 401);
```

`success()` setzt `status=true` und standardmäßig HTTP 200; `error()` setzt `status=false` und standardmäßig HTTP 400. Beide akzeptieren einen String-backed Enum-Code, optionale Daten und den HTTP-Status. Daten dürfen auch `null` sein.

Enums liegen in `app/Enums/Api/`. Die ersten zwei Ziffern identifizieren den Bereich, die letzten drei das Ereignis. `UserResponseCode`: `05001` für `LoginSuccessful`, `05002` für `CredentialsError`. `SystemResponseCode` verwendet `00` für allgemeinen Erfolg und HTTP-Fehler, z. B. `00404` für eine unbekannte Route. Codes stets als Strings behandeln.

```json
{"code":"00001","status":true,"data":{"status":"ok"},"responseStatus":200}
```

Die eigenen JSON-Endpunkte `/` und `/api/*` sowie deren zentral behandelte Fehler nutzen dieses Format. Validierungsfehler stehen in `data.errors`, Fehlermeldungen in `data.message`; HTTP-Header wie `Retry-After` bleiben erhalten. Serverfehler geben keine internen Exception-Details aus. Browser-/Filament-/Runner-Antworten, CSRF-Initialisierung und der Laravel-Healthcheck `/up` behalten ihr jeweiliges Protokoll.

## Controller und Actions

Eigene Controller enthalten ausschließlich die vom Router aufgerufenen Methoden. Jede Methode erhält ihre Action per typisierter Methodeninjektion und gibt deren `handle()`-Ergebnis zurück. Die Logik einschließlich Validierung und Antworterzeugung liegt in der Action.

Actions liegen unter `app/Actions/<Controllerbereich>/`. Der Bereich ist der Controllername ohne `Controller` im Plural, beispielsweise `UserController` → `Users`, `SessionController` → `Sessions`. Der Klassenname entspricht der Methode in PascalCase mit dem Zusatz `Action`: `login` → `LoginAction`, `store` → `StoreAction`.

```php
public function store(Request $request, StoreAction $action): RedirectResponse
{
    return $action->handle($request);
}
```

Die vorhandene Session-Anmeldung nutzt `app/Actions/Sessions/`. Konkrete Action-Klassen werden automatisch durch Laravels Container aufgelöst; kein manuelles `new` oder zusätzliches Binding nötig. Controller erhalten keine Hilfsmethoden mit Anwendungslogik. Für neue Controller dasselbe Muster verwenden.

## Konfiguration

Lokale Einstellungen stehen in `.env`, Standardwerte in `.env.example`. `.env` und `vendor/` sind vom Git-Tracking ausgeschlossen. Keine Zugangsdaten einchecken. Auf produktiven Systemen `APP_DEBUG=false` setzen.
