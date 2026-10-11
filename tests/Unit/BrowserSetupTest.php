<?php

namespace Tests\Unit;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class BrowserSetupTest extends TestCase
{
    public function test_browser_setup_before_dependencies_guards_changes_and_configures_an_isolated_checkout(): void
    {
        $source = dirname(__DIR__, 2);
        $root = sys_get_temp_dir().'/browser-setup-'.bin2hex(random_bytes(8));
        mkdir($root);
        $files = ['public/setup.php', 'app/Support/SetupAccess.php', 'app/Actions/Setup/IndexAction.php', 'app/Actions/Setup/RunAction.php', 'resources/views/setup/wizard.php', 'scripts/SetupEnvironment.php', '.env.example'];
        foreach ($files as $file) {
            if (! is_dir(dirname($root.'/'.$file))) {
                mkdir(dirname($root.'/'.$file), 0777, true);
            }
            copy($source.'/'.$file, $root.'/'.$file);
        }
        mkdir($root.'/database');
        $socket = stream_socket_server('tcp://127.0.0.1:0', $errno, $error);
        $this->assertIsResource($socket, $error);
        $address = stream_socket_get_name($socket, false);
        fclose($socket);
        $log = tmpfile();
        $process = proc_open([PHP_BINARY, '-S', $address, '-t', $root.'/public'], [['pipe', 'r'], $log, $log], $pipes, $root);
        $this->assertIsResource($process);
        fclose($pipes[0]);
        $cookie = '';
        $token = '';
        $request = function (array $post = [], string $query = '') use ($address, &$cookie, &$token): string {
            $context = stream_context_create(['http' => ['method' => $post ? 'POST' : 'GET', 'header' => "Content-Type: application/x-www-form-urlencoded\r\nCookie: $cookie\r\n", 'content' => $post ? http_build_query($post) : '', 'ignore_errors' => true, 'timeout' => 10]]);
            $body = file_get_contents('http://'.$address.'/setup.php'.$query, false, $context);
            $headers = function_exists('http_get_last_response_headers') ? http_get_last_response_headers() : ($http_response_header ?? []);
            foreach ($headers as $header) {
                if (preg_match('/^Set-Cookie: ([^;]+)/i', $header, $match)) {
                    $cookie = $match[1];
                }
            }
            if (preg_match('/name="csrf" value="([a-f0-9]+)"/', $body, $match)) {
                $token = $match[1];
            }

            return $body;
        };
        $csrf = function (string $html): string {
            $this->assertSame(1, preg_match('/name="csrf" value="([a-f0-9]+)"/', $html, $match));

            return $match[1];
        };
        try {
            $ready = false;
            for ($i = 0; $i < 50; $i++) {
                $connection = @stream_socket_client('tcp://'.$address, $errno, $error, 0.1);
                if ($connection) {
                    fclose($connection);
                    $ready = true;
                    break;
                }
                usleep(100000);
            }
            $this->assertTrue($ready, 'PHP web server did not start.');
            $html = $request();
            $this->assertStringContainsString('Einrichtung gesperrt', $html);
            $this->assertFileDoesNotExist($root.'/.env');
            $key = bin2hex(random_bytes(32));
            file_put_contents($root.'/.setup-access', $key);
            $html = $request();
            $html = $request(['csrf' => $csrf($html), 'operation' => 'login', 'access_key' => $key]);
            $this->assertStringContainsString('Angemeldet.', $html);
            $token = $csrf($html);
            $html = $request(['csrf' => $token, 'operation' => 'run', 'step' => 'environment', 'confirm' => 'yes']);
            $this->assertStringContainsString('zuerst Composer', $html);
            $this->assertFileDoesNotExist($root.'/.env');
            mkdir($root.'/vendor');
            file_put_contents($root.'/vendor/autoload.php', '<?php require '.var_export($source.'/vendor/autoload.php', true).';');
            file_put_contents($root.'/artisan', '<?php exit(0);');
            $html = $request(['csrf' => 'wrong', 'operation' => 'run', 'step' => 'environment', 'confirm' => 'yes']);
            $this->assertStringContainsString('Formularschutz ungültig', $html);
            $this->assertFileDoesNotExist($root.'/.env');
            $html = $request(['csrf' => $token, 'operation' => 'run', 'step' => 'environment']);
            $this->assertStringContainsString('Bestätigung fehlt', $html);
            $this->assertFileDoesNotExist($root.'/.env');
            $oldToken = $token;
            $request(['csrf' => $token, 'operation' => 'run', 'step' => 'environment', 'confirm' => 'yes']);
            $this->assertFileExists($root.'/.env');
            $html = $request(['csrf' => $oldToken, 'operation' => 'run', 'step' => 'key', 'confirm' => 'yes']);
            $this->assertStringContainsString('Formularschutz ungültig', $html);
            $this->assertEmpty(Dotenv::parse(file_get_contents($root.'/.env'))['APP_KEY']);
            $html = $request(['csrf' => $token, 'operation' => 'run', 'step' => 'key', 'confirm' => 'yes']);
            $this->assertStringContainsString('Anwendungsschlüssel gespeichert', $html);
            $before = file_get_contents($root.'/.env');
            $html = $request(['csrf' => $token, 'operation' => 'run', 'step' => 'database', 'confirm' => 'yes', 'driver' => 'sqlite', 'mode' => 'new', 'path' => '../outside.sqlite']);
            $this->assertStringContainsString('innerhalb von database/', $html);
            $this->assertSame($before, file_get_contents($root.'/.env'));
            $request(['csrf' => $token, 'operation' => 'run', 'step' => 'database', 'confirm' => 'yes', 'driver' => 'sqlite', 'mode' => 'new', 'path' => 'database/database.sqlite']);
            $this->assertFileExists($root.'/database/database.sqlite');
            $request(['csrf' => $token, 'operation' => 'run', 'step' => 'cors', 'confirm' => 'yes', 'api' => 'http://localhost:8000', 'frontend' => 'http://localhost:9200']);
            $environment = Dotenv::parse(file_get_contents($root.'/.env'));
            $this->assertSame('http://localhost:9200', $environment['CORS_ALLOWED_ORIGINS']);
            $this->assertSame('localhost:9200', $environment['SANCTUM_STATEFUL_DOMAINS']);
            $html = $request(['csrf' => $token, 'operation' => 'run', 'step' => 'finish', 'confirm' => 'yes']);
            $this->assertStringContainsString('abgeschlossen und gesperrt', $html);
            $this->assertStringContainsString('/filament/login', $html);
            $this->assertFileExists($root.'/.setup-complete');
            $this->assertFileDoesNotExist($root.'/.setup-access');
            $this->assertStringContainsString('Einrichtung gesperrt', $request());
        } finally {
            proc_terminate($process);
            proc_close($process);
            fclose($log);
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
            }
            rmdir($root);
        }
    }
}
