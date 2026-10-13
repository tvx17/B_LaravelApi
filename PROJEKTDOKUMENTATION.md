# Projektdokumentation und Arbeitsprotokoll

Stand: 13.10.2026. Diese Datei hält Arbeitsschritte, Entscheidungen, Prüfergebnisse und offene Aufgaben fest. Die bisherigen Schritte wurden aus dem Gespräch und den Projektdateien rekonstruiert. Die README enthält die kurze Anleitung zur Nutzung.

## Dokumentation künftiger Arbeiten

Bei jeder weiteren Aufgabe diese Datei mitführen:

1. Auftrag, Fragen und bestätigte Entscheidungen notieren.
2. Durchgeführte Schritte mit betroffenen Dateien und relevanten Befehlen ergänzen.
3. Fehler, deren Behebung und verbleibende Einschränkungen festhalten.
4. Prüfungen mit Ergebnis dokumentieren; ausgeführte und lediglich vorgeschlagene Befehle unterscheiden.
5. Offene Aufgaben aktualisieren und erledigte Punkte abhaken.

Keine Passwörter, Schlüssel, Tokens oder vollständigen Inhalte der lokalen `.env` dokumentieren. Frühere Einträge erhalten; Änderungen von Entscheidungen im nächsten Eintrag erklären.

## Bestätigte Entscheidungen und aktueller Stand

| Thema | Stand |
| --- | --- |
| Ziel | Laravel-API als Basis; fachlicher Umfang wird später spezifiziert |
| Controller-Architektur | Router-Methoden delegieren an per Methodensignatur injizierte Actions unter `app/Actions/<Controllerbereich>/` |
| Framework | Laravel 13.35.0 laut `composer.lock`; PHP-Anforderung ab 8.3 |
| Lokale Umgebung | Windows/PowerShell; bei Einrichtung PHP 8.5.6 und Composer 2.9.8 |
| Datenbank | Aktuell SQLite, später MariaDB |
| Berechtigungen | Spatie Laravel Permission 8.3.0, Guard `web` |
| Artisan-Oberfläche | CodeSquirrel Artisan Runner UI 1.0.1 |
| Debugging | Laravel Telescope 5.25.0 als Entwicklungsabhängigkeit; ausschließlich lokal |
| Anmeldung | Browser-Anmeldung mit Sessions für die Runner UI |
| Runner-Zugriff | Berechtigung `run-artisan-runner-ui` |
| API-Anmeldung | Sanctum 4.3.3 mit Cookies/Sessions für localhost:9200; Login/Logout und aktueller Benutzer |
| Authentifizierungsbackend | Fortify 1.41.0 für die API-Login-Pipeline; eigene Routen und Enum-Responses |
| Registrierung | Keine öffentliche Registrierung |
| OAuth | Laravel Socialite 5.31.0 als Grundlage; Anbieter und Login-Ablauf noch offen |
| Medien | Spatie Media Library 11.23.9; Modellanbindung noch offen |
| Admin-Panel | Filament 5.10.0 mit Livewire 4.4.7 unter `/filament`; Zugriff mit `access-admin-panel` |

## Arbeitsprotokoll: 07.10.2026

### 1. Contributor Guide erstellt

- Vor dem Schreiben geprüft, ob `AGENTS.md` vorhanden war: Datei fehlte, Verzeichnis war leer.
- `AGENTS.md` mit dem Titel „Repository Guidelines“ angelegt; mangels Anwendung waren Laravel-Konventionen als zukünftige Orientierung beschrieben.
- Die Datei bei allen folgenden Arbeiten unverändert gelassen, entsprechend der ursprünglichen Vorgabe, sie nicht zu überschreiben oder zu ändern.
- Hinweis: Die Aussagen zum leeren Projekt sind inzwischen veraltet. Die aktuellen Informationen stehen hier und in `README.md`.

### 2. Laravel-Basis aufgebaut

- PHP, Composer und benötigte Erweiterungen geprüft; die Laravel-13-Dokumentation zur Kompatibilität konsultiert.
- Datenbank und Anmeldungsumfang erfragt. Datenbankwahl wurde später bestätigt: zunächst SQLite, anschließend MariaDB.
- Mit `composer create-project laravel/laravel .laravel-scaffold --prefer-dist --no-interaction --no-scripts` die Vorlage erstellt.
- Der erste Download scheiterte an der Netzwerkbeschränkung. Installation mit erweitertem Netzwerkzugriff erfolgreich wiederholt.
- Vorlagendateien in das Projektverzeichnis verschoben, vorhandene `AGENTS.md` erhalten und das temporäre Gerüst entfernt.
- Mit `php artisan package:discover` Pakete registriert; `.env` aus `.env.example` erstellt und `php artisan key:generate` ausgeführt.
- `routes/api.php`, Routing in `bootstrap/app.php` und JSON-Fehlerausgabe für API-Routen eingerichtet.
- `GET /`, `GET /api/status` und Laravels Healthcheck `GET /up` bereitgestellt. Die Status-Endpunkte brauchen keine Datenbank.
- `tests/Feature/ApiTest.php` ergänzt und README erstellt.
- Anfangs fehlender Anwendungsschlüssel durch die Schlüsselerzeugung behoben. Einen Fehler beim Entfernen einzelner Session-Middleware durch die vollständige Nutzung der zustandslosen API-Middleware für die Root-Route behoben.
- Abschlussprüfung dieser Phase: vier Tests, zehn Assertions erfolgreich; Formatprüfung erfolgreich.

### 3. Spatie Laravel Permission integriert

- Kompatibilität anhand der Spatie-Dokumentation geprüft und `composer require spatie/laravel-permission --no-interaction` ausgeführt.
- `HasRoles` in `app/Models/User.php` ergänzt.
- In `bootstrap/app.php` die Aliase `permission`, `role` und `role_or_permission` registriert.
- Composer brach beim optimierten Autoloader ab. Mit `composer dump-autoload --no-scripts --no-interaction` behoben; anschließend Paketregistrierung wiederholt.
- Konfiguration und Migration mit `php artisan vendor:publish --provider='Spatie\Permission\PermissionServiceProvider' --no-interaction` veröffentlicht.
- `database/database.sqlite` erstellt und `php artisan migrate --no-interaction` ausgeführt: Benutzer-, Session-, Cache-, Job- und Berechtigungstabellen eingerichtet.
- Konfiguration: `config/permission.php`; Paketmigration: `database/migrations/2026_10_07_043956_create_permission_tables.php`.
- Tests für Rollenvergabe, Rechteentzug und erlaubte/verweigerte Zugriffe ergänzt; keine fachlichen Rollen angelegt.
- Abschlussprüfung: sechs Tests, 18 Assertions erfolgreich; Formatprüfung erfolgreich.

### 4. Artisan Runner UI installiert

- Angefordertes Paket `codesquirrel/artisan-runner-ui` identifiziert und Anforderungen geprüft.
- Paket über Composer installiert; Versionsvorgabe auf `~1.0` gesetzt, dabei auf 1.0.1 aktualisiert.
- Service Provider automatisch registriert und `php artisan vendor:publish --tag=artisan-runner-ui-config --no-interaction` ausgeführt.
- `config/artisan-runner-ui.php` und Umgebungsvariablen in `.env.example` ergänzt.
- UI zunächst deaktiviert gelassen, solange keine Anmeldung vorhanden war.
- Tests für deaktivierte Routen, Authentifizierung, Befehlsausführung, gesperrte Befehle und Umgebungsschutz ergänzt.
- Composer-Konfiguration validiert. Hinweis auf die ungebundene Spatie-Versionsvorgabe `*` festgestellt; weiterhin offen.
- Abschlussprüfung: zehn Tests, 30 Assertions erfolgreich; Formatprüfung erfolgreich.

### 5. Browser-Anmeldung und Runner-Zugriffsschutz ergänzt

- Nach Zustimmung zur Anmeldung Browser-Anmeldung umgesetzt; eine zusätzliche Sanctum-Anmeldung ist bislang nicht beauftragt.
- `app/Http/Controllers/Auth/SessionController.php` für Login und Logout angelegt; Browser-Routen mit `web`-Middleware eingerichtet.
- Login unter `/login`, Konto unter `/runner`, Abmeldung über `POST /logout` bereitgestellt.
- Blade-Ansichten unter `resources/views/auth/` angelegt. API-Root bleibt zustandslos.
- Erfolgreicher Login erneuert die Session-ID; Logout invalidiert die Session und erneuert den CSRF-Token.
- In `AppServiceProvider` Anmeldebegrenzung eingerichtet: fünf Anfragen pro Minute je E-Mail/IP-Kombination und 20 je IP. Ungültige Eingabetypen werden ohne Serverfehler validiert.
- Runner-Middleware auf `web`, `auth:web`, `can:run-artisan-runner-ui` gesetzt. Recht gilt für Oberfläche und Befehlsausführung.
- `app/Console/Commands/CreateRunnerUser.php` angelegt: interaktive, verdeckte Passworteingabe mit Bestätigung, mindestens zwölf Zeichen, Hashing, transaktionale Benutzeranlage und direkte Rechtevergabe. Vorhandene Benutzer werden nicht geändert.
- `runner:create-user` in der Runner UI gesperrt.
- Paketansicht veröffentlicht und Kontolink ergänzt: `resources/views/vendor/artisan-runner-ui/index.blade.php`.
- Lokal `ARTISAN_RUNNER_UI_ENABLED=true` gesetzt und Konfigurationscache geleert. `.env.example` bleibt standardmäßig deaktiviert; Tests erzwingen ebenfalls deaktivierte Grundkonfiguration.
- Tests für Login, Logout, Rate Limit, Validierung, CSRF, Rechteentzug und Benutzeranlage ergänzt.
- Letzte ausgeführte Codeprüfung: **24 Tests, 92 Assertions erfolgreich**, Pint erfolgreich. Routen inklusive Middleware geprüft. Ein Browser-Test wurde nicht durchgeführt.
- Im Rahmen dieser Arbeiten keinen echten Zugangsbenutzer und kein Standardpasswort angelegt.

### 6. Dokumentation zentralisiert

- Auf Wunsch eine zentrale Datei für Arbeitsschritte und wichtige offene Informationen angelegt.
- README, Composer-Dateien, Konfiguration, Tests, Migrationen und Benutzeranlage gelesen; Paketversionen aus `composer.lock` abgeglichen.
- Dieses Protokoll erstellt und aus `README.md` verlinkt.
- Dokumentation auf Vollständigkeit und Verlinkung geprüft. Keine erneuten Anwendungstests ausgeführt, da dieser Schritt ausschließlich Dokumentation verändert.

### 7. Laravel Telescope hinzugefügt

