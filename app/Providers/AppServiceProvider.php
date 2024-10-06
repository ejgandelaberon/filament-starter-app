<?php

declare(strict_types=1);

namespace App\Providers;

use App\DataTable\DTO\DataTableRequest;
use App\Filament\FilamentConfigurations;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\SerializableClosure\SerializableClosure;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DataTableRequest::class, function (Application $app) {
            return DataTableRequest::fromRequest($app->make(Request::class));
        });
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

        Model::shouldBeStrict();

        SerializableClosure::setSecretKey(Config::string('app.key'));
    }
}
