# Einrichtung nach dem Klonen

Im Projektverzeichnis genügt `composer setup`. Alternativ startet `php scripts/Setup.php` denselben Assistenten. Er setzt kein vorhandenes `vendor/` oder `.env` voraus. Windows ist die primäre Plattform; PHP und Composer erlauben denselben Ablauf unter Linux/macOS.

## Voraussetzungen

- PHP ab 8.3, Composer im PATH und die vom Lockfile benötigten PHP-Erweiterungen. Composer prüft die Plattform beim Installieren.
- Für SQLite: `pdo_sqlite`; für MariaDB/MySQL: `pdo_mysql`, erreichbarer Datenbankserver und Zugangsdaten. Serverinstallation erfolgt separat.
- Beschreibbares Projektverzeichnis und ein interaktives Terminal für verdeckte Passworteingaben. Keine automatisierte Ausführung mit `--no-interaction`.
- Hinweis für Windows: Laravel Pail benötigt PCNTL und läuft dort nicht nativ. Der Assistent startet Pail nicht. Kopia wird separat eingerichtet, siehe `KOPIA_SETUP.md`.

## Ablauf und Bestätigungen

Jeder Schritt fragt nach Zustimmung; Enter lehnt ab. Abhängigkeiten, `.env` und Anwendungsschlüssel sind für einen neuen Checkout erforderlich. Bei Fehlern endet die Einrichtung; bereits bestätigte Schritte werden nicht zurückgerollt. Ein erneuter Aufruf ist möglich.

1. Composer installiert einschließlich Entwicklungspaketen aus `composer.lock`, zunächst ohne Laravel-Hooks.
2. Eine fehlende `.env` entsteht aus `.env.example`: sichere Projektstandards, keine übernommenen lokalen Geheimnisse. Ein vorhandener Anwendungsschlüssel bleibt erhalten.
3. Datenbank auswählen: SQLite, MariaDB oder MySQL; danach vorhandene oder neue Datenbank. SQLite-Dateien werden nicht überschrieben; ihr Elternverzeichnis muss existieren. Neue Serverdatenbanken benötigen `CREATE DATABASE`-Rechte und einen noch freien Namen. Nach Verbindungstest wird die Speicherung separat bestätigt. Eine zuvor bestätigte neue Datenbank bleibt bei Ablehnung dieser Speicherung bestehen.
4. API-URL und Frontend-Origin konfigurieren, standardmäßig `http://localhost:8000` und `http://localhost:9200`. Für diesen Session-Ablauf denselben Host und dasselbe Schema verwenden. CORS erlaubt Cookies; das Frontend muss Credentials und CSRF mitsenden. Details stehen in der README.
   Danach lassen sich Hinweise zur nativen E-Mail-Verifizierung anzeigen: Maildienst konfigurieren, öffentliche APP_URL prüfen und AUTH_VERIFICATION_EXPIRE festlegen (Standard 60 Minuten). Die Hinweisabfrage sendet keine Mail und verändert keine Benutzer oder Mailzugänge.
5. Migrationen für das angezeigte Ziel, Paketregistrierung, Filament-Assets und Permission-Cache bestätigen. Migrationen verwenden kein `fresh`/`reset`; eigene spätere Migrationen trotzdem vor Ausführung prüfen.

   Eloquent Sluggable wird ebenfalls über Composer installiert und automatisch registriert. `config/sluggable.php` ist bereits im Repository enthalten; kein zusätzlicher Setup-Schritt nötig. Für spätere Ressourcen jeweils Sluggable-Trait, Quellattribut und eigene Slug-Spalte/Migration ergänzen. Noch keine Slug-Spalte für vorhandene Anwendungsmodelle angelegt.
6. Optional einen Administrator anlegen oder einem bestehenden Konto beide Rechte vergeben: `access-admin-panel` und `run-artisan-runner-ui`. Bestehender Name und Passwort bleiben erhalten. Neue Passwörter benötigen mindestens zwölf Zeichen und eine verdeckte Bestätigung. Separat wiederholbar: `php artisan setup:admin`.
7. Runner-Aktivierung, öffentlichen Storage-Link und lokalen Brain-Scan bestätigen.

Kitloong Laravel Migrations Generator wird als Entwicklungsabhängigkeit über den Composer-Schritt installiert und automatisch registriert. Keine Paketmigration, Benutzeranlage oder zusätzliche Assistenten-Abfrage nötig. Der Assistent exportiert kein Schema. Spätere Exporte bewusst per CLI mit separatem `--path` und `--skip-log` starten; Beispiele in der README. Bei Installation mit `--no-dev` fehlt dieses Werkzeug.

