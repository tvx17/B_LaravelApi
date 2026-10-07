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
| Anmeldung | Browser-Anmeldung mit Sessions für die Runner UI |
| Runner-Zugriff | Berechtigung `run-artisan-runner-ui` |
| API-Anmeldung | Noch keine Sanctum- oder andere Token-Anmeldung |
| Registrierung | Keine öffentliche Registrierung |

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
- [ ] Veraltete Informationen in `AGENTS.md` erst aktualisieren, wenn die ursprüngliche Vorgabe zum Nichtändern aufgehoben wird.

### Späterer MariaDB-Wechsel und Betrieb

- [ ] MariaDB-Datenbank und Zugang bereitstellen; lokal `DB_CONNECTION=mariadb`, Host, Port, Datenbank und Zugangsdaten konfigurieren.
- [ ] Konfigurationscache leeren und Migrationen gegen die vorgesehene MariaDB-Datenbank ausführen; Tests zusätzlich auf MariaDB prüfen.
- [ ] Bei zu erhaltenden SQLite-Daten einen separaten Datenumzug planen. Migrationen übertragen nur das Schema.
- [ ] Deployment, HTTPS, `APP_DEBUG=false`, Session-Cookie-Einstellungen, Backups und Protokollierung für die Zielumgebung festlegen.

Diese Punkte sind offen oder vorgeschlagen; sie wurden noch nicht umgesetzt. Bei fachlichen Fragen oder fehlenden Angaben vor der abhängigen Umsetzung nachfragen.
