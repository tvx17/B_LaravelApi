<?php

use Illuminate\Contracts\Console\Kernel;

require dirname(__DIR__).'/vendor/autoload.php';

$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

function documentationValue(mixed $value, string $path = ''): mixed
{
    $leaf = substr($path, strrpos('.'.$path, '.'));
    if (preg_match('/(?:^|\.)(?:key|previous_keys|password|secret|token|dsn|username|url|private_key|certificate|credentials)$/i', $path)
        || preg_match('/(?:secret|access_key|api_key|auth_token|user_handle_secret|^password$|_password$)/i', $leaf)) {
        return '[REDACTED: Wert lokal prüfen]';
    }

    if (is_array($value)) {
        $result = [];
        foreach ($value as $key => $item) {
            $result[$key] = documentationValue($item, $path.'.'.$key);
        }

        return $result;
    }

    if ($value instanceof DateInterval) {
        return ['type' => DateInterval::class, 'interval' => $value->format('P%yY%mM%dDT%hH%iM%sS')];
    }

    if (is_object($value)) {
        return '[Object: '.$value::class.']';
    }

    if (is_resource($value)) {
        return '[Resource]';
    }

    if (is_string($value) && (preg_match('/(?:base64:|-----BEGIN .*PRIVATE KEY|:\/\/[^\/\s]+@)/', $value))) {
        return '[REDACTED: möglicher Schlüssel oder Zugangsdaten]';
    }

    return $value;
}

$configuration = $app['config']->all();
ksort($configuration);
$output = "# Laravel-Konfigurationsreferenz\n\nStand: ".date('Y-m-d').". Automatisch aus der lokalen Laravel-Konfiguration erzeugt.\n\n";
$output .= "Diese Referenz umfasst alle aktuell geladenen Konfigurationsgruppen einschließlich Paketdefaults. Werte stammen aus dem CLI-Bootstrap; Middleware kann sie pro Request ändern. Keine vollständige `.env` wird kopiert. Zugangsdaten, Schlüssel und URLs werden vorsorglich maskiert; maskierte Werte ausschließlich lokal prüfen. Pfade sind Momentaufnahmen dieses Rechners. Objektwerte werden typisiert dargestellt.\n\n";
$output .= "Aktualisieren: `php scripts/Export-LaravelConfiguration.php`. Übersicht und Nutzung: [Laravel-Handbuch](../LARAVEL_HANDBUCH.md). Maßgeblich bleiben `config/`, `bootstrap/` und die lokale Umgebung.\n\n";
foreach ($configuration as $group => $values) {
    $output .= '## '.$group."\n\n```json\n";
    $output .= json_encode(documentationValue($values, $group), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $output .= "\n```\n\n";
}

$target = dirname(__DIR__).'/docs/LARAVEL_KONFIGURATION.md';
if (! is_dir(dirname($target))) {
    mkdir(dirname($target), 0755, true);
}
file_put_contents($target, $output);
echo 'Konfigurationsreferenz geschrieben: '.count($configuration)." Gruppen.\n";