Laravel IDE Helper wird ebenfalls als Entwicklungsabhängigkeit installiert und automatisch registriert. `config/ide-helper.php` ist im Repository enthalten; keine Paketmigration, Benutzeranlage oder weitere Pflichtabfrage nötig. Nach dem Setup bei Bedarf lokal `php artisan ide-helper:generate --no-interaction`, `php artisan ide-helper:models --nowrite --no-interaction` und für PhpStorm `php -d memory_limit=512M artisan ide-helper:meta --no-interaction` ausführen. Die Modellgenerierung benötigt eine erreichbare, migrierte Datenbank. Generierte Dateien bleiben lokal/Git-ignoriert; Modelle unverändert. Keine automatische Generierung beim Setup oder nach Migrationen; bei `--no-dev` fehlt das Werkzeug.

Scramble wird als Laufzeitabhängigkeit automatisch installiert und registriert. Konfiguration und Dokumentationsprovider sind im Repository enthalten; keine Migration, Benutzeranlage oder weitere Pflichtabfrage nötig. Abschlussübersichten nennen `/docs/api` und `/docs/api.json`; beide sind standardmäßig nur lokal erreichbar. Ein optionaler `php artisan scramble:export` schreibt die Spezifikation nach `storage/app/private/openapi.json`. Ein Export ist für den Aufruf im Browser nicht erforderlich.

## Nach der Einrichtung

Der Assistent zeigt API-, Filament-, Artisan-, Telescope- und Brain-Adressen sowie einen passenden Serverbefehl. Ausgelassene Schritte können deren Nutzbarkeit einschränken. `storage:link` kann unter Windows entsprechende Symlink-Rechte benötigen.

Server, Queue und Backup werden separat gestartet. Socialite bleibt ohne Anbieter; Media Library benötigt weiterhin ein geeignetes Modell und Upload-Endpunkte. Kopia-Repository, Sicherungsregeln und Wiederherstellungstest bleiben separat einzurichten. `.env`, SQLite-Dateien und Passwörter niemals einchecken. Für Produktion HTTPS, Debug-Modus und Zugriffsrechte gesondert prüfen.

## Einrichtung im Browser

Der grafische Assistent liegt unter `/setup.php`, lokal beispielsweise `http://localhost:8000/setup.php`. Er läuft unabhängig von Laravel, funktioniert also auch ohne `vendor/`, `.env`, Anwendungsschlüssel oder Session-Tabellen. Es werden keine zusätzlichen Composer-/npm-Pakete benötigt. Seine Actions liegen in `app/Actions/Setup/`; das reine PHP-Template in `resources/views/setup/wizard.php`. Filament und Artisan behalten ihre bisherigen Präfixe.

### Ohne eigenen Terminalzugang vorbereiten

1. Projekt auf einen bereits laufenden PHP-Webserver kopieren oder über dessen Verwaltungsoberfläche klonen. Den Document Root ausdrücklich auf `public/` setzen. Das Projektverzeichnis darüber darf nicht öffentlich ausgeliefert werden.
2. Mit Texteditor oder Hosting-Dateimanager im Projektverzeichnis eine Datei namens `.setup-access` erstellen. Als einzigen Inhalt einen eigenen zufälligen Schlüssel mit mindestens 32 Zeichen aus einem Passwortmanager verwenden. Kein Beispielpasswort übernehmen. Unter Windows darauf achten, dass der Dateiname nicht `.setup-access.txt` lautet. Dateizugriff auf den Webserver-Benutzer beschränken.
3. `/setup.php` aufrufen und diesen Schlüssel eingeben. Lokal sind HTTP und HTTPS möglich; bei externem Zugriff ist HTTPS verpflichtend. HTTPS muss vom Webserver als `HTTPS=on` gemeldet werden; vorgeschaltete Proxys entsprechend konfigurieren. Beliebige Forwarded-Header werden nicht als Vertrauensnachweis verwendet.
4. Zu Beginn die Sprache auswählen: English (Standard), Deutsch, Français, Italiano oder Español. Schritte über „Execute step“ beziehungsweise die übersetzte Schaltfläche ausführen oder überspringen; ein zusätzlicher Bestätigungshaken ist nicht erforderlich. Zurückliegende Schritte sind über die Navigation erneut erreichbar. Die Datenbankseite führt Anlage, Verbindungstest und Speicherung gemeinsam aus; bestehende Dateien werden nicht überschrieben. Bei MariaDB/MySQL das Passwort bei jeder Neukonfiguration erneut eingeben.
5. In der Adressübersicht „Abschließen und sperren“ bestätigen. Dadurch entsteht `.setup-complete`, und `.setup-access` wird entfernt. Die Anwendung selbst läuft weiter. Auch eine anschließend neu angelegte Zugangsdatei hebt die Abschlusssperre nicht auf.

