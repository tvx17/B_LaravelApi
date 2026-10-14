<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\ScrambleServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    AdminPanelProvider::class,
    ScrambleServiceProvider::class,
];