- Auftrag: Laravel Telescope ergänzen und alle Schritte dokumentieren.
- Projektkonfiguration, Service Provider, Abhängigkeiten und Migrationen geprüft; offizielle Laravel-13-Telescope-Dokumentation zur Installation gelesen.
- Lokale Nutzung als Ausgangspunkt vorgeschlagen und nach einem möglichen QA-/Staging-Bedarf gefragt. Bestehende SQLite-Datenbank soll genutzt werden.
- Nächste Schritte: Paket installieren, Konfiguration/Assets/Migration veröffentlichen, lokale Providerregistrierung einrichten, migrieren und prüfen.
- `composer require laravel/telescope --dev --no-interaction --no-scripts` gestartet. Composer hat Telescope 5.25.0 und Laravel Sentinel 1.1.0 ausgewählt; Autoloader-Erzeugung läuft noch.
- Lokale, durch `class_exists` abgesicherte Registrierung in `AppServiceProvider` vorbereitet. Die Absicherung ermöglicht später Installationen ohne Entwicklungsabhängigkeiten.
- `TELESCOPE_ENABLED` und `TELESCOPE_PATH` in `.env.example` dokumentiert.
- Composer-Installation abgeschlossen. `php artisan package:discover --ansi` und `php artisan telescope:install --no-interaction` ausgeführt; Konfiguration und Tabellenmigration veröffentlicht.
- Den durch die Installation hinzugefügten globalen Provider aus `bootstrap/providers.php` wieder entfernt; Telescope-Autodiscovery in `composer.json` ausgeschaltet. Registrierung erfolgt ausschließlich lokal über `AppServiceProvider`.
- Eigenen `TelescopeServiceProvider` mit lokaler Aufzeichnung und Maskierung von Passwörtern, Cookies, Authorization- und CSRF-Werten angelegt. Der Gate-Zugriff außerhalb von `local` wird verweigert.
- Datenbank-Fallback in `config/telescope.php` an SQLite angepasst; eine spätere konfigurierte MariaDB-Verbindung wird automatisch übernommen.
- `composer update --lock --no-install --no-scripts --no-interaction` ausgeführt, um die Lockdatei nach der Autodiscovery-Konfiguration abzugleichen. Keine weiteren Paketversionen geändert; Composer meldete keine Sicherheitswarnungen zu Abhängigkeiten.
- Pakete erneut registriert, Konfigurationscache geleert und `php artisan migrate --no-interaction` ausgeführt. Migration `2026_10_07_051957_create_telescope_entries_table.php` erfolgreich angewendet.
- Dashboard-Routen geprüft und Composer-Konfiguration validiert. Die bereits bekannte Warnung zur Spatie-Versionsvorgabe bleibt offen.
- Die aktuelle Paketversion lädt ihre gebündelten CSS-/JS-Assets direkt; kein eigener Frontend-Build oder separates Kopieren von Dashboard-Assets erforderlich.
- README um Nutzung, lokale Erreichbarkeit und manuelles Pruning ergänzt. Vier Integrationstests für Umgebungsgrenzen, Dashboard und maskierte Request-Aufzeichnung hinzugefügt; vollständiger Testlauf gestartet.
- Erster Testlauf: 27 Tests erfolgreich, ein Dashboard-Test scheiterte an einer Textannahme. Das Dashboard lieferte HTTP 200; sein Titel verteilt „Laravel“ und „Telescope“ auf HTML-Elemente. Prüfung auf die tatsächlich gerenderte Dashboard-Ansicht korrigiert.
- Abschlussprüfung: **28 Tests, 106 Assertions erfolgreich**. Die Tests bestätigen verfügbare lokale Dashboard-Ansicht, fehlende Registrierung in Testing/Produktion sowie gespeicherte und maskierte Request-Daten.
- `php vendor/bin/pint --test` erfolgreich; `php artisan migrate:status --no-interaction` bestätigt alle fünf Migrationen als ausgeführt, einschließlich Telescope.
- Lokal ist `/telescope` gemäß Telescope-Standard ohne Anmeldung erreichbar. Eine automatische Bereinigung und ein Zugang für QA/Staging wurden nicht eingerichtet. Die zuvor gestellte Frage zum QA-/Staging-Bedarf blieb unbeantwortet; die angekündigte lokale Variante wurde umgesetzt.
- README und dieses Protokoll aktualisiert. Bisherige Arbeitsschritte und offene Aufgaben bleiben erhalten.

### 8. Rückbau der entfernten Queue-Monitoring-Integration und Backup-Prüfung

