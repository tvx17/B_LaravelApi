# E-Mail-Verifizierung: Paketprüfung

Stand: 13.10.2026. Geprüft wurde `jrean/laravel-user-verification` v14.0.0, Quellstand `0c6b7b3725da85bc1b5d55b8936ab12dc26b2bc6`, gegen dieses Laravel-13-Projekt.

## Ergebnis

E-Mail-Verifizierung ist für Benutzerkonten sinnvoll. Das Paket ist technisch mit Laravel 13 kompatibel und seine Kernmethoden können aus API-Actions aufgerufen werden. Der Betreiber hat am 13.10.2026 die empfohlene native Laravel-Verifizierung bestätigt. Sie ist inzwischen über eigene API-Actions eingerichtet; das Zusatzpaket wurde nicht installiert.

## Vergleich

| Punkt | jrean-Paket | Eingebautes Laravel |
| --- | --- | --- |
| Verifizierungszustand | `verified`, `verification_token`; erfolgreiche Verarbeitung setzt auch `email_verified_at` | Vorhandene Spalte `email_verified_at` |
| Link | Gespeicherter Token; geprüfte `process()`-Methode kontrolliert keine Ablaufzeit | Temporär signierter Link; installierter Standard: 60 Minuten |
| Standardrouten | Web-Middleware, `Auth\RegisterController`, Redirects/Views | Eigene API-Routen und JSON-Actions möglich |
| Projektänderungen | Weitere Spalten, eigene API-Anbindung und abgestimmte Status-/Tokenregeln | Modellvertrag und eigene API-Actions/Routen |
| Zusatzabhängigkeit | Erforderlich | Framework bereits vorhanden |

Der Paket-Provider registriert seine Browser-Routen automatisch. Der vorausgesetzte `Auth\RegisterController` existiert hier nicht; dessen Controller-Trait passt außerdem nicht zur Vorgabe, Fachlogik in Actions zu halten. `generate()` setzt das zusätzliche `verified`-Flag zurück, lässt einen bereits vorhandenen Laravel-Zeitstempel jedoch stehen. Bei paralleler Nutzung beider Mechanismen müsste dieser Zustand ausdrücklich synchronisiert werden. Diese Feststellungen sind eine Integrationsbewertung, keine Behauptung einer veröffentlichten Sicherheitslücke.

Die geprüften Laufzeitabhängigkeiten enthalten keine Linux-exklusive Anforderung. Eine praktische Windows-/API-Integration des Pakets wurde nicht ausgeführt.

## Aktueller Projektstand

- Laravel/Fortify sind installiert; das User-Modell implementiert nun `MustVerifyEmail`. Native Verifizierung ist über eigene Actions eingerichtet.
- Die erforderliche Spalte `email_verified_at` und Laravels Modellmethoden sind vorhanden.
- Fortify verwendet weiterhin `ignoreRoutes()` und `features=[]`. Eigene Endpunkte: `GET /api/email/verify`, `POST /api/email/verification-notification`, signierter `GET /api/email/verify/{id}/{hash}`. Letzterer startet die Web-Session für direkte Mail-Klicks und liefert JSON. Anmeldung desselben Benutzers nötig.
- Öffentliche Registrierung ist weiterhin nicht eingerichtet. Bestehende Login-, Filament- und Runner-Rechte wurden nicht verändert.
- `.env.example` verwendet `MAIL_MAILER=log`: echte Verifizierungs-E-Mails benötigen eine konfigurierte Mailzustellung.

## Einrichtung und offene Aufgaben

Versand/erneuter Versand, Linkprüfung und JSON-Codes 05005–05008 sind umgesetzt. Signierte Links laufen standardmäßig nach 60 Minuten ab; AUTH_VERIFICATION_EXPIRE steuert die Dauer. Account-/IP-bezogene Versandlimits schützen vor wiederholten Anfragen. Bestehende Benutzer wurden nicht verifiziert; Login-/Panel-Routen erhalten keine neue Verifizierungspflicht.

Beide Installationsassistenten zeigen nun Hinweise zu Mailzustellung, APP_URL und Linkdauer; die Browserfassung in allen fünf Sprachen. Keine zusätzlichen Composer-Pakete oder Verifizierungsspalten erforderlich. SMTP/Maildienst muss für echte Zustellung lokal konfiguriert werden; Details und Aufrufe stehen in der README. Keine Testmails oder Migrationen gegen die bestehende Installation ausgeführt. Für künftige fachliche Routen festlegen, ob `verified` erforderlich ist, und bei E-Mail-Änderungen den Zustand zurücksetzen.

Quellen: [Paket und Versionsvorgaben](https://packagist.org/packages/jrean/laravel-user-verification), [geprüfte Kernmethoden](https://github.com/jrean/laravel-user-verification/blob/0c6b7b3725da85bc1b5d55b8936ab12dc26b2bc6/src/UserVerification.php), [Paket-Provider](https://github.com/jrean/laravel-user-verification/blob/0c6b7b3725da85bc1b5d55b8936ab12dc26b2bc6/src/UserVerificationServiceProvider.php), [Paket-Routen](https://github.com/jrean/laravel-user-verification/blob/0c6b7b3725da85bc1b5d55b8936ab12dc26b2bc6/src/routes.php), [Laravel-Verifizierung](https://laravel.com/framework/docs/13.x/verification).