### Servervoraussetzungen und Grenzen

Der Browser-Schritt „E-Mail-Verifizierung prüfen“ zeigt die vorhandenen Mailer-/URL-/Linkdauer-Werte und Hinweise in allen fünf Sprachen. API-Endpunkte sind bereits eingerichtet; zusätzliche Pakete oder Verifizierungsmigrationen sind nicht nötig. SMTP-Zugangsdaten bleiben außerhalb des Assistenten in der lokalen `.env` zu konfigurieren. Mit `MAIL_MAILER=log` werden Nachrichten nur protokolliert. Neue und bestehende Setup-Administratoren werden nicht automatisch verifiziert; Filament-/Runner-Zugang bleibt über die bisherigen Rechte geregelt. Die Abschlussübersicht enthält den Status-Endpunkt und den Versandpfad.

Ein Browser kann keinen PHP-Webserver starten. Lokal muss dieser bereits laufen, etwa über eine vorhandene Entwicklungsumgebung; beim Hosting übernimmt dies der Anbieter. Ein bloßes Öffnen einer PHP-Datei per Doppelklick genügt nicht.

Für Composer-/Artisan-Schritte benötigt der Server PHP-CLI und die erlaubte PHP-Funktion `proc_open`, außerdem temporären Speicher, Schreibrechte und passende Erweiterungen. Composer muss für den Webserver-Benutzer im PATH liegen; alternativ eine serverseitige Variable `COMPOSER_BINARY` mit absolutem Pfad zur vorhandenen `composer.phar` setzen. Ein abweichender PHP-CLI-Pfad lässt sich über die Servervariable `SETUP_PHP_BINARY` angeben. Diese Einstellungen werden nicht als frei editierbare Browserfelder angeboten. PHP-FPM/CGI wird nicht als CLI ausgeführt.

Kein eigener SSH-/CLI-Zugang ist erforderlich, aber das Hosting muss diese Fähigkeiten bereitstellen. Sperrt es Prozessausführung, sind vollständige Installation und Artisan-Schritte über den Browser dort nicht möglich. Alternativ Abhängigkeiten vorab auf einem geeigneten Rechner installieren und `vendor/` hochladen; für Migrationen/Assets wird weiterhin ein geeigneter Server benötigt. Installation kann mehrere Minuten dauern; Webserver-/Proxy-Zeitlimits können den Ablauf trotz aufgehobenem PHP-Zeitlimit abbrechen. Nicht erneut absenden, solange ein Schritt noch läuft.

Die Browser-Sitzung verwendet eigene PHP-Cookies, Formularschutz und ein einstündiges Zugangsfenster, unabhängig von Laravel-Sessions. Parallele Änderungen werden gesperrt; nach zehn falschen Anmeldungen gilt je IP eine 15-minütige Sperre. Schlüssel und Passwörter werden nicht in Formularwerte oder Befehlsargumente übernommen; Prozessausgaben bleiben zum Schutz von Geheimnissen verborgen. Schrittstatus gehört zur Browser-Sitzung; tatsächliche Einstellungen bleiben bei Abmeldung erhalten.

Die Sprache gilt für Browseroberfläche, Beschriftungen, Statusstrom und Fehlermeldungen. Sie wird über die Setup-Sitzung und einen separaten Sprach-Cookie beibehalten; Laravel-APP_LOCALE und der CLI-Assistent werden dadurch nicht geändert. Übersetzungen liegen in `resources/setup/translations.json` und funktionieren auch vor der Composer-Installation. Die Kontoanlage wird über eine eigene Auswahl gesteuert: vorhandenes Konto verwenden oder bei Bedarf neues Konto erstellen.

Die Adressübersicht verwendet Schema, Host und Port des aktuellen HTTP-Aufrufs, unabhängig von APP_URL. Beispielsweise erzeugt `http://localhost:8090/setup.php` den Filament-Link `http://localhost:8090/filament/login`. Dieselbe Basis wird im API-URL-Feld vorgeschlagen. Die tatsächlich konfigurierte Frontend-Origin bleibt separat sichtbar. Ungeprüfte Forwarded-Header werden nicht verwendet; HTTPS muss der Webserver korrekt melden. Das Anzeigen der Adresse ändert `.env` nicht automatisch.

