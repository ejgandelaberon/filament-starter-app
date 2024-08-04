<?php

declare(strict_types=1);

namespace App\Providers;

use App\Filament\FilamentConfigurations;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Column;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(FilamentConfigurations $filamentConfigurations): void
    {
        $filamentConfigurations->boot();
    }
}
