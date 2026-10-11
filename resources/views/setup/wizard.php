<?php

// Plain PHP template: available before Composer or Laravel have been initialized.
$escape = fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$csrf = $escape($_SESSION['setup_csrf'] ?? '');
$status = $_SESSION['setup_steps'] ?? [];
$base = rtrim($environment['APP_URL'] ?? 'http://localhost:8000', '/');
if (! in_array(parse_url($base, PHP_URL_SCHEME), ['http', 'https'], true)) {
    $base = 'http://localhost:8000';
}
$defaults = [
    'path' => ($environment['DB_CONNECTION'] ?? 'sqlite') === 'sqlite' ? ($environment['DB_DATABASE'] ?? 'database/database.sqlite') : 'database/database.sqlite',
    'host' => $environment['DB_HOST'] ?? '127.0.0.1',
    'port' => $environment['DB_PORT'] ?? '3306',
    'database' => ($environment['DB_CONNECTION'] ?? 'sqlite') === 'sqlite' ? 'laravel' : ($environment['DB_DATABASE'] ?? 'laravel'),
    'username' => $environment['DB_USERNAME'] ?? 'root',
    'api' => $environment['APP_URL'] ?? 'http://localhost:8000',
    'frontend' => $environment['CORS_ALLOWED_ORIGINS'] ?? 'http://localhost:9200',
];
$field = function (string $name, string $label, string $type = 'text', ?string $default = null) use ($escape, $defaults): void {
    $value = $type === 'password' ? '' : ($default ?? $defaults[$name] ?? '');
    echo '<label>'.$escape($label).'<input type="'.$escape($type).'" name="'.$escape($name).'" value="'.$escape($value).'" autocomplete="'.($type === 'password' ? 'new-password' : 'off').'"></label>';
};
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LaravelAPI · Einrichtung</title>
    <style>
        :root{color-scheme:light;font-family:system-ui,sans-serif;color:#17283b;background:#eef2f7}*{box-sizing:border-box}body{margin:0}main{max-width:1050px;margin:40px auto;padding:0 20px}header{margin-bottom:24px}h1{margin:8px 0;font-size:2rem}h2{margin-top:0}p{line-height:1.6}.eyebrow{color:#405975;font-weight:650;font-size:.85rem;letter-spacing:.06em}.layout{display:grid;grid-template-columns:260px 1fr;gap:24px}nav,.panel{background:white;border:1px solid #d8e1ec;border-radius:12px;padding:24px}nav{padding:12px}nav a{display:block;padding:10px;color:#304c6b;text-decoration:none;border-radius:6px}nav a[aria-current=step]{background:#e6efff;color:#123f83;font-weight:650}.status{display:block;color:#64758a;font-size:.75rem}.notice{padding:14px 18px;border-radius:8px;margin:16px 0;background:#e0f2e8}.error{background:#fce8e6;color:#802d26}label{display:block;margin:16px 0;font-weight:550}input,select{display:block;width:100%;padding:11px;border:1px solid #a6b6c9;border-radius:6px;margin-top:6px;font:inherit}input[type=checkbox]{display:inline;width:auto;margin-right:8px}.confirmation{background:#f2f5f9;padding:12px;border-radius:6px;font-weight:400}.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:24px}button{border:0;border-radius:6px;padding:12px 18px;font:inherit;font-weight:600;cursor:pointer;background:#174d96;color:white}button.secondary{background:#e9eef4;color:#274360}button:focus-visible,a:focus-visible,input:focus-visible,select:focus-visible{outline:3px solid #74a8ee;outline-offset:3px}small{color:#52677f}code{overflow-wrap:anywhere}a{color:#174d96}ul{padding-left:20px;line-height:1.8}dl{display:grid;grid-template-columns:1fr 1fr;gap:8px}dt,dd{margin:0}footer{color:#52677f;font-size:.85rem;margin-top:20px}@media(max-width:760px){main{margin:24px auto}.layout{grid-template-columns:1fr}nav{display:flex;overflow:auto}nav a{flex:0 0 190px}.panel{padding:20px}}
    </style>
</head>
<body><main>
    <header><span class="eyebrow">LARAVELAPI</span><h1>Einrichtung im Browser</h1><p>Ein Schritt nach dem anderen. Jede Änderung wird ausdrücklich bestätigt.</p></header>
    <?php if ($message) { ?><div class="notice" role="status"><?= $escape($message) ?></div><?php } ?>
    <?php if ($error) { ?><div class="notice error" role="alert"><?= $escape($error) ?></div><?php } ?>
    <?php if ($locked) { ?>
        <section class="panel"><h2>Einrichtung gesperrt</h2><p>Der Webserver muss ausschließlich <code>public/</code> ausliefern. Die Freigabedatei gehört in das darüberliegende Projektverzeichnis und darf nicht öffentlich erreichbar sein.</p><p>Nach dem Abschluss bleibt der Assistent gesperrt. Die Anwendung wird dadurch nicht angehalten.</p></section>
    <?php } elseif (! $authenticated) { ?>
        <section class="panel"><h2>Anmelden</h2><p>Den Zugangsschlüssel aus der selbst angelegten Datei <code>.setup-access</code> eingeben. Er ist unabhängig vom späteren Administratorkonto.</p>
            <form method="post" action="setup.php"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="operation" value="login">
                <?php $field('access_key', 'Zugangsschlüssel', 'password') ?>
                <button type="submit">Einrichtung öffnen</button>
            </form>
        </section>
    <?php } else { ?>
        <div class="layout">
            <nav aria-label="Einrichtungsschritte">
                <?php foreach ($steps as $id => $title) { ?><a href="setup.php?step=<?= $escape($id) ?>" <?= $step === $id ? 'aria-current="step"' : '' ?>><?= $escape($title) ?><span class="status"><?= $escape($status[$id] ?? 'offen') ?></span></a><?php } ?>
            </nav>
            <section class="panel">
                <h2><?= $escape($steps[$step]) ?></h2>
                <?php if (! $finished) { ?><form method="post" action="setup.php?step=<?= $escape($step) ?>">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="step" value="<?= $escape($step) ?>">
                    <?php if ($step === 'install') { ?>
                        <p>Composer installiert die Pakete aus dem Lockfile einschließlich Entwicklungspaketen. Laravel-Hooks werden später im Schritt „Erweiterungen“ ausgeführt.</p>
                        <dl><dt>PHP</dt><dd><?= $escape(PHP_VERSION) ?></dd><dt>Prozessausführung</dt><dd><?= function_exists('proc_open') ? 'Verfügbar' : 'Gesperrt' ?></dd><dt>SQLite-Treiber</dt><dd><?= extension_loaded('pdo_sqlite') ? 'Verfügbar' : 'Fehlt' ?></dd><dt>MariaDB/MySQL-Treiber</dt><dd><?= extension_loaded('pdo_mysql') ? 'Verfügbar' : 'Fehlt' ?></dd><dt>vendor/ vorhanden</dt><dd><?= is_file($this->root.'/vendor/autoload.php') ? 'Ja' : 'Nein' ?></dd><dt>.env vorhanden</dt><dd><?= is_file($this->root.'/.env') ? 'Ja' : 'Nein' ?></dd></dl>
                        <p>PHP-CLI, Composer und <code>proc_open</code> müssen auf dem Server verfügbar sein. Bei vorab hochgeladenem <code>vendor/</code> kann dieser Schritt übersprungen werden. Die Installation kann einige Minuten dauern.</p>
                    <?php } elseif ($step === 'environment') { ?>
                        <p>Eine fehlende <code>.env</code> wird aus der sicheren Projektvorlage erstellt. Eine vorhandene Datei bleibt erhalten.</p>
                    <?php } elseif ($step === 'cache') { ?>
                        <p>Konfigurations- und Routencache leeren, damit die aktuelle <code>.env</code> verwendet wird.</p>
                    <?php } elseif ($step === 'key') { ?>
                        <p>Fehlt der Anwendungsschlüssel, wird er neu erzeugt. Ein vorhandener Schlüssel bleibt erhalten.</p>
                    <?php } elseif ($step === 'database') { ?>
                        <p>Verbindung testen und anschließend die Konfiguration speichern. Neue Datenbanken werden angelegt, vorhandene behalten ihre Daten. Für MariaDB/MySQL muss der Server schon laufen; neue Datenbanken benötigen CREATE-Rechte.</p>
                        <label>Datenbanktyp<select name="driver"><?php foreach (['sqlite' => 'SQLite', 'mariadb' => 'MariaDB', 'mysql' => 'MySQL'] as $id => $label) { ?><option value="<?= $id ?>" <?= ($environment['DB_CONNECTION'] ?? 'sqlite') === $id ? 'selected' : '' ?>><?= $label ?></option><?php } ?></select></label>
                        <label>Vorhandene oder neue Datenbank?<select name="mode"><option value="existing">Vorhandene Datenbank</option><option value="new">Neue Datenbank anlegen</option></select></label>
                        <?php $field('path', 'SQLite: Datei innerhalb von database/ (.sqlite)') ?>
                        <details><summary>MariaDB / MySQL: Verbindungsdaten</summary>
                            <?php $field('host', 'Host');
                        $field('port', 'Port', 'number');
                        $field('database', 'Datenbankname');
                        $field('username', 'Benutzer');
                        $field('password', 'Passwort (bei Änderung erneut eingeben)', 'password') ?>
                        </details><p><small>Eine neue Datenbank bleibt bestehen, falls ein späterer Schritt scheitert. Passwörter werden weder angezeigt noch nach einem Fehler ins Formular übernommen.</small></p>
                    <?php } elseif ($step === 'cors') { ?>
                        <p>API und Frontend benötigen hier denselben Host und dasselbe Schema; verschiedene Ports sind möglich. Cookies/CSRF und Credentials müssen vom Frontend mitgesendet werden.</p>
                        <?php $field('api', 'API-URL', 'url');
                        $field('frontend', 'Frontend-Origin', 'url') ?>
                    <?php } elseif ($step === 'migrate') { ?>
                        <p>Migrationen auf dem folgenden Ziel ausführen. Kein Reset der Datenbank.</p><p><strong><?= $escape($environment['DB_CONNECTION'] ?? 'sqlite') ?></strong> · <code><?= $escape($environment['DB_DATABASE'] ?? 'database/database.sqlite') ?></code></p>
                    <?php } elseif ($step === 'packages') { ?>
                        <p>Pakete registrieren, Filament-Assets veröffentlichen und Permission-Cache zurücksetzen. Die Migrationen sollten bereits ausgeführt sein.</p>
                    <?php } elseif ($step === 'admin') { ?>
                        <p>Beide Zugänge vergeben: Filament und Artisan Runner. Bei bestehender E-Mail bleiben Name und Passwort unverändert.</p>
                        <?php $field('email', 'E-Mail', 'email');
                        $field('name', 'Name (nur für neues Konto)');
                        $field('password', 'Passwort für neues Konto (mindestens 12 Zeichen)', 'password');
                        $field('password_confirmation', 'Passwort bestätigen', 'password') ?>
                        <label class="confirmation"><input type="checkbox" name="create_account" value="yes">Falls die E-Mail noch nicht existiert, ein neues Konto mit diesen Angaben anlegen.</label>
                    <?php } elseif ($step === 'runner') { ?>
                        <p>Artisan Runner aktivieren. Zugriff erfolgt über Anmeldung und das Recht <code>run-artisan-runner-ui</code>.</p>
                    <?php } elseif ($step === 'storage') { ?>
                        <p>Öffentlichen Link für zukünftige Media-Dateien erstellen. Unter Windows können dafür zusätzliche Symlink-Rechte erforderlich sein.</p>
                    <?php } elseif ($step === 'brain') { ?>
                        <p>Architekturgraph erzeugen. Nur mit Entwicklungspaketen und <code>APP_ENV=local</code> verfügbar; andernfalls überspringen.</p>
                    <?php } elseif ($step === 'finish') { ?>
                        <p>Abschließen sperrt diesen Assistenten dauerhaft und entfernt den Zugangsschlüssel. Ausgelassene Schritte werden dadurch nicht ausgeführt. Anwendung, Queue und Backup werden nicht gestartet.</p>
                    <?php } ?>
                    <label class="confirmation"><input type="checkbox" name="confirm" value="yes">Ich bestätige ausdrücklich die Ausführung dieses Schritts<?= $step === 'admin' ? ' einschließlich beider Zugangsrechte' : '' ?>.</label>
                    <div class="actions"><button type="submit" name="operation" value="run"><?= $step === 'finish' ? 'Abschließen und sperren' : 'Schritt ausführen' ?></button><?php if ($step !== 'finish') { ?><button class="secondary" type="submit" name="operation" value="skip">Überspringen</button><?php } ?></div>
                </form><?php } ?>
                <?php if ($step === 'finish') { ?>
                    <h3>Adressübersicht</h3><p>Erreichbarkeit hängt von den ausgeführten Schritten und der Webserver-Konfiguration ab.</p>
                    <ul><?php foreach (['/api/status' => 'API-Status', '/sanctum/csrf-cookie' => 'CSRF-Cookie', '/filament/login' => 'Filament', '/artisan/login' => 'Artisan-Anmeldung', '/artisan' => 'Artisan Runner'] as $path => $label) { ?><li><?= $escape($label) ?>: <a href="<?= $escape($base.$path) ?>"><?= $escape($base.$path) ?></a></li><?php } ?></ul>
                    <p>API: <code>POST /api/login</code>, <code>GET /api/user</code>, <code>POST /api/logout</code>.</p>
                    <?php if (($environment['APP_ENV'] ?? 'local') === 'local') { ?><p>Telescope: <a href="<?= $escape($base.'/telescope') ?>">/telescope</a> · Brain: <a href="<?= $escape($base.'/_laravel-brain') ?>">/_laravel-brain</a> (Entwicklungspakete).</p><?php } ?>
                    <p>Frontend-Origin: <code><?= $escape($environment['CORS_ALLOWED_ORIGINS'] ?? 'http://localhost:9200') ?></code>.</p>
                <?php } ?>
                <?php if (! $finished) { ?><form method="post" action="setup.php" class="actions"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="operation" value="logout"><button class="secondary" type="submit">Abmelden</button></form><?php } ?>
            </section>
        </div>
    <?php } ?>
    <footer>Bestätigte Änderungen bleiben bei Abbruch bestehen. Anleitung und offene Aufgaben: docs/SETUP.md. Kopia und externe Dienste werden separat eingerichtet.</footer>
</main></body></html>