- Auftrag: Nach manuellem Rückbau verbliebene Dateien, lokale Konfiguration und Paketreste entfernen; anschließend Spatie Laravel Backup prüfen.
- Composer-Dateien, Providerregistrierung, Runner-Konfiguration, Kontoansicht, Scheduler, Tests und Dokumentation überprüft. Die manuellen Änderungen hatten die registrierten Integrationen bereits zurückgesetzt; unversionierte Provider-, Befehls-, Konfigurations-, Test- und Deployment-Dateien waren noch vorhanden.
- Diese fünf Restdateien gelöscht. Lokale Umgebungsvariable und abgeleitete Paket-/Test-Caches werden ebenfalls bereinigt; Windows-Installationsausnahmen sind in der README bereits entfernt.
- Spatie Laravel Backup vor einer Installation geprüft: [offizielle Anforderungen](https://spatie.be/docs/laravel-backup/v10/requirements) schließen Windows-Server ausdrücklich aus. PHP/ZIP passen grundsätzlich, die Plattform jedoch nicht.
- Benutzer vorab informiert und gefragt, ob eine Windows-kompatible Alternative geprüft oder das Paket nur für späteren Linux-Betrieb integriert werden soll. Bis zur Entscheidung keine Installation und keine Plattformausnahmen vorgenommen.
- Künftig bei neuen Produkten vor Installation/Betrieb die Plattformvoraussetzungen prüfen und fehlende Windows-Unterstützung vorab mitteilen.
- `composer install --no-interaction --no-scripts` gegen die bereits zurückgesetzte Lockdatei ausgeführt: drei veraltete Pakete aus `vendor/` entfernt. Keine neue Abhängigkeit installiert.
- Lokalen Aktivierungsschalter entfernt und Redis-Client auf den ursprünglichen Vorlagenwert zurückgesetzt. Leeren Deployment-Ordner und alten PHPUnit-Ergebniscache entfernt.
- Generierte Provider-/Konfigurations-/Routencaches entfernt, Paketregistrierung neu aufgebaut, kompilierte Blade-Ansichten und Berechtigungscache geleert.
- Berechtigungsprüfung zunächst über Tinker versucht; wegen fehlender Schreibberechtigung für dessen History abgebrochen. Anschließend direkte PHP-Abfrage verwendet: keine obsolete Zugriffsberechtigung vorhanden, keine Datenbankdaten gelöscht.
- Abschlussprüfung: **28 Tests, 106 Assertions erfolgreich**; Pint erfolgreich. Composer-Plattformprüfung vollständig erfolgreich, ohne Ausnahmen; Konfiguration gültig mit der bekannten Spatie-Versionswarnung.
- Suche in aktiven Projektdateien, lokaler Umgebung und generierten Provider-Caches ergab keine Referenzen auf die entfernte Integration. Die zugehörigen Vendor-Paketverzeichnisse sind nicht mehr vorhanden.
- Backup-Installation bleibt bis zur Klärung der Zielplattform offen. Kein Backup erstellt und keine automatische Sicherung eingerichtet.

### 9. Windows-taugliche Backup-Alternativen geprüft

- Auftrag: Windows-Unterstützung und Alternativen prüfen; keine Installation beauftragt.
- Offizielle Anforderungen von Spatie, SQLite-Dokumentation, MariaDB-Dump-Dokumentation sowie Restic- und Kopia-Herstellerinformationen gelesen. Spatie bleibt wegen fehlender Windows-Unterstützung ausgeschlossen.
- Lokale PHP-/ZIP-/SQLite-Voraussetzungen und vorhandene Backup-Werkzeuge geprüft. Ein erster Probeaufruf über die separate `SQLite3`-Klasse scheiterte, weil die Erweiterung fehlt; über PDO erfolgreich geprüft: SQLite 3.51.3, ZIP vorhanden.
- MariaDB-Dump-Programme unter `C:/Apps/mariadb/bin/` gefunden und `mariadb-dump.exe --version` aufgerufen. Keine echte MariaDB-Sicherung durchgeführt.
- Temporäre SQLite-Testdatenbank über PDO und `VACUUM INTO` gesichert; Sicherung wieder geöffnet und auf Datensatz sowie Integrität geprüft. Ergebnis: `restored_rows=1`, `integrity=ok`. Temporäre Datei entfernt; echte Projektdatenbank unverändert.
- Ergebnis in `BACKUP_PRUEFUNG.md` dokumentiert und aus der README verlinkt. Empfehlung: kleiner eigener Artisan-Befehl für Datenbank-Snapshot/Dump und Dateisicherung; optional Restic/Kopia für verschlüsselte externe Speicherung. Eigenentwicklung ist vorgeschlagen, nicht umgesetzt.
- Restic und Kopia sind laut Hersteller Windows-tauglich. Keine lokale Installation/Produkttests durchgeführt; Datenbanksicherungen müssen vor einer gewöhnlichen Dateisicherung konsistent erzeugt werden.
- Kein Backup-Paket installiert, keine reale Sicherung erstellt, keine Zeitplanung eingerichtet. Anwendungstests nicht erneut ausgeführt, da nur Recherche und Dokumentation geändert wurden; die gezielte SQLite-Laufzeitprüfung war erfolgreich.

### 10. Kopia unter Windows installiert und geprüft

- Auf Benutzerwunsch Kopia gewählt; offizielle Windows-Unterstützung und Installationsweg geprüft.
- KopiaUI 0.23.1 per `winget install --id Kopia.KopiaUI --exact --source winget --scope user --silent --accept-package-agreements --accept-source-agreements --disable-interactivity` installiert; winget bestätigte den Installer-Hash und erfolgreiche Installation.
- Mitgelieferte CLI unter `%LOCALAPPDATA%/Programs/KopiaUI/resources/server/kopia.exe` mit `--version` geprüft. Kein Laravel-Composer-Paket hinzugefügt.
- Isoliertes verschlüsseltes Test-Repository erstellt, eine Testdatei gesichert und in einen neuen Testordner wiederhergestellt. SHA256-Prüfsummen identisch; Testdaten, Repository, Cache und Konfiguration anschließend entfernt. Kein echtes Backup und keine Änderung der Anwendungsdatenbank.
- `KOPIA_SETUP.md` angelegt, `BACKUP_PRUEFUNG.md` und README aktualisiert. Anwendungs-Code unverändert; deshalb keine erneuten Laravel-Tests erforderlich.
- Zielpfad beziehungsweise Anbieter und Umfang per Rückfrage angefordert; Antworten stehen aus. Echtes Repository, SQLite-Vorbereitung, Zeitplanung, Aufbewahrung und vollständiger Restore-Test bleiben offen. Passwort ausschließlich lokal in KopiaUI eingeben.

### 11. Laravel Socialite hinzugefügt

- Offizielle Socialite-Dokumentation und Paket-Kompatibilität mit Laravel 13 geprüft. Installation mit `composer require laravel/socialite --no-interaction`; `composer.json` und `composer.lock` aktualisiert.
- Socialite 5.31.0 und vier neue Abhängigkeiten installiert: `firebase/php-jwt`, `league/oauth1-client`, `paragonie/constant_time_encoding` und `phpseclib/phpseclib`. Keine bestehenden Pakete aktualisiert oder entfernt.
- Rückfrage nach zunächst reiner Installation oder gewünschtem Login-Anbieter gestellt. Ohne Auswahl keine Anbieter-Zugangsdaten, Routen oder Benutzerverknüpfung eingerichtet. README um Einrichtungshinweise ergänzt.
- Erster Composer-Lauf brach ohne Diagnose bei der Autoload-Erzeugung ab; `composer dump-autoload --no-interaction -v` stellte Autoload und Paketregistrierung erfolgreich fertig. Eine zwischenzeitliche Containerprüfung vor Abschluss schlug wegen fehlender Autoload-Einträge fehl; nach Abschluss löst der Container korrekt `Laravel\Socialite\SocialiteManager` auf.
- Versionsbereich abschließend auf `^5.31` begrenzt. Ein Versuch ohne Netzwerkfreigabe scheiterte an Packagist und wurde von Composer zurückgerollt; Wiederholung mit freigegebenem Netzwerk erfolgreich. Wegen Weitergabe des Caret-Zeichens durch `composer.bat` abschließend direkt `php C:/Apps/php/composer.phar require 'laravel/socialite:^5.31' --no-interaction` verwendet. Composer-Skripte und Paketregistrierung erfolgreich; keine Sicherheitswarnungen gefunden.
- Prüfungen: `composer check-platform-reqs` vollständig erfolgreich unter Windows; `php artisan test`: 28 Tests, 106 Assertions bestanden. Socialite-Factory zusätzlich direkt im Laravel-Container aufgelöst. Kein echter OAuth-Aufruf geprüft, da Anbieter und Zugangsdaten fehlen.
- Offen: Anbieter auswählen, OAuth-App und Callback-URL konfigurieren, Browser-/API-Ablauf sowie Kontoverknüpfung entscheiden und anschließend Redirect/Callback einschließlich Fehlerfällen testen. Keine automatische Runner-Berechtigung vergeben.

### 12. Spatie Media Library hinzugefügt

- Benutzer bestätigt: Socialite vorerst ohne Anbieter belassen. Keine OAuth-Konfiguration ergänzt.
- Offizielle Media-Library-Anforderungen und Laravel-13-Kompatibilität geprüft. Lokale PHP-Erweiterungen EXIF, GD und Imagick vorhanden; Composer-Plattformprüfung erfolgreich. Die Basiseinrichtung benötigt kein Linux-only-Produkt. Zusätzliche Programme für PDF/SVG/Video und Bildoptimierung wurden nicht installiert oder geprüft.
- `php C:/Apps/php/composer.phar require 'spatie/laravel-medialibrary:^11.0' --no-interaction` ausgeführt; Version 11.23.9 und fünf Abhängigkeiten ausgewählt, keine bestehenden Pakete aktualisiert oder entfernt. Composer-Manifeste aktualisiert.
- Rückfrage zur reinen Grundlage oder direkten User-Anbindung gestellt; Modellanbindung noch offen.
- README um Speicherung, Modellanbindung, Queue, Windows-Zusatzwerkzeuge und Kopia-Umfang ergänzt.
- Die optimierte Autoload-Erzeugung blieb mehrere Minuten ohne Ausgabe; auch ein Versuch ohne Xdebug brachte keinen Abschluss. Eigene laufende Composer-Prozesse abgebrochen. Für einen erfolgreichen `dump-autoload` wurde `optimize-autoloader` vorübergehend deaktiviert und `composer.json` anschließend vollständig wiederhergestellt; die Projektvorgabe bleibt `true`. Die Ursache ist nicht abschließend geklärt, künftige optimierte Composer-Läufe bei Bedarf prüfen.
- Ein vor Abschluss der Paketregistrierung ausgeführter Publish-Versuch fand keine Ressourcen. Nach erfolgreicher Registrierung `php artisan vendor:publish --provider='Spatie\MediaLibrary\MediaLibraryServiceProvider' --tag=medialibrary-migrations --tag=medialibrary-config` erneut ausgeführt: `config/media-library.php` und `database/migrations/2026_10_07_082753_create_media_table.php` angelegt.
- `php artisan migrate --no-interaction` erfolgreich: `media`-Tabelle in der lokalen SQLite-Datenbank erstellt. Keine Bestandsdaten gelöscht. `MEDIA_DISK=public` und `IMAGE_DRIVER=gd` als Paketstandard in `.env.example` dokumentiert; lokale `.env` unverändert. Kein Storage-Link erstellt.
- Prüfung: `composer check-platform-reqs` vollständig erfolgreich; `composer validate --no-check-publish` gültig, nur vorhandene Warnung zum unbegrenzten Spatie-Permission-Versionsbereich. `php artisan test`: 28 Tests, 106 Assertions bestanden. `php vendor/bin/pint --test` bestanden. Migrationstatus bestätigt; Tabelle, konfigurierte Disk/Bildtreiber und Media-Modell zusätzlich über Laravel-Bootstrap geprüft.
- Offen: fachliches Modell, Collections, Zugriffsregeln, Dateitypen und Upload-API festlegen. Dateiablage und Konvertierungen mit dem gewählten Modell testen; derzeit kein realer Datei-Upload oder Konvertierungstest. Bei Kopia Dateien der Media-Disk zusammen mit der Datenbank berücksichtigen.

### 13. Filament hinzugefügt

- Offizielle Filament-5-Installation und Panel-Autorisierung geprüft. Composer löst Filament 5.10.0 und Livewire 4.4.7 mit Laravel 13 auf; keine bestehenden Pakete aktualisiert oder entfernt. `composer check-platform-reqs` bestätigt alle Anforderungen auf Windows, einschließlich Intl und ZIP.
- `php C:/Apps/php/composer.phar require 'filament/filament:^5.0' --no-interaction` ausgeführt. Bekannte langsame optimierte Autoload-Erzeugung abgebrochen; anschließend Autoload wie in Schritt 12 vorübergehend ohne Optimierung erzeugt, ursprüngliche `composer.json` wiederhergestellt. Projektvorgabe `optimize-autoloader=true` bleibt erhalten.
- `php artisan filament:install --panels --no-interaction` erfolgreich: `app/Providers/Filament/AdminPanelProvider.php` erzeugt und in `bootstrap/providers.php` registriert; Assets veröffentlicht, Cache geleert, Asset-Ausschlüsse in `.gitignore` und `filament:upgrade` im Composer-Hook ergänzt.
- Admin-Panel unter `/admin` mit Login, Dashboard und Guard `web`. `User` implementiert `FilamentUser`; Panel-Zugang verlangt explizit die Spatie-Berechtigung `access-admin-panel`, auch lokal. Keine fachlichen Ressourcen, kein Standardkonto und keine öffentliche Registrierung hinzugefügt.
- `admin:grant-access EMAIL` ergänzt: erteilt einem bestehenden Benutzer die Berechtigung, legt bei unbekannter E-Mail kein Konto an. Konto bei Bedarf lokal mit `make:filament-user` anlegen. Beide Befehle in der Runner UI gesperrt; Runner-Recht allein erlaubt keinen Admin-Zugang.
- README um Nutzung, Freischaltung, Asset-Veröffentlichung und Rechteentzug ergänzt. Plattformabhängige Zusatzprodukte wurden nicht installiert; Standardpanel nutzt mitgelieferte Assets ohne eigenen Frontend-Build.
- Feature-Tests für Gastzugriff/Loginseite, verweigerten Runner-Benutzer, freigeschalteten Admin und unbekannten Benutzer ergänzt; zusätzlich Runner-Sperre für Admin-Freischaltung geprüft. Vollständige Suite: 32 Tests, 116 Assertions bestanden. Danach Filament-Tests (4 Tests, 10 Assertions) und Runner-Tests nach der Sperrlistenänderung (7 Tests, 22 Assertions) gezielt erfolgreich geprüft.
- Pint meldete zunächst die vom Installer erzeugte Formatierung in `bootstrap/providers.php`; mit `php vendor/bin/pint bootstrap/providers.php` korrigiert. Abschließendes `php vendor/bin/pint --test` bestanden. `composer validate --no-check-publish` erfolgreich mit der bestehenden Spatie-Permission-Warnung; `php artisan route:list --path=admin` bestätigt Dashboard, Login und Logout. Browser-Interaktion wurde noch nicht geprüft.
- Offen: Betreiberkonto anlegen/freischalten, Browser-Anmeldung prüfen; fachliche Ressourcen und Policies erst nach Spezifikation erstellen. Socialite bleibt ohne Anbieter, Media Library ohne Modellanbindung.

### 14. PowerShell-Skript zum Wiederaufnehmen der Sitzung erstellt

- OpenAI-Docs-Skill verwendet, offizielle Codex-Befehlsreferenz geöffnet und installierten Aufruf über `codex resume --help` geprüft. Die Hilfe meldete eingeschränkten Zugriff auf Codex-Temporärverzeichnisse innerhalb der Sandbox; der Hilfeaufruf selbst war erfolgreich.
- Aktuelle Sitzungs-ID gezielt aus `CODEX_THREAD_ID`/`CODEX_SESSION_ID` ermittelt; keine Zugangsdaten oder vollständigen Umgebungsvariablen ausgegeben.
- `Resume-Sitzung.ps1` im Projektstamm angelegt: startet standardmäßig genau diese Sitzung, unterstützt `-SessionId`, `-Auswahl` und `-WhatIf`. Projektpfad wird aus dem Skriptstandort ermittelt, Codex-Verfügbarkeit geprüft und das ursprüngliche Arbeitsverzeichnis nach Ende wiederhergestellt. Keine Rechte-, Modell- oder Sandbox-Vorgaben überschrieben.
- README um Verwendung und Voraussetzung eines erhaltenen lokalen Sitzungsverlaufs ergänzt. Keine Laravel-Dienste gestartet.
- Prüfungen: PowerShell-Parser ohne Syntaxfehler; Standardaufruf und `-Auswahl` mit `-WhatIf` erfolgreich. Die interaktive Wiederaufnahme wurde nicht ausgeführt, um keine zweite laufende Sitzung zu starten. Laravel-Code unverändert, deshalb keine erneute Laravel-Testsuite nötig.

### 15. Controller-Logik in Actions ausgelagert

- Benutzerkonvention übernommen: eigene Controller enthalten nur Router-Methoden; Actions werden in der Methodensignatur injiziert und über `handle()` aufgerufen. Ablage unter `app/Actions/<Controllerbereich>/<Methodenname>Action.php`; Controllerbereich ohne `Controller` im Plural, z. B. `Users` oder `Sessions`.
- Controller und Routen geprüft: einziger eigener konkreter Controller ist `Auth/SessionController`. Seine Methoden `create`, `store`, `destroy` delegieren nun an `CreateAction`, `StoreAction`, `DestroyAction` in `app/Actions/Sessions/`.
- Validierung, Authentifizierung, Session-Erneuerung, Logout und Antworten unverändert in die Actions übertragen. Vorhandene Routen und Middleware beibehalten; Paketcontroller nicht verändert. Kein Container-Binding erforderlich.
- Konvention in README dokumentiert; vorhandenes `AGENTS.md` entsprechend der ursprünglichen Vorgabe nicht geändert.
- Prüfungen: `php artisan test` vollständig bestanden (32 Tests, 117 Assertions); `php vendor/bin/pint --test` bestanden; `git diff --check` ohne Fehler. Bestehende HTTP-Tests prüfen die Action-Auflösung sowie bisheriges Login-/Logout-Verhalten über die unveränderten Routen.

### 16. Einheitliche API-Antworten mit Trait und Enums eingerichtet

- Bestätigt: HTTP-Status zusätzlich als `responseStatus` im JSON; `success()` und `error()` setzen den Boolean automatisch. Antwortstruktur: `code`, `status`, `data`, `responseStatus`; Code als fünfstelliger String zur Erhaltung führender Nullen.
- `app/Traits/ApiResponse.php` erstellt: typisierter Backed-Enum-Code, optionale Daten, Standardstatus 200 bei Erfolg beziehungsweise 400 bei Fehler. Fünfstelligen String-Enum-Wert prüfen.
- Enums unter `app/Enums/Api/`: `UserResponseCode` mit `05001`/`05002`; allgemeiner Nummernkreis `00` in `SystemResponseCode` für Status und zentrale Fehler. User-Nummernkreis vorbereitet, keine neue Login-API umgesetzt.
- Status-Endpunkte von Closures auf `SystemController` mit methodeninjizierten `IndexAction`/`StatusAction` unter `app/Actions/Systems/` umgestellt. Actions erzeugen Antworten über den Trait.
- `ApiExceptionResponse` und Exception-Response-Hook in `bootstrap/app.php` ergänzt: Fehler für `/` und `/api/*` erhalten das gemeinsame Format; Validierungsdetails und relevante HTTP-Header bleiben erhalten. Serverfehler werden auch bei Debug ohne interne Details ausgegeben. Browser- und Paketprotokolle sowie `/up` unverändert.
- API-Tests auf neue Struktur angepasst und um führende Nullen, automatische Statuswerte, Root-Antwort, Validierung, 401, 429-Header und vertrauliche 500-Details ergänzt. Gezielt 8 Tests mit 37 Assertions bestanden. Vollständige Suite: 38 Tests, 146 Assertions bestanden; `php vendor/bin/pint --test` und `git diff --check` erfolgreich.
- README um Format, Enum-Verzeichnis, Codes, Aufrufbeispiele und Geltungsbereich ergänzt. Fachliche Nummernkreise und weitere Ereigniscodes bei späterem Ausbau festlegen.

### 17. Filament- und Runner-Routen getrennt organisiert

- Benutzerwunsch: Filament unter `/filament`, Artisan Runner UI unter `/artisan`, mit zugehörigen Panel-/Runner-Routen unter dem jeweiligen Präfix.
- `AdminPanelProvider` auf Pfad `filament` umgestellt. Panel-ID `admin` und Routennamen bleiben erhalten, damit die vorhandene Autorisierung weiter greift.
- Runner-Paketpfad fest auf `artisan` gesetzt; veraltetes `ARTISAN_RUNNER_UI_PATH` aus `.env.example` und lokaler `.env` entfernt. Die lokale Aktivierung unverändert gelassen, keine Zugangsdaten ausgegeben.
- Eigene Session-Routen nach `/artisan/login`, `/artisan/account`, `/artisan/logout` verschoben. Paketoberfläche `/artisan`, Ausführung `/artisan/run`. Benannte Routen und Links lösen automatisch die neuen Pfade auf. CLI-Hinweise und README aktualisiert.
- `php artisan config:clear` und `php artisan route:clear` ausgeführt. Bestehende Authentifizierung, CSRF-Schutz und getrennte Spatie-Berechtigungen beibehalten.
- Session-, Filament- und Runner-Tests auf neue URLs umgestellt; zusätzlichen Test für eindeutige Präfixe und nicht mehr erreichbare alte URLs ergänzt. Vollständige Suite: 39 Tests, 159 Assertions bestanden. `php vendor/bin/pint --test` und `git diff --check` erfolgreich. Routenlisten bestätigen sechs Runner-Routen unter `artisan` sowie Filament-Panel-/Download-Routen unter `filament` ohne Überschneidung.
- Laravel-/Livewire-Infrastruktur und statische Paketassets behalten ihre paketweiten Pfade; diese sind keine eigenen Panel- oder Runner-Fachrouten. Keine Paketdateien verändert.

### 18. Vorhandene Media-Library-Installation erneut geprüft

- Auf Benutzerwunsch Installation geprüft: `composer show spatie/laravel-medialibrary` bestätigt Version 11.23.9 in `vendor/`.
- `config/media-library.php` vorhanden. `php artisan migrate:status` bestätigt ausgeführte Media-Migration; Laravel-Bootstrap bestätigt registrierten `MediaLibraryServiceProvider` und vorhandene Tabelle `media`.
- Keine erneute Installation oder Migration erforderlich; keine Abhängigkeiten oder Anwendungsdaten verändert. Fachliche Modellanbindung bleibt wie bisher offen.

### 19. Aktuelle CORS-Handhabung geprüft

- Keine eigene `config/cors.php` vorhanden; Laravel lädt seine Framework-Standardkonfiguration. Globale `HandleCors`-Middleware ist aktiv und nicht durch die Anwendung entfernt.
- Wirksame Werte über Laravel-Bootstrap geprüft: Pfade `api/*` und `sanctum/csrf-cookie`, Origins/Methoden/Header jeweils `*`, `supports_credentials=false`, `exposed_headers=[]`, `max_age=0`. Sanctum-Pfad ist nur ein Standardwert, keine Aussage über eine installierte Sanctum-Anmeldung.
- OPTIONS-Preflight für `/api/status` mit Origin `http://localhost:5173` lokal über den HTTP-Kernel geprüft: HTTP 204, `Access-Control-Allow-Origin: *`, angefragte Methode GET und Header Authorization/Content-Type erlaubt; kein Allow-Credentials-Header.
- `/`, `/filament` und `/artisan` sind nicht in den CORS-Pfaden enthalten. Keine Konfiguration geändert. Bei gewünschter Anpassung `php artisan config:publish cors` ausführen und konkrete Frontend-Origins sowie Authentifizierungsart festlegen.

### 20. CORS für localhost:9200 mit Cookies eingerichtet

- Frontend-Origin als `http://localhost:9200` interpretiert; Benutzer bestätigt Cookies/Sessions. Laravel-Sanctum-Dokumentation für Cookie-/SPA-Voraussetzungen geprüft.
- `config/cors.php` angelegt: explizite Origin, `supports_credentials=true`, Pfade `api/*` und vorbereitend `sanctum/csrf-cookie`; Methoden/Header wie bisher erlaubt. `CORS_ALLOWED_ORIGINS` als optionale kommagetrennte Konfiguration in `.env.example` dokumentiert; Default greift auch lokal ohne `.env`-Änderung.
- Konfigurationscache geleert. Cookie-Domain und SameSite nicht geändert: hostgebundene Cookies und `lax` passen zu HTTP-Frontend/API auf `localhost` mit unterschiedlichen Ports. Frontend muss Credentials aktivieren; für spätere mutierende Session-Anfragen zusätzlich CSRF-Ablauf erforderlich.
- CORS-Tests für erlaubten Preflight, Credentials, normale Antwort, andere Origin und ausgeschlossene Panels ergänzt. Erster Test erwartete bei fremder Origin keinen Allow-Origin-Header; die Bibliothek setzt bei einer einzelnen freigegebenen Origin diesen festen Wert unabhängig von der Anfrage. Browser verweigern fremde Origins wegen der fehlenden Übereinstimmung. Test entsprechend auf festen erlaubten Wert korrigiert. Gezielt 4 Tests, 21 Assertions bestanden; Pint bestanden.
- Keine Sanctum- oder API-Anmeldung vorhanden. Rückfrage zur anschließenden Sanctum-/Login-/Logout-Einrichtung gestellt; noch keine Antwort. CORS-Pfad erstellt keine CSRF-Route. README dokumentiert aktuellen Umfang und Frontend-Voraussetzungen.
- Abschließende vollständige Testsuite: 43 Tests, 180 Assertions bestanden. `git diff --check` erfolgreich. Keine Browser-Anmeldung getestet, da SPA-Authentifizierung noch nicht eingerichtet.

### 21. Sanctum und API-Session-Anmeldung eingerichtet

- Benutzer autorisiert Sanctum mit CSRF-Initialisierung und API-Login/Logout für das Frontend auf `http://localhost:9200`.
- `laravel/sanctum:^4.0` als reguläre Composer-Abhängigkeit ergänzt; gezieltes Update installiert 4.3.3 ohne bestehende Paketupdates. Plattformanforderungen erfüllt; Composer meldet keine Sicherheitsadvisories. Installationsupdate bewusst ohne Autoload/Skripte; anschließender Autoload wie zuvor temporär ohne Optimierung erzeugt, Manifest wiederhergestellt und alle Composer-Hooks einschließlich Filament-Assets erfolgreich ausgeführt.
- Sanctum-Konfiguration veröffentlicht, Standard-Stateful-Domain gezielt auf `localhost:9200` begrenzt und in `.env.example` dokumentiert. `statefulApi()` aktiviert, Konfigurationscache geleert; wirksame Domain und Guard `web` geprüft. Keine Token-Migration, kein HasApiTokens-Trait und keine Token-Erzeugung für den reinen Session-Betrieb.
- `UserController` delegiert per Methodeninjektion an `app/Actions/Users/LoginAction.php`, `LogoutAction.php`, `MeAction.php`. Routen: POST `/api/login` mit Login-Throttle; GET `/api/user` und POST `/api/logout` mit `auth:sanctum`. Sanctum stellt GET `/sanctum/csrf-cookie` bereit.
- Login validiert Eingaben, verlangt eine Stateful-Session, authentifiziert über `web` und erneuert die Session-ID. Logout invalidiert Session und CSRF-Token. Rückgaben über API-Trait; keine neuen Konten oder Admin-/Runner-Rechte. Codes `05003` für Logout und `05004` für aktuellen Benutzer ergänzt, `00419` für CSRF-Fehler.
- Gezielt 8 Authentifizierungstests mit 58 Assertions bestanden: CSRF-Cookies/CORS, Login/User/Logout und Session-Erneuerung, keine Passwortfelder, falsche Zugangsdaten, Validierung, fremde Origin, CSRF-Verweigerung und gültiger Token, Login-Throttle. README mit Frontend-Ablauf, Axios-/fetch-Hinweisen und gemeinsamem Session-Guard aktualisiert.
- Bestehende Panelpfade und Rechte unverändert. API-Logout beendet die gemeinsame `web`-Session auch für die Browser-Panels. Echte Frontend-/Browser-Interaktion noch nicht geprüft; Tests verwenden isolierte SQLite-Datenbank.
- Abschließende Prüfungen: vollständige Suite mit 51 Tests und 238 Assertions bestanden; Pint und `git diff --check` erfolgreich. Composer-Plattformprüfung und Manifestvalidierung erfolgreich, nur bestehende Warnung zu Spatie Permission `*`.

## Arbeitsprotokoll: 08.10.2026

### 22. Laravel Fortify integriert

- Offizielle Fortify-Dokumentation und vorhandene Authentifizierung geprüft. Fortify als Backend in vorhandene API integrieren; Sanctum bleibt für Stateful-Cookies/CSRF zuständig. Keine neue Registrierung oder andere zuvor nicht spezifizierte Authentifizierungsfeatures aktiviert.
- `php C:/Apps/php/composer.phar require 'laravel/fortify:^1.0' --no-interaction --no-update` und gezieltes Update mit `--no-scripts --no-autoloader` ausgeführt. Fortify 1.41.0 mit 18 weiteren Abhängigkeiten installiert, keine bestehenden Pakete aktualisiert oder entfernt. Composer meldet keine Sicherheitsadvisories; Plattformanforderungen unter Windows erfüllt.
- Autoload wie zuvor temporär ohne Optimierung erzeugt, originales Manifest wiederhergestellt. Paketregistrierung und Composer-Hooks einschließlich Filament-Asset-Veröffentlichung erfolgreich; Cache dadurch geleert.
- Eigene minimale `config/fortify.php` und registrierten `FortifyServiceProvider` ergänzt. `Fortify::ignoreRoutes()` verhindert Überschneidungen; Views und optionale Features deaktiviert. `fortify:install` bewusst nicht ausgeführt, da dessen Standardaktionen/-migrationen zu den nicht aktivierten Features gehören.
- `LoginAction` delegiert an Fortifys `AttemptToAuthenticate` und `PrepareAuthenticatedSession`. Bestehende Request-Validierung/Stateful-Prüfung und Route-Throttle erhalten; fehlgeschlagene Authentifizierung weiterhin `05002`/401, Eingabevalidierung weiterhin `00422`/422. Optionales `remember` als Boolean validiert.
- Eigene Fortify-Login-/Logout-Response-Verträge gebunden; Trait und Enum-Antwortformat beibehalten. Logout invalidiert weiterhin Session/CSRF; Controller enthalten ausschließlich delegierende Router-Methoden. Panel-Anmeldungen und Pfade unverändert.
- Keine neuen Migrationen oder Änderungen an Bestandskonten. Fortify-/Passkeys-Abhängigkeiten sind installiert, ihre optionalen Features und Routen jedoch deaktiviert. Erweiterungen benötigen später explizite eigene Routen/Actions und gegebenenfalls Schemaänderungen.
- README um Rollen von Sanctum/Fortify, Konfiguration, optionale Features und Migrationseinschränkungen ergänzt. Authentifizierungstests um Fortify-Callback-Nachweis und fehlende Zusatz-/Konfliktrouten erweitert: gezielt 10 Tests mit 73 Assertions bestanden. Vollständige Suite: 53 Tests mit 253 Assertions bestanden; Pint und `git diff --check` erfolgreich. Manifestvalidierung erfolgreich mit bestehender Spatie-Permission-Warnung. Browser-/Frontend-Interaktion nicht zusätzlich geprüft.

### 23. Laravel Brain installiert und unter Windows geprüft

- Angefragtes Paket als `laramint/laravel-brain` identifiziert; offizielle Projektbeschreibung nennt Laravel 13 und die Entwicklungsinstallation. Composer-Anforderungen geprüft und lokal erfüllt; keine Linux-only-Erweiterung erforderlich.
- Als Entwicklungsabhängigkeit mit `^2.4` ergänzt; gezieltes Update ohne Skripte/Autoload installiert 2.7.1 sowie `laramint/laravel-stress` 1.0.3. Keine bestehenden Pakete aktualisiert oder entfernt; keine Sicherheitsadvisories gemeldet.
- Autoload vorübergehend ohne Optimierung erzeugt, ursprüngliches Manifest wiederhergestellt, Paketregistrierung und Composer-Hooks erfolgreich. Erstversuch von `brain:scan` vor abgeschlossener Registrierung fand noch keine Befehle; nach Abschluss erfolgreich wiederholt.
- `php artisan brain:scan` unter Windows erfolgreich: 46 Knoten, 47 Verbindungen, 11 Tabs. Dateitreiber nutzt `storage/app/laravel-brain/`; Ausgabe in `.gitignore` aufgenommen. Keine Brain-Datenbankmigration notwendig. Automatische Analysehinweise sind keine separat geprüften Sicherheitsbefunde.
- Paketcode geprüft: Brain-Routen und -Befehle nur in `local`. Routenliste bestätigt zehn Brain-Routen unter `/_laravel-brain`, unabhängig von `/filament` und `/artisan`. Kein MCP-Paket ergänzt, keine Lasttests ausgeführt und keine KI-Regeldateien erzeugt; `AGENTS.md` unverändert.
- README um Scan, Oberfläche und Grenzen ergänzt. Oberfläche über Laravel-HTTP-Kernel mit HTTP 200 und HTML-Content-Type geprüft; keine interaktive Browserprüfung. Vollständige Suite: 53 Tests, 253 Assertions bestanden. Composer-Plattformprüfung, Pint und `git diff --check` erfolgreich; Manifestvalidierung mit bestehender Spatie-Permission-Warnung.

### 24. Vollständige Einstellungs- und Erweiterungsdokumentation erstellt

- Lokalen Stand aus Composer-Manifests/Installationsbestand, Konfigurationsdateien, Laravel-Bootstrap, Providern, Routen und vorhandenen Dokumenten erfasst. Keine komplette `.env` oder Zugangsdaten ausgegeben.
- `LARAVEL_HANDBUCH.md` erstellt: Basiswerte, Konfigurationsbereiche, URLs/HTTP-Methoden, Zugangsrechte, sämtliche direkten Composer-Pakete mit Version/Aufruf, wichtige transitive Integrationen, API-Konvention, Frontend-/Composer-Skripte und Kopia. Aktive Funktionen gegenüber vorbereiteten Konfigurationen und offenen Aufgaben abgegrenzt.
- `docs/COMPOSER_PAKETE.md` aus dem tatsächlichen `vendor/composer/installed.json` erzeugt: 180 installierte Pakete mit Version und direkter/transitiver Laufzeit-/Entwicklungseinordnung. npm-Vorgaben getrennt dokumentiert; ohne Lockdatei keine behaupteten konkreten Installationsversionen.
- `scripts/Export-LaravelConfiguration.php` ergänzt und ausgeführt: alle 29 geladenen Konfigurationsgruppen als `docs/LARAVEL_KONFIGURATION.md` dokumentiert, einschließlich Paketdefaults. Credential-/Schlüsselfelder vorsorglich maskiert; keine `.env`-Kopie. URLs im Detailinventar ebenfalls maskiert; öffentliche Aufrufadressen stehen im Handbuch.
- Pail-Einschränkung anhand des installierten PCNTL-Guards dokumentiert; native PowerShell-Loganzeige als Alternative genannt. Keine Zusatzprodukte installiert oder Anwendungseinstellungen geändert. README auf die neuen Referenzen verlinkt; AGENTS.md unverändert.
- Abschlussprüfungen: Export erneut erfolgreich (29 Gruppen), PHP-Syntax und Pint bestanden; lokale Dokumentationslinks und Maskierung vorhandener Credential-Werte ohne Ausgabe der Werte geprüft. Transitive Entwicklungseinordnung anhand von Composers `dev-package-names` verifiziert/korrigiert. `git diff --check` erfolgreich. Keine erneute Anwendungstestsuite erforderlich, da Anwendungscode unverändert.

### 25. Interaktiven Einrichtungsassistenten für GitHub-Checkouts umgesetzt (11.10.2026)

- Anforderungen bestätigt: primär Windows, außerdem Linux/macOS; SQLite oder MariaDB/MySQL, vorhandene/neue Datenbank; sichere `.env`-Vorlage, API-/Frontend-Adressen, optionaler gemeinsamer Administrator und Bestätigung jedes Schritts.
- `composer setup` ersetzt den bisherigen ungefragten Vorlagenablauf einschließlich npm. `scripts/Setup.php` funktioniert auch ohne `vendor/` und `.env`; alternativ direkt mit PHP starten. Composer-Zeitlimit für den interaktiven Ablauf deaktiviert. Installation verwendet das Lockfile mit Entwicklungspaketen, ohne automatische Laravel-Hooks; Paketregistrierung und Filament-Assets folgen als bestätigter Schritt.
- `.env.example` bleibt frei von lokalen Geheimnissen; Runner-Standard auf den aktuellen lokalen Wert `true` angepasst. `scripts/SetupEnvironment.php` schreibt nur ausgewählte Schlüssel, erhält andere Werte/Kommentare und behandelt Sonderzeichen ohne Variableninterpolation. Bestehender APP_KEY wird nicht erneuert.
- Datenbankwahl, vorhandene/neue Datei beziehungsweise Serverdatenbank, verdeckte Passwortabfrage, Verbindungsprüfung und separate Bestätigung der Speicherung ergänzt. Kein Überschreiben bestehender SQLite-Dateien und kein Datenbank-Reset. Neue Serverdatenbanken benötigen CREATE-Rechte. Ziel vor Migration anzeigen; eine bereits bestätigte Datenbankanlage bleibt bei späterer Ablehnung bestehen.
- CORS-/Session-Konfiguration für API und Frontend auf gleichem Host/Schema; anschließend einzeln bestätigte Migrationen, Paketassets, Administrator, Runner-Aktivierung, Storage-Link und Brain-Scan. Abschließend URLs und Serveraufruf ausgeben; Server, Queues und Kopia nicht automatisch starten.
- `setup:admin` ergänzt: neue Konten erst nach Anlage- und Rechtebestätigung erstellen; vorhandene Namen/Passwörter erhalten. Beide Web-Guard-Rechte gemeinsam vergeben. `setup:*` in der Runner UI gesperrt.
- README, Handbuch und `docs/SETUP.md` aktualisiert; Konfigurationsreferenz neu exportiert (29 Gruppen). Bestehende AGENTS.md unverändert.
- Prüfung: kompletter damaliger Testsatz 59 Tests/287 Assertions bestanden; zusätzlicher isolierter Setup-Test 1 Test/8 Assertions bestanden (zusammen 60/295). Neue Tests prüfen Kontoanlage, Rechte, Ablehnung, nichtinteraktive Aufrufe, `.env`-Sonderzeichen und einen neuen SQLite-/CORS-Setupablauf. Der isolierte Test ersetzt Artisan-Unterprozesse durch einen Stub; echte Paketinstallation/Migrationen werden darin nicht ausgeführt. Alle Schritte ablehnen als separater Smoke-Test erfolgreich; lokale `.env` unverändert. Pint, Composer-Validierung und Diff-Prüfung bestanden; bestehende Warnung zum Permission-Versionsbereich `*` bleibt.
- Keine echte Installation, Migration oder Kontoanlage in der vorhandenen Anwendung ausgeführt. MariaDB/MySQL und Linux/macOS müssen noch praktisch geprüft werden. Pail bleibt unter nativem Windows ungeeignet und wird vom Assistenten nicht gestartet. Einrichtungsfehler brechen ab; bestätigte Schritte werden nicht automatisch rückgängig gemacht.

### 26. Grafischen Browser-Assistenten ergänzt (11.10.2026)

- `/setup.php` als unabhängigen PHP-Einstieg ergänzt, damit die Oberfläche auch ohne Composer-Abhängigkeiten, `.env`, Laravel-Schlüssel oder Session-Tabellen startet. Keine zusätzlichen Pakete installiert. Webserver muss bereits laufen und ausschließlich `public/` ausliefern; eigener Terminalzugang ist nicht erforderlich, serverseitige PHP-CLI und Prozessausführung für Composer/Artisan jedoch schon.
- Fachliche Einrichtung in `app/Actions/Setup/RunAction.php`, HTTP-Ablauf in `IndexAction.php`, Kontoanlage in `AdminAction.php`; Template `resources/views/setup/wizard.php`. Keine Controller mit zusätzlicher Fachlogik eingeführt. Filament-/Artisan-Routen unverändert.
- Grafische Navigation mit Status, Ausführen-/Überspringen-Schaltflächen, Eingabefeldern und ausdrücklicher Bestätigung ergänzt. PHP, PDO-Treiber, Prozessausführung und vorhandene Dateien werden angezeigt. Schritte decken Installation, `.env`, Caches, Schlüssel, Datenbank, CORS, Migrationen, Paketassets, gemeinsamen Administrator, Runner, Storage, Brain und Abschlussübersicht ab.
- Sicheren Standard vorgesehen: keine aktive Setup-Freigabe mitgeliefert. Betreiber erstellt per Editor/Hosting-Dateimanager `.setup-access` außerhalb des öffentlichen Verzeichnisses mit eigenem zufälligem Schlüssel (mindestens 32 Zeichen). Eigene PHP-Sitzung mit HttpOnly-/SameSite-Cookie, Formularschutz, einer Stunde Zugangszeit und Anmeldebegrenzung ergänzt. Externe Zugriffe benötigen HTTPS; lokale HTTP-Freigabe prüft sowohl Gegenstelle als auch Hostnamen. Keine ungeprüften Proxy-Header akzeptiert.
- Parallele POST-Schritte über Dateisperre geschützt; Formular-Token vor Ausführung erneuern, damit erneutes Absenden keinen Schritt wiederholt. Passwörter nicht zurück ins Formular übernehmen und nicht in Prozessargumenten übergeben. Subprozessausgaben nicht im Browser veröffentlichen. Nur fest vorgegebene Composer-/Artisan-Befehle ausführen; PHP-CLI-/Composer-Pfade stammen ausschließlich aus Servereinstellungen.
- Neue SQLite-Dateien ausschließlich innerhalb von `database/` und mit `.sqlite`-Endung zulassen; vorhandene Dateien nicht überschreiben. Browser-Bestätigung umfasst Datenbankanlage, Verbindungstest und Speicherung gemeinsam. Anwendungsdaten nicht zurücksetzen; bereits bestätigte Änderungen bleiben bei Fehlern erhalten.
- Kontoanlage in frischem PHP-Prozess, damit die neue Datenbankkonfiguration gilt. Neue Konten benötigen separate Anlagebestätigung und gültige Passwortbestätigung; vorhandene Namen/Passwörter erhalten und beide Web-Guard-Rechte vergeben. Abschluss legt `.setup-complete` an und entfernt den Zugangsschlüssel; spätere Aufrufe bleiben gesperrt.
- Setup-Zugangs-, Sperr- und Anmeldebegrenzungsdateien in `.gitignore` aufgenommen. README, Handbuch und `docs/SETUP.md` um Aufruf, Freigabe ohne CLI, Servervoraussetzungen, Windows-Dateinamen, HTTPS/Proxy-Konfiguration, Hosting-Zeitlimits, Wiederfreigabe und offene Aufgaben ergänzt. Pail und Kopia werden nicht gestartet oder installiert; AGENTS.md unverändert.
- Prüfung: vollständige Suite 69 Tests/361 Assertions bestanden. Neue Tests prüfen Schlüsselfreigabe, CSRF, Bestätigung, Ablauf/Rotation der Sitzung, Anmeldebegrenzung, Transport, Kontoanlage/-erhalt sowie einen echten HTTP-Ablauf im isolierten Testverzeichnis mit eigenem PHP-Webserver bis zur Abschlusssperre. Composer-Abhängigkeiten werden dort erst nach der Anmeldung simuliert; Artisan-Aufrufe verwenden einen Stub. Echte Composer-Neuinstallation, MariaDB/MySQL, Paketassets und Migrationen über Webhosting wurden nicht praktisch ausgeführt; keine visuelle Prüfung in einem interaktiven Browser. PHP-Syntax, Pint, Composer-Validierung und Diff-Prüfung bestanden; bekannte Permission-Versionsbereichswarnung bleibt.
- Bestehende lokale `.env`, Datenbank und Zugangskonten nicht durch den Assistenten verändert; Oberfläche nicht für die bestehende Installation freigeschaltet. Linux/macOS und konkrete Webhosting-Einschränkungen bleiben praktisch zu prüfen. Vor öffentlichem Betrieb insbesondere APP_ENV/APP_DEBUG, HTTPS, Berechtigungen und Backups festlegen.

### 27. Windows-Eingabe und Apache-Prozessstart korrigiert (11.10.2026)

- Nutzerfehler geprüft: `composer setup` erhielt bei der ersten Bestätigung sofort EOF; Browser zeigte nur einen allgemeinen Prozessfehler. Installierten Composer-ProcessExecutor gelesen: externe Scripts erhalten keine Standardeingabe, und TTY wird unter Windows nicht aktiviert.
- `App\Support\ComposerSetup::run` als Composer-Callback ergänzt; startet den unabhängigen Setup-Prozess mit echter geerbter STDIN/STDOUT/STDERR. Laravel wird nicht in den Composer-Prozess geladen. Callback-Autoload funktioniert auch vor Installation von vendor. Kein automatisches Bestätigen oder erneutes Generieren bestehender Schlüssel.
- Tatsächliche PHP-Installation `C:\Apps\php\php.exe` gegenüber eingebautem `PHP_BINDIR=C:\php` festgestellt. `SetupRuntime` ergänzt: PHP über Servervariable, laufende CLI, php.ini-Verzeichnis und PATH ermitteln; Composer-PHP-Datei direkt mit der gefundenen absoluten PHP-CLI starten. CLI und Browser verwenden denselben Resolver. Keine Änderung an Apache-/PHP-Systemkonfiguration notwendig.
- Browser-Fehler um Schritt/Exit-Code und feste, auf bekannte Fehler beschränkte Hinweise erweitert. Keine rohe Prozessausgabe oder Zugangsdaten anzeigen. Konto-Validierungsfehler geben einen festen Marker zurück; Installationsseite zeigt nach Anmeldung ermittelte Programmdateien. Dokumentation zu Ursachen, Diagnose und erneutem Bestätigen ergänzt.
- Apache-VHost auf Port 8090 verweist auf die separate Kopie `C:\Entwicklung\www\laravelApi`. Fünf betroffene Bestandsdateien mit dem vorherigen Repository-Stand verglichen und nur bei Übereinstimmung überschrieben; zwei Support-Dateien ergänzt. Übertragung außerhalb des Workspace durch automatische Freigabeprüfung genehmigt. Projektkopie hat noch keine Composer-Abhängigkeiten; dort muss der Installationsschritt nach der Korrektur erneut bestätigt werden.
- Prüfungen: `composer setup` mit allen Schritten abgelehnt erfolgreich; lokale .env-Hash unverändert. Auch in der Apache-Kopie wird die erste Eingabe angenommen, bei abgelehnter Installation und fehlendem vendor korrekt gestoppt. Neues isoliertes Composer-Callback-Testprojekt ohne vendor prüft Eingabeweitergabe; Diagnose-Test prüft feste Fehlermeldungen ohne Testgeheimnisse. Gesamtsuite 71 Tests/371 Assertions bestanden; Pint, Syntax und Composer-Validierung bestanden, bekannte Permission-Versionsbereichswarnung bleibt. Diagnoseansicht und Dokumentation ebenfalls gezielt in die Apache-Kopie übertragen. Echte Anmeldung über localhost:8090/setup.php erfolgreich (HTTP 200); PHP-CLI und composer.phar werden unter Apache gefunden. Schlüssel nicht ausgegeben und anschließend abgemeldet. Echte Neuinstallation oder Migration in der Apache-Kopie nicht ungefragt ausgeführt.

### 28. Laufende Setup-Schritte sichtbar gemacht (11.10.2026)

- Browser meldete bisher erst nach Abschluss eines Schritts ein Ergebnis. JavaScript-Unterstützung in `public/setup-progress.js` und Statusbereich im unabhängigen PHP-Template ergänzt: aktuelle Aufgabe, Laufzeit, Zeit seit letzter Statusmeldung und unbestimmte Fortschrittsanzeige. Keine zusätzlichen Pakete und kein geschätzter Prozentwert.
- Bestätigte Browser-Schritte senden über denselben POST-Aufruf einen NDJSON-Statusstrom. Unterprozesse werden über `SetupProcess::wait` überwacht; ungefähr alle zwei Sekunden Aktivität und Zeit seit neuer Prozessausgabe melden. Ausgaben selbst bleiben verborgen. Nach Abschluss wird das normale Formular mit Ergebnis und rotiertem Token angezeigt; ohne JavaScript bleibt der bisherige HTML-Ablauf erhalten.
- Content Security Policy gezielt für eigenes JavaScript und eigene Fetch-Verbindungen erweitert. Ausgabepuffer für den Statusstrom aufgelöst, PHP-Kompression deaktiviert und Proxy-Pufferung über Header angesprochen. Zugang, CSRF, Bestätigung, Formularrotation und Serversperre unverändert erforderlich.
- Bei 15 Sekunden ohne neue Statusmeldung Hinweis auf ausbleibende Übertragung; dies ist kein sicherer Nachweis für einen festgefahrenen Prozess. Bei Verbindungsabbruch auf möglichen weiterlaufenden Prozess hinweisen und sofortiges erneutes Absenden unterbinden. Hosting-/Proxy-Puffer können Rückmeldungen weiterhin verzögern.
- CLI ergänzt Start, Abschluss mit Dauer und Aktivitätsmeldungen etwa alle fünf Sekunden; native Composer-/Artisan-Ausgaben bleiben sichtbar. Während interaktiver Admin-Eingaben zusätzliche Meldungen unterdrücken. Prozessüberwachung erhält Exit-Codes auch nach `proc_get_status`.
- README und `docs/SETUP.md` um Bedienung, Rückmeldungsintervalle, Aussagegrenzen, JavaScript-Fallback und Verhalten bei Verbindungsabbrüchen ergänzt. Betroffene Dateien in der Apache-Kopie gegen den vorherigen Repository-Stand geprüft und Fortschrittsanzeige einschließlich Anleitung mit genehmigter Übertragung gezielt aktualisiert.
- Prüfungen: gesamte Suite 72 Tests/385 Assertions bestanden. Neuer Prozess-Test prüft Aktivitätsmeldungen und Fehlercode 17. HTTP-Integration mit bewusst langsamem Artisan-Stub prüft, dass erste Statusmeldung vor Prozessende ankommt, weitere Meldungen folgen, ein Ergebnis geliefert wird und Testgeheimnisse aus Prozessausgaben nicht übertragen werden. Node-Syntaxprüfung, Pint und Diff-Prüfung bestanden.
- Tatsächliche Apache-Auslieferung unter localhost:8090 geprüft: JavaScript HTTP 200, Statuspanel vorhanden; authentifizierter POST mit absichtlich ungültiger Datenbankwahl liefert drei NDJSON-Ereignisse einschließlich Startstatus und korrekt abgelehntem Endergebnis. Keine echte Datenbankkonfiguration, Installation oder Migration dadurch ausgeführt; anschließend abgemeldet. Erste PowerShell-Auswertung als Bytefolge erkannt und mit UTF-8-Dekodierung korrigiert. Keine visuelle Prüfung in einem interaktiven Browser; Proxy-Pufferung auf anderen Hostings bleibt praktisch zu prüfen.

### 29. Browser-Bedienung, Aufrufadressen und fünf Sprachen ergänzt (11.10.2026)

- Auf Nutzerwunsch zusätzliche Bestätigungshaken in setup.php entfernt. Der ausdrücklich angeklickte Ausführen-/Abschlussbutton autorisiert den Schritt; `SetupAccess::verifyPost` prüft weiterhin Anmeldung, Sitzungsablauf, CSRF und Operation `run`. Tokenrotation und Serversperre bleiben bestehen. Interne Admin-Action erhält die bestätigte Ausführung vom HTTP-Ablauf; neue Konten werden separat über eine Auswahl erlaubt. Auch hier kein Checkbox-Zwang. CLI-Bestätigungen unverändert.
- `SetupOrigin` ergänzt: validierte HTTP_HOST-Angabe samt Port und serverseitigem HTTPS-Status verwenden. Alle Abschlusslinks und der Vorschlag für das API-URL-Feld nutzen diese Aufrufbasis, unabhängig von APP_URL. Beispiel: Aufruf unter localhost:8090 erzeugt Filament-/Artisan-Links mit Port 8090. Host-Injektionen und ungültige Ports ablehnen; keine ungeprüften Forwarded-Header übernehmen. Tatsächlich konfigurierte Frontend-Origin separat anzeigen; .env nicht automatisch umschreiben.
- `SetupLocale` und `resources/setup/translations.json` ergänzt. English ist Standard; Auswahl English, Deutsch, Français, Italiano, Español gleich zu Beginn und jederzeit verfügbar. Auswahl in separatem Sprach-Cookie und Setup-Sitzung erhalten, auch nach Anmeldung. Beschriftungen, Hilfetexte, Erfolgs-/Fehlermeldungen, Schrittstatus und NDJSON-/JavaScript-Fortschritt übersetzt. Funktioniert ohne Composer; Laravel-APP_LOCALE und CLI-Sprache unverändert.
- PHP-Template entsprechend überarbeitet; Sprach- und Statusdaten escaped ausgeben. JavaScript nutzt übersetzte Nachrichtenvorlagen statt fester deutscher Texte. Versionsparameter für die JavaScript-Datei verhindert die Wiederverwendung einer alten Checkbox-Prüfung aus dem Browsercache. Keine zusätzlichen Pakete installiert; AGENTS.md unverändert.
- README, Setup-Anleitung und Handbuch aktualisiert. Frühere Apache-Kopie `C:\Entwicklung\www\laravelApi` derzeit nicht vorhanden; VHost verweist noch auf diesen Pfad. Diese Kopie nicht erneut angelegt und Apache-Konfiguration nicht verändert. Änderungen liegen im aktuellen Repository.
- Prüfungen: vollständige Suite 74 Tests/831 Assertions bestanden. HTTP-Integration prüft English als Standard, Auswahl aller fünf Sprachen, übersetzte Anmeldefehler, Sprachpersistenz, Ausführung ohne Checkbox, erhaltenen Formularschutz und Aufrufport in Abschlusslinks trotz abweichender APP_URL. Katalog-Vollständigkeit, dynamische Prozess-/Fehlertexte sowie HTTPS, IPv6 und ungültige Host-/Portwerte getestet. Backend-Meldungen zusätzlich gegen den Katalog abgeglichen; keine fehlenden statischen Fehler-/Erfolgstexte. PHP-/Node-Syntax, Pint und Diff-Prüfung bestanden. Keine neue visuelle Prüfung in einem interaktiven Browser und kein Test auf anderen Betriebssystemen; bestehende .env/Datenbank nicht konfiguriert oder migriert.

### 30. jrean/laravel-user-verification für die API bewertet (13.10.2026)

- Repository-Anleitung, Composer-/Fortify-Konfiguration, User-Modell, API-Routen und vorhandene Migration geprüft. E-Mail-Verifizierung hier noch nicht aktiv: MustVerifyEmail-Vertrag fehlt, Fortify-Routen werden ignoriert, Featureliste leer; email_verified_at ist vorhanden. Standard-Mailer der Vorlage schreibt ins Log.
- Offizielle Paket-/Laravel-Dokumentation und Packagist geprüft. Paket v14.0.0 unterstützt Illuminate 13; Quellcode des konkreten Release-Commits 0c6b7b3725da85bc1b5d55b8936ab12dc26b2bc6 untersucht: Composer-Manifest, Kernmethoden, ServiceProvider, Konfiguration und Routen. Direkter Abruf im Sandbox-Kontext scheiterte; nach automatisch genehmigter Netzwerkfreigabe erfolgreich. Keine Pakete oder Dienste installiert.
- Technische API-Nutzung möglich, Standardintegration jedoch über Web-Routen zu einem hier fehlenden RegisterController und Controller-Trait mit Redirects/Views. Zusätzliche verified-/verification_token-Spalten gegenüber dem vorhandenen Laravel-Zustand nötig; Tokenprüfung kontrolliert keine Ablaufzeit. Native Framework-Notification im installierten Code nutzt temporär signierte URLs mit Standardablauf 60 Minuten. Keine Linux-exklusive Laufzeitabhängigkeit in dem geprüften Manifest gefunden.
- Native Laravel-Verifizierung empfohlen; Variantenentscheidung dem Betreiber gestellt. Keine Ersatzfunktion ungefragt aktiviert, kein RegisterController hinzugefügt, keine Benutzerrechte/-zustände geändert und keine Migration ausgeführt. Installationsassistent bis zur Entscheidung unverändert; spätere Mailkonfiguration und Umgang mit bestehenden Admin-Konten dokumentiert.
- Detaillierte Bewertung als docs/EMAIL_VERIFIZIERUNG_PRUEFUNG.md erstellt und im Handbuch verlinkt. Routenliste bestätigt, dass aktuell keine Verifizierungsrouten registriert sind. AGENTS.md unverändert. Da ausschließlich Dokumentation ergänzt wurde, keine erneute Anwendungstestsuite erforderlich; Diff-Prüfung erfolgreich.

### 31. Native Laravel-E-Mail-Verifizierung umgesetzt (13.10.2026)

- Betreiber hat die native Variante bestätigt. User-Modell implementiert MustVerifyEmail; vorhandenes email_verified_at weiterverwendet. Keine jrean-Abhängigkeit, keine neuen Verifizierungsspalten oder Migrationen und keine automatische Bestätigung bestehender Konten. Fortify-Routen bleiben ignoriert; öffentliche Registrierung und zusätzliche Verifizierungspflicht für Login/Filament/Runner weiterhin nicht aktiviert.
- UserController um ausschließlich routengebundene Methoden mit injizierten Actions ergänzt: EmailVerificationStatusAction, SendEmailVerificationAction und VerifyEmailAction unter app/Actions/Users/. Eigene Enums 05005–05008 für Status, versandten Link, erfolgreiche und bereits erfolgte Bestätigung. Gemeinsames JSON-Format und vorhandene Systemfehlercodes erhalten.
- Authentifizierte API-Endpunkte GET /api/email/verify und POST /api/email/verification-notification ergänzt. Versandlimit sechs Anfragen pro Minute/Konto plus 30/IP; CSRF über bestehenden Sanctum-Stateful-Ablauf. Bereits bestätigte Konten erhalten keine weitere Mail.
- GET /api/email/verify/{id}/{hash} unter dem API-Präfix mit Web-Session-Middleware registriert, damit ein direkter Mail-Klick ohne Frontend-Origin die vorhandene Anmeldung nutzt. Auth:sanctum, signierte URL und Throttle erforderlich; native EmailVerificationRequest prüft Konto-ID und E-Mail-Hash. Rückgabe JSON statt Redirect. Wiederholte gültige Bestätigung erhält Timestamp und löst nicht erneut das Verified-Event aus.
- AUTH_VERIFICATION_EXPIRE=60 in die sichere .env.example aufgenommen und über config/auth.php verfügbar gemacht. Native temporäre Signatur/Notification verwenden; Registered-Event unterstützt den künftigen Registrierungsablauf. Maildienst nicht ersetzt und keine echten Mails versandt. Bestehende lokale .env, Konten und Datenbank nicht durch Setup verändert.
- Eigenen Hinweis-Schritt in Browser- und CLI-Assistent ergänzt: Mailzustellung/öffentliche APP_URL prüfen, Log-Mailer gegenüber SMTP erklären, Linkdauer und nötige Anmeldung nennen. Browsertexte in allen fünf Sprachen und neue Adressen in Abschlussübersichten ergänzt. Schritt dient der Information, verändert keine SMTP-Zugangsdaten und sendet keine Testmail. Neue CLI-Abfrage im isolierten Setup-Test berücksichtigt.
- README mit Aufrufen, Codes, SMTP-Platzhaltern, Ablauf, Grenzen und künftiger Verifizierungspflicht ergänzt. Handbuch und Paketbewertung auf entschiedenen/implementierten Stand aktualisiert; Setup-Anleitung erweitert. Konfigurationsreferenz erneut exportiert (29 Gruppen); keine Paketbestandsänderung. AGENTS.md unverändert.
- Prüfungen: vollständige Suite 84 Tests/918 Assertions bestanden. Neue Featuretests prüfen Gäste, Status, native Notification mit Signatur/Ablauf, konfigurierbare Dauer, erfolgreiche/idempotente Bestätigung, abgelaufene/manipulierte/fremde Links, Versandlimits, bereits bestätigte Konten, Registered-Event, unverifizierten Login, CSRF und direkten Mail-Link ohne SPA-Origin. Alle Mailprüfungen nutzen Notification::fake, keine echte Zustellung. HTTP-Setup-Test prüft Hinweis-Ausgabe ohne .env-Änderung. Pint, Syntax-/Diff-Prüfung und Composer-Validierung bestanden; bestehende Warnung zur Permission-Versionsvorgabe * unverändert.
- Offen: SMTP/Maildienst mit realen Zugangsdaten konfigurieren und Zustellung später gezielt prüfen; APP_URL/HTTPS für die Zielumgebung festlegen. Fachliche verified-Routen und das Zurücksetzen des Zeitstempels bei künftigem E-Mail-Wechsel bewusst spezifizieren. Keine manuelle Datenmigration oder pauschale Freischaltung/Aussperrung von Administratorkonten durchgeführt.

### 32. Eloquent Sluggable installiert und geprüft (13.10.2026)

- Offizielle Repository-/Packagist-Dokumentation und Laufzeit-Manifeste geprüft: Paketreihe 13.x passt zu Laravel 13 und PHP ab 8.3; keine Linux-exklusive Laufzeitanforderung. Eloquent-Modellereignisse unterstützen automatische Slugs und Kollisionssuffixe. Kein vorhandenes Anwendungsmodell benötigt derzeit eine Slug-Spalte; fachliche Integration deshalb für spätere Ressourcen vorbereitet.
- cviebrock/eloquent-sluggable 13.0.1 als direkte Laufzeitabhängigkeit mit Bereich ^13.0 installiert; cocur/slugify v4.7.1 als zusätzliche transitive Bibliothek. Bestehende andere Paketversionen unverändert. composer.json und Lockdatei aktualisiert. Composer meldet keine bekannten Sicherheitsadvisories; Plattformprüfung erfolgreich. mbstring-Funktionen durch vorhandenen Symfony-Polyfill bereitgestellt.
- Windows-Composer-Batchaufruf entfernte zunächst den Caret und installierte strikt 13.0.0. Bereich über direkten PHP-/Composer-PHAR-Aufruf korrekt auf ^13.0 gesetzt und Patch 13.0.1 installiert. Ein Versuch mit nicht unterstütztem --no-autoloader wurde ohne Änderung abgewiesen; danach Autoload-Optimierung nur während des Patchupdates deaktiviert und die ursprüngliche Konfiguration true im finally wiederhergestellt. Gültiger Autoloader generiert. Netzwerk-/Cachezugriff für Patchupdate nach Sandbox-Verbindungsfehler automatisch genehmigt. Keine Composer-Hooks ausgeführt, um fremde Asset-Veröffentlichungen zu vermeiden.
- package:discover ausgeführt und Paketprovider über Auto-Discovery registriert; Konfiguration ohne --force nach config/sluggable.php veröffentlicht. Defaults erhalten: unique=true, onUpdate=false, firstUniqueSuffix=2. Keine neue Migration, keine bestehende User-/API-Route verändert und kein Produktions-/Anwendungsdatensatz bearbeitet. Noch kein fachliches Modell mit dem Trait versehen.
- Integrationstest mit eigenem Fixture-Modell und ausschließlich einer temporären SQLite-Testtabelle ergänzt: registrierter Provider, automatische slug-Erzeugung beim Speichern, eindeutiger Suffix bei gleichen Titeln und stabile URL bei Titeländerung geprüft. Fixture liegt unter tests/Fixtures/; keine zusätzliche Anwendungsressource angelegt.
- README um Modellbeispiel, Slug-Spalte/Unique-Index, maximale Länge, Backfill für vorhandene Daten, Routennutzung und Autorisierung ergänzt. Handbuch und Setup-Anleitung aktualisiert: vorhandene Composer-/Registrierungsschritte decken das Paket ab; keine weitere Assistenten-Abfrage oder Paketmigration nötig. Inventar neu erzeugt: 182 Pakete; Konfigurationsreferenz neu exportiert: 30 Gruppen. AGENTS.md unverändert.
- Prüfungen: vollständige Suite 85 Tests/922 Assertions bestanden. Composer-Manifest und Plattformanforderungen gültig; bekannte Permission-Versionsbereichswarnung * bleibt. Pint und Diff-Prüfung bestanden. Native Windows-/SQLite-Integration praktisch geprüft; Linux/macOS und MariaDB nicht separat getestet.
- Für spätere Ressourcen Quellattribut, slug-Spalte und Länge, reservierte Namen sowie eventuelle Änderungen bestehender URLs festlegen. Unique-Index schützt parallele Schreibzugriffe; Konflikte in den zugehörigen Actions behandeln. Installation ist abgeschlossen, fachliche Slug-Endpunkte stehen weiterhin aus.

### 33. Kitloong Laravel Migrations Generator installiert (13.10.2026)

- Offizielle Dokumentation, Paketmanifest und Plattform geprüft: Version 7.4.0 unterstützt Laravel 13, PHP ab 8.2 und SQLite/MariaDB/MySQL. Keine Linux-exklusive Anforderung; der jeweilige PDO-Treiber und bei Serverdatenbanken eine erreichbare Verbindung werden benötigt. Quellen: https://github.com/kitloong/laravel-migrations-generator und https://packagist.org/packages/kitloong/laravel-migrations-generator.
- kitloong/laravel-migrations-generator mit Bereich ^7.4 als Entwicklungsabhängigkeit installiert; composer.json und composer.lock aktualisiert. Genau ein neues Paket, andere Paketversionen unverändert. Direkter PHP-/Composer-PHAR-Aufruf verwendet, damit Windows den Caret erhält. Autoload-Optimierung während der Installation deaktiviert und im finally wieder auf true gesetzt. Installation ohne Composer-Hooks; danach package:discover explizit ausgeführt. Netzwerk-/Cachezugriff nach fehlgeschlagener Paketabfrage durch automatische Prüfung genehmigt. Composer meldet keine bekannten Sicherheitsadvisories.
- Auto-Discovery registriert migrate:generate. Paketdefaults werden unter migrations-generator geladen, ohne eigene Konfigurationsdatei. php artisan help migrate:generate geprüft. Keine Paketmigration, kein Benutzer und keine Änderung an Anwendungsdaten nötig. CLI-/Browser-Setup installiert Entwicklungspakete und registriert den Provider bereits; keine weitere Abfrage und kein automatischer Schemaexport ergänzt. Bei --no-dev fehlt das Werkzeug.
- migrate:generate im Artisan Runner gesperrt: Schemaexport ist ein bewusster CLI-Entwicklungsschritt, der Dateien und ohne --skip-log die Migrationstabelle verändert. README, Handbuch und Setup-Anleitung um Aufruf, Verbindungsauswahl, separaten Ausgabeordner, --skip-log und Prüfung vor Übernahme ergänzt. Bestehende Projektmigrationen nicht ungeprüft durch generierte Tabellenanlagen ergänzen; kein Datentransfer enthalten. SQLite-Einschränkungen bei nachträglichen Fremdschlüsseln dokumentiert.
- Integrationstest ergänzt: temporäre SQLite-Testtabelle mit Unique-Index und nullable-Spalte exportiert, erzeugte PHP-Migration wieder angewendet und zurückgenommen; unverändertes Migrationsprotokoll geprüft. Testdateien und Tabelle werden anschließend entfernt. Ausschließlich isolierte Testdatenbank verwendet; kein Export der lokalen Anwendungsdatenbank und keine neue produktive Migration angelegt.
- Paketbestand aktualisiert: 183 Pakete. Konfigurationsreferenz neu exportiert: 31 Gruppen. AGENTS.md unverändert. Vollständige Testsuite erfolgreich: 86 Tests/930 Assertions. Composer-Validierung und Plattformprüfung erfolgreich; bestehende Warnung zur unbeschränkten Permission-Versionsvorgabe bleibt. Pint und Diff-Prüfung erfolgreich. Native Windows-/SQLite-Integration geprüft; MariaDB/MySQL und Linux/macOS nicht separat ausgeführt.
- Für spätere Apps: Quellverbindung zuerst prüfen, zusammengehörige Tabellen auswählen, Export außerhalb database/migrations mit --skip-log erzeugen und Dateien vor Übernahme auf Duplikate, Datentypen, Indizes und Fremdschlüssel prüfen. Migrationen auf einer separaten Zieldatenbank testen; Datenübertragung gesondert planen.

## Betrieb und wichtige Befehle

### Lokal starten und ersten Zugang anlegen

```powershell
php artisan runner:create-user admin@example.com --name="Administrator"
php artisan serve
```

Die E-Mail ist ein Beispiel. Passwort erst in der verdeckten Abfrage eingeben. Danach `http://127.0.0.1:8000/artisan/login` öffnen. Bei aktiviertem Runner und zugewiesenem Recht führt der Kontobereich zur Oberfläche.

### Änderungen prüfen

```powershell
php artisan test
php vendor/bin/pint --test
php artisan route:list --path=artisan-runner-ui -vv
```

Für detaillierte PHPUnit-Ausgaben wurde verwendet:

```powershell
$env:PAO_DISABLE='true'
php vendor/phpunit/phpunit/phpunit --display-all-issues
```

Tests nutzen SQLite im Arbeitsspeicher. Für neue Checkouts und die Ersteinrichtung siehe `README.md`.

### Konfiguration und Wartung

- Änderungen an `.env`: bei gecachter Konfiguration `php artisan config:clear` ausführen.
- Änderungen an Berechtigungen über Spatie-Methoden durchführen. Nach direkten Datenbankänderungen `php artisan permission:cache-reset` verwenden.
- Runner ist nur in `local`, `qa`, `staging`, `uat` verfügbar; Produktion bleibt ausgeschlossen.
- `allowed` ist aktuell leer: alle Befehle außer denen in `denied` sind verfügbar. Vor Nutzung außerhalb der lokalen Entwicklung die tatsächlich benötigten Befehle festlegen.
- Passwörter und Anwendungsschlüssel bleiben lokal; `.env`, SQLite-Datei und `vendor/` werden nicht eingecheckt.
- Bei Paketupdates die veröffentlichte Runner-Ansicht mit der neuen Originalansicht vergleichen.
- Telescope lokal unter `/telescope` öffnen. `TELESCOPE_ENABLED=false` schaltet Aufzeichnung und Dashboard ab; außerhalb von `local` wird der Provider grundsätzlich nicht registriert.
- Telescope nutzt die konfigurierte Datenbank. Bei Bedarf mit `php artisan telescope:prune --hours=48` Aufzeichnungen älter als 48 Stunden löschen; der Befehl wurde bei der Installation nicht ausgeführt.

## Offene Aufgaben und nächste Entscheidungen

### Direkt nutzbar, noch vom Betreiber auszuführen

- [ ] Eigenen Zugangsbenutzer mit `runner:create-user` anlegen und Anmeldung im Browser prüfen.
- [ ] Festlegen, welche Personen Runner-Zugriff erhalten und welche Befehle erlaubt sein sollen.

### Vor Ausbau der API zu entscheiden

- [ ] Fachliche Ressourcen, Endpunkte, Validierungen und Fehler-/Antwortformat spezifizieren.
- [x] API-Authentifizierung entschieden: Sanctum mit Cookies/Sessions für das lokale Frontend; spätere Produktionsdomains und HTTPS-Konfiguration separat festlegen.
- [ ] Fachliche Rollen und Rechte sowie Policies festlegen; aktuell existiert nur die Runner-Zugriffsintegration.

### Bekannte Verbesserungen

- [ ] Spatie-Versionsvorgabe von `*` auf einen passenden Versionsbereich begrenzen; Lockdatei aktualisieren und prüfen.
- [x] `composer setup` durch den interaktiven API-Einrichtungsassistenten ersetzt; ohne npm-Schritte. `composer dev` bleibt der Vorlagenbefehl; empfohlen wird der ausgegebene `artisan serve`-Aufruf.
- [ ] Standard-`DatabaseSeeder` prüfen: er enthält noch die Laravel-Testbenutzeranlage. Nicht ungeprüft für reale Umgebungen verwenden.
- [ ] Aufbewahrungsdauer für Telescope-Aufzeichnungen festlegen; bei Bedarf automatische Bereinigung und lokalen Scheduler einrichten.
- [ ] Veraltete Informationen in `AGENTS.md` erst aktualisieren, wenn die ursprüngliche Vorgabe zum Nichtändern aufgehoben wird.
- [ ] Kopia fertig einrichten: Ziel und Umfang klären, konsistenten SQLite-Snapshot vorbereiten, echtes Repository einrichten, Intervall/Aufbewahrung festlegen und Wiederherstellung prüfen. Installation und isolierter Kopia-Dateitest sind abgeschlossen; siehe `KOPIA_SETUP.md`.

### Späterer MariaDB-Wechsel und Betrieb

- [ ] MariaDB-Datenbank und Zugang bereitstellen; lokal `DB_CONNECTION=mariadb`, Host, Port, Datenbank und Zugangsdaten konfigurieren.
- [ ] Konfigurationscache leeren und Migrationen gegen die vorgesehene MariaDB-Datenbank ausführen; Tests zusätzlich auf MariaDB prüfen.
- [ ] Bei zu erhaltenden SQLite-Daten einen separaten Datenumzug planen. Migrationen übertragen nur das Schema.
- [ ] Deployment, HTTPS, `APP_DEBUG=false`, Session-Cookie-Einstellungen, Backups und Protokollierung für die Zielumgebung festlegen.

Diese Punkte sind offen oder vorgeschlagen; sie wurden noch nicht umgesetzt. Bei fachlichen Fragen oder fehlenden Angaben vor der abhängigen Umsetzung nachfragen.
