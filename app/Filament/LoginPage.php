<?php

declare(strict_types=1);

namespace App\Filament;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Width;

class LoginPage extends Login
{
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),

            Action::make('dev_login')
                ->visible(fn (): bool => app()->isLocal())
                ->schema([
                    Select::make('user_id')
                        ->label('User')
                        ->required()
                        ->native(false)
                        ->searchable()
                        ->options(function (): array {
                            return User::query()
                                ->with('roles')
                                ->get(['id', 'name'])
                                ->mapWithKeys(function (User $user): array {
                                    $roles = $user->roles->pluck('name')->join(', ');

                                    return [$user->id => "{$user->name} ({$roles})"]; // @phpstan-ignore-line
                                })
                                ->toArray();
                        }),
                ])
                ->modalWidth(Width::Medium)
                ->action(function (array $data): LoginResponse {
                    $userId = data_get($data, 'user_id');

                    Filament::auth()->loginUsingId($userId);

                    return app(LoginResponse::class);
                }),
        ];
    }
}
