<?php

use App\Actions\Setup\AdminAction;
use Illuminate\Contracts\Console\Kernel;

// Browser passwords travel through STDIN, never process arguments or log messages.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

try {
    chdir(dirname(__DIR__));
    require 'vendor/autoload.php';
    $app = require 'bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();
    $data = json_decode(stream_get_contents(STDIN), true, 512, JSON_THROW_ON_ERROR);
    (new AdminAction)->handle($data);
} catch (Throwable) {
    // Avoid Laravel exception rendering/logging of sensitive setup payloads.
    exit(1);
}