SQLite-Auswahl im Browser ist auf `.sqlite`-Dateien innerhalb von `database/` begrenzt. Andere Pfade sind über den CLI-Assistenten möglich. Alle optionalen Integrationen und offenen Aufgaben aus dem obigen Abschnitt bleiben bestehen. Der Assistent ändert `APP_ENV`/`APP_DEBUG` nicht automatisch: vor öffentlichem Betrieb diese Werte auf Produktion abstimmen.

`.setup-access`, `.setup-complete`, `.setup.lock` und `.setup-login-attempts.json` sind von Git ausgeschlossen. Für eine ausdrücklich gewollte erneute Einrichtung zuerst die Abschlusssperre im Dateimanager entfernen und einen neuen Zugangsschlüssel erstellen. Die bestehende lokale Installation wird durch das Hinzufügen der Oberfläche nicht eingerichtet oder freigeschaltet.

Technische Grundlage: PHP-[Prozessausführung](https://www.php.net/proc-open) und [Session-Cookie-Einstellungen](https://www.php.net/session-set-cookie-params).

### Fehler bei Eingabe und Prozessstart

`composer setup` verwendet einen Composer-PHP-Callback, der die echten Konsoleneingaben an den Setup-Prozess weitergibt. Damit funktioniert die Bestätigung auch unter Windows, wo der frühere `@php`-Aufruf die Standardeingabe geschlossen hat. Ohne verfügbares Terminal oder Eingabestream wird weiterhin abgebrochen; keine Zustimmung wird automatisch angenommen. Alternativ direkt `php scripts/Setup.php` starten.

Für Apache/PHP wird der CLI-Pfad zuerst aus einer expliziten Servervariable, im CLI aus dem laufenden PHP, danach aus dem Verzeichnis der geladenen `php.ini`, dem eingebauten PHP-Verzeichnis und PATH ermittelt. Dies berücksichtigt verschobene Windows-Installationen, deren eingebauter Pfad noch auf `C:\php` zeigt. Composer wird als PHP-Datei gefunden und mit dieser absoluten PHP-CLI gestartet; ein nicht verfügbarer Apache-PATH für `php` oder `composer.bat` ist dadurch kein Hindernis, wenn `composer.phar` neben PHP liegt.

Die Installationsseite zeigt die gefundenen PHP-/Composer-Pfade nur nach Anmeldung. Fehler nennen den Schritt, Exit-Code und bekannte feste Ursachen, etwa fehlende PDO-Treiber, Tabellen, Plattformanforderungen, Berechtigungen oder TLS-Probleme. Ungefilterte Prozessausgaben werden weiterhin nicht veröffentlicht. Browserseite nach Aktualisierung neu laden und den gewünschten Schritt erneut ausdrücklich bestätigen.

### Laufende Schritte beobachten

Mit aktiviertem JavaScript zeigt `/setup.php` während eines bestätigten Schritts die aktuelle Aufgabe, Laufzeit und Zeit seit der letzten Statusmeldung. Unterprozesse melden ungefähr alle zwei Sekunden, dass sie noch laufen; abgeschlossene Teilbefehle werden ebenfalls gemeldet. Zusätzlich wird angezeigt, seit wann keine neue Prozessausgabe vorliegt. Die Anzeige nutzt denselben HTTP-Aufruf als Datenstrom und benötigt keinen zweiten Worker oder zusätzliche Pakete. Ohne JavaScript bleibt das normale Formular verfügbar.

Eine aktive Rückmeldung bedeutet, dass der Server den Prozess noch überwacht; sie beweist nicht, dass dieser fachlich Fortschritte macht. Deshalb gibt es keinen erfundenen Prozentwert. Bleibt eine Statusmeldung länger als 15 Sekunden aus, meldet die Oberfläche dies ausdrücklich. Hosting-/Proxy-Pufferung kann Aktualisierungen verzögern; `X-Accel-Buffering: no` und deaktivierte PHP-Ausgabepuffer helfen, können aber nicht jede Serverkonfiguration beeinflussen.

Bei Verbindungsabbruch kann die Aufgabe serverseitig weiterlaufen. Nicht erneut absenden; später die Seite neu laden und den Zustand prüfen. Die bestehende Serversperre schützt weiterhin vor parallelen Schritten. Zugangsdaten und rohe Prozessausgaben werden auch im Statusstrom nicht übertragen.

In der CLI werden Start und Abschluss jedes Unterbefehls einschließlich Dauer ausgegeben. Während langer Befehle erscheint etwa alle fünf Sekunden eine Aktivitätsmeldung; native Composer-/Artisan-Ausgaben bleiben sichtbar. Bei der interaktiven Administratorkonto-Abfrage werden zusätzliche Meldungen unterdrückt, damit Passworteingaben nicht gestört werden.
