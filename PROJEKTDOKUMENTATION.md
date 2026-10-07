# Projektdokumentation und Arbeitsprotokoll

Stand: 07.10.2026. Diese Datei hält Arbeitsschritte, Entscheidungen, Prüfergebnisse und offene Aufgaben fest. Die bisherigen Schritte wurden aus dem Gespräch und den Projektdateien rekonstruiert. Die README enthält die kurze Anleitung zur Nutzung.

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
| Framework | Laravel 13.35.0 laut `composer.lock`; PHP-Anforderung ab 8.3 |
| Lokale Umgebung | Windows/PowerShell; bei Einrichtung PHP 8.5.6 und Composer 2.9.8 |
| Datenbank | Aktuell SQLite, später MariaDB |
| Berechtigungen | Spatie Laravel Permission 8.3.0, Guard `web` |
| Artisan-Oberfläche | CodeSquirrel Artisan Runner UI 1.0.1 |
| Debugging | Laravel Telescope 5.25.0 als Entwicklungsabhängigkeit; ausschließlich lokal |
| Anmeldung | Browser-Anmeldung mit Sessions für die Runner UI |
| Runner-Zugriff | Berechtigung `run-artisan-runner-ui` |
| API-Anmeldung | Noch keine Sanctum- oder andere Token-Anmeldung |
| Registrierung | Keine öffentliche Registrierung |
| OAuth | Laravel Socialite 5.31.0 als Grundlage; Anbieter und Login-Ablauf noch offen |

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

## Betrieb und wichtige Befehle

### Lokal starten und ersten Zugang anlegen

```powershell
php artisan runner:create-user admin@example.com --name="Administrator"
php artisan serve
```

Die E-Mail ist ein Beispiel. Passwort erst in der verdeckten Abfrage eingeben. Danach `http://127.0.0.1:8000/login` öffnen. Bei aktiviertem Runner und zugewiesenem Recht führt der Kontobereich zur Oberfläche.

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
- [ ] API-Authentifizierung entscheiden, beispielsweise Sanctum, falls erforderlich.
- [ ] Fachliche Rollen und Rechte sowie Policies festlegen; aktuell existiert nur die Runner-Zugriffsintegration.

### Bekannte Verbesserungen

- [ ] Spatie-Versionsvorgabe von `*` auf einen passenden Versionsbereich begrenzen; Lockdatei aktualisieren und prüfen.
- [ ] `composer setup` und `composer dev` für den gewünschten reinen API-Betrieb überprüfen; die Vorlage enthält noch Frontend-Schritte.
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
