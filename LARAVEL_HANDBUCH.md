# Laravel: Einstellungen, Erweiterungen und Aufrufe

Stand: 11.10.2026. Diese Dokumentation beschreibt den geprüften lokalen Projektstand unter Windows. Konfiguration ist keine Zusage, dass jede optionale Integration schon genutzt wird.

## Vollständige Referenzen

- [Alle geladenen Laravel- und Paketkonfigurationen](docs/LARAVEL_KONFIGURATION.md): 29 Gruppen mit verschachtelten Einstellungen; Zugangsdaten und Schlüssel maskiert.
- [Alle installierten Composer-Pakete](docs/COMPOSER_PAKETE.md): 180 Pakete mit Version und Einordnung als direkte/transitive Laufzeit-/Entwicklungsabhängigkeit.
- [Arbeitsprotokoll und offene Aufgaben](PROJEKTDOKUMENTATION.md).
- [Kopia-Einrichtung](KOPIA_SETUP.md) und [Windows-Backup-Prüfung](BACKUP_PRUEFUNG.md).

Die Konfigurationsreferenz ist eine Momentaufnahme aus dem CLI-Bootstrap. Middleware kann Einstellungen pro Anfrage ergänzen. `.env` enthält lokale Werte; `.env.example` dokumentiert Vorgaben für neue Checkouts. Bei einem Konfigurationscache gelten dessen gespeicherte Werte. Maßgeblich bleiben die Quelldateien und die jeweilige Umgebung.

## Umgebung und Start

| Bestandteil | Lokaler Stand |
| --- | --- |
| Laravel | 13.35.0 |
| PHP | 8.5.6; Projektanforderung `^8.3` |
| Composer | 2.9.8 |
| Anwendung | Name `Laravel`, Umgebung `local`, Debug aktiv |
| Sprache / Zeitzone | `en`, Fallback `en`, Anwendung `UTC` |
| Frontend | `http://localhost:9200`, getrennte Anwendung |
| API für Cookie-Nutzung | `http://localhost:8000` |

Laravel verwendet `UTC` unabhängig von der Windows-Zeitzone. Der JSON-Root-Endpunkt nennt sich derzeit fest `LaravelAPI`, unabhängig von `APP_NAME`.

```powershell
composer install
php artisan migrate
php artisan serve --host=localhost --port=8000
```

