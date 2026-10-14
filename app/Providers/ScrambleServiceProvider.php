<?php

namespace App\Providers;

use App\Support\Documentation\ApiOperationTransformer;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;

class ScrambleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Scramble::configure()
            ->routes(fn (Route $route): bool => $route->uri() === '/' || str_starts_with($route->uri(), 'api/'))
            ->withDocumentTransformers(function (OpenApi $document): void {
                $document->secure(SecurityScheme::apiKey('cookie', config('session.cookie'))->as('sessionCookie'));
                foreach ($document->paths as $path) {
                    $path->path = trim($path->path, '/');
                }
            })
            ->withOperationTransformers(ApiOperationTransformer::class);
    }
}
