<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditTeamProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Team Profile';
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
        ]);
    }
}