Für einen neuen Checkout bevorzugt `composer setup` verwenden. Alternativ bietet `/setup.php` einen unabhängigen Browser-Assistenten, der auch ohne `vendor/` und `.env` startet. Er ist standardmäßig gesperrt und benötigt eine selbst angelegte `.setup-access`-Datei. Webserver, PHP-CLI und Prozessausführung müssen vorhanden sein; eigener Terminalzugang ist nicht erforderlich. Anleitung: [Setup](docs/SETUP.md#einrichtung-im-browser). Einen vorhandenen Anwendungsschlüssel nicht bei jedem Start erneuern.

Browser-Sprachen: English (Standard), Deutsch, Français, Italiano und Español. „Execute step“ beziehungsweise die übersetzte Schaltfläche genügt zur Ausführung; zusätzliche Bestätigungshaken entfallen. Status- und Fehlermeldungen folgen der Auswahl. Abschlusslinks verwenden Schema, Host und Port des aktuellen Aufrufs statt APP_URL. Die Sprachwahl verändert Laravel-APP_LOCALE nicht.

```powershell
php artisan config:clear     # lokale Konfigurationsänderungen übernehmen
php artisan route:list      # tatsächliche registrierte Routen anzeigen
php artisan list            # verfügbare Artisan-Befehle
php artisan migrate:status  # Migrationen kontrollieren
```

## Laravel-Grundeinstellungen

| Konfiguration | Aktuell wirksam / Verwendung |
| --- | --- |
| `config/app.php` | Name, Umgebung, Debug, URL, Sprache, UTC, Anwendungsschlüssel; Schlüssel bleibt lokal |
| `config/auth.php` | Standardguard `web`, Session-Authentifizierung, Eloquent-Provider mit `App\Models\User`; Passwortbroker `users` |
| `config/database.php` | SQLite in `database/database.sqlite`; MariaDB später geplant. Andere vorhandene Verbindungen sind noch keine eingerichteten Dienste |
| `config/session.php` | Datenbank-Sessions, 120 Minuten, Payload-Verschlüsselung aus, HttpOnly, Pfad `/`, hostgebundene Domain, SameSite `lax`; Secure ohne explizite lokale Vorgabe |
| `config/cache.php` | Standardstore `database`; Cache-Tabelle vorhanden |
| `config/queue.php` | Standardverbindung `database`; Jobs/Fehlerjobs in der Datenbank, Worker nicht automatisch gestartet |
| `config/filesystems.php` | Standarddisk `local` in `storage/app/private`; `public` in `storage/app/public`; S3-Konfiguration vorbereitet, kein Bucket eingerichtet |
| `config/mail.php` | Mailer `log`; keine echte Mailzustellung eingerichtet |
| `config/logging.php` | Stack mit Single-Dateilog, Level `debug`; typischer Logpfad `storage/logs/laravel.log` |
| `config/services.php` | Platzhalter für Postmark, Resend, AWS SES und Slack; keine Socialite-Anbieter konfiguriert |
| Framework-Broadcasting | Verbindung `log`, keine fachlichen Channels konfiguriert |
| Wartungsmodus | Treiber `file`; `php artisan down` / `php artisan up` nur bewusst verwenden |

Die vollständigen Werte, auch für nicht aktive Treiber, stehen in der Konfigurationsreferenz. Passwortbroker-Vorgaben: Ablauf 60 Minuten, Throttle 60 Sekunden, Passwortbestätigung 10.800 Sekunden. Diese Vorgaben aktivieren keine Passwort-Reset-API.

MariaDB-Wechsel: lokal `DB_CONNECTION=mariadb`, Host, Port, Datenbank und Zugangsdaten setzen, Konfiguration leeren und Migrationen gezielt auf die neue Datenbank anwenden. Vorhandene SQLite-Daten werden dadurch nicht übertragen. Redis-/Memcached-Konfigurationswerte allein bedeuten nicht, dass entsprechende Server oder PHP-Erweiterungen verfügbar sind.

Queue bei Bedarf starten:

```powershell
php artisan queue:work
```

Keine automatisch eingerichtete Windows-Aufgabenplanung. `routes/console.php` enthält aktuell den Beispielbefehl `inspire`, keinen fachlichen Sicherungs- oder Bereinigungsplan.

## URLs und Zugangsrechte

Alle Pfade beziehen sich auf die API-Basisadresse `http://localhost:8000`.

| Methode und Pfad | Zweck | Voraussetzung |
| --- | --- | --- |
| `GET /` | JSON-Anwendungsstatus | öffentlich |
| `GET /api/status` | JSON-API-Status | öffentlich |
| `GET /up` | Laravel-Healthcheck | öffentlich, eigenes Healthcheck-Format |
| `GET /sanctum/csrf-cookie` | Session/CSRF initialisieren | Frontend-Credentials; HTTP 204 |
| `POST /api/login` | API-Anmeldung | freigegebene Stateful-Origin, CSRF, E-Mail/Passwort |
| `GET /api/user` | Aktueller Benutzer | `auth:sanctum` |
| `POST /api/logout` | Session beenden | `auth:sanctum`, CSRF |
| `GET /artisan/login`, `POST /artisan/login` | Runner-Anmeldung | Gast, POST mit CSRF/Throttle |
| `GET /artisan/account` | Konto-/Rechtehinweise | angemeldet |
| `GET /artisan` | Runner-Oberfläche | Paket aktiv, erlaubte Umgebung, `run-artisan-runner-ui` |
| `POST /artisan/run` | Artisan ausführen | gleiche Rechte, CSRF, Befehl zugelassen |
| `POST /artisan/logout` | Runner-Session beenden | angemeldet, CSRF |
| `GET /filament`, `GET /filament/login` | Filament-Dashboard bzw. Login | Dashboard: `access-admin-panel` |
| `POST /filament/logout` | Filament abmelden | Panel-Session, CSRF |
| `/telescope` | Debug-Dashboard | ausschließlich `local`, Telescope aktiv; lokal ohne Login |
| `/_laravel-brain` | Architekturgraph | ausschließlich `local`, nach Scan |

Filament-Downloads liegen ebenfalls unter `/filament`. Paketassets und Livewire verwenden teilweise gemeinsame technische Routen; diese sind keine Runner-Routen. Alte Panelpfade `/admin`, `/artisan-runner-ui`, `/login`, `/logout`, `/runner` sind entfernt.

## Installierte Laufzeiterweiterungen

| Paket / Version | Einrichtung und Aufruf |
| --- | --- |
| `codesquirrel/artisan-runner-ui` 1.0.1 | `/artisan`; `config/artisan-runner-ui.php`. Lokal aktiv, in `.env.example` aus. Erlaubte Umgebungen: local/qa/staging/uat |
| `filament/filament` 5.10.0 | `/filament`; `app/Providers/Filament/AdminPanelProvider.php`. Panel-ID `admin`, Guard `web`, Dashboard; keine fachlichen Resources |
| `laravel/fortify` 1.41.0 | Kein eigenes Dashboard. API-Login über Fortify-Pipeline; `config/fortify.php` und eigener Provider/Responses |
| `laravel/sanctum` 4.3.3 | CSRF-Cookie und Session-Guard der API; `config/sanctum.php`, `statefulApi()` aktiv; keine Token-Erzeugung |
| `laravel/socialite` 5.31.0 | Verwendung später über `Socialite::driver(...)`; noch kein Anbieter, keine OAuth-Routen |
| `laravel/tinker` 3.0.2 | `php artisan tinker`; interaktive PHP-Konsole; in Runner gesperrt |
| `spatie/laravel-medialibrary` 11.23.9 | `config/media-library.php`, Tabelle `media` vorhanden; keine eigene Oberfläche, noch kein Modell angebunden |
| `spatie/laravel-permission` 8.3.0 | `HasRoles` im User-Modell; `config/permission.php`; Rollen/Rechte über Modellmethoden |

Laravel Framework selbst ist das Laufzeit-Basispaket. Versionsvorgaben stehen in `composer.json`, konkrete Versionen in `composer.lock`. Für Permission besteht noch die unbeschränkte Vorgabe `*`; Composer warnt darüber.

### Runner und Benutzerrechte

```powershell
php artisan runner:create-user admin@example.com --name="Administrator"
php artisan make:filament-user
php artisan admin:grant-access admin@example.com
php artisan permission:cache-reset
```

E-Mail/Name sind Beispiele. `runner:create-user` erstellt ein neues Konto mit verdeckter Passwortbestätigung (mindestens 12 Zeichen) und nur Runner-Recht. Filament-Kontoanlage allein vergibt noch keine Admin-Berechtigung; `admin:grant-access` erteilt sie einem bestehenden Konto. Keine öffentliche Registrierung, keine automatisch erstellten Zugangskonten. Admin- und Runner-Rechte sind getrennt.

Runner-Konfiguration: `allowed=[]` erlaubt grundsätzlich alle Befehle außerhalb von `denied`. Gesperrt sind aktuell:

```text
tinker, serve, down, up, db:wipe, migrate:fresh, migrate:reset,
migrate:refresh, migrate:rollback, key:generate, env:*, queue:work,
queue:listen, schedule:work, schedule:run, vendor:publish,
package:discover, runner:create-user, admin:grant-access, make:filament-user, setup:*
```

Permission verwendet Guard `web`, Standardcache mit 24 Stunden Ablauf; Teams, Wildcard-Berechtigungen und Passport-Client-Credentials sind aus. Middleware-Aliase: `role`, `permission`, `role_or_permission`; einzelne Rechte über `can`. Methoden wie `givePermissionTo`, `assignRole`, `revokePermissionTo` halten den Paketcache aktuell.

### Media Library und Socialite

Media Library: Disk `public`, maximale Datei 10 MB, Bildtreiber GD, Konvertierungen standardmäßig über Queue und nach Datenbank-Commit. Ein Modell muss `HasMedia` implementieren und `InteractsWithMedia` verwenden; dann etwa `$model->addMedia($file)->toMediaCollection('images')` und `$model->getMedia('images')` nutzen. Modell/Collections, Uploadvalidierung und Zugriffsregeln stehen noch aus.

```powershell
php artisan storage:link       # nur für gewünschte öffentliche Dateiverfügbarkeit
php artisan filament:upgrade  # mitgelieferte Panelassets veröffentlichen
```

GD/EXIF sind vorhanden. PDF-/SVG-/Video-Vorschaubilder benötigen weitere Pakete/Programme; nicht eingerichtet. S3 ist nicht eingerichtet. Konfiguration enthält optionale Optimierer und FFmpeg-Pfade; deren Vorhandensein ist kein Installationsnachweis. Beim Backup sowohl Dateien als auch Media-Datenbankeinträge sichern.

Socialite benötigt später Anbieter-Credentials und Callback-URL in `config/services.php`; auf Benutzerwunsch derzeit ohne Anbieter. Es erteilt keine Runner-/Admin-Rechte und erzeugt keine API-Tokens.

## CORS, Sanctum und Fortify

| Einstellung | Wert |
| --- | --- |
| CORS-Pfade | `api/*`, `sanctum/csrf-cookie` |
| Origin | `http://localhost:9200` |
| Cookies | `supports_credentials=true` |
| Methoden / Header | jeweils `*`; keine Wildcard-Origin |
| Exponierte Header / Max-Age | keine / 0 |
| Stateful-Hosts | `localhost:9200` ohne Schema |
| Auth-Guard | `web` |
| Fortify-Views / Zusatzfeatures | aus / leere Featureliste |

Umgebungsvariablen: `CORS_ALLOWED_ORIGINS` enthält vollständige URLs, `SANCTUM_STATEFUL_DOMAINS` nur Host/Port. Lokal Frontend und API beide mit `localhost` verwenden. `/` und Browserpanels sind nicht in den CORS-Pfaden.

Frontend-Ablauf: CSRF-Cookie holen → Login → Benutzer abrufen → Logout. Axios braucht `withCredentials: true`, `withXSRFToken: true`, `Accept: application/json`. Bei fetch `credentials: 'include'` und bei POST den URL-dekodierten `XSRF-TOKEN` als `X-XSRF-TOKEN` mitsenden. Beispiel in [README](README.md#api-anmeldung-mit-sanctum).

Fortify registriert wegen `ignoreRoutes()` keine eigenen Routen. Login verwendet `AttemptToAuthenticate` und `PrepareAuthenticatedSession`; eigene Responses erhalten das API-Format. Login begrenzt auf 20 Versuche pro Minute/IP und 5 pro Minute/E-Mail-IP-Kombination. Optionales `remember` ist ein Boolean. Alle Panels und API teilen den Session-Guard: Logout beendet die gemeinsame Browser-Session.

Deaktiviert: Registrierung, Reset, Profil-/Passwortänderung, E-Mail-Verifikation, Zwei-Faktor-Anmeldung, Passkeys. Die transitive Installation von `laravel/passkeys` bedeutet keine aktivierte Passkey-Anmeldung. Später passende Actions/Routen, Responses und gegebenenfalls Migrationen ergänzen.

## Controller und API-Antworten

Controller enthalten nur Router-Methoden und injizieren ihre Actions in die Methodensignatur. Actions: `app/Actions/Users/`, `Sessions/`, `Systems/`, Dateiname `<Methodenname>Action.php`, Aufruf `handle()`.

```php
return $this->success(UserResponseCode::LoginSuccessful, $data);
return $this->error(UserResponseCode::CredentialsError, null, 401);
```

Trait: `app/Traits/ApiResponse.php`; Enums: `app/Enums/Api/`. Antwortfelder: `code` als fünfstelliger String, `status` als Boolean, `data`, `responseStatus` zusätzlich zum HTTP-Status. Erfolg standardmäßig 200, Fehler standardmäßig 400.

| Code | Bedeutung |
| --- | --- |
| `00001` | Allgemeiner Erfolg |
| `05001` / `05002` | Login erfolgreich / Zugangsdaten falsch |
| `05003` / `05004` | Logout erfolgreich / aktueller Benutzer |
| `00400`, `00401`, `00403`, `00404`, `00405` | Bad Request, fehlende Anmeldung, verboten, nicht gefunden, falsche Methode |
| `00419`, `00422`, `00429` | CSRF, Validierung, zu viele Anfragen |
| `00500`, `00999` | Serverfehler, sonstiger Requestfehler |

Zentrale Fehlerbehandlung für `/` und `/api/*`; Feldfehler in `data.errors`, Meldung in `data.message`. Interne 500-Details werden nicht ausgeliefert. Browserprotokolle, Preflight und CSRF-Initialisierung verwenden ihr jeweiliges Format.

## Entwicklungswerkzeuge

| Paket / Version | Aufruf und Status |
| --- | --- |
| Telescope 5.25.0 | `/telescope`; nur local; `TELESCOPE_ENABLED`; Speicherung in SQLite; `php artisan telescope:prune --hours=48` löscht ältere Aufzeichnungen |
| Laravel Brain 2.7.1 | `php artisan brain:scan --no-interaction`, dann `/_laravel-brain`; nur local; Graphen in `storage/app/laravel-brain/`, Git ignoriert |
| Laravel Pail 1.2.7 | `php artisan pail`; installiert, benötigt `pcntl` und deshalb nativ hier unter Windows nicht nutzbar. Alternative: `Get-Content storage/logs/laravel.log -Wait -Tail 50` |
| Laravel Pao 1.1.5 | Optimiert Test-/Toolausgabe automatisch für Agenten; kein Dashboard. Normale Ausgabe bei Bedarf mit `$env:PAO_DISABLE='true'` |
| Laravel Pint 1.32.1 | `php vendor/bin/pint --test`; formatieren mit `php vendor/bin/pint` |
| PHPUnit 12.5.38 | `php artisan test` oder `php vendor/phpunit/phpunit/phpunit` |
| Collision 8.9.5 | Konsolenfehler-/Testausgabe automatisch; kein eigener Webaufruf |
| Mockery 1.6.15 | PHP-Testdoubles; innerhalb von Tests, kein Dashboard |
| FakerPHP 1.24.1 | Testdaten/Factories, z. B. `User::factory()`; keine öffentliche Schnittstelle |

Telescope maskiert Passwörter, Authorization-, Cookie- und CSRF-Header. Automatische Bereinigung noch offen. Brain-Konfiguration wird aus Paketdefaults geladen; keine eigene `config/laravel-brain.php`. Kein MCP-Zusatzpaket eingerichtet, keine Stress-Tests durchgeführt. Brain kann Regeldateien generieren; das vorhandene `AGENTS.md` gemäß Nutzerregel nicht überschreiben.

Weitere wichtige transitive Pakete: Livewire 4.4.7 (Filament), Filament-Komponenten, Laravel Passkeys 0.2.1 (Fortify), Laravel Stress 1.0.3 (Brain), Laravel Sentinel (Framework), Guzzle/OAuth/JWT-Bibliotheken (Socialite), Spatie Image/Optimizer (Media Library). Sie werden durch PHP-Autoload beziehungsweise ihr Hauptpaket aufgerufen; es gibt nicht für jede Bibliothek eine eigene URL. Alle Versionen im Paketverzeichnis.

Tests nutzen SQLite im Arbeitsspeicher, Array-Sessions/-Cache, synchrone Queue; Telescope/Runner standardmäßig aus. Zuletzt bestanden: 53 Tests mit 253 Assertions. Für reine Dokumentation wurden diese nicht erneut ausgeführt.

## Frontend-Vorlage und Composer-Automatisierung

`package.json` enthält Vite `^8.0.0`, Tailwind `^4.0.0`, Laravel Vite Plugin `^3.1`, concurrently `^10.0.3`, optional Multiplex `^0.4.1`. Keine `package-lock.json` vorhanden; dies sind Versionsvorgaben, keine bestätigten installierten Versionen. API und mitgeliefertes Filament-Standardtheme benötigen keinen eigenen Build.

```powershell
npm install --ignore-scripts  # nur bei Nutzung der Frontend-Vorlage
npm run dev                  # Vite-Entwicklungsserver
npm run build                # Vite-Build
```

Composer-Hooks: Autoload-Dump registriert Pakete und veröffentlicht Filament-Assets; Update publiziert Laravel-Assets. `composer test` leert Konfiguration und führt Tests aus. `composer setup` startet die bestätigungspflichtige, plattformübergreifende Einrichtung: Abhängigkeiten, sichere `.env`, Schlüssel, Datenbankwahl, CORS, Migrationen, Paketassets und optionales Admin-Konto. Vorhandene Schlüssel und Kontodaten bleiben erhalten; npm ist nicht erforderlich. Anleitung: [Setup](docs/SETUP.md). `composer dev` ruft `artisan dev` der Vorlage auf; für das reine API-Projekt bewusst `artisan serve` und bei Bedarf Queue separat starten.

Die optimierte Autoload-Erzeugung war unter Windows mehrfach langsam. Arbeitsprotokoll enthält den verwendeten temporären Workaround; `optimize-autoloader=true` bleibt Projektvorgabe. Telescope ist vom Auto-Discovery ausgeschlossen und wird nur lokal über den AppServiceProvider registriert.

## Kopia und Wiederaufnahme

KopiaUI 0.23.1 ist eine Windows-Anwendung außerhalb von Composer. Start über Windows-Startmenü; CLI:

```powershell
& "$env:LOCALAPPDATA\Programs\KopiaUI\resources\server\kopia.exe" --version
.\Resume-Sitzung.ps1
```

Kopia-Dateisicherung/-Restore getestet; echtes Backupziel, Intervall, Aufbewahrung und konsistenter SQLite-Snapshot beziehungsweise später MariaDB-Dump fehlen noch. Keine automatische Projektsicherung aktiv. Das PowerShell-Skript nimmt die gespeicherte Codex-Sitzung auf, startet keinen Laravel-Dienst; `-Auswahl`, `-SessionId` und `-WhatIf` verfügbar.

## Dokumentation aktuell halten

```powershell
php scripts/Export-LaravelConfiguration.php
composer show
composer show --direct
php artisan route:list
```

Nach Paket-/Konfigurationsänderungen Handbuch und Paketbestand aktualisieren. Keine `.env`, Schlüssel oder Passwörter in Dokumentation kopieren. Vor Veröffentlichung der automatisch erzeugten Referenz die Maskierung neuer projektspezifischer Credential-Felder prüfen. Für Produktion Domains/HTTPS, Debug, Mail, Backup und Bereinigungs-/Queue-Betrieb gesondert festlegen.
