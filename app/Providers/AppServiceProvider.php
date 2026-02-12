<?php

declare(strict_types=1);

namespace App\Providers;

use App\Filament\FilamentConfigurations;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Override;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
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

        Gate::before(function (User $user): ?bool {
            if ($user->isSuperAdmin()) {
                return true;
            }

            return null;
        });

        Gate::define('viewLogViewer', fn (User $user): bool => $user->isSuperAdmin());
        Gate::define('viewPulse', fn (User $user): bool => $user->isSuperAdmin());

        Model::shouldBeStrict();
    }
}
